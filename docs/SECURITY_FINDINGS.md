# Security Review Findings

Scope: full application source (`app/`, `views/`, `public/`, `config/`, `database/`, root entry points).
Vendored libraries (`vendor/fpdf`, `vendor/phpqrcode`) were inventoried but not audited line by line.

Severity key: **Critical** = remote compromise / mass data exposure, **High** = privilege or integrity
loss requiring little effort, **Medium** = meaningful weakening of a control, **Low** = hardening.

---

## Fixed in this change

| # | Severity | Issue | Location | Fix |
|---|----------|-------|----------|-----|
| 1 | Critical | `.env` (DB credentials, `APP_KEY`) committed to the repository and served from the web root — the root `.htaccess` did not block dotfiles, so `GET /.env` returned the file | `.env`, `.htaccess` | Untracked `.env`, added `.gitignore` and `.env.example`, root `.htaccess` now denies dotfiles, `.log`/`.sql`/`.zip`/`.md` files and the `app/ config/ database/ docs/ logs/ storage/ vendor/` directories |
| 2 | Critical | Unrestricted file write on discharge-summary upload: `move_uploaded_file()` used the attacker-supplied filename into `public/uploads/`, a directory with no script-execution guard — a `.php` upload is remote code execution | `DischargeController::saveSummary()` | Routed through the `Upload` helper (extension/MIME whitelist, random filename, hardened target directory) |
| 3 | Critical | Application source archive and bootstrapping test script exposed over HTTP (`test_foundation.zip`, `test_foundation.php`, which connects to the DB and writes config rows when requested) | repository root | Files removed; `.htaccess` blocks `test_*.php` and archive extensions |
| 4 | High | Attendance JSON endpoints had no permission check — any authenticated user (including a receptionist or doctor) could enumerate full employee records and mark check-in/out for an arbitrary `employee_id` | `AttendanceController::fetchEmployee/markAttendance/todayAttendance/generateQR` | `Permission::check()` added on each; `markAttendance` also verifies a CSRF token |
| 5 | High | Missing CSRF verification on state-changing POST handlers | `AttendanceController` (attendance save, leave apply, QR mark), `BillingController` (payment, refund), `DischargeController`, `InventoryController`, `SalaryController`, `WebsiteController` (comment, enquiry) | `Security::verifyRequestToken()` added; the public contact form now emits `csrf_field()` and the QR scanner sends `X-CSRF-Token` |
| 6 | High | Invoice receipts readable by any logged-in user regardless of role | `BillingController::receiptPrint()` | Permission check added |
| 7 | Medium | SQL built by string interpolation of `$branchId` (currently an int cast from session, so not exploitable today, but a pattern that breaks the moment the source changes) | `ReceptionController` (3x), `Followup::getStats()` | Converted to bound parameters |
| 8 | Medium | Debug/verification endpoints reachable in production: `/admin/pdf-test`, `/admin/qr-test`, `/admin/upload-test`, plus a dashboard panel disclosing the PHP version and environment | `public/index.php`, `AdminController`, `views/admin/dashboard.php` | Routes, handlers, view and dashboard panel removed |
| 9 | Medium | Slot-availability API logged request data to the PHP error log and returned an internal `debug` object (raw schedule rows) to unauthenticated callers | `AppointmentController::getSlotsAjax()` | Debug logging and payload removed |
| 10 | Medium | WhatsApp gateway calls disabled TLS peer verification, exposing the API key to interception | `Helpers/WhatsApp.php` | `CURLOPT_SSL_VERIFYPEER`/`VERIFYHOST` enabled |
| 11 | Medium | Password-reset OTP: no rate limit on request or verification (a 6-digit code is brute-forceable), and the code came from `mt_rand()` | `AuthController::sendOtp/verifyOtp` | Per-IP rate limits added; OTP now generated with `random_int()` |
| 12 | Medium | Public comment/enquiry forms accepted unbounded, unsanitised input with no abuse control | `WebsiteController` | Sanitisation, email validation, length caps and per-IP rate limiting added |
| 13 | Low | `logs/app.log` committed to the repository | `logs/app.log` | Untracked and ignored |

---

## Action required by the operator (cannot be fixed in code)

1. **Rotate the leaked credentials.** The database user/password and `APP_KEY` in the committed `.env`
   must be considered public. Removing the file from the working tree does **not** remove it from git
   history — rotate first, then rewrite history (`git filter-repo`) if the repository is or ever was public.
2. **Set `APP_ENV=production`** in the deployed `.env`. In `development`, `config/config.php` prints
   exception messages, file paths and full stack traces to the browser.
3. **Change the seeded default admin password** (`database/migrations_v3.sql`) on every install.

## Known remaining issues (not addressed here)

- **No dependency manifest.** `vendor/` contains hand-copied copies of FPDF and phpqrcode with no
  `composer.json`/`composer.lock`, so there is no version pinning and no way to receive security
  updates. Recommend adopting Composer (`setasign/fpdf`, or a maintained QR library) so advisories
  can be tracked.
- **Broken route** `/admin/employees/generate-id-cards` points at `AttendanceController@generateIDCards`,
  which does not exist. The router throws, which in a `development` deployment renders a stack trace.
- **User enumeration** on `/forgot-password`: the response distinguishes known from unknown addresses.
  This is a deliberate product decision per the code comment; flagged for awareness.
- **Authorization is coarse.** Several modules gate on `manage_reception_dashboard` or
  `manage_employees` with a `// Or ... role` comment; billing, payroll and inventory should get
  dedicated permission slugs. Note also that `manage_employees`, `view_logs`, `manage_branches` and
  `manage_reception_dashboard` are referenced in code but never seeded into the `permissions` table,
  so today only the super-admin bypass grants them.
- **No branch scoping on record lookups.** `BillingController::receiptPrint()`,
  `DischargeController::printSummary()` and similar detail views fetch by primary key only, so a user
  from branch A can read a branch B record by guessing an ID. Fixing this needs an ownership check in
  each model lookup.
- **Session cookie `SameSite=Lax`** is reasonable, but the HTTPS redirect in `public/.htaccess` is
  commented out; enable it so the `secure` cookie flag actually engages.
- `docs/SECURITY_AUDIT_REPORT.md` rates the system "LOW risk" with every control passing. It did not
  reflect reality; treat it as historical.

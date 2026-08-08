# MedClinic — Installation Guide

## Requirements
- PHP 8.1+ with PDO, PDO_MySQL, fileinfo, openssl, mbstring extensions
- MySQL 8.0+ or MariaDB 10.6+
- Apache 2.4+ with mod_rewrite enabled
- XAMPP (local) or any LAMP/LEMP stack

---

## Step 1: Clone / Copy Files

```
C:\xampp\htdocs\clinic\
```

Ensure the directory structure is:
```
clinic/
  app/
  config/
  database/
  public/
  views/
  vendor/
  logs/
  .env
```

---

## Step 2: Create Database

```sql
CREATE DATABASE clinic_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import the schema:
```
mysql -u root -p clinic_db < database/migrations_v1.sql
mysql -u root -p clinic_db < database/migrations_v2.sql
mysql -u root -p clinic_db < database/migrations_v3.sql
```

Run performance indexes:
```
mysql -u root -p clinic_db < database/indexes.sql
```

---

## Step 3: Configure Environment

Copy `.env.example` to `.env` and fill in values:

```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/clinic/public

DB_HOST=localhost
DB_PORT=3306
DB_NAME=clinic_db
DB_USER=root
DB_PASS=

SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your@email.com
SMTP_PASS=your_app_password
SMTP_SECURE=tls
SMTP_FROM_EMAIL=noreply@medclinic.com
SMTP_FROM_NAME=MedClinic

WHATSAPP_API_URL=
WHATSAPP_API_KEY=
WHATSAPP_SENDER_NUMBER=
```

---

## Step 4: Apache Virtual Host (Optional)

Add to `httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName medclinic.local
    DocumentRoot "C:/xampp/htdocs/clinic/public"
    <Directory "C:/xampp/htdocs/clinic/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Step 5: Set Directory Permissions

```bash
chmod 755 public/assets/uploads
chmod 755 logs/
chmod 755 storage/backups/
```

On Windows: ensure the `logs/` and `public/assets/uploads/` directories are writable.

---

## Step 6: Default Admin Login

```
URL:      http://localhost/clinic/public/login
Username: admin
Password: Admin@1234
```

> ⚠️ **Change the default password immediately after first login** via Admin → System Settings.

---

## Step 7: Seed Sample Data (Optional)

```
mysql -u root -p clinic_db < database/seed_data.sql
```

---

## Step 8: Verify Installation

Visit: `http://localhost/clinic/public/admin/dashboard`

Check:
- ✅ Login works
- ✅ Dashboard KPIs load
- ✅ PDF generation: `/admin/pdf-test`
- ✅ QR generation: `/admin/qr-test`
- ✅ File upload: Dashboard upload form

---

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Uncomment HTTPS redirect in `public/.htaccess`
3. Point domain to `public/` directory
4. Configure SMTP credentials
5. Set up daily cron for backup:
   ```cron
   0 2 * * * php /path/to/clinic/artisan backup:run
   ```
6. Enable SSL certificate (Let's Encrypt recommended)

---

## Troubleshooting

| Error | Solution |
|-------|----------|
| Blank page | Enable `display_errors` in `.env` (APP_ENV=development) |
| Database connection failed | Verify DB_* in `.env`, check MySQL service |
| File upload fails | Check `public/assets/uploads/` is writable |
| Email not sending | Verify SMTP credentials, check spam folder |
| Session issues | Ensure `session.save_path` is writable |

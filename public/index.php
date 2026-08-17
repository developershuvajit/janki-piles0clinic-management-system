<?php
declare(strict_types=1);
 

// বাকি কোড...
// ============================================================
// BOOTSTRAP APPLICATION
// ============================================================
require_once dirname(__DIR__) . '/config/config.php';

// Instantiate the Router
$router = new \App\Router();

// ============================================================
// 1. PUBLIC / WEBSITE ROUTES
// ============================================================
$router->get('/', 'WebsiteController@home');
$router->get('/about', 'WebsiteController@about');
$router->get('/doctors', 'WebsiteController@doctors');
$router->get('/treatments', 'WebsiteController@treatments');
$router->get('/treatments/{slug}', 'WebsiteController@treatmentDetail');
$router->get('/faqs', 'WebsiteController@faqs');
$router->get('/insurance', 'WebsiteController@insurance');
$router->get('/health-packages', 'WebsiteController@healthPackages');
$router->get('/gallery', 'WebsiteController@gallery');
$router->get('/blog', 'WebsiteController@blog');
$router->get('/blog/{slug}', 'WebsiteController@blogDetail');
$router->post('/blog/comment/save', 'WebsiteController@saveComment');
$router->get('/contact', 'WebsiteController@contact');
$router->post('/contact/enquiry/save', 'WebsiteController@saveEnquiry');

// ============================================================
// 2. AUTHENTICATION ROUTES
// ============================================================
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/logout', 'AuthController@logout');

// ============================================================
// 3. FORGOT PASSWORD / OTP ROUTES
// ============================================================
$router->get('/forgot-password', 'AuthController@showForgotPassword');
$router->post('/forgot-password', 'AuthController@sendOtp');
$router->get('/verify-otp', 'AuthController@showVerifyOtp');
$router->post('/verify-otp', 'AuthController@verifyOtp');
$router->get('/reset-password', 'AuthController@showResetPassword');
$router->post('/reset-password', 'AuthController@resetPassword');

// ============================================================
// 4. SUPER ADMIN / ADMIN CORE ROUTES
// ============================================================
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings/save', 'AdminController@saveSettings');
$router->get('/admin/logs', 'AdminController@logs');
// Admin Profile Routes
$router->get('/admin/profile', 'AdminController@profile');
$router->post('/admin/profile/update', 'AdminController@updateProfile');

// ============================================================
// 5. ROLE & PERMISSION MANAGEMENT
// ============================================================
$router->get('/admin/roles', 'RoleController@index');
$router->get('/admin/roles/create', 'RoleController@create');
$router->post('/admin/roles/save', 'RoleController@store');
$router->get('/admin/roles/edit/{id}', 'RoleController@edit');
$router->post('/admin/roles/update/{id}', 'RoleController@update');
$router->get('/admin/roles/delete/{id}', 'RoleController@delete');
$router->get('/admin/permissions', 'RoleController@permissionList');

// ============================================================
// 6. BRANCH MANAGEMENT
// ============================================================
$router->get('/admin/branches', 'BranchController@index');
$router->get('/admin/branches/create', 'BranchController@create');
$router->post('/admin/branches/save', 'BranchController@store');
$router->get('/admin/branches/edit/{id}', 'BranchController@edit');
$router->post('/admin/branches/update/{id}', 'BranchController@update');
$router->get('/admin/branches/delete/{id}', 'BranchController@delete');
$router->get('/admin/branches/dashboard/{id}', 'BranchController@dashboard');

// ============================================================
// 7. BRANCH ADMIN PORTAL
// ============================================================
$router->get('/branch/dashboard/{id}', 'BranchController@dashboard');
$router->get('/branch/patients/{id}', 'BranchController@patients');
$router->get('/branch/appointments/{id}', 'BranchController@appointments');
$router->get('/branch/employees/{id}', 'BranchController@employees');
$router->get('/branch/reports/{id}', 'BranchController@reports');
$router->get('/branch/settings/{id}', 'BranchController@settings');

// ============================================================
// 8. USER MANAGEMENT
// ============================================================
// $router->get('/admin/users', 'UserController@index');
// $router->get('/admin/users/create', 'UserController@create');
// $router->post('/admin/users/save', 'UserController@store');
// $router->get('/admin/users/edit/{id}', 'UserController@edit');
// $router->post('/admin/users/update/{id}', 'UserController@update');
// $router->get('/admin/users/delete/{id}', 'UserController@delete');

// ============================================================
// 9. EMPLOYEE MANAGEMENT
// ============================================================
$router->get('/admin/employees', 'EmployeeController@index');
$router->get('/admin/employees/create', 'EmployeeController@create');
$router->post('/admin/employees/save', 'EmployeeController@store');
$router->get('/admin/employees/edit/{id}', 'EmployeeController@edit');
$router->post('/admin/employees/update/{id}', 'EmployeeController@update');
$router->get('/admin/employees/delete/{id}', 'EmployeeController@delete');
$router->get('/admin/employees/delete-doc/{id}', 'EmployeeController@deleteDoc');




// ============================================================
// 10. ATTENDANCE & QR SCANNER ROUTES
// ============================================================
$router->get('/admin/attendance/scan', 'AttendanceController@scanAttendance');
$router->get('/admin/attendance/fetch-employee', 'AttendanceController@fetchEmployee');
$router->post('/admin/attendance/mark', 'AttendanceController@markAttendance');
$router->get('/admin/attendance/today', 'AttendanceController@todayAttendance');
$router->get('/admin/attendance/register', 'AttendanceController@register');
$router->post('/admin/attendance/save', 'AttendanceController@saveAttendance');
$router->get('/admin/attendance/leaves', 'AttendanceController@leavesList');
$router->post('/admin/attendance/leaves/apply', 'AttendanceController@applyLeave');
$router->get('/admin/attendance/leaves/approve/{id}', 'AttendanceController@approveLeave');
$router->get('/admin/attendance/leaves/reject/{id}', 'AttendanceController@rejectLeave');
$router->get('/admin/attendance/report', 'AttendanceController@attendanceReport');

// ============================================================
// 11. EMPLOYEE ID CARDS
// ============================================================
$router->get('/admin/employees/id-cards', 'AttendanceController@idCards');
$router->post('/admin/employees/generate-id-cards', 'AttendanceController@generateIDCards');

// ============================================================
// 12. QR GENERATOR
// ============================================================
$router->get('/admin/qr/generate', 'AttendanceController@generateQR');

// ============================================================
// 13. HR REPORTS
// ============================================================
$router->get('/admin/hr/reports', 'AttendanceController@hrReports');


$router->get('/reception/attendance/scan', 'AttendanceController@scanAttendance');
$router->get('/reception/attendance/report', 'AttendanceController@attendanceReport');
$router->get('/reception/id-cards', 'AttendanceController@idCards');
$router->post('/reception/id-cards/generate', 'AttendanceController@generateIDCards');
















// ============================================================
// 14. SALARY / PAYROLL MANAGEMENT
// ============================================================
$router->get('/admin/salary', 'SalaryController@index');
$router->post('/admin/salary/settle', 'SalaryController@settleSalary');
$router->get('/admin/salary/payslip/{id}', 'SalaryController@paySlip');

// ============================================================
// 15. PATIENT MANAGEMENT
// ============================================================
$router->get('/admin/patients', 'PatientController@index');
$router->get('/admin/patients/create', 'PatientController@create');
$router->post('/admin/patients/save', 'PatientController@store');
$router->get('/admin/patients/edit/{id}', 'PatientController@edit');
$router->post('/admin/patients/update/{id}', 'PatientController@update');
$router->get('/admin/patients/delete/{id}', 'PatientController@delete');
$router->get('/admin/patients/history/{patientId}', 'PatientController@history');
$router->post('/admin/patients/upload-doc/{id}', 'PatientController@uploadDoc');
$router->get('/admin/patients/delete-doc/{id}', 'PatientController@deleteDoc');

// ============================================================
// 16. APPOINTMENT MANAGEMENT
// ============================================================
$router->get('/admin/appointments', 'AppointmentController@index');
$router->get('/admin/appointments/pending', 'AppointmentController@pendingList');
$router->get('/admin/appointments/approve/{id}', 'AppointmentController@approve');
$router->get('/admin/appointments/cancel/{id}', 'AppointmentController@cancel');
$router->get('/admin/appointments/schedule', 'AppointmentController@schedule');
$router->post('/admin/appointments/schedule/save', 'AppointmentController@saveSchedule');
$router->get('/admin/appointments/slots', 'AppointmentController@getSlotsAjax');

// ============================================================
// 17. IPD WARD ADMISSIONS
// ============================================================
$router->get('/admin/ipd', 'IpdController@index');
$router->get('/admin/ipd/admit', 'IpdController@admitForm');
$router->post('/admin/ipd/admit/save', 'IpdController@saveAdmission');
$router->get('/admin/ipd/nursing-logs/{id}', 'IpdController@nursingLogs');
$router->post('/admin/ipd/nursing-logs/{id}/save', 'IpdController@saveNursingLog');
$router->post('/admin/ipd/procedures/{id}/save', 'IpdController@saveProcedure');
$router->post('/admin/ipd/discharge/{id}', 'IpdController@discharge');
$router->get('/admin/ipd/discharge-summary/{id}', 'DischargeController@summaryForm');
$router->post('/admin/ipd/discharge-summary/save', 'DischargeController@saveSummary');
$router->get('/admin/ipd/discharge-summary/print/{id}', 'DischargeController@printSummary');
$router->get('/admin/ipd/discharge-summary/pdf/{id}', 'DischargeController@pdfSummary');

// ============================================================
// 18. BILLING MANAGEMENT
// ============================================================
$router->get('/admin/billing', 'BillingController@index');
$router->get('/admin/billing/collect/{id}', 'BillingController@collectForm');
$router->post('/admin/billing/pay', 'BillingController@processPayment');
$router->get('/admin/billing/refund/{id}', 'BillingController@refundForm');
$router->post('/admin/billing/refund/save', 'BillingController@processRefund');
$router->get('/admin/billing/receipt/{id}', 'BillingController@receiptPrint');

// ============================================================
// 19. INVENTORY MANAGEMENT
// ============================================================
$router->get('/admin/inventory', 'InventoryController@index');
$router->get('/admin/inventory/low-stock', 'InventoryController@lowStockList');
$router->get('/admin/inventory/purchase', 'InventoryController@purchaseForm');
$router->post('/admin/inventory/purchase/save', 'InventoryController@savePurchase');
$router->post('/admin/inventory/supplier/save', 'InventoryController@saveSupplier');

// ============================================================
// 20. REPORTS & ANALYTICS
// ============================================================
$router->get('/admin/reports', 'ReportsController@dashboard');

// ============================================================
// 21. WEBSITE CMS & BLOG MANAGEMENT
// ============================================================
$router->get('/admin/cms/settings', 'CmsController@index');
$router->post('/admin/cms/settings/save', 'CmsController@saveSettings');
$router->get('/admin/cms/gallery', 'CmsController@gallery');
$router->post('/admin/cms/gallery/album/save', 'CmsController@saveAlbum');
$router->post('/admin/cms/gallery/media/save', 'CmsController@saveMedia');
$router->get('/admin/cms/testimonials', 'CmsController@testimonials');
$router->post('/admin/cms/testimonials/save', 'CmsController@saveTestimonial');

// Blog Management
$router->get('/admin/cms/blogs', 'BlogController@index');
$router->post('/admin/cms/blogs/save', 'BlogController@save');
$router->get('/admin/cms/comments', 'BlogController@comments');
$router->get('/admin/cms/comments/approve/{id}', 'BlogController@approveComment');
$router->get('/admin/cms/comments/reject/{id}', 'BlogController@rejectComment');

// Treatment Management
$router->get('/admin/cms/treatments', 'TreatmentController@index');
$router->post('/admin/cms/treatments/save', 'TreatmentController@save');

// ============================================================
// 22. CRM / ENQUIRY MANAGEMENT
// ============================================================
$router->get('/admin/cms/enquiries', 'EnquiryController@index');
$router->post('/admin/cms/enquiries/update', 'EnquiryController@update');

// ============================================================
// 23. ONLINE APPOINTMENT BOOKING (PUBLIC)
// ============================================================
$router->get('/appointments/book', 'AppointmentController@showOnlineBooking');
$router->post('/appointments/book/otp', 'AppointmentController@sendBookingOtp');
$router->post('/appointments/book/submit', 'AppointmentController@submitOnlineBooking');
$router->get('/appointments/book/success', 'AppointmentController@bookingSuccess');

// ============================================================
// 24. RECEPTION PORTAL (RECEPTIONIST ONLY)
// ============================================================
$router->get('/reception', 'ReceptionController@dashboard');
$router->get('/reception/dashboard', 'ReceptionController@dashboard');
$router->get('/reception/patients', 'ReceptionController@patientsIndex');
$router->get('/reception/patients/create', 'ReceptionController@createPatientForm');
$router->post('/reception/patients/save', 'ReceptionController@savePatient');
$router->get('/reception/patients/edit/{id}', 'ReceptionController@editPatientForm');
$router->post('/reception/patients/update/{id}', 'ReceptionController@updatePatient');
$router->get('/reception/patients/history/{id}', 'ReceptionController@patientHistory');
$router->post('/reception/patients/upload-doc/{id}', 'ReceptionController@uploadPatientDoc');
$router->get('/reception/walk-in', 'ReceptionController@showWalkInForm');
$router->post('/reception/walk-in/save', 'ReceptionController@saveWalkIn');
$router->get('/reception/queues', 'ReceptionController@queuesList');
$router->get('/reception/queues/update/{id}', 'ReceptionController@updateQueue');
$router->get('/reception/ipd', 'ReceptionController@ipdIndex');
$router->get('/reception/ipd/admit', 'ReceptionController@ipdAdmitForm');
$router->post('/reception/ipd/admit/save', 'ReceptionController@saveIpdAdmission');
$router->get('/reception/ipd/beds', 'ReceptionController@ipdBedsView');
$router->get('/reception/billing', 'ReceptionController@billingIndex');
$router->get('/reception/billing/collect/{id}', 'ReceptionController@collectForm');
$router->post('/reception/billing/pay', 'ReceptionController@processPayment');
$router->get('/reception/billing/receipt/{id}', 'ReceptionController@receiptPrint');
$router->get('/reception/billing/refund/{id}', 'ReceptionController@refundForm');
$router->post('/reception/billing/refund/save', 'ReceptionController@processRefund');
$router->get('/reception/medicine-issue', 'ReceptionController@medicineDispatchIndex');
$router->get('/reception/medicine-issue/dispatch/{id}', 'ReceptionController@dispatchMedicine');
$router->get('/reception/medicines', 'ReceptionController@medicinesIndex');
$router->get('/reception/medicines/low-stock', 'ReceptionController@lowStockMedicines');
$router->get('/reception/discharge', 'ReceptionController@dischargeIndex');
$router->post('/reception/discharge/checkout/{id}', 'ReceptionController@completeCheckout');
$router->get('/reception/reports', 'ReceptionController@reportsDashboard');
$router->get('/reception/profile', 'ReceptionController@profile');
$router->post('/reception/profile/update', 'ReceptionController@updateProfile');
$router->get('/reception/followups', 'ReceptionController@followupsIndex');
$router->get('/reception/leads', 'ReceptionController@leadsIndex');
$router->post('/reception/leads/save', 'ReceptionController@saveLead');
$router->get('/reception/leads/status/{id}', 'ReceptionController@updateLeadStatus');
$router->post('/reception/leads/status/{id}', 'ReceptionController@updateLeadStatus');
$router->get('/reception/communication', 'ReceptionController@communicationIndex');
$router->get('/reception/attendance', 'ReceptionController@attendanceIndex');
$router->post('/reception/attendance/save', 'ReceptionController@markAttendance');
$router->get('/reception/search', 'ReceptionController@globalSearchAjax');



$router->get('/reception/appointments', 'AppointmentController@index');
$router->get('/reception/appointments/pending', 'AppointmentController@pendingList');
$router->get('/reception/appointments/approve/{id}', 'AppointmentController@approve');
$router->get('/reception/appointments/cancel/{id}', 'AppointmentController@cancel');
$router->get('/reception/appointments/schedule', 'AppointmentController@schedule');
$router->post('/reception/appointments/schedule/save', 'AppointmentController@saveSchedule');




// ============================================================
// 25. DOCTOR PORTAL (DOCTOR ONLY)
// ============================================================
$router->get('/doctor', 'DoctorController@dashboard');
$router->get('/doctor/dashboard', 'DoctorController@dashboard');
$router->get('/doctor/patients', 'DoctorController@patientsIndex');
$router->get('/doctor/patients/history/{id}', 'DoctorController@patientHistory');
$router->get('/doctor/opd', 'DoctorController@opdQueue');
$router->get('/doctor/opd/consult/{id}', 'DoctorController@consultForm');
$router->post('/doctor/opd/consult/save', 'DoctorController@saveConsultation');





$router->get('/doctor/ipd', 'DoctorController@ipdIndex');
$router->get('/doctor/ipd/visit-notes/{id}', 'DoctorController@visitNotesForm');
$router->post('/doctor/ipd/visit-notes/{id}/save', 'DoctorController@saveVisitNotes');
$router->get('/doctor/ipd/procedure-notes/{id}', 'DoctorController@procedureNotesForm');
$router->post('/doctor/ipd/procedure-notes/{id}/save', 'DoctorController@saveProcedureNotes');




$router->get('/doctor/discharge', 'DoctorController@dischargeIndex');
$router->get('/doctor/discharge/approve/{id}', 'DoctorController@approveDischarge');
$router->get('/doctor/discharge/summary/{id}', 'DoctorController@dischargeSummaryForm');
$router->post('/doctor/discharge/summary/save', 'DoctorController@saveDischargeSummary');
$router->get('/doctor/discharge/summary-print/{id}', 'DoctorController@printDischargeSummary');
$router->get('/doctor/prescriptions', 'DoctorController@prescriptionsIndex');
$router->get('/doctor/prescriptions/create', 'DoctorController@createPrescriptionForm');
$router->post('/doctor/prescriptions/save', 'DoctorController@savePrescription');
$router->get('/doctor/prescriptions/print/{id}', 'DoctorController@printPrescription');
$router->get('/doctor/medicines', 'DoctorController@medicinesIndex');
$router->get('/doctor/billing-summary', 'DoctorController@billingSummaryIndex');
$router->get('/doctor/billing-summary/view/{id}', 'DoctorController@viewBillSummary');
$router->get('/doctor/ai-assist', 'DoctorController@aiAssistAjax');
$router->get('/doctor/reports', 'DoctorController@reportsDashboard');
$router->get('/doctor/profile', 'DoctorController@profile');
$router->post('/doctor/profile/update', 'DoctorController@updateProfile');

// ============================================================
// 26. SYSTEM TEST ROUTES
// ============================================================
$router->get('/admin/pdf-test', 'AdminController@pdfTest');
$router->get('/admin/qr-test', 'AdminController@qrTest');
$router->post('/admin/upload-test', 'AdminController@uploadTest');

// ============================================================
// ROUTER DISPATCH
// ============================================================
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$basePath = dirname($scriptName);

$basePath = str_replace('\\', '/', $basePath);
if ($basePath === '/') {
    $basePath = '';
}

$routeUri = $requestUri;
if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
    $routeUri = substr($requestUri, strlen($basePath));
}

if (!str_starts_with($routeUri, '/')) {
    $routeUri = '/' . $routeUri;
}

$routeUri = explode('?', $routeUri, 2)[0];
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($routeUri, $requestMethod);
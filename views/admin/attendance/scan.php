<?php 
$activePage = 'attendance_scan';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    .scan-container{max-width:900px;margin:0 auto}
    .camera-wrapper{background:#0b1a2b;border-radius:16px;padding:1rem;position:relative;min-height:500px;overflow:hidden}
    .camera-wrapper video{width:100%;border-radius:12px;background:#000;min-height:500px;object-fit:cover}
    .camera-wrapper .scan-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:300px;height:300px;border:3px solid rgba(37,99,235,0.5);border-radius:16px;pointer-events:none;box-shadow:0 0 0 4000px rgba(0,0,0,0.3);animation:pulse-border 2s ease-in-out infinite}
    @keyframes pulse-border{0%,100%{border-color:rgba(37,99,235,0.5)}50%{border-color:rgba(37,99,235,0.8)}}
    .camera-wrapper .scan-overlay .corner{position:absolute;width:25px;height:25px;border-color:#2563eb;border-style:solid;border-width:0;transition:all .3s}
    .camera-wrapper .scan-overlay .corner-tl{top:-2px;left:-2px;border-top-width:4px;border-left-width:4px}
    .camera-wrapper .scan-overlay .corner-tr{top:-2px;right:-2px;border-top-width:4px;border-right-width:4px}
    .camera-wrapper .scan-overlay .corner-bl{bottom:-2px;left:-2px;border-bottom-width:4px;border-left-width:4px}
    .camera-wrapper .scan-overlay .corner-br{bottom:-2px;right:-2px;border-bottom-width:4px;border-right-width:4px}
    .camera-wrapper .scan-line{position:absolute;top:50%;left:15%;right:15%;height:2px;background:linear-gradient(90deg,transparent,#2563eb,transparent);animation:scanLine 2s ease-in-out infinite;opacity:.6;pointer-events:none}
    @keyframes scanLine{0%,100%{transform:translateY(-80px)}50%{transform:translateY(80px)}}
    .btn-camera-toggle{border-radius:40px;padding:.4rem 1.2rem;font-size:.78rem;border:1px solid #e2e8f0;background:#fff;transition:all .15s;cursor:pointer}
    .btn-camera-toggle:hover{background:#f1f5f9}
    .attendance-status{padding:.5rem 1rem;border-radius:8px;font-weight:500;font-size:.85rem}
    .attendance-status.success{background:#e6f5ed;color:#0f7b4a}
    .attendance-status.error{background:#ffe9e9;color:#b33c3c}
    .attendance-status.pending{background:#fef7e8;color:#c5711e}
    .attendance-status.info{background:#e8f0fe;color:#1a56db}
    .attendance-status.late-status{background:#fef7e8;color:#c5711e;border-left:4px solid #f59e0b}
    .badge-soft{background:#f1f4f8;color:#1e293b;padding:.15rem .8rem;border-radius:40px;font-size:.7rem}
    .card-clean{background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);border:1px solid #f0f2f5;padding:1.2rem}
    .table-clean{font-size:.82rem;width:100%;border-collapse:collapse}
    .table-clean th{font-size:.6rem;text-transform:uppercase;color:#6b7a8f;font-weight:600;padding:.4rem .8rem;border-bottom:1px solid #edf2f7;text-align:left}
    .table-clean td{padding:.4rem .8rem;border-bottom:1px solid #f1f5f9;vertical-align:middle}
    .scan-flash{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(15,123,74,.95);color:#fff;padding:2rem 3rem;border-radius:16px;font-size:1.5rem;font-weight:600;z-index:9999;pointer-events:none;animation:flashPop 1.2s ease-out forwards;box-shadow:0 20px 60px rgba(0,0,0,.3);text-align:center}
    .scan-flash.error{background:rgba(179,60,60,.95)}
    .scan-flash .icon{font-size:3rem;display:block;margin-bottom:.5rem}
    .scan-flash .sub-msg{font-size:.7rem;font-weight:400;margin-top:.3rem;opacity:.9}
    @keyframes flashPop{0%{opacity:0;transform:translate(-50%,-50%) scale(.7)}20%{opacity:1;transform:translate(-50%,-50%) scale(1.05)}80%{opacity:1;transform:translate(-50%,-50%) scale(1)}100%{opacity:0;transform:translate(-50%,-50%) scale(.9)}}
    .detected-card{background:#fff;border-radius:12px;padding:1rem 1.2rem;border:2px solid #0f7b4a;box-shadow:0 4px 20px rgba(15,123,74,.15);margin-top:1rem;display:none;animation:slideUp .3s ease}
    .detected-card.show{display:block}
    .detected-card.late{border-color:#f59e0b;box-shadow:0 4px 20px rgba(245,158,11,.15)}
    @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
    .detected-card .emp-avatar{width:50px;height:50px;border-radius:50%;background:#f1f4f8;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;border:2px solid #e2e8f0}
    .detected-card .emp-avatar img{width:100%;height:100%;object-fit:cover}
    .detected-card .emp-avatar .no-photo{font-size:1.5rem;color:#94a3b8}
    .detected-card .emp-name{font-weight:600;color:#0b1a2b;font-size:.95rem}
    .detected-card .emp-detail{font-size:.75rem;color:#94a3b8}
    .detected-card .emp-status{font-size:.7rem;font-weight:600;padding:.2rem .8rem;border-radius:40px;display:inline-block}
    .detected-card .emp-status.checkin{background:#e6f5ed;color:#0f7b4a}
    .detected-card .emp-status.checkout{background:#e8f0fe;color:#1a56db}
    .detected-card .emp-status.late{background:#fef7e8;color:#c5711e}
    .detected-card .shift-info{font-size:.6rem;color:#94a3b8;margin-top:.2rem}
    .table-avatar{width:30px;height:30px;border-radius:50%;object-fit:cover;border:1px solid #e2e8f0}
    .table-avatar-placeholder{width:30px;height:30px;border-radius:50%;background:#f1f4f8;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;font-size:.7rem}
    @media(max-width:768px){.camera-wrapper{min-height:350px}.camera-wrapper video{min-height:350px}.camera-wrapper .scan-overlay{width:200px;height:200px}.scan-flash{font-size:1rem;padding:1.5rem 2rem;width:90%}.scan-flash .icon{font-size:2rem}.detected-card .emp-avatar{width:40px;height:40px}.table-avatar{width:24px;height:24px}}
</style>

<div class="scan-container mt-5">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
                <i class="bi bi-qr-code-scan text-primary"></i> QR Attendance Scanner
            </h5>
            <span style="font-size:0.72rem;color:#94a3b8;">Auto-scan & mark attendance with late detection</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button onclick="switchCamera()" class="btn-camera-toggle" id="cameraToggle">
                <i class="bi bi-arrow-repeat me-1"></i> Switch Camera
            </button>
            <button onclick="resetScanner()" class="btn-camera-toggle">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="camera-wrapper">
        <video id="video" autoplay playsinline></video>
        <div class="scan-overlay">
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
        </div>
        <div class="scan-line"></div>
    </div>

    <div id="attendanceStatus" class="mt-3">
        <div class="attendance-status info">
            <i class="bi bi-camera me-2"></i> Scanning... Place QR code in the frame.
        </div>
    </div>

    <div id="detectedCard" class="detected-card">
        <div class="d-flex align-items-center gap-3">
            <div class="emp-avatar" id="empAvatar">
                <span class="no-photo">👤</span>
            </div>
            <div class="flex-grow-1">
                <div class="emp-name" id="empName">Employee Name</div>
                <div class="emp-detail" id="empDetail">EMP-00000 • Role</div>
                <div class="shift-info" id="shiftInfo">Shift: 09:00 AM - 05:00 PM</div>
            </div>
            <div>
                <span class="emp-status" id="empStatus">✅ Present</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#0f7b4a;" id="todayPresent">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Present</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#c5711e;" id="todayLate">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Late</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#b33c3c;" id="todayAbsent">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Absent</div>
            </div>
        </div>
    </div>

    <div class="card-clean mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                <i class="bi bi-clock-history me-1"></i> Today's Log
            </span>
            <span style="font-size:0.7rem;color:#94a3b8;" id="todayDate"><?= date('d M, Y') ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Role</th>
                        <th>Shift</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceLog">
                    <tr><td colspan="6" class="text-center text-muted" style="padding:2rem;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let currentStream = null;
let currentCamera = 'environment';
let scanInterval = null;
let isScanning = false;
let lastScannedCode = null;
let isProcessing = false;
let scanCooldown = false;
const video = document.getElementById('video');

async function startCamera() {
    try {
        if (currentStream) currentStream.getTracks().forEach(track => track.stop());
        const constraints = { video: { facingMode: currentCamera, width: { ideal: 640 }, height: { ideal: 480 } } };
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        await video.play();
        startScanning();
        updateCameraButton();
        setStatus('info', '<i class="bi bi-camera me-2"></i> Camera ready. Place QR code in the frame.');
    } catch (err) {
        console.error('Camera error:', err);
        setStatus('error', '<i class="bi bi-exclamation-triangle me-2"></i> Unable to access camera. Please allow camera permissions.');
    }
}

function switchCamera() {
    currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
    startCamera();
}

function updateCameraButton() {
    const btn = document.getElementById('cameraToggle');
    btn.innerHTML = currentCamera === 'environment' ? 
        '<i class="bi bi-arrow-repeat me-1"></i> Front Camera' : 
        '<i class="bi bi-arrow-repeat me-1"></i> Back Camera';
}

function setStatus(type, message) {
    document.getElementById('attendanceStatus').innerHTML = `<div class="attendance-status ${type}">${message}</div>`;
}

function showFlash(type, title, message, subMsg = '') {
    const icon = type === 'success' ? '✅' : '❌';
    const color = type === 'success' ? 'rgba(15,123,74,.95)' : 'rgba(179,60,60,.95)';
    const flash = document.createElement('div');
    flash.className = `scan-flash ${type === 'error' ? 'error' : ''}`;
    flash.style.background = color;
    flash.innerHTML = `<span class="icon">${icon}</span>${title}<div class="sub-msg">${message}</div>${subMsg ? `<div class="sub-msg" style="font-weight:300;">${subMsg}</div>` : ''}`;
    document.body.appendChild(flash);
    setTimeout(() => { if (flash.parentNode) flash.remove(); }, 1800);
}

function showDetectedEmployee(employee, action, isLate = false, shiftData = null) {
    const card = document.getElementById('detectedCard');
    card.classList.add('show');
    if (isLate) { card.classList.add('late'); } else { card.classList.remove('late'); }
    
    document.getElementById('empName').textContent = employee.username || 'Unknown';
    
    const roleName = employee.role_name || 'Staff';
    const empCode = `EMP-${String(employee.id).padStart(5, '0')}`;
    document.getElementById('empDetail').textContent = `${empCode} • ${roleName}`;
    
    let shiftStart = '09:00 AM', shiftEnd = '05:00 PM';
    if (shiftData) {
        shiftStart = shiftData.start || '09:00 AM';
        shiftEnd = shiftData.end || '05:00 PM';
    } else if (employee.shift_start) {
        shiftStart = formatTime(employee.shift_start);
        shiftEnd = formatTime(employee.shift_end || '17:00:00');
    }
    document.getElementById('shiftInfo').textContent = `Shift: ${shiftStart} - ${shiftEnd}`;
    
    const avatar = document.getElementById('empAvatar');
    if (employee.photo) {
        const photoPath = employee.photo.startsWith('http') ? employee.photo : '<?= site_url('/') ?>' + employee.photo;
        avatar.innerHTML = `<img src="${photoPath}" alt="Photo">`;
    } else {
        avatar.innerHTML = '<span class="no-photo">👤</span>';
    }
    
    const statusEl = document.getElementById('empStatus');
    if (action === 'checkin') {
        statusEl.className = `emp-status ${isLate ? 'late' : 'checkin'}`;
        statusEl.textContent = isLate ? '⚠️ Late Entry' : '✅ Checked In';
    } else if (action === 'checkout') {
        statusEl.className = 'emp-status checkout';
        statusEl.textContent = '⏰ Checked Out';
    } else {
        statusEl.className = 'emp-status checkin';
        statusEl.textContent = '✅ Present';
    }
}

function startScanning() {
    if (scanInterval) clearInterval(scanInterval);
    isScanning = true;
    scanCooldown = false;
    scanInterval = setInterval(() => {
        if (!isScanning || isProcessing || scanCooldown || video.readyState !== 4) return;
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: "dontInvert" });
        if (code && code.data && code.data !== lastScannedCode) {
            lastScannedCode = code.data;
            processQRCode(code.data);
        }
    }, 300);
}

function processQRCode(qrData) {
    isProcessing = true;
    try {
        const data = JSON.parse(qrData);
        if (data.type === 'employee_id' && data.id) {
            fetchEmployee(data.id);
        } else {
            showFlash('error', 'Invalid QR Code', 'Please scan a valid employee ID card');
            setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Invalid QR code.');
            isProcessing = false;
        }
    } catch (e) {
        showFlash('error', 'Invalid Format', 'QR code format not recognized');
        setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Invalid QR code format.');
        isProcessing = false;
    }
}

function fetchEmployee(employeeId) {
    fetch('<?= site_url('/admin/attendance/fetch-employee') ?>?id=' + employeeId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                autoMarkAttendance(data.employee);
            } else {
                showFlash('error', 'Not Found', 'Employee not found in system');
                setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Employee not found.');
                isProcessing = false;
            }
        })
        .catch(err => {
            showFlash('error', 'Error', 'Failed to fetch employee data');
            setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Error fetching employee data.');
            isProcessing = false;
        });
}

 function autoMarkAttendance(employee) {
    setStatus('info', `<i class="bi bi-person-check me-2"></i> Detected: <strong>${employee.username}</strong> - Processing...`);
    fetch('<?= site_url('/admin/attendance/mark') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_id: employee.id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const isCheckin = data.status === 'checkin';
            const isLate = data.is_late || false;
            const action = isCheckin ? 'checkin' : 'checkout';
            
            // Get employee details with shift and role
            const employeeDetails = {
                name: employee.username || 'Unknown',
                role: employee.role_name || 'Staff',
                shiftStart: employee.shift_start ? formatTime(employee.shift_start) : '09:00 AM',
                shiftEnd: employee.shift_end ? formatTime(employee.shift_end) : '05:00 PM',
                photo: employee.photo || null,
                lateMinutes: data.late_minutes || 0
            };
            
            // Update detected card
            showDetectedEmployee(employee, action, isLate, {
                start: employeeDetails.shiftStart,
                end: employeeDetails.shiftEnd
            });
            
            // Show flash message with employee details
            let flashTitle = isCheckin ? '✅ Checked In' : '✅ Checked Out';
            let flashMsg = `${employeeDetails.name} - ${data.message}`;
            let flashSub = `👤 ${employeeDetails.role} | ⏰ ${employeeDetails.shiftStart} - ${employeeDetails.shiftEnd}`;
            
            if (isCheckin && isLate) {
                flashTitle = '⚠️ Late Check-in';
                flashSub = `👤 ${employeeDetails.role} | ⏰ Late by ${employeeDetails.lateMinutes} minutes`;
            }
            
            showFlash('success', flashTitle, flashMsg, flashSub);
            
            setStatus(isCheckin && isLate ? 'late-status' : 'success', 
                `<i class="bi ${isCheckin && isLate ? 'bi-exclamation-triangle' : 'bi-check-circle-fill'} me-2"></i> ${data.message} - ${employeeDetails.name}`);
            
            loadTodayAttendance();
            loadStats();
            
            setTimeout(() => document.getElementById('detectedCard').classList.remove('show'), 5000);
            scanCooldown = true;
            setTimeout(() => { scanCooldown = false; lastScannedCode = null; isProcessing = false; }, 3000);
        } else {
            showFlash('error', 'Failed', data.message || 'Could not mark attendance');
            setStatus('error', `<i class="bi bi-exclamation-circle me-2"></i> ${data.message || 'Failed to mark attendance.'}`);
            isProcessing = false;
        }
    })
    .catch(err => {
        showFlash('error', 'Error', 'Failed to mark attendance');
        setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Error marking attendance.');
        isProcessing = false;
    });
}

function formatTime(time) {
    if (!time) return '09:00 AM';
    if (time.includes('AM') || time.includes('PM')) return time;
    const parts = time.split(':');
    const hours = parseInt(parts[0]);
    const minutes = parts[1] || '00';
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const hour12 = hours % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

  function loadTodayAttendance() {
    fetch('<?= site_url('/admin/attendance/today') ?>')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('attendanceLog');
            if (data.attendance && data.attendance.length > 0) {
                tbody.innerHTML = data.attendance.map(a => {
                    // Determine status badge
                    let statusBadge = '';
                    let lateDisplay = '';
                    
                    if (a.status === 'present') {
                        statusBadge = '<span class="badge-soft" style="background:#e6f5ed;color:#0f7b4a;">✅ Present</span>';
                    } else if (a.status === 'late') {
                        statusBadge = '<span class="badge-soft" style="background:#fef7e8;color:#c5711e;">⚠️ Late</span>';
                        // Show late minutes from API
                        if (a.late_minutes && a.late_minutes > 0) {
                            lateDisplay = `<div style="font-size:0.55rem;color:#c5711e;margin-top:0.1rem;">${a.late_minutes} min late</div>`;
                        }
                    } else {
                        statusBadge = '<span class="badge-soft" style="background:#ffe9e9;color:#b33c3c;">❌ Absent</span>';
                    }
                    
                    // Get role and shift from API data
                    const roleName = a.role_name || 'Staff';
                    const shiftStart = a.shift_start ? formatTime(a.shift_start) : '09:00 AM';
                    const shiftEnd = a.shift_end ? formatTime(a.shift_end) : '05:00 PM';
                    
                    // Employee photo
                    let photoHtml = a.photo ? 
                        `<img src="${a.photo.startsWith('http') ? a.photo : '<?= site_url('/') ?>' + a.photo}" class="table-avatar">` 
                        : `<div class="table-avatar-placeholder"><i class="bi bi-person"></i></div>`;
                    
                    return `<tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                ${photoHtml}
                                <strong>${a.username || 'Unknown'}</strong>
                            </div>
                        </td>
                        <td style="font-size:0.7rem;color:#6b7a8f;">${roleName}</td>
                        <td style="font-size:0.7rem;color:#6b7a8f;">${shiftStart} - ${shiftEnd}</td>
                        <td>${a.check_in ? formatTime(a.check_in) : '-'}</td>
                        <td>${a.check_out ? formatTime(a.check_out) : '-'}</td>
                        <td>
                            ${statusBadge}
                            ${lateDisplay}
                        </td>
                    </tr>`;
                }).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted" style="padding:2rem;">No attendance records for today.</td></tr>';
            }
        })
        .catch(err => console.error('Error loading attendance:', err));
}

function loadStats() {
    fetch('<?= site_url('/admin/attendance/today') ?>')
        .then(res => res.json())
        .then(data => {
            if (data.attendance) {
                const present = data.attendance.filter(a => a.status === 'present').length;
                const late = data.attendance.filter(a => a.status === 'late').length;
                const absent = data.attendance.filter(a => a.status !== 'present' && a.status !== 'late').length;
                document.getElementById('todayPresent').textContent = present;
                document.getElementById('todayLate').textContent = late;
                document.getElementById('todayAbsent').textContent = absent;
            }
        })
        .catch(err => console.error('Error loading stats:', err));
}

function resetScanner() {
    lastScannedCode = null;
    isProcessing = false;
    scanCooldown = false;
    document.getElementById('detectedCard').classList.remove('show');
    setStatus('info', '<i class="bi bi-arrow-counterclockwise me-2"></i> Scanner reset. Ready to scan.');
}

document.addEventListener('DOMContentLoaded', function() {
    startCamera();
    loadTodayAttendance();
    loadStats();
    setInterval(loadStats, 30000);
});

window.addEventListener('beforeunload', function() {
    if (scanInterval) clearInterval(scanInterval);
    if (currentStream) currentStream.getTracks().forEach(track => track.stop());
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
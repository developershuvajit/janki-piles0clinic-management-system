<?php 
$activePage = 'attendance_scan';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<style>
    .scan-container {
        max-width: 800px;
        margin: 0 auto;
    }
    .camera-wrapper {
        background: #0b1a2b;
        border-radius: 16px;
        padding: 1rem;
        position: relative;
        min-height: 400px;
        overflow: hidden;
    }
    .camera-wrapper video {
        width: 100%;
        border-radius: 12px;
        background: #000;
        min-height: 400px;
        object-fit: cover;
    }
    .camera-wrapper .scan-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 200px;
        height: 200px;
        border: 3px solid rgba(37,99,235,0.5);
        border-radius: 12px;
        pointer-events: none;
        box-shadow: 0 0 0 4000px rgba(0,0,0,0.3);
    }
    .camera-wrapper .scan-overlay .corner {
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #2563eb;
        border-style: solid;
        border-width: 0;
    }
    .camera-wrapper .scan-overlay .corner-tl { top: -2px; left: -2px; border-top-width: 3px; border-left-width: 3px; }
    .camera-wrapper .scan-overlay .corner-tr { top: -2px; right: -2px; border-top-width: 3px; border-right-width: 3px; }
    .camera-wrapper .scan-overlay .corner-bl { bottom: -2px; left: -2px; border-bottom-width: 3px; border-left-width: 3px; }
    .camera-wrapper .scan-overlay .corner-br { bottom: -2px; right: -2px; border-bottom-width: 3px; border-right-width: 3px; }
    .camera-wrapper .scan-line {
        position: absolute;
        top: 50%;
        left: 10%;
        right: 10%;
        height: 2px;
        background: linear-gradient(90deg, transparent, #2563eb, transparent);
        animation: scanLine 2s ease-in-out infinite;
        opacity: 0.6;
        pointer-events: none;
    }
    @keyframes scanLine {
        0%, 100% { transform: translateY(-60px); }
        50% { transform: translateY(60px); }
    }
    .employee-detected {
        background: #fff;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
        margin-top: 1rem;
    }
    .btn-camera-toggle {
        border-radius: 40px;
        padding: 0.4rem 1.2rem;
        font-size: 0.78rem;
        border: 1px solid #e2e8f0;
        background: #fff;
        transition: all 0.15s;
        cursor: pointer;
    }
    .btn-camera-toggle:hover {
        background: #f1f5f9;
    }
    .btn-camera-toggle.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }
    .btn-camera-toggle.active:hover {
        background: #1d4ed8;
    }
    .attendance-status {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .attendance-status.success {
        background: #e6f5ed;
        color: #0f7b4a;
    }
    .attendance-status.error {
        background: #ffe9e9;
        color: #b33c3c;
    }
    .attendance-status.pending {
        background: #fef7e8;
        color: #c5711e;
    }
    .badge-soft {
        background: #f1f4f8;
        color: #1e293b;
        padding: .15rem .8rem;
        border-radius: 40px;
        font-size: .7rem;
    }
    .card-clean {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,.04);
        border: 1px solid #f0f2f5;
        padding: 1.2rem;
    }
    .table-clean {
        font-size: .82rem;
        width: 100%;
        border-collapse: collapse;
    }
    .table-clean th {
        font-size: .6rem;
        text-transform: uppercase;
        color: #6b7a8f;
        font-weight: 600;
        padding: .4rem .8rem;
        border-bottom: 1px solid #edf2f7;
        text-align: left;
    }
    .table-clean td {
        padding: .4rem .8rem;
        border-bottom: 1px solid #f1f5f9;
    }
    @media (max-width: 768px) {
        .camera-wrapper {
            min-height: 300px;
        }
        .camera-wrapper video {
            min-height: 300px;
        }
        .camera-wrapper .scan-overlay {
            width: 150px;
            height: 150px;
        }
        .employee-detected .d-flex {
            flex-direction: column;
            text-align: center;
        }
        .employee-detected .ms-auto {
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }
    }
</style>

<div class="scan-container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
                <i class="bi bi-qr-code-scan text-primary"></i> QR Code Attendance Scanner
            </h5>
            <span style="font-size:0.72rem;color:#94a3b8;">Scan employee ID card QR code to mark attendance</span>
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

    <!-- Camera -->
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

    <!-- Status -->
    <div id="attendanceStatus" class="mt-3"></div>

    <!-- Detected Employee -->
    <div id="detectedEmployee" class="employee-detected" style="display:none;">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div id="empPhoto" style="width:60px;height:60px;border-radius:50%;background:#f1f4f8;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                <i class="bi bi-person fs-2 text-muted"></i>
            </div>
            <div style="flex:1;min-width:150px;">
                <div id="empName" style="font-weight:600;font-size:1rem;color:#0b1a2b;">Employee Name</div>
                <div id="empId" style="font-size:0.78rem;color:#94a3b8;">EMP-000</div>
                <div id="empRole" style="font-size:0.78rem;color:#6b7a8f;">Role</div>
            </div>
            <div id="attendanceAction" class="ms-auto">
                <button onclick="markAttendance()" class="btn btn-primary btn-sm rounded-pill px-4" style="background:#2563eb;border:none;">
                    <i class="bi bi-check-circle me-1"></i> Mark Present
                </button>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Log -->
    <div class="card-clean mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span style="font-size:.65rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#6b7a8f;">
                <i class="bi bi-clock-history me-1"></i> Today's Attendance Log
            </span>
            <span style="font-size:0.7rem;color:#94a3b8;"><?= date('d M, Y') ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-clean">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceLog">
                    <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">No attendance records for today.</td></tr>
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
let detectedEmployeeData = null;

const video = document.getElementById('video');

async function startCamera() {
    try {
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
        }
        
        const constraints = {
            video: {
                facingMode: currentCamera,
                width: { ideal: 640 },
                height: { ideal: 480 }
            }
        };
        
        currentStream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = currentStream;
        await video.play();
        
        startScanning();
        updateCameraButton();
        document.getElementById('attendanceStatus').innerHTML = `
            <div class="attendance-status pending">
                <i class="bi bi-camera me-2"></i> Camera ready. Place QR code in the frame.
            </div>
        `;
    } catch (err) {
        console.error('Camera error:', err);
        document.getElementById('attendanceStatus').innerHTML = `
            <div class="attendance-status error">
                <i class="bi bi-exclamation-triangle me-2"></i> Unable to access camera. Please allow camera permissions.
            </div>
        `;
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

function startScanning() {
    if (scanInterval) clearInterval(scanInterval);
    isScanning = true;
    
    scanInterval = setInterval(() => {
        if (!isScanning || video.readyState !== 4) return;
        
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: "dontInvert",
        });
        
        if (code && code.data) {
            const qrData = code.data;
            if (qrData !== lastScannedCode) {
                lastScannedCode = qrData;
                processQRCode(qrData);
            }
        }
    }, 500);
}

function processQRCode(qrData) {
    try {
        const data = JSON.parse(qrData);
        if (data.type === 'employee_id' && data.id) {
            fetchEmployee(data.id);
        } else {
            showError('Invalid QR code. Please scan a valid employee ID card.');
        }
    } catch (e) {
        showError('Invalid QR code format. Please scan a valid employee ID card.');
    }
}

function fetchEmployee(employeeId) {
    fetch('<?= site_url('/admin/attendance/fetch-employee') ?>?id=' + employeeId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showDetectedEmployee(data.employee);
            } else {
                showError('Employee not found. Please try again.');
            }
        })
        .catch(err => {
            showError('Error fetching employee data.');
            console.error(err);
        });
}

function showDetectedEmployee(employee) {
    detectedEmployeeData = employee;
    const container = document.getElementById('detectedEmployee');
    container.style.display = 'block';
    
    document.getElementById('empName').textContent = employee.username || 'Unknown';
    document.getElementById('empId').textContent = 'EMP-' + (employee.id || '000');
    document.getElementById('empRole').textContent = employee.role_name || 'Staff';
    
    if (employee.photo) {
        document.getElementById('empPhoto').innerHTML = `<img src="<?= site_url('/') ?>${employee.photo}" style="width:60px;height:60px;object-fit:cover;">`;
    } else {
        document.getElementById('empPhoto').innerHTML = '<i class="bi bi-person fs-2 text-muted"></i>';
    }
    
    document.getElementById('attendanceStatus').innerHTML = `
        <div class="attendance-status pending">
            <i class="bi bi-qr-code me-2"></i> Employee detected. Click "Mark Present" to record attendance.
        </div>
    `;
}

function showError(message) {
    document.getElementById('attendanceStatus').innerHTML = `
        <div class="attendance-status error">
            <i class="bi bi-exclamation-circle me-2"></i> ${message}
        </div>
    `;
}

function markAttendance() {
    if (!detectedEmployeeData) return;
    
    const btn = document.querySelector('#attendanceAction button');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';
    
    fetch('<?= site_url('/admin/attendance/mark') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_id: detectedEmployeeData.id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('attendanceStatus').innerHTML = `
                <div class="attendance-status success">
                    <i class="bi bi-check-circle-fill me-2"></i> ${data.message || 'Attendance marked successfully!'}
                </div>
            `;
            loadTodayAttendance();
            setTimeout(() => {
                document.getElementById('detectedEmployee').style.display = 'none';
                detectedEmployeeData = null;
                lastScannedCode = null;
            }, 3000);
        } else {
            showError(data.message || 'Failed to mark attendance.');
        }
    })
    .catch(err => {
        showError('Error marking attendance.');
        console.error(err);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> Mark Present';
    });
}

function loadTodayAttendance() {
    fetch('<?= site_url('/admin/attendance/today') ?>')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('attendanceLog');
            if (data.attendance && data.attendance.length > 0) {
                tbody.innerHTML = data.attendance.map(a => `
                    <tr>
                        <td>${a.username || 'Unknown'}</td>
                        <td>${a.check_in || '-'}</td>
                        <td>${a.check_out || '-'}</td>
                        <td>
                            <span class="badge-soft" style="background:#e6f5ed;color:#0f7b4a;">
                                ${a.status === 'present' ? 'Present' : 'Absent'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">No attendance records for today.</td></tr>';
            }
        })
        .catch(err => {
            console.error('Error loading attendance:', err);
        });
}

function resetScanner() {
    lastScannedCode = null;
    detectedEmployeeData = null;
    document.getElementById('detectedEmployee').style.display = 'none';
    document.getElementById('attendanceStatus').innerHTML = `
        <div class="attendance-status pending">
            <i class="bi bi-arrow-counterclockwise me-2"></i> Scanner reset. Ready to scan.
        </div>
    `;
}

// Start camera and load attendance on page load
document.addEventListener('DOMContentLoaded', function() {
    startCamera();
    loadTodayAttendance();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (scanInterval) clearInterval(scanInterval);
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
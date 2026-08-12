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
    .attendance-status.info {
        background: #e8f0fe;
        color: #1a56db;
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
    .scan-flash {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(15, 123, 74, 0.95);
        color: #fff;
        padding: 2rem 3rem;
        border-radius: 16px;
        font-size: 1.5rem;
        font-weight: 600;
        z-index: 9999;
        pointer-events: none;
        animation: flashPop 1s ease-out forwards;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        text-align: center;
    }
    .scan-flash.error {
        background: rgba(179, 60, 60, 0.95);
    }
    .scan-flash .icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 0.5rem;
    }
    @keyframes flashPop {
        0% { opacity: 0; transform: translate(-50%, -50%) scale(0.7); }
        20% { opacity: 1; transform: translate(-50%, -50%) scale(1.05); }
        80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        100% { opacity: 0; transform: translate(-50%, -50%) scale(0.9); }
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
        .scan-flash {
            font-size: 1rem;
            padding: 1.5rem 2rem;
            width: 90%;
        }
        .scan-flash .icon {
            font-size: 2rem;
        }
    }
</style>

<div class="scan-container">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold text-slate mb-0" style="font-size:1rem;">
                <i class="bi bi-qr-code-scan text-primary"></i> QR Attendance Scanner
            </h5>
            <span style="font-size:0.72rem;color:#94a3b8;">Auto-scan & mark attendance instantly</span>
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
    <div id="attendanceStatus" class="mt-3">
        <div class="attendance-status info">
            <i class="bi bi-camera me-2"></i> Scanning... Place QR code in the frame.
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-3 mt-2">
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#0f7b4a;" id="todayPresent">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Present</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#c5711e;" id="todayAbsent">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Absent</div>
            </div>
        </div>
        <div class="col-4">
            <div class="card-clean text-center">
                <div style="font-size:1.5rem;font-weight:700;color:#2563eb;" id="todayTotal">0</div>
                <div style="font-size:0.65rem;color:#94a3b8;">Total Staff</div>
            </div>
        </div>
    </div>

    <!-- Today's Attendance Log -->
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
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="attendanceLog">
                    <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Audio Element - Correct path as per your folder structure -->
<audio id="successSound" preload="auto">
    <source src="/public/assets/sounds/success.mp3" type="audio/mpeg">
</audio>

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
const successSound = document.getElementById('successSound');

// Debug audio loading
console.log('🔊 Audio element:', successSound);
console.log('🔊 Audio source:', successSound.querySelector('source')?.src);

// Check if audio file exists
fetch('/public/assets/sounds/success.mp3')
    .then(response => {
        if (response.ok) {
            console.log('✅ Audio file found at: /public/assets/sounds/success.mp3');
        } else {
            console.warn('⚠️ Audio file NOT found! Status:', response.status);
            console.warn('⚠️ Please check if file exists at: public/assets/sounds/success.mp3');
        }
    })
    .catch(err => {
        console.error('❌ Error checking audio file:', err);
    });

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
    const el = document.getElementById('attendanceStatus');
    el.innerHTML = `<div class="attendance-status ${type}">${message}</div>`;
}

function showFlash(type, title, message) {
    const icon = type === 'success' ? '✅' : '❌';
    const color = type === 'success' ? 'rgba(15, 123, 74, 0.95)' : 'rgba(179, 60, 60, 0.95)';
    
    const flash = document.createElement('div');
    flash.className = `scan-flash ${type === 'error' ? 'error' : ''}`;
    flash.style.background = color;
    flash.innerHTML = `
        <span class="icon">${icon}</span>
        ${title}
        <div style="font-size:0.7rem;font-weight:400;margin-top:0.3rem;">${message}</div>
    `;
    document.body.appendChild(flash);
    
    setTimeout(() => {
        if (flash.parentNode) flash.remove();
    }, 1500);
}

function playSuccessSound() {
    console.log('🔊 Attempting to play success sound...');
    
    try {
        // Reset audio to start
        successSound.currentTime = 0;
        
        // Play with promise
        const playPromise = successSound.play();
        
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    console.log('✅ Success sound played successfully!');
                })
                .catch(err => {
                    console.warn('⚠️ Audio playback failed:', err);
                    console.warn('⚠️ Trying fallback method...');
                    
                    // Fallback: Create new audio element
                    try {
                        const fallbackAudio = new Audio('../../../public/assets/sounds/success.mp3');
                        fallbackAudio.play()
                            .then(() => console.log('✅ Fallback audio played!'))
                            .catch(e => console.warn('❌ Fallback audio failed:', e));
                    } catch (e) {
                        console.warn('❌ Fallback creation failed:', e);
                    }
                });
        }
    } catch (e) {
        console.warn('❌ Audio error:', e);
    }
}

function startScanning() {
    if (scanInterval) clearInterval(scanInterval);
    isScanning = true;
    scanCooldown = false;
    
    scanInterval = setInterval(() => {
        if (!isScanning || isProcessing || scanCooldown) return;
        if (video.readyState !== 4) return;
        
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
            setStatus('error', '<i class="bi bi-exclamation-circle me-2"></i> Invalid QR code. Please scan a valid employee ID card.');
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
    // Show detected employee info
    setStatus('info', `<i class="bi bi-person-check me-2"></i> Detected: <strong>${employee.username}</strong> - Marking attendance...`);
    
    // Auto mark attendance
    fetch('<?= site_url('/admin/attendance/mark') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ employee_id: employee.id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Play success sound
            playSuccessSound();
            
            // Show success flash
            const isCheckIn = data.message.includes('Check-in');
            showFlash('success', 
                isCheckIn ? '✅ Checked In' : '✅ Checked Out', 
                `${employee.username} - ${data.message}`
            );
            
            setStatus('success', `<i class="bi bi-check-circle-fill me-2"></i> ✅ ${data.message} - ${employee.username}`);
            
            // Reload attendance log
            loadTodayAttendance();
            loadStats();
            
            // Cooldown to prevent duplicate scans
            scanCooldown = true;
            setTimeout(() => {
                scanCooldown = false;
                lastScannedCode = null;
                isProcessing = false;
            }, 3000);
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

function loadTodayAttendance() {
    fetch('<?= site_url('/admin/attendance/today') ?>')
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('attendanceLog');
            if (data.attendance && data.attendance.length > 0) {
                tbody.innerHTML = data.attendance.map(a => `
                    <tr>
                        <td><strong>${a.username || 'Unknown'}</strong></td>
                        <td>${a.check_in || '-'}</td>
                        <td>${a.check_out || '-'}</td>
                        <td>
                            <span class="badge-soft" style="background:#e6f5ed;color:#0f7b4a;">
                                ${a.status === 'present' ? '✅ Present' : '❌ Absent'}
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

function loadStats() {
    fetch('<?= site_url('/admin/attendance/today') ?>')
        .then(res => res.json())
        .then(data => {
            if (data.attendance) {
                const present = data.attendance.filter(a => a.status === 'present').length;
                const absent = data.attendance.filter(a => a.status !== 'present').length;
                const total = data.attendance.length;
                
                document.getElementById('todayPresent').textContent = present;
                document.getElementById('todayAbsent').textContent = absent;
                document.getElementById('todayTotal').textContent = total;
            }
        })
        .catch(err => {
            console.error('Error loading stats:', err);
        });
}

function resetScanner() {
    lastScannedCode = null;
    isProcessing = false;
    scanCooldown = false;
    setStatus('info', '<i class="bi bi-arrow-counterclockwise me-2"></i> Scanner reset. Ready to scan.');
}

// Start on load
document.addEventListener('DOMContentLoaded', function() {
    startCamera();
    loadTodayAttendance();
    loadStats();
    
    // Refresh stats every 30 seconds
    setInterval(loadStats, 30000);
});

// Cleanup
window.addEventListener('beforeunload', function() {
    if (scanInterval) clearInterval(scanInterval);
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
    }
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>
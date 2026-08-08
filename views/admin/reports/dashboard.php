<?php 
$activePage = 'reports';
include VIEWS_PATH . '/layout/admin_header.php'; 
?>

<!-- Analytical Charts Grid -->
<div class="row text-slate">
    <!-- Row 1 -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-fill text-success me-2"></i>Revenue Split by Clinic Branch</h6>
            <div style="position: relative; height:250px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-primary me-2"></i>Top Doctors by Consultations</h6>
            <div style="position: relative; height:250px;">
                <canvas id="doctorChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-graph-up text-danger me-2"></i>Patient Registrations Trend (Monthly)</h6>
            <div style="position: relative; height:250px;">
                <canvas id="patientChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card border-0 shadow-sm p-4 h-100">
            <h6 class="fw-bold mb-3"><i class="bi bi-capsule-pill text-warning me-2"></i>Top Consumed Medications</h6>
            <div style="position: relative; height:250px;">
                <canvas id="medicineChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Revenue Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const revLabels = <?= json_encode(array_column($revenueData, 'label')) ?>;
    const revValues = <?= json_encode(array_map('floatval', array_column($revenueData, 'value'))) ?>;
    
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: revLabels.length ? revLabels : ['Direct'],
            datasets: [{
                label: 'Revenue (INR)',
                data: revValues.length ? revValues : [0.00],
                backgroundColor: 'rgba(25, 135, 84, 0.25)',
                borderColor: 'rgb(25, 135, 84)',
                borderWidth: 1.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // 2. Doctor Chart
    const docCtx = document.getElementById('doctorChart').getContext('2d');
    const docLabels = <?= json_encode(array_column($doctorStats, 'label')) ?>;
    const docValues = <?= json_encode(array_map('intval', array_column($doctorStats, 'value'))) ?>;

    new Chart(docCtx, {
        type: 'doughnut',
        data: {
            labels: docLabels.length ? docLabels : ['No Consultations'],
            datasets: [{
                data: docValues.length ? docValues : [1],
                backgroundColor: [
                    'rgba(13, 110, 253, 0.7)',
                    'rgba(25, 135, 84, 0.7)',
                    'rgba(255, 193, 7, 0.7)',
                    'rgba(220, 53, 69, 0.7)',
                    'rgba(108, 117, 125, 0.7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // 3. Patient Chart
    const patCtx = document.getElementById('patientChart').getContext('2d');
    const patLabels = <?= json_encode(array_column($patientStats, 'label')) ?>;
    const patValues = <?= json_encode(array_map('intval', array_column($patientStats, 'value'))) ?>;

    new Chart(patCtx, {
        type: 'line',
        data: {
            labels: patLabels.length ? patLabels : ['Jan', 'Feb', 'Mar'],
            datasets: [{
                label: 'Monthly Enrolled Patients',
                data: patValues.length ? patValues : [0, 0, 0],
                fill: true,
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderColor: 'rgb(220, 53, 69)',
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

    // 4. Medicine Chart
    const medCtx = document.getElementById('medicineChart').getContext('2d');
    const medLabels = <?= json_encode(array_column($medicineStats, 'label')) ?>;
    const medValues = <?= json_encode(array_map('intval', array_column($medicineStats, 'value'))) ?>;

    new Chart(medCtx, {
        type: 'bar',
        data: {
            labels: medLabels.length ? medLabels : ['None issued'],
            datasets: [{
                label: 'Units Dispatched',
                data: medValues.length ? medValues : [0],
                backgroundColor: 'rgba(255, 193, 7, 0.25)',
                borderColor: 'rgb(255, 193, 7)',
                borderWidth: 1.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true } }
        }
    });
});
</script>

<?php include VIEWS_PATH . '/layout/admin_footer.php'; ?>

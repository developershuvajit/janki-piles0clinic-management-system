<?php
$user = \App\Helpers\Session::user();
$roleSlug = $user['role_slug'] ?? '';
$userId = (int)($user['id'] ?? 0);

if (empty($roleSlug) && $userId > 0) {
    $r = \App\Helpers\Database::row(
        "SELECT r.slug FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id",
        ['id' => $userId]
    );
    $roleSlug = $r['slug'] ?? '';
}

if ($roleSlug === 'receptionist') {
    include __DIR__ . '/reception_footer.php';
    return;
}
if ($roleSlug === 'doctor') {
    include __DIR__ . '/doctor_footer.php';
    return;
}
?>
            </div><!-- /.container-fluid removed - content ends here -->
            <footer class="mt-5 pt-3 border-top text-center" style="font-size:0.78rem; color:var(--text-muted);">
                &copy; <?= date('Y') ?> Janki Piles Clinic Portal &mdash; Advanced Proctology Management System &mdash;
                <span class="text-success fw-500">System Online</span>
            </footer>
        </main>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Application JS -->
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>

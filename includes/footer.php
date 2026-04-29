<?php

declare(strict_types=1);

$footerMode = $footerMode ?? 'full';
?>
<?php if ($footerMode === 'full'): ?>
<footer class="hanzo-footer pt-5 pb-4">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-md-4">
                <h5 class="text-white mb-3">HANZO</h5>
                <p class="small">Controlled B2B trade between East African buyers and verified overseas factories. Inquiries, quotations, and logistics are coordinated through HANZO — not direct factory contact.</p>
            </div>
            <div class="col-md-2">
                <h6 class="text-white mb-3">Buyers</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= e(app_url('register.php')) ?>">Create account</a></li>
                    <li class="mb-2"><a href="<?= e(app_url('categories.php')) ?>">Browse categories</a></li>
                    <li class="mb-2"><a href="<?= e(app_url('dashboard.php')) ?>">Dashboard</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white mb-3">Popular sectors</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= e(app_url('category.php?id=4')) ?>">Machinery &amp; equipment</a></li>
                    <li class="mb-2"><a href="<?= e(app_url('category.php?id=12')) ?>">Bikes &amp; motorcycles</a></li>
                    <li class="mb-2"><a href="<?= e(app_url('category.php?id=11')) ?>">Roofing &amp; building</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white mb-3">Compliance</h6>
                <p class="small mb-0">Supplier identities and pricing are managed internally. Public listings do not display direct factory contact details.</p>
            </div>
        </div>
        <hr class="border-secondary mt-4 mb-3">
        <div class="text-center small">&copy; <?= date('Y') ?> HANZO. All rights reserved.</div>
    </div>
</footer>
<?php else: ?>
<footer class="border-top py-3 mt-auto bg-white">
    <div class="container-fluid px-4 text-center small text-muted">&copy; <?= date('Y') ?> HANZO Admin</div>
</footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(app_url('assets/js/hanzo.js')) ?>"></script>
<script src="<?= e(app_url('assets/js/admin.js')) ?>"></script>
</body>
</html>

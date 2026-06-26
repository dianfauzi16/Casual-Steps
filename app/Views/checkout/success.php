<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .status-container { max-width: 600px; margin-top: 100px; text-align: center; margin-bottom: 100px; }
    .status-icon { font-size: 4rem; margin-bottom: 20px; }
</style>

<main class="container py-5">
    <div class="status-container mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <i class="fas <?= htmlspecialchars($icon); ?> status-icon text-<?= htmlspecialchars($color); ?>"></i>
                <h2 class="mb-3 fw-bold text-<?= htmlspecialchars($color); ?>"><?= htmlspecialchars($page_title); ?></h2>
                <p class="lead text-muted"><?= htmlspecialchars($message); ?></p>
                
                <div class="p-3 bg-light rounded mt-4 mb-4">
                    <p class="mb-0">Nomor Pesanan Anda:</p>
                    <strong class="fs-4"><?= htmlspecialchars($order_id); ?></strong>
                </div>
                
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-outline-primary px-4">
                        <i class="fas fa-shopping-bag me-2"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

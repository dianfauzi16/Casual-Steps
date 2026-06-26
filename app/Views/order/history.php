<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .dashboard-menu .list-group-item {
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 0.5rem;
        border-radius: 0.75rem !important;
        transition: all 0.3s ease;
        font-weight: 500;
        color: #6c757d;
    }
    .dashboard-menu .list-group-item:hover {
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    .dashboard-menu .list-group-item.active {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        color: white;
        box-shadow: 0 4px 15px rgba(13,110,253,0.2);
    }
    .order-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    .order-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
    }
    .order-status-badge {
        font-size: 0.75rem;
        padding: 0.5em 1em;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
    </div>

    <div class="row g-4">
        <div class="col-lg-4" data-aos="fade-right">
            <div class="dashboard-menu list-group shadow-sm bg-white p-3 rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=Profile/index" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-circle me-3"></i> Profil Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Order/history" class="list-group-item list-group-item-action active">
                    <i class="fas fa-box-open me-3"></i> Riwayat Pesanan
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Address/index" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-3"></i> Alamat Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Profile/changePassword" class="list-group-item list-group-item-action">
                    <i class="fas fa-lock me-3"></i> Ubah Password
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="list-group-item list-group-item-action text-danger mt-3 bg-light">
                    <i class="fas fa-sign-out-alt me-3"></i> Logout
                </a>
            </div>
        </div>

        <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-5">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-shopping-bag fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Belum Ada Transaksi</h4>
                    <p class="text-muted mb-4">Anda belum memiliki riwayat pesanan. Yuk, temukan produk menarik dan mulai berbelanja sekarang!</p>
                    <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Mulai Belanja</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($orders as $index => $order): ?>
                        <div class="card order-card shadow-sm" data-aos="fade-up" data-aos-delay="<?= ($index % 5) * 100 ?>">
                            <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                                <div>
                                    <span class="fw-bold text-dark fs-6 d-block mb-1"><i class="fas fa-hashtag text-muted me-1"></i> <?= htmlspecialchars($order['id']); ?></span>
                                    <small class="text-muted fw-medium"><i class="far fa-calendar-alt text-primary me-1"></i> <?= htmlspecialchars(date('d M Y, H:i', strtotime($order['tanggal_pesanan']))); ?></small>
                                </div>
                                <?php 
                                    $status = htmlspecialchars($order['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                    else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                    else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                    else if ($status == 'Selesai' || $status == 'settlement' || $status == 'capture') $badge_class = 'bg-success';
                                    else if ($status == 'Dibatalkan' || $status == 'cancelled' || $status == 'deny' || $status == 'expire' || $status == 'cancel') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class; ?> order-status-badge rounded-pill shadow-sm"><?= $status; ?></span>
                            </div>
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <p class="mb-1 text-muted small fw-bold text-uppercase">Total Belanja</p>
                                        <h4 class="mb-0 fw-bold text-dark">Rp <?= number_format($order['total_price'], 0, ',', '.'); ?></h4>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-4 mt-md-0">
                                        <a href="<?= BASE_URL ?>index.php?url=Order/detail&id=<?= $order['id']; ?>" class="btn btn-outline-primary rounded-pill px-4 fw-medium w-100 w-md-auto">
                                            Lihat Detail <i class="fas fa-chevron-right ms-1" style="font-size: 0.8em;"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

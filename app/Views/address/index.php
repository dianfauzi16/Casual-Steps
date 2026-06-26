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
    .address-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .address-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background-color: transparent;
        transition: all 0.3s ease;
    }
    .address-card.is-primary::before {
        background: linear-gradient(to bottom, #198754, #20c997);
    }
    .address-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .address-card:hover::before {
        background: linear-gradient(to bottom, #0d6efd, #0dcaf0);
    }
    .address-card.is-primary:hover::before {
        background: linear-gradient(to bottom, #198754, #20c997);
    }
    .primary-badge {
        font-size: 0.7em;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
    </div>

    <?php if (isset($_SESSION['alamat_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['alamat_message_type']); ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert" data-aos="fade-in">
            <i class="fas <?= $_SESSION['alamat_message_type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i> <?= $_SESSION['alamat_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alamat_message'], $_SESSION['alamat_message_type']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4" data-aos="fade-right">
            <div class="dashboard-menu list-group shadow-sm bg-white p-3 rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=Profile/index" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-circle me-3"></i> Profil Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Order/history" class="list-group-item list-group-item-action">
                    <i class="fas fa-box-open me-3"></i> Riwayat Pesanan
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Address/index" class="list-group-item list-group-item-action active">
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
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-4 gap-3">
                <h4 class="mb-0 fw-bold text-dark"><i class="fas fa-address-book text-primary me-2"></i> Daftar Alamat Tersimpan</h4>
                <a href="<?= BASE_URL ?>index.php?url=Address/create" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">
                    <i class="fas fa-plus me-1"></i> Tambah Alamat
                </a>
            </div>

            <?php if (empty($daftar_alamat)): ?>
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-5">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3">Belum Ada Alamat</h4>
                    <p class="text-muted mb-4">Anda belum menambahkan alamat pengiriman. Tambahkan alamat sekarang untuk mempermudah proses checkout Anda nantinya.</p>
                    <a href="<?= BASE_URL ?>index.php?url=Address/create" class="btn btn-outline-primary btn-lg rounded-pill px-5">Tambah Alamat Sekarang</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($daftar_alamat as $index => $alamat): ?>
                        <div class="col-12" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
                            <div class="card address-card shadow-sm <?= $alamat['is_primary'] == 1 ? 'is-primary' : '' ?> bg-white">
                                <div class="card-body p-4 ms-2">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                                                <h5 class="card-title fw-bold text-dark mb-0">
                                                    <?= htmlspecialchars($alamat['label'] ?: 'Alamat'); ?>
                                                </h5>
                                                <?php if ($alamat['is_primary'] == 1): ?>
                                                    <span class="badge bg-success primary-badge rounded-pill shadow-sm"><i class="fas fa-check-circle me-1"></i> Utama</span>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="card-subtitle mb-3 text-primary fw-bold"><?= htmlspecialchars($alamat['recipient_name']); ?></h6>
                                            
                                            <div class="d-flex flex-column gap-2 text-muted">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-phone-alt me-2 text-secondary" style="width: 16px;"></i>
                                                    <span class="fw-medium"><?= htmlspecialchars($alamat['phone_number']); ?></span>
                                                </div>
                                                <div class="d-flex align-items-start">
                                                    <i class="fas fa-map-pin me-2 text-secondary mt-1" style="width: 16px;"></i>
                                                    <span style="line-height: 1.5;">
                                                        <?= nl2br(htmlspecialchars($alamat['street_address'])); ?><br>
                                                        <?= htmlspecialchars($alamat['city']); ?>, <?= htmlspecialchars($alamat['province']); ?><br>
                                                        <?= htmlspecialchars($alamat['postal_code']); ?> <?= htmlspecialchars($alamat['country']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-light rounded-circle shadow-sm p-0 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v text-secondary"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                                <li><a class="dropdown-item py-2 fw-medium" href="<?= BASE_URL ?>index.php?url=Address/edit&id=<?= $alamat['id']; ?>"><i class="fas fa-pen text-primary me-2"></i> Edit Alamat</a></li>
                                                <?php if ($alamat['is_primary'] == 0): ?>
                                                    <li><a class="dropdown-item py-2 fw-medium" href="<?= BASE_URL ?>index.php?url=Address/setPrimary&id=<?= $alamat['id']; ?>" onclick="return confirm('Jadikan alamat ini sebagai alamat utama?')"><i class="fas fa-star text-warning me-2"></i> Set Utama</a></li>
                                                <?php endif; ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item py-2 text-danger fw-bold" href="<?= BASE_URL ?>index.php?url=Address/delete&id=<?= $alamat['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')"><i class="fas fa-trash me-2"></i> Hapus</a></li>
                                            </ul>
                                        </div>
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

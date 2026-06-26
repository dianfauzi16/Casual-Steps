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
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
    </div>

    <?php if (isset($_SESSION['password_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['password_message_type']); ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert" data-aos="fade-in">
            <i class="fas <?= $_SESSION['password_message_type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i> <?= $_SESSION['password_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['password_message'], $_SESSION['password_message_type']); ?>
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
                <a href="<?= BASE_URL ?>index.php?url=Address/index" class="list-group-item list-group-item-action">
                    <i class="fas fa-map-marker-alt me-3"></i> Alamat Saya
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Profile/changePassword" class="list-group-item list-group-item-action active">
                    <i class="fas fa-lock me-3"></i> Ubah Password
                </a>
                <a href="<?= BASE_URL ?>index.php?url=Auth/logout" class="list-group-item list-group-item-action text-danger mt-3 bg-light">
                    <i class="fas fa-sign-out-alt me-3"></i> Logout
                </a>
            </div>
        </div>

        <div class="col-lg-8" data-aos="fade-left" data-aos-delay="100">
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 px-md-5">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-key text-primary me-2"></i> Form Ubah Password</h5>
                </div>
                <div class="card-body p-4 p-md-5 position-relative">
                    <div style="position: absolute; bottom: -50px; right: -50px; width: 150px; height: 150px; background: rgba(13,202,240,0.1); border-radius: 50%; z-index: 0;"></div>
                    
                    <form action="<?= BASE_URL ?>index.php?url=Profile/processChangePassword" method="POST" class="position-relative z-1">
                        <div class="mb-4">
                            <label for="current_password" class="form-label text-muted small fw-bold text-uppercase">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg bg-light border-0" id="current_password" name="current_password" required>
                        </div>
                        <div class="mb-4">
                            <label for="new_password" class="form-label text-muted small fw-bold text-uppercase">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg bg-light border-0" id="new_password" name="new_password" required>
                            <small class="form-text text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Minimal 8 karakter, pastikan kombinasi yang kuat.</small>
                        </div>
                        <div class="mb-5">
                            <label for="confirm_password" class="form-label text-muted small fw-bold text-uppercase">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control form-control-lg bg-light border-0" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">
                            <i class="fas fa-save me-2"></i> Simpan Password Baru
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-store text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
</div>

<?php if (isset($_SESSION['form_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['form_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
<?php endif; ?>

<div class="row">
    <!-- Menu Samping (Bisa dikembangkan untuk tab lain) -->
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="list-group list-group-flush rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=AdminSetting/index" class="list-group-item list-group-item-action active fw-bold py-3 border-0 rounded-top-4" style="background-color: #f8f9fa; color: #0d6efd; border-left: 4px solid #0d6efd !important;">
                    <i class="fas fa-store me-2"></i> Info Toko & Sosial Media
                </a>
                <a href="<?= BASE_URL ?>index.php?url=AdminSetting/system" class="list-group-item list-group-item-action text-muted py-3 border-0">
                    <i class="fas fa-cogs me-2"></i> Pengaturan Sistem
                </a>
                <a href="#" class="list-group-item list-group-item-action text-muted py-3 border-0 rounded-bottom-4" onclick="alert('Fitur segera hadir!'); return false;">
                    <i class="fas fa-shield-alt me-2"></i> Keamanan Akun
                </a>
            </div>
        </div>
    </div>

    <!-- Form Area -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-edit text-primary me-2"></i> Perbarui Profil Toko</h5>
            </div>
            
            <div class="card-body p-4 pt-2">
                <form action="<?= BASE_URL ?>index.php?url=AdminSetting/update" method="POST">
                    
                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Informasi Dasar</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="nama_toko" class="form-label fw-medium">Nama Toko</label>
                            <input type="text" class="form-control bg-light" id="nama_toko" name="nama_toko" value="<?= htmlspecialchars($settings['nama_toko'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email_kontak" class="form-label fw-medium">Email Utama</label>
                            <input type="email" class="form-control bg-light" id="email_kontak" name="email_kontak" value="<?= htmlspecialchars($settings['email_kontak'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telepon_toko" class="form-label fw-medium">Nomor WhatsApp / Telepon</label>
                            <input type="text" class="form-control bg-light" id="telepon_toko" name="telepon_toko" value="<?= htmlspecialchars($settings['telepon_toko'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="deskripsi_toko" class="form-label fw-medium">Deskripsi Singkat (Slogan)</label>
                            <input type="text" class="form-control bg-light" id="deskripsi_toko" name="deskripsi_toko" value="<?= htmlspecialchars($settings['deskripsi_toko'] ?? '') ?>">
                            <div class="form-text">Tampil di beberapa tempat seperti meta description atau bagian bawah halaman.</div>
                        </div>
                        <div class="col-12">
                            <label for="alamat_toko_lengkap" class="form-label fw-medium">Alamat Lengkap</label>
                            <textarea class="form-control bg-light" id="alamat_toko_lengkap" name="alamat_toko_lengkap" rows="3"><?= htmlspecialchars($settings['alamat_toko_lengkap'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Tautan Sosial Media</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="social_instagram" class="form-label fw-medium"><i class="fab fa-instagram text-danger me-1"></i> URL Instagram</label>
                            <input type="url" class="form-control bg-light" id="social_instagram" name="social_instagram" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/namatoko">
                        </div>
                        <div class="col-md-12">
                            <label for="social_facebook" class="form-label fw-medium"><i class="fab fa-facebook text-primary me-1"></i> URL Facebook</label>
                            <input type="url" class="form-control bg-light" id="social_facebook" name="social_facebook" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/namatoko">
                        </div>
                        <div class="col-md-12">
                            <label for="social_tiktok" class="form-label fw-medium"><i class="fab fa-tiktok text-dark me-1"></i> URL TikTok</label>
                            <input type="url" class="form-control bg-light" id="social_tiktok" name="social_tiktok" value="<?= htmlspecialchars($settings['social_tiktok'] ?? '') ?>" placeholder="https://tiktok.com/@namatoko">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

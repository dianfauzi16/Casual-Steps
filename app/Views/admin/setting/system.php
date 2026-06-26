<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-cogs text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
</div>

<?php if (isset($_SESSION['form_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['form_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
<?php endif; ?>

<div class="row">
    <!-- Menu Samping -->
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="list-group list-group-flush rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=AdminSetting/index" class="list-group-item list-group-item-action text-muted py-3 border-0 rounded-top-4">
                    <i class="fas fa-store me-2"></i> Info Toko & Sosial Media
                </a>
                <a href="<?= BASE_URL ?>index.php?url=AdminSetting/system" class="list-group-item list-group-item-action active fw-bold py-3 border-0" style="background-color: #f8f9fa; color: #0d6efd; border-left: 4px solid #0d6efd !important;">
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
                <h5 class="mb-0 fw-bold"><i class="fas fa-bullhorn text-primary me-2"></i> Konfigurasi Sistem & Banner UI</h5>
            </div>
            
            <div class="card-body p-4 pt-2">
                <form action="<?= BASE_URL ?>index.php?url=AdminSetting/updateSystem" method="POST" enctype="multipart/form-data">
                    
                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Top Bar Pengumuman</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch fs-5 mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="announcement_active" name="announcement_active" value="1" <?= (isset($settings['announcement_active']) && $settings['announcement_active'] == '1') ? 'checked' : '' ?>>
                                <label class="form-check-label ms-2" for="announcement_active" style="font-size: 0.9rem; font-weight: 500;">Aktifkan Banner Pengumuman Ticker</label>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <label for="announcement_text" class="form-label fw-medium">Teks Pengumuman</label>
                            <input type="text" class="form-control bg-light" id="announcement_text" name="announcement_text" value="<?= htmlspecialchars($settings['announcement_text'] ?? '') ?>" placeholder="Misal: Gratis Ongkir Pembelian di Atas Rp500.000">
                        </div>
                        <div class="col-md-5">
                            <label for="announcement_link" class="form-label fw-medium">URL Tujuan (Opsional)</label>
                            <input type="text" class="form-control bg-light" id="announcement_link" name="announcement_link" value="<?= htmlspecialchars($settings['announcement_link'] ?? '') ?>" placeholder="Misal: https://...">
                        </div>
                    </div>

                    <hr class="mb-4">

                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Gambar Promo Utama</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label for="promo_banner_image" class="form-label fw-medium">Upload Gambar (Header Promo)</label>
                            <input class="form-control bg-light" type="file" id="promo_banner_image" name="promo_banner_image" accept="image/*">
                            <div class="form-text">Gambar akan muncul di halaman Promo atau Home. Format: JPG, PNG, WEBP.</div>
                            
                            <?php if(!empty($settings['promo_banner_image'])): ?>
                                <div class="mt-3">
                                    <p class="mb-2 fw-medium text-muted small">Banner Saat Ini:</p>
                                    <img src="<?= BASE_URL ?>admin/uploads/banners/<?= htmlspecialchars($settings['promo_banner_image']) ?>" alt="Promo Banner" class="img-fluid rounded-3 border" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="mb-4">
                    
                    <h6 class="fw-bold text-uppercase text-muted mb-3" style="font-size: 0.8rem; letter-spacing: 1px;">Pengaturan Logistik</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="default_shipping_cost" class="form-label fw-medium">Biaya Pengiriman Standar (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" class="form-control bg-light border-start-0" id="default_shipping_cost" name="default_shipping_cost" value="<?= htmlspecialchars($settings['default_shipping_cost'] ?? '0') ?>" min="0">
                            </div>
                            <div class="form-text">Biaya tetap ini akan ditambahkan ke total belanja. Set ke 0 untuk Gratis Ongkir.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> Simpan Sistem
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

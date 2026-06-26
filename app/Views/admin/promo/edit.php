<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-gift text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
    <a href="<?= BASE_URL ?>index.php?url=AdminPromo/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Promo
    </a>
</div>

<?php if (isset($_SESSION['form_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['form_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-percentage text-danger me-2"></i> Form Pengaturan Diskon</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>index.php?url=AdminPromo/update" method="POST">
                    <input type="hidden" name="id_produk" value="<?= $product['id'] ?>">

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <?php 
                            $gambar = htmlspecialchars($product['image'] ?? '');
                            $is_url = filter_var($gambar, FILTER_VALIDATE_URL);
                            $path_gambar = $is_url ? $gambar : BASE_URL . "admin/uploads/produk/" . $gambar;
                            if (!$is_url) {
                                $local_path = __DIR__ . '/../../../../admin/uploads/produk/' . $gambar;
                                if (!file_exists($local_path)) {
                                    $path_gambar = BASE_URL . "assets/images/placeholder.jpg";
                                }
                            }
                            ?>
                            <img src="<?= $path_gambar ?>" alt="img" class="img-fluid rounded-3 border shadow-sm">
                        </div>
                        <div class="col-md-9">
                            <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars($product['brand'] ?? 'Tanpa Merk') ?> | Kategori: <?= htmlspecialchars($product['nama_kategori'] ?? '-') ?></p>
                            <h4 class="fw-bold text-success">Rp <?= number_format($product['price'], 0, ',', '.') ?></h4>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="discount_percent" class="form-label fw-bold">Persentase Diskon (%) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="<?= htmlspecialchars($product['discount_percent'] ?? '0') ?>" min="0" max="100" required>
                            <span class="input-group-text bg-white"><i class="fas fa-percent"></i></span>
                        </div>
                        <div class="form-text">Masukkan 0 untuk menghapus diskon.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="discount_start_date" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg shadow-sm" id="discount_start_date" name="discount_start_date" value="<?= htmlspecialchars($product['discount_start_date'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="discount_end_date" class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg shadow-sm" id="discount_end_date" name="discount_end_date" value="<?= htmlspecialchars($product['discount_end_date'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <div class="form-text text-muted"><i class="fas fa-info-circle"></i> Tanggal mulai dan selesai wajib diisi jika persentase diskon lebih dari 0.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Pengaturan Diskon
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

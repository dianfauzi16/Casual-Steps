<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-bolt text-warning me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
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
        <div class="alert alert-warning border-warning border-opacity-50 shadow-sm rounded-4 mb-4" role="alert">
            <h5 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i> Peringatan Penting!</h5>
            <p class="mb-0">Aksi ini akan mengubah persentase diskon dan periode diskon untuk <strong>SEMUA PRODUK</strong> secara serentak. Tindakan ini akan menimpa diskon individual yang mungkin telah diatur sebelumnya. Jika Anda memasukkan persentase 0, maka semua produk tidak akan memiliki diskon.</p>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-cogs text-danger me-2"></i> Form Diskon Massal</h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>index.php?url=AdminPromo/massUpdate" method="POST" onsubmit="return confirm('Apakah Anda sangat yakin ingin menerapkan diskon ini ke SEMUA produk? Aksi ini tidak dapat dibatalkan secara otomatis.');">
                    
                    <div class="mb-4">
                        <label for="discount_percent" class="form-label fw-bold">Persentase Diskon Serentak (%) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="number" class="form-control" id="discount_percent" name="discount_percent" value="0" min="0" max="100" required>
                            <span class="input-group-text bg-white"><i class="fas fa-percent"></i></span>
                        </div>
                        <div class="form-text">Masukkan 0 untuk menghapus seluruh diskon yang ada di toko.</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="discount_start_date" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg shadow-sm" id="discount_start_date" name="discount_start_date">
                        </div>
                        <div class="col-md-6">
                            <label for="discount_end_date" class="form-label fw-bold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-lg shadow-sm" id="discount_end_date" name="discount_end_date">
                        </div>
                        <div class="col-12">
                            <div class="form-text text-muted"><i class="fas fa-info-circle"></i> Tanggal mulai dan selesai wajib diisi jika persentase diskon lebih dari 0.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-5">
                        <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-exclamation-circle me-2"></i> Terapkan ke Semua Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
        <a href="<?= BASE_URL ?>index.php?url=AdminProduct/index" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <div class="card-body p-4">
        <?php if (isset($_SESSION['form_message'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3" role="alert">
                <?= htmlspecialchars($_SESSION['form_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>index.php?url=AdminProduct/store" method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0" id="product_name" name="product_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label fw-semibold">Deskripsi Produk</label>
                        <textarea class="form-control bg-light border-0" id="product_description" name="product_description" rows="5"></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="product_brand" class="form-label fw-semibold">Merek</label>
                        <input type="text" class="form-control bg-light border-0" id="product_brand" name="product_brand">
                    </div>

                    <div class="mb-3">
                        <label for="product_price" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light border-0" id="product_price" name="product_price" step="1" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_stock" class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light border-0" id="product_stock" name="product_stock" step="1" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_size" class="form-label fw-semibold">Ukuran (pisahkan dengan koma)</label>
                        <input type="text" class="form-control bg-light border-0" id="product_size" name="product_size" placeholder="Contoh: 39,40,41">
                    </div>

                    <div class="mb-3">
                        <label for="id_kategori" class="form-label fw-semibold">Kategori Produk</label>
                        <select class="form-select bg-light border-0" id="id_kategori" name="id_kategori">
                            <option value="">-- Pilih Kategori (Opsional) --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id_kategori']) ?>">
                                        <?= htmlspecialchars($cat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>Belum ada kategori ditambahkan</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="product_image" class="form-label fw-semibold">Gambar Produk</label>
                <input type="file" class="form-control bg-light border-0" id="product_image" name="product_image" accept="image/png, image/jpeg, image/gif">
                <small class="form-text text-muted">Format yang didukung: JPG, PNG, GIF. Ukuran maks: 2MB.</small>
            </div>

            <hr class="text-muted opacity-25">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>index.php?url=AdminProduct/index" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-1"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

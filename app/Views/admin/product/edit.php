<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-edit text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
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

        <form action="<?= BASE_URL ?>index.php?url=AdminProduct/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
            
            <div class="row g-4">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="product_name" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light border-0" id="product_name" name="product_name" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_description" class="form-label fw-semibold">Deskripsi Produk</label>
                        <textarea class="form-control bg-light border-0" id="product_description" name="product_description" rows="5"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="product_brand" class="form-label fw-semibold">Merek</label>
                        <input type="text" class="form-control bg-light border-0" id="product_brand" name="product_brand" value="<?= htmlspecialchars($product['brand'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="product_price" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light border-0" id="product_price" name="product_price" value="<?= htmlspecialchars($product['price']) ?>" step="1" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_stock" class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                        <input type="number" class="form-control bg-light border-0" id="product_stock" name="product_stock" value="<?= htmlspecialchars($product['stock']) ?>" step="1" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="product_size" class="form-label fw-semibold">Ukuran (pisahkan dengan koma)</label>
                        <input type="text" class="form-control bg-light border-0" id="product_size" name="product_size" value="<?= htmlspecialchars($product['size'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="id_kategori" class="form-label fw-semibold">Kategori Produk</label>
                        <select class="form-select bg-light border-0" id="id_kategori" name="id_kategori">
                            <option value="">-- Pilih Kategori (Opsional) --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['id_kategori']) ?>" <?= ($product['id_kategori'] == $cat['id_kategori']) ? 'selected' : '' ?>>
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
                <label for="product_image" class="form-label fw-semibold">Gambar Produk Baru (Opsional)</label>
                <?php if (!empty($product['image'])): ?>
                    <div class="mb-2">
                        <?php $img_url = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : BASE_URL . "admin/uploads/produk/" . htmlspecialchars($product['image']); ?>
                        <img src="<?= $img_url ?>" alt="Current Image" class="rounded-3 border" style="max-height: 100px;">
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control bg-light border-0" id="product_image" name="product_image" accept="image/png, image/jpeg, image/gif">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>

            <hr class="text-muted opacity-25">
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>index.php?url=AdminProduct/index" class="btn btn-light rounded-pill px-4">Batal</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-1"></i> Perbarui Produk
                </button>
            </div>
        </form>
    </div>
</div>

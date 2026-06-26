<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-box text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
        <a href="<?= BASE_URL ?>index.php?url=AdminProduct/create" class="btn btn-primary rounded-pill px-3">
            <i class="fas fa-plus me-1"></i> Tambah Produk Baru
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

        <form action="<?= BASE_URL ?>index.php" method="GET" class="mb-4">
            <input type="hidden" name="url" value="AdminProduct/index">
            <div class="input-group">
                <input type="text" name="search_keyword" class="form-control rounded-start-pill bg-light border-0 px-4" placeholder="Cari produk berdasarkan nama, merek, atau kategori..." value="<?= htmlspecialchars($search_keyword ?? '') ?>">
                <button class="btn btn-primary px-4" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                <?php if (!empty($search_keyword)): ?>
                    <a href="<?= BASE_URL ?>index.php?url=AdminProduct/index" class="btn btn-outline-secondary rounded-end-pill px-3" title="Reset Pencarian"><i class="fas fa-undo"></i></a>
                <?php else: ?>
                    <span class="input-group-text bg-white border-0 rounded-end-pill px-3"></span>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($search_keyword)): ?>
            <p class="text-muted mb-3 small">Hasil pencarian untuk: <strong class="text-dark">"<?= htmlspecialchars($search_keyword) ?>"</strong> (<?= count($products) ?> produk ditemukan)</p>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 rounded-start">Gambar</th>
                        <th class="border-0">Produk</th>
                        <th class="border-0 text-center">Kategori</th>
                        <th class="border-0 text-end">Harga</th>
                        <th class="border-0 text-center">Stok</th>
                        <th class="border-0 text-center rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php 
                                $img_url = filter_var($p['image'], FILTER_VALIDATE_URL) ? $p['image'] : BASE_URL . "admin/uploads/produk/" . htmlspecialchars($p['image']);
                                ?>
                                <img src="<?= $img_url ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="rounded-3 shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($p['name']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($p['brand'] ?? 'Tanpa Merek') ?></div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border"><?= htmlspecialchars($p['nama_kategori_produk'] ?? 'Tanpa Kategori') ?></span>
                            </td>
                            <td class="text-end fw-medium text-success">
                                Rp <?= number_format($p['price'], 0, ',', '.') ?>
                            </td>
                            <td class="text-center">
                                <?php if ($p['stock'] > 10): ?>
                                    <span class="badge bg-success rounded-pill"><?= $p['stock'] ?></span>
                                <?php elseif ($p['stock'] > 0): ?>
                                    <span class="badge bg-warning text-dark rounded-pill"><?= $p['stock'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?url=AdminProduct/edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-light text-primary shadow-sm border me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Tombol Hapus -->
                                <a href="<?= BASE_URL ?>index.php?url=AdminProduct/delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-light text-danger shadow-sm border" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-box-open fa-3x opacity-25"></i></div>
                                Tidak ada produk yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

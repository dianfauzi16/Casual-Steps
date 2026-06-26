<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-tags text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
        <a href="<?= BASE_URL ?>index.php?url=AdminCategory/create" class="btn btn-primary rounded-pill px-3">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
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

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 rounded-start">ID</th>
                        <th class="border-0">Nama Kategori</th>
                        <th class="border-0">Tanggal Dibuat</th>
                        <th class="border-0 text-center rounded-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td class="text-muted">#<?= htmlspecialchars($cat['id_kategori']) ?></td>
                            <td class="fw-bold text-dark">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2 rounded-pill">
                                    <i class="fas fa-tag me-1"></i> <?= htmlspecialchars($cat['nama_kategori']) ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?= date('d M Y, H:i', strtotime($cat['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?url=AdminCategory/edit&id=<?= $cat['id_kategori'] ?>" class="btn btn-sm btn-light text-primary shadow-sm border me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>index.php?url=AdminCategory/delete&id=<?= $cat['id_kategori'] ?>" class="btn btn-sm btn-light text-danger shadow-sm border" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Produk yang menggunakan kategori ini akan diatur ulang menjadi Tanpa Kategori.');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-tags fa-3x opacity-25"></i></div>
                                Belum ada kategori yang ditambahkan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-gift text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
    <a href="<?= BASE_URL ?>index.php?url=AdminPromo/massEdit" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold">
        <i class="fas fa-bolt me-1"></i> Atur Diskon Massal
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-tags text-primary me-2"></i> Daftar Produk</h5>
    </div>
    
    <div class="card-body p-4">
        <p class="card-text text-muted mb-4"><i class="fas fa-info-circle me-1"></i> Gunakan halaman ini untuk memantau status diskon/promo pada produk Anda. Untuk mengatur besaran diskon dan jadwalnya, klik tombol <strong>"Atur Diskon"</strong> pada produk yang diinginkan.</p>
        
        <?php if (isset($_SESSION['form_message'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <?= htmlspecialchars($_SESSION['form_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
        <?php endif; ?>

        <div class="table-responsive border rounded-4">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">Nama Produk</th>
                        <th class="border-0 text-end">Harga Asli</th>
                        <th class="border-0 text-center">Diskon (%)</th>
                        <th class="border-0 text-center">Periode Diskon</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-center px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td class="px-4 fw-medium text-dark">
                                    <div class="d-flex align-items-center">
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
                                        <img src="<?= $path_gambar ?>" alt="img" class="rounded-3 me-3 border" style="width: 45px; height: 45px; object-fit: cover;">
                                        <?= htmlspecialchars($product['name']) ?>
                                    </div>
                                </td>
                                <td class="text-end text-muted">
                                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($product['discount_percent'] > 0): ?>
                                        <span class="badge bg-danger rounded-pill px-3 py-2 fs-6 shadow-sm"><?= htmlspecialchars($product['discount_percent']) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($product['discount_start_date'] && $product['discount_end_date']): ?>
                                        <div class="small fw-medium text-dark"><?= date('d M Y', strtotime($product['discount_start_date'])) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;">s/d</div>
                                        <div class="small fw-medium text-dark"><?= date('d M Y', strtotime($product['discount_end_date'])) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $sekarang = date('Y-m-d');
                                    $status_badge = '<span class="badge bg-secondary rounded-pill px-3 py-1">Tidak Aktif</span>';
                                    
                                    if ($product['discount_percent'] > 0 && $product['discount_start_date'] && $product['discount_end_date']) {
                                        if ($sekarang >= $product['discount_start_date'] && $sekarang <= $product['discount_end_date']) {
                                            $status_badge = '<span class="badge bg-success rounded-pill px-3 py-1 shadow-sm"><i class="fas fa-bolt me-1"></i> Aktif</span>';
                                        } elseif ($sekarang < $product['discount_start_date']) {
                                            $status_badge = '<span class="badge bg-info text-dark rounded-pill px-3 py-1"><i class="fas fa-clock me-1"></i> Terjadwal</span>';
                                        } else {
                                            $status_badge = '<span class="badge bg-danger bg-opacity-75 rounded-pill px-3 py-1">Kadaluarsa</span>';
                                        }
                                    }
                                    echo $status_badge;
                                    ?>
                                </td>
                                <td class="text-center px-4">
                                    <a href="<?= BASE_URL ?>index.php?url=AdminPromo/edit&id=<?= $product['id'] ?>" class="btn btn-warning btn-sm shadow-sm rounded-pill px-3 fw-medium" title="Atur Diskon">
                                        <i class="fas fa-edit me-1"></i> Atur Diskon
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-gift fa-3x opacity-25"></i></div>
                                Belum ada produk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 1rem;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
    .icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1 text-dark">Dashboard</h1>
        <p class="text-muted mb-0">Selamat datang kembali, Admin! Berikut ringkasan performa toko Anda.</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>index.php?url=AdminReport/index" class="btn btn-outline-primary shadow-sm rounded-pill px-4">
            <i class="fas fa-chart-bar me-2"></i>Lihat Laporan Penuh
        </a>
    </div>
</div>

<!-- Statistik Utama -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold text-uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.8rem;">Pendapatan</h6>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1">Rp <?= number_format($total_revenue ?? 0, 0, ',', '.') ?></h3>
                <p class="text-success small mb-0"><i class="fas fa-arrow-up me-1"></i>Total pesanan selesai</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold text-uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.8rem;">Total Pesanan</h6>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($total_orders ?? 0, 0, ',', '.') ?></h3>
                <p class="text-primary small mb-0"><i class="fas fa-box-open me-1"></i>Semua status pesanan</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold text-uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.8rem;">Pelanggan</h6>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($total_customers ?? 0, 0, ',', '.') ?></h3>
                <p class="text-info small mb-0"><i class="fas fa-user-check me-1"></i>Akun terdaftar</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold text-uppercase mb-0" style="letter-spacing: 0.5px; font-size: 0.8rem;">Katalog Produk</h6>
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-tags fa-lg"></i>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-1"><?= number_format($total_products ?? 0, 0, ',', '.') ?></h3>
                <p class="text-warning small mb-0"><i class="fas fa-store me-1"></i>Produk aktif</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Pesanan Terbaru -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-clock text-primary me-2"></i>Pesanan Terbaru</h6>
                <a href="<?= BASE_URL ?>index.php?url=AdminOrder/index" class="btn btn-sm btn-light text-primary fw-medium rounded-pill px-3">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Order ID</th>
                                <th class="border-0">Pelanggan</th>
                                <th class="border-0">Tanggal</th>
                                <th class="border-0">Total</th>
                                <th class="border-0 text-center px-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recent_orders)): ?>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td class="px-4 fw-bold text-muted">#<?= htmlspecialchars($order['id']) ?></td>
                                        <td class="fw-medium text-dark"><?= htmlspecialchars($order['nama_pelanggan']) ?></td>
                                        <td class="text-muted small"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?></td>
                                        <td class="fw-bold">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                                        <td class="text-center px-4">
                                            <?php 
                                            $status = $order['status'];
                                            $badgeClass = 'bg-secondary';
                                            if ($status == 'pending') $badgeClass = 'bg-warning text-dark';
                                            if ($status == 'Lunas') $badgeClass = 'bg-info text-dark';
                                            if ($status == 'Dikirim') $badgeClass = 'bg-primary';
                                            if ($status == 'Selesai') $badgeClass = 'bg-success';
                                            if ($status == 'Batal') $badgeClass = 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?> rounded-pill px-3 py-1"><?= htmlspecialchars($status) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada pesanan masuk.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Peringatan Stok</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-bottom-4">
                    <?php if (!empty($low_stock_products)): ?>
                        <?php foreach ($low_stock_products as $prod): ?>
                            <a href="<?= BASE_URL ?>index.php?url=AdminProduct/edit&id=<?= $prod['id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom">
                                <?php 
                                $gambar = htmlspecialchars($prod['image'] ?? '');
                                $is_url = filter_var($gambar, FILTER_VALIDATE_URL);
                                $path_gambar = $is_url ? $gambar : BASE_URL . "admin/uploads/produk/" . $gambar;
                                ?>
                                <img src="<?= $path_gambar ?>" alt="Produk" class="rounded-3 shadow-sm me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold text-dark text-truncate" style="max-width: 150px;"><?= htmlspecialchars($prod['name']) ?></h6>
                                    <?php if ($prod['stock'] == 0): ?>
                                        <span class="badge bg-danger rounded-pill px-2">Habis</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-2">Sisa <?= htmlspecialchars($prod['stock']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 px-3 text-muted">
                            <i class="fas fa-check-circle fa-2x text-success mb-2 opacity-50"></i>
                            <p class="mb-0 small">Stok semua produk aman.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

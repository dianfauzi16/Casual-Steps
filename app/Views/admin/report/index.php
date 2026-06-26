<style>
    .stat-card-report {
        border-radius: 1rem;
        color: white;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card-report:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-card-report .stat-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
        z-index: 1;
    }

    .stat-card-report .stat-value {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        word-break: break-all;
        z-index: 1;
    }

    .stat-card-report .stat-icon-wrapper {
        font-size: 4rem;
        opacity: 0.15;
        position: absolute;
        right: -10px;
        bottom: -10px;
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
    }

    .stat-card-report:hover .stat-icon-wrapper {
        opacity: 0.25;
        transform: scale(1.1);
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 1rem 1.5rem;
        position: relative;
    }

    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background: transparent;
        font-weight: 600;
    }
    
    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background-color: #0d6efd;
        border-radius: 3px 3px 0 0;
    }

    .tab-content {
        padding: 2rem 0;
    }
    
    .product-report-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: .5rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white pt-4 pb-0 border-bottom-0 rounded-top-4">
        <ul class="nav nav-tabs border-bottom" id="laporanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab == 'ringkasan') ? 'active' : '' ?>" id="ringkasan-tab" data-bs-toggle="tab" data-bs-target="#ringkasan-tab-pane" type="button" role="tab" aria-controls="ringkasan-tab-pane" aria-selected="<?= ($activeTab == 'ringkasan') ? 'true' : 'false' ?>">
                    <i class="fas fa-chart-pie me-1"></i> Ringkasan Umum
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab == 'penjualan_periode') ? 'active' : '' ?>" id="penjualan-periode-tab" data-bs-toggle="tab" data-bs-target="#penjualan-periode-tab-pane" type="button" role="tab" aria-controls="penjualan-periode-tab-pane" aria-selected="<?= ($activeTab == 'penjualan_periode') ? 'true' : 'false' ?>">
                    <i class="fas fa-calendar-alt me-1"></i> Penjualan per Periode
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link <?= ($activeTab == 'produk_terlaris') ? 'active' : '' ?>" id="produk-terlaris-tab" data-bs-toggle="tab" data-bs-target="#produk-terlaris-tab-pane" type="button" role="tab" aria-controls="produk-terlaris-tab-pane" aria-selected="<?= ($activeTab == 'produk_terlaris') ? 'true' : 'false' ?>">
                    <i class="fas fa-star me-1"></i> Produk Terlaris
                </button>
            </li>
        </ul>
    </div>
    
    <div class="card-body p-4">
        <div class="tab-content pt-0" id="laporanTabContent">
            
            <!-- TAB: RINGKASAN UMUM -->
            <div class="tab-pane fade <?= ($activeTab == 'ringkasan') ? 'show active' : '' ?>" id="ringkasan-tab-pane" role="tabpanel" aria-labelledby="ringkasan-tab">
                <div class="row g-4 mb-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card-report bg-info bg-gradient text-white">
                            <div class="stat-label">Total Produk</div>
                            <div class="stat-value"><?= htmlspecialchars($stats['total_produk']) ?></div>
                            <div class="stat-icon-wrapper"><i class="fas fa-box"></i></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card-report bg-success bg-gradient text-white">
                            <div class="stat-label">Total Kategori</div>
                            <div class="stat-value"><?= htmlspecialchars($stats['total_kategori']) ?></div>
                            <div class="stat-icon-wrapper"><i class="fas fa-tags"></i></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card-report bg-warning bg-gradient text-dark">
                            <div class="stat-label">Total Pelanggan</div>
                            <div class="stat-value"><?= htmlspecialchars($stats['total_pelanggan']) ?></div>
                            <div class="stat-icon-wrapper"><i class="fas fa-users"></i></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card-report bg-primary bg-gradient text-white">
                            <div class="stat-label">Total Pesanan</div>
                            <div class="stat-value"><?= htmlspecialchars($stats['total_pesanan']) ?></div>
                            <div class="stat-icon-wrapper"><i class="fas fa-file-invoice-dollar"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-12">
                        <div class="stat-card-report bg-danger bg-gradient text-white p-4">
                            <div class="stat-label text-uppercase mb-2 text-white-50">Total Pendapatan (Pesanan Selesai)</div>
                            <div class="stat-value display-5 fw-bold">Rp <?= number_format($stats['total_pendapatan'], 0, ',', '.') ?></div>
                            <div class="stat-icon-wrapper" style="font-size: 6rem; opacity: 0.1; right: -20px; bottom: -20px;"><i class="fas fa-dollar-sign"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- TAB: PENJUALAN PER PERIODE -->
            <div class="tab-pane fade <?= ($activeTab == 'penjualan_periode') ? 'show active' : '' ?>" id="penjualan-periode-tab-pane" role="tabpanel" aria-labelledby="penjualan-periode-tab">
                
                <div class="bg-light p-4 rounded-4 mb-4 border">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="row g-3 align-items-end">
                        <input type="hidden" name="url" value="AdminReport/index">
                        <input type="hidden" name="tab" value="penjualan_periode">
                        
                        <div class="col-md-5">
                            <label for="tanggal_mulai" class="form-label fw-bold text-muted small">Tanggal Mulai:</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= htmlspecialchars($filterStartDate) ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label for="tanggal_akhir" class="form-label fw-bold text-muted small">Tanggal Akhir:</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="<?= htmlspecialchars($filterEndDate) ?>" required>
                        </div>
                        <div class="col-md-2 d-grid gap-2 d-md-block">
                            <button type="submit" class="btn btn-primary w-100 fw-medium shadow-sm"><i class="fas fa-filter me-1"></i> Filter</button>
                            <?php if ($salesPeriod !== null || !empty($error)): ?>
                                <a href="<?= BASE_URL ?>index.php?url=AdminReport/index&tab=penjualan_periode" class="btn btn-light w-100 mt-2 text-muted border"><i class="fas fa-undo me-1"></i> Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <?php if ($salesPeriod !== null): ?>
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <h5 class="fw-bold mb-0">Hasil Laporan: <span class="text-primary"><?= date('d M Y', strtotime($filterStartDate)) ?></span> s/d <span class="text-primary"><?= date('d M Y', strtotime($filterEndDate)) ?></span></h5>
                        <h5 class="fw-bold mb-0 text-success bg-success bg-opacity-10 px-4 py-2 rounded-pill">Total: Rp <?= number_format($salesPeriod['total_revenue'], 0, ',', '.') ?></h5>
                    </div>
                    
                    <div class="table-responsive border rounded-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 px-4">ID Pesanan</th>
                                    <th class="border-0">Tanggal Pesan</th>
                                    <th class="border-0">Nama Pelanggan</th>
                                    <th class="border-0 text-end">Total Harga</th>
                                    <th class="border-0 text-center px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($salesPeriod['orders'])): ?>
                                    <?php foreach ($salesPeriod['orders'] as $order): ?>
                                        <tr>
                                            <td class="px-4 fw-bold text-muted">#<?= htmlspecialchars($order['id']) ?></td>
                                            <td><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?></td>
                                            <td class="fw-medium text-dark"><?= htmlspecialchars($order['nama_pelanggan']) ?></td>
                                            <td class="text-end fw-bold text-success">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                                            <td class="text-center px-4">
                                                <span class="badge bg-success rounded-pill px-3 py-1"><?= htmlspecialchars($order['status']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <div class="mb-3"><i class="fas fa-file-invoice fa-3x opacity-25"></i></div>
                                            Tidak ada pesanan dengan status "Selesai" pada periode ini.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- TAB: PRODUK TERLARIS -->
            <div class="tab-pane fade <?= ($activeTab == 'produk_terlaris') ? 'show active' : '' ?>" id="produk-terlaris-tab-pane" role="tabpanel" aria-labelledby="produk-terlaris-tab">
                <?php if (!empty($topProducts)): ?>
                    <div class="table-responsive border rounded-4">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0 text-center px-4" style="width: 80px;">Rank</th>
                                    <th class="border-0" style="width: 80px;">Gambar</th>
                                    <th class="border-0">Nama Produk</th>
                                    <th class="border-0 text-center px-4">Total Terjual (Status Selesai)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rank = 1; foreach ($topProducts as $produk): ?>
                                    <tr>
                                        <td class="text-center px-4">
                                            <?php if ($rank == 1): ?>
                                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 35px; height: 35px;">1</div>
                                            <?php elseif ($rank == 2): ?>
                                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 35px; height: 35px;">2</div>
                                            <?php elseif ($rank == 3): ?>
                                                <div class="text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold shadow-sm" style="background-color: #cd7f32; width: 35px; height: 35px;">3</div>
                                            <?php else: ?>
                                                <div class="bg-light text-muted rounded-circle d-inline-flex align-items-center justify-content-center fw-bold border" style="width: 35px; height: 35px;"><?= $rank ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $gambar = htmlspecialchars($produk['gambar_produk'] ?? '');
                                            $is_url = filter_var($gambar, FILTER_VALIDATE_URL);
                                            $path_gambar = $is_url ? $gambar : BASE_URL . "admin/uploads/produk/" . $gambar;
                                            
                                            // Fallback local check logic from previous controller
                                            if (!$is_url) {
                                                $local_path = __DIR__ . '/../../../../admin/uploads/produk/' . $gambar;
                                                if (!file_exists($local_path)) {
                                                    $path_gambar = BASE_URL . "assets/images/placeholder.jpg"; // Use a placeholder if it doesn't exist
                                                }
                                            }
                                            ?>
                                            <img src="<?= $path_gambar ?>" alt="<?= htmlspecialchars($produk['nama_produk']) ?>" class="product-report-img">
                                        </td>
                                        <td class="fw-medium text-dark fs-6">
                                            <?= htmlspecialchars($produk['nama_produk']) ?>
                                        </td>
                                        <td class="text-center px-4">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fs-6 border border-primary border-opacity-25">
                                                <i class="fas fa-shopping-cart me-1"></i> <?= htmlspecialchars($produk['total_terjual']) ?> unit
                                            </span>
                                        </td>
                                    </tr>
                                <?php $rank++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <div class="mb-3"><i class="fas fa-star fa-3x opacity-25"></i></div>
                        Belum ada data produk terlaris yang pesanan-nya sudah selesai.
                    </div>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Restore tab if URL contains tab parameter and no filter form was submitted
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('tab') && !urlParams.has('tanggal_mulai')) {
            const tabId = urlParams.get('tab') + '-tab';
            const tabEl = document.getElementById(tabId);
            if(tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
    });
</script>

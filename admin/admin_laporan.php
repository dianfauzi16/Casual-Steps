<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';
$page_title = "Laporan Toko";

// Inisialisasi variabel statistik dasar
$total_produk = 0;
$total_kategori = 0;
$total_pelanggan = 0;
$total_pesanan_keseluruhan = 0;
$total_pendapatan_keseluruhan = 0;
$produk_terlaris = [];
$error_message = '';

// Mengambil semua statistik dasar (sama seperti kode Anda sebelumnya)
// 1. Total Produk
$sql_total_produk = "SELECT COUNT(id) AS total FROM product";
$result_produk = $conn->query($sql_total_produk);
if ($result_produk) $total_produk = $result_produk->fetch_assoc()['total'];
else $error_message .= " Gagal mengambil total produk.";

// 2. Total Kategori
$sql_total_kategori = "SELECT COUNT(id_kategori) AS total FROM categories";
$result_kategori = $conn->query($sql_total_kategori);
if ($result_kategori) $total_kategori = $result_kategori->fetch_assoc()['total'];
else $error_message .= " Gagal mengambil total kategori.";

// 3. Total Pelanggan
$sql_total_pelanggan = "SELECT COUNT(id) AS total FROM users";
$result_pelanggan = $conn->query($sql_total_pelanggan);
if ($result_pelanggan) $total_pelanggan = $result_pelanggan->fetch_assoc()['total'];
else $error_message .= " Gagal mengambil total pelanggan.";

// 4. Total Pesanan Keseluruhan
$sql_total_pesanan_all = "SELECT COUNT(id) AS total FROM orders";
$result_pesanan_all = $conn->query($sql_total_pesanan_all);
if ($result_pesanan_all) $total_pesanan_keseluruhan = $result_pesanan_all->fetch_assoc()['total'];
else $error_message .= " Gagal mengambil total pesanan.";

// 5. Total Pendapatan Keseluruhan (Selesai)
$sql_total_pendapatan_all = "SELECT SUM(total_price) AS total FROM orders WHERE status = 'Selesai'";
$result_pendapatan_all = $conn->query($sql_total_pendapatan_all);
if ($result_pendapatan_all) $total_pendapatan_keseluruhan = $result_pendapatan_all->fetch_assoc()['total'] ?? 0;
else $error_message .= " Gagal mengambil total pendapatan.";

// 6. Produk Terlaris
$sql_produk_terlaris = "SELECT p.id, p.name AS nama_produk, p.image AS gambar_produk, SUM(oi.quantity) AS total_terjual FROM order_items oi JOIN product p ON oi.id_produk = p.id JOIN orders o ON oi.id_order = o.id WHERE o.status = 'Selesai' GROUP BY oi.id_produk ORDER BY total_terjual DESC LIMIT 5";
$result_produk_terlaris = $conn->query($sql_produk_terlaris);
if ($result_produk_terlaris) {
    while ($row_laris = $result_produk_terlaris->fetch_assoc()) {
        $produk_terlaris[] = $row_laris;
    }
} else {
    $error_message .= " Gagal mengambil produk terlaris.";
}

// Logika Laporan Penjualan per Periode
$laporan_penjualan_periode = [];
$total_pendapatan_periode = 0;
$tanggal_mulai_filter = $_GET['tanggal_mulai'] ?? '';
$tanggal_akhir_filter = $_GET['tanggal_akhir'] ?? '';
$filter_aktif = false;
$active_tab = $_GET['tab'] ?? 'ringkasan'; // Tab aktif default atau dari URL

if (!empty($tanggal_mulai_filter) && !empty($tanggal_akhir_filter)) {
    $filter_aktif = true;
    $date_format = 'Y-m-d';
    $d_mulai = DateTime::createFromFormat($date_format, $tanggal_mulai_filter);
    $d_akhir = DateTime::createFromFormat($date_format, $tanggal_akhir_filter);

    if ($d_mulai && $d_mulai->format($date_format) === $tanggal_mulai_filter && $d_akhir && $d_akhir->format($date_format) === $tanggal_akhir_filter && $d_mulai <= $d_akhir) {
        $tanggal_mulai_query = $tanggal_mulai_filter . ' 00:00:00';
        $tanggal_akhir_query = $tanggal_akhir_filter . ' 23:59:59';
        $sql_laporan_periode = "SELECT id, nama_pelanggan, tanggal_pesanan, total_price, status FROM orders WHERE status = 'Selesai' AND tanggal_pesanan BETWEEN ? AND ? ORDER BY tanggal_pesanan DESC";
        if ($stmt_periode = $conn->prepare($sql_laporan_periode)) {
            $stmt_periode->bind_param("ss", $tanggal_mulai_query, $tanggal_akhir_query);
            $stmt_periode->execute();
            $result_periode = $stmt_periode->get_result();
            while ($row_periode = $result_periode->fetch_assoc()) {
                $laporan_penjualan_periode[] = $row_periode;
                $total_pendapatan_periode += $row_periode['total_price'];
            }
            $stmt_periode->close();
        } else {
            $error_message .= " Gagal mempersiapkan statement laporan periode.";
        }
    } else {
        $error_message .= " Format tanggal tidak valid.";
        $filter_aktif = false;
    }
    if ($filter_aktif) $active_tab = 'penjualan_periode'; // Otomatis pindah ke tab laporan jika filter tanggal aktif
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .stat-card-report {
            border-radius: 0.75rem;
            color: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .stat-card-report:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card-report .card-content {
            flex-grow: 1;
        }

        .stat-card-report .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            font-weight: 500;
            margin-bottom: 0.35rem;
            opacity: 0.9;
        }

        .stat-card-report .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-all;
        }

        .stat-card-report .stat-icon-wrapper {
            font-size: 2.8rem;
            opacity: 0.25;
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            transition: opacity 0.3s ease-in-out;
        }

        .stat-card-report:hover .stat-icon-wrapper {
            opacity: 0.4;
        }

        .product-report-img {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: .25rem;
        }

        .table-sm th,
        .table-sm td {
            font-size: 0.875rem;
            padding: 0.6rem 0.5rem;
        }

        .card-header h5 {
            font-size: 1.15rem;
            color: #495057;
        }

        .nav-tabs .nav-link {
            color: #495057;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-color: #dee2e6 #dee2e6 #fff;
            font-weight: 600;
        }

        .tab-content {
            border: 1px solid #dee2e6;
            border-top: 0;
            padding: 1.5rem;
            background-color: #fff;
            border-bottom-left-radius: .375rem;
            border-bottom-right-radius: .375rem;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
        <a href="admin_kategori.php"><i class="fa fa-tags me-2"></i>Kategori</a>
        <a href="admin_laporan.php" class="active"><i class="fa fa-chart-line me-2"></i>Laporan</a>
        <a href="admin_pengaturan.php"><i class="fa fa-cog me-2"></i>Pengaturan</a>
        <a href="admin-pesan-kontak.php"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
        <a href="admin_promo.php"><i class="fa fa-gift me-2"></i>Promo</a>
        <hr class="text-secondary">
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-gray-800"><?php echo htmlspecialchars($page_title); ?></h1>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Terjadi kesalahan: <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs mb-3" id="laporanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php if ($active_tab == 'ringkasan') echo 'active'; ?>" id="ringkasan-tab" data-bs-toggle="tab" data-bs-target="#ringkasan-tab-pane" type="button" role="tab" aria-controls="ringkasan-tab-pane" aria-selected="<?php echo ($active_tab == 'ringkasan' ? 'true' : 'false'); ?>">Ringkasan Umum</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php if ($active_tab == 'penjualan_periode') echo 'active'; ?>" id="penjualan-periode-tab" data-bs-toggle="tab" data-bs-target="#penjualan-periode-tab-pane" type="button" role="tab" aria-controls="penjualan-periode-tab-pane" aria-selected="<?php echo ($active_tab == 'penjualan_periode' ? 'true' : 'false'); ?>">Penjualan per Periode</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php if ($active_tab == 'produk_terlaris') echo 'active'; ?>" id="produk-terlaris-tab" data-bs-toggle="tab" data-bs-target="#produk-terlaris-tab-pane" type="button" role="tab" aria-controls="produk-terlaris-tab-pane" aria-selected="<?php echo ($active_tab == 'produk_terlaris' ? 'true' : 'false'); ?>">Produk Terlaris</button>
                </li>
            </ul>

            <div class="tab-content" id="laporanTabContent">
                <div class="tab-pane fade <?php if ($active_tab == 'ringkasan') echo 'show active'; ?>" id="ringkasan-tab-pane" role="tabpanel" aria-labelledby="ringkasan-tab">
                    <h4 class="mb-3 text-muted">Ringkasan Umum</h4>
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-report bg-info text-white h-100">
                                <div class="card-content">
                                    <div class="stat-label">Total Produk</div>
                                    <div class="stat-value"><?php echo $total_produk; ?></div>
                                </div>
                                <div class="stat-icon-wrapper"><i class="fas fa-box"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-report bg-success text-white h-100">
                                <div class="card-content">
                                    <div class="stat-label">Total Kategori</div>
                                    <div class="stat-value"><?php echo $total_kategori; ?></div>
                                </div>
                                <div class="stat-icon-wrapper"><i class="fas fa-tags"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-report bg-warning text-dark h-100">
                                <div class="card-content">
                                    <div class="stat-label">Total Pelanggan</div>
                                    <div class="stat-value"><?php echo $total_pelanggan; ?></div>
                                </div>
                                <div class="stat-icon-wrapper"><i class="fas fa-users"></i></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card stat-card-report bg-primary text-white h-100">
                                <div class="card-content">
                                    <div class="stat-label">Total Pesanan</div>
                                    <div class="stat-value"><?php echo $total_pesanan_keseluruhan; ?></div>
                                </div>
                                <div class="stat-icon-wrapper"><i class="fas fa-file-invoice-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="stat-card-report bg-danger text-white h-100">
                                <div class="card-content">
                                    <div class="stat-label">Total Pendapatan (Pesanan Selesai)</div>
                                    <div class="stat-value">Rp <?php echo number_format($total_pendapatan_keseluruhan, 0, ',', '.'); ?></div>
                                </div>
                                <div class="stat-icon-wrapper"><i class="fas fa-dollar-sign"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade <?php if ($active_tab == 'penjualan_periode') echo 'show active'; ?>" id="penjualan-periode-tab-pane" role="tabpanel" aria-labelledby="penjualan-periode-tab">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>Laporan Penjualan Per Periode (Status Pesanan: Selesai)</h5>
                        </div>
                        <div class="card-body filter-form">
                            <form action="admin_laporan.php" method="GET" class="row g-3 align-items-end">
                                <input type="hidden" name="tab" value="penjualan_periode">
                                <div class="col-md-5 col-sm-12"><label for="tanggal_mulai" class="form-label">Tanggal Mulai:</label><input type="date" class="form-control form-control-sm" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($tanggal_mulai_filter); ?>"></div>
                                <div class="col-md-5 col-sm-12"><label for="tanggal_akhir" class="form-label">Tanggal Akhir:</label><input type="date" class="form-control form-control-sm" id="tanggal_akhir" name="tanggal_akhir" value="<?php echo htmlspecialchars($tanggal_akhir_filter); ?>"></div>
                                <div class="col-md-2 col-sm-12 d-grid gap-2 d-md-block"><button type="submit" class="btn btn-primary btn-sm w-100 mb-1 mb-md-0"><i class="fas fa-filter me-1"></i> Tampilkan</button><a href="admin_laporan.php?tab=penjualan_periode" class="btn btn-outline-secondary btn-sm w-100" title="Reset Filter"><i class="fas fa-undo"></i> Reset</a></div>
                            </form>
                        </div>
                    </div>
                    <?php if ($filter_aktif): ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Hasil Laporan Penjualan dari <?php echo htmlspecialchars(date('d M Y', strtotime($tanggal_mulai_filter))); ?> s/d <?php echo htmlspecialchars(date('d M Y', strtotime($tanggal_akhir_filter))); ?></h5>
                            </div>
                            <div class="card-body">
                                <h6 class="mb-3">Total Pendapatan Periode Ini: <span class="fw-bold text-success fs-5">Rp <?php echo number_format($total_pendapatan_periode, 0, ',', '.'); ?></span></h6>
                                <?php if (!empty($laporan_penjualan_periode)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID Pesanan</th>
                                                    <th>Tanggal Pesan</th>
                                                    <th>Nama Pelanggan</th>
                                                    <th class="text-end">Total Harga</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($laporan_penjualan_periode as $pesanan_periode): ?>
                                                    <tr>
                                                        <td>#<?php echo htmlspecialchars($pesanan_periode['id']); ?></td>
                                                        <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($pesanan_periode['tanggal_pesanan']))); ?></td>
                                                        <td><?php echo htmlspecialchars($pesanan_periode['nama_pelanggan']); ?></td>
                                                        <td class="text-end">Rp <?php echo number_format($pesanan_periode['total_price'], 0, ',', '.'); ?></td>
                                                        <td class="text-center"><span class="badge bg-success"><?php echo htmlspecialchars($pesanan_periode['status']); ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?><p class="text-muted">Tidak ada penjualan "Selesai" pada periode ini.</p><?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (isset($_GET['tanggal_mulai']) && isset($_GET['tanggal_akhir']) && !$filter_aktif && !empty($_GET['tanggal_mulai'])): ?>
                        <div class="alert alert-warning">Filter tanggal tidak valid atau rentang tanggal salah.</div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade <?php if ($active_tab == 'produk_terlaris') echo 'show active'; ?>" id="produk-terlaris-tab-pane" role="tabpanel" aria-labelledby="produk-terlaris-tab">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold"><i class="fas fa-star me-2"></i>Top 5 Produk Terlaris (Status Pesanan: Selesai)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($produk_terlaris)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Gambar</th>
                                                <th>Nama Produk</th>
                                                <th class="text-center">Total Terjual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $rank = 1;
                                            foreach ($produk_terlaris as $produk_item): ?>
                                                <tr>
                                                    <td><?php echo $rank++; ?>.</td>
                                                    <td><?php $gambar_produk_laris = htmlspecialchars($produk_item['gambar_produk'] ?? '');
                                                        $path_gambar_laris = "uploads/produk/" . $gambar_produk_laris;
                                                        if (!empty($gambar_produk_laris) && file_exists($path_gambar_laris)): ?><img src="<?php echo $path_gambar_laris; ?>" alt="<?php echo htmlspecialchars($produk_item['nama_produk']); ?>" class="product-report-img"><?php else: ?><img src="placeholder_image.png" alt="Tidak ada gambar" class="product-report-img"><?php endif; ?></td>
                                                    <td><a href="edit_produk.php?id=<?php echo $produk_item['id']; ?>" target="_blank" title="Lihat Produk Ini"><?php echo htmlspecialchars($produk_item['nama_produk']); ?></a></td>
                                                    <td class="text-center"><?php echo htmlspecialchars($produk_item['total_terjual']); ?> unit</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?><p class="text-muted">Belum ada data produk terlaris.</p><?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk menjaga tab aktif setelah form filter tanggal disubmit (jika menggunakan GET)
        const activeTabEl = document.querySelector('#laporanTab button[data-bs-target="#<?php echo $active_tab; ?>-tab-pane"]');
        if (activeTabEl) {
            const tab = new bootstrap.Tab(activeTabEl);
            // tab.show(); // Uncomment jika Anda ingin otomatis pindah tab setelah filter.
            // Atau biarkan pengguna di tab filter jika mereka baru saja memfilter.
            // Jika filter tanggal aktif, $active_tab sudah di-set ke 'penjualan_periode' di PHP.
        }
    </script>
</body>

</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>
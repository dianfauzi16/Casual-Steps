<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$page_title = "Manajemen Promo";

// Mengambil semua data produk beserta info diskonnya
$products = [];
$sql_products = "SELECT id, name, price, discount_percent, discount_start_date, discount_end_date FROM product ORDER BY name ASC";
$result_products = $conn->query($sql_products);
if ($result_products) {
    while ($row = $result_products->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    // Handle error jika diperlukan
    $error_fetch = "Gagal mengambil data produk: " . $conn->error;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .price-column {
            min-width: 120px;
        }
        .action-column {
            min-width: 90px;
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
        <a href="admin_laporan.php"><i class="fa fa-chart-line me-2"></i>Laporan</a>
        <a href="admin_pengaturan.php"><i class="fa fa-cog me-2"></i>Pengaturan</a>
        <a href="admin-pesan-kontak.php"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
        <a href="admin_promo.php" class="active"><i class="fa fa-gift me-2"></i>Promo</a>
        <hr class="text-secondary">
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <h1 class="h3 mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

            <?php
            // Menampilkan pesan notifikasi dari proses edit produk
            if (isset($_SESSION['form_message']) && isset($_SESSION['form_message_type'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['form_message_type']) . ' alert-dismissible fade show" role="alert">';
                echo htmlspecialchars($_SESSION['form_message']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['form_message']);
                unset($_SESSION['form_message_type']);
            }
            ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-tags me-2"></i>Daftar Produk dan Diskon</h5>
                </div>
                <div class="card-body">
                    <p class="card-text text-muted">Gunakan halaman ini untuk mengatur diskon pada produk tertentu. Klik tombol "Atur Diskon" untuk mengubah persentase dan periode diskon.</p>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th class="text-end price-column">Harga Asli</th>
                                    <th class="text-center">Diskon (%)</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center action-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td class="text-end">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($product['discount_percent'] > 0 ? $product['discount_percent'] : '-'); ?></td>
                                            <td><?php echo htmlspecialchars($product['discount_start_date'] ? date('d M Y', strtotime($product['discount_start_date'])) : '-'); ?></td>
                                            <td><?php echo htmlspecialchars($product['discount_end_date'] ? date('d M Y', strtotime($product['discount_end_date'])) : '-'); ?></td>
                                            <td class="text-center">
                                                <?php
                                                $sekarang = date('Y-m-d');
                                                $status_badge = '<span class="badge bg-secondary">Tidak Aktif</span>';
                                                if ($product['discount_percent'] > 0 && $product['discount_start_date'] && $product['discount_end_date']) {
                                                    if ($sekarang >= $product['discount_start_date'] && $sekarang <= $product['discount_end_date']) {
                                                        $status_badge = '<span class="badge bg-success">Aktif</span>';
                                                    } elseif ($sekarang < $product['discount_start_date']) {
                                                        $status_badge = '<span class="badge bg-info">Terjadwal</span>';
                                                    } else {
                                                        $status_badge = '<span class="badge bg-danger">Kadaluarsa</span>';
                                                    }
                                                }
                                                echo $status_badge;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="edit_produk.php?id=<?php echo $product['id']; ?>#promo-section" class="btn btn-warning btn-sm" title="Atur Diskon">
                                                    <i class="fa fa-edit"></i> Atur
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada produk.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>


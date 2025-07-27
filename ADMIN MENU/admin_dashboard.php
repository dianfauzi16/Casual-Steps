<?php
// Memulai atau melanjutkan sesi yang sudah ada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

// Menyertakan file koneksi database
require_once 'db_connect.php'; //

// --- Mengambil Data Statistik Dasar ---
$total_produk_db = 0;
$total_pesanan_db = 0;
$total_pelanggan_db = 0;
$total_pendapatan_db = 0;
$error_stats = '';

// Total Produk
$sql_total_produk = "SELECT COUNT(id) AS total FROM product";
$result_total_produk = $conn->query($sql_total_produk);
if ($result_total_produk) {
    $total_produk_db = $result_total_produk->fetch_assoc()['total'];
} else {
    $error_stats .= " Gagal mengambil total produk. ";
}

// Total Pesanan (Semua status)
$sql_total_pesanan = "SELECT COUNT(id) AS total FROM orders"; // Menggunakan 'id' dari tabel 'orders' Anda
$result_total_pesanan = $conn->query($sql_total_pesanan);
if ($result_total_pesanan) {
    $total_pesanan_db = $result_total_pesanan->fetch_assoc()['total'];
} else {
    $error_stats .= " Gagal mengambil total pesanan. ";
}

// Total Pelanggan
$sql_total_pelanggan = "SELECT COUNT(id) AS total FROM users";
$result_total_pelanggan = $conn->query($sql_total_pelanggan);
if ($result_total_pelanggan) {
    $total_pelanggan_db = $result_total_pelanggan->fetch_assoc()['total'];
} else {
    $error_stats .= " Gagal mengambil total pelanggan. ";
}

// Total Pendapatan (dari pesanan yang statusnya 'Selesai')
// Pastikan nilai 'Selesai' sesuai dengan ENUM di tabel orders Anda
$sql_total_pendapatan = "SELECT SUM(total_price) AS total FROM orders WHERE status = 'Selesai'";
$result_total_pendapatan = $conn->query($sql_total_pendapatan);
if ($result_total_pendapatan) {
    $row_pendapatan = $result_total_pendapatan->fetch_assoc();
    $total_pendapatan_db = $row_pendapatan['total'] ?? 0;
} else {
    $error_stats .= " Gagal mengambil total pendapatan. ";
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        /* Penyesuaian tambahan untuk tampilan dashboard baru */
        .stat-card-enhanced {
            border-radius: 0.5rem;
            color: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .stat-card-enhanced:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .stat-card-enhanced .stat-icon {
            font-size: 3rem;
            opacity: 0.7;
        }

        .stat-card-enhanced .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
        }

        .stat-card-enhanced .stat-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card-enhanced .stat-footer {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .bg-gradient-primary {
            background: linear-gradient(45deg, #0d6efd, #6f42c1);
        }

        .bg-gradient-success {
            background: linear-gradient(45deg, #198754, #20c997);
        }

        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            color: #212529 !important;
        }

        /* pastikan teks terbaca */
        .bg-gradient-info {
            background: linear-gradient(45deg, #0dcaf0, #6610f2);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #fd7e14);
        }

        .welcome-message {
            font-size: 1.2rem;
        }

        .quick-links .btn {
            margin: 5px;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php" class="active"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
        <a href="admin_kategori.php"><i class="fa fa-tags me-2"></i>Kategori</a>
        <a href="admin_laporan.php"><i class="fa fa-chart-line me-2"></i>Laporan</a>
        <a href="admin_pengaturan.php"><i class="fa fa-cog me-2"></i>Pengaturan</a>
        <a href="admin-pesan-kontak.php"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
        <a href="admin_promo.php"><i class="fa fa-gift me-2"></i>Promo</a>
        <hr class="text-secondary">
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                <?php if (isset($_SESSION['admin_username'])): ?>
                    <span class="welcome-message">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</strong></span>
                <?php endif; ?>
            </div>

            <?php
            // Menampilkan pesan notifikasi dari session
            if (isset($_SESSION['form_message']) && isset($_SESSION['form_message_type'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['form_message_type']) . ' alert-dismissible fade show mb-3" role="alert">';
                echo htmlspecialchars($_SESSION['form_message']);
                if (isset($_SESSION['form_message_extra'])) {
                    echo '<br><small>' . htmlspecialchars($_SESSION['form_message_extra']) . '</small>';
                    unset($_SESSION['form_message_extra']);
                }
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['form_message']);
                unset($_SESSION['form_message_type']);
            }
            if (!empty($error_stats)): ?>
                <div class="alert alert-warning">Gagal memuat semua statistik: <?php echo htmlspecialchars($error_stats); ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card-enhanced bg-gradient-primary">
                        <div class="row">
                            <div class="col">
                                <div class="stat-label text-xs font-weight-bold text-uppercase mb-1">Total Penjualan (Selesai)</div>
                                <div class="stat-value mb-0">Rp <?php echo number_format($total_pendapatan_db, 0, ',', '.'); ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer mt-2">Estimasi pendapatan bersih</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card-enhanced bg-gradient-success">
                        <div class="row">
                            <div class="col">
                                <div class="stat-label text-xs font-weight-bold text-uppercase mb-1">Total Pesanan</div>
                                <div class="stat-value mb-0"><?php echo $total_pesanan_db; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer mt-2">Jumlah semua pesanan masuk</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card-enhanced bg-gradient-info">
                        <div class="row">
                            <div class="col">
                                <div class="stat-label text-xs font-weight-bold text-uppercase mb-1">Total Produk</div>
                                <div class="stat-value mb-0"><?php echo $total_produk_db; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box-open stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer mt-2">Jumlah varian produk</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card-enhanced bg-gradient-warning">
                        <div class="row">
                            <div class="col">
                                <div class="stat-label text-xs font-weight-bold text-uppercase mb-1">Total Pelanggan</div>
                                <div class="stat-value mb-0"><?php echo $total_pelanggan_db; ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users stat-icon"></i>
                            </div>
                        </div>
                        <div class="stat-footer mt-2">Jumlah pelanggan terdaftar</div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="card shadow-sm">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Akses Cepat</h6>
                        </div>
                        <div class="card-body quick-links">
                            <p>Selamat datang di panel admin Casual Steps. Dari sini Anda dapat mengelola produk, pesanan, pelanggan, dan lainnya.</p>
                            <a href="admin_produk.php" class="btn btn-outline-primary"><i class="fas fa-box me-1"></i> Kelola Produk</a>
                            <a href="admin_pesanan.php" class="btn btn-outline-success"><i class="fas fa-file-invoice me-1"></i> Lihat Pesanan</a>
                            <a href="tambah_produk.php" class="btn btn-outline-info"><i class="fas fa-plus-circle me-1"></i> Tambah Produk Baru</a>
                            <a href="admin_pengaturan.php" class="btn btn-outline-secondary"><i class="fas fa-cog me-1"></i> Pengaturan Situs</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
// Tutup koneksi database di akhir file.
if (isset($conn)) {
    $conn->close();
}
?>
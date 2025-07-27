<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php'; // Path ke db_connect.php dari dalam ADMIN MENU

$page_title = "Manajemen Pesanan";
$daftar_pesanan = [];
$error_message = '';

// Ambil semua pesanan, diurutkan dari yang terbaru
// Jika Anda memiliki kolom user_id dan ingin menampilkan nama user dari tabel users, Anda bisa melakukan JOIN
// Untuk sekarang, kita ambil nama_pelanggan langsung dari tabel orders
$sql_pesanan = "SELECT id, nama_pelanggan, tanggal_pesanan, total_price, status 
                FROM orders 
                ORDER BY tanggal_pesanan DESC";
// Note: 'total_price' dan 'status' adalah nama kolom dari tabel 'orders' yang Anda buat. 
// Jika Anda menggunakan 'total_harga_pesanan' dan 'status_pesanan', sesuaikan query di atas.

$result_pesanan = $conn->query($sql_pesanan);

if ($result_pesanan) {
    while ($row = $result_pesanan->fetch_assoc()) {
        $daftar_pesanan[] = $row;
    }
} else {
    $error_message = "Gagal mengambil data pesanan: " . $conn->error;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php" class="active"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3"><?php echo htmlspecialchars($page_title); ?></h1>
                </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <?php
            // Menampilkan pesan notifikasi dari session (misalnya setelah update status)
            if (isset($_SESSION['pesan_notifikasi_order']) && isset($_SESSION['tipe_notifikasi_order'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['tipe_notifikasi_order']) . ' alert-dismissible fade show mb-3" role="alert">';
                echo htmlspecialchars($_SESSION['pesan_notifikasi_order']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['pesan_notifikasi_order']);
                unset($_SESSION['tipe_notifikasi_order']);
            }
            ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fa fa-list-alt me-2"></i>Daftar Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Tanggal Pesan</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($daftar_pesanan)): ?>
                                    <?php foreach ($daftar_pesanan as $pesanan): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($pesanan['id']); // Menggunakan 'id' sesuai tabel orders Anda ?></td>
                                        <td><?php echo htmlspecialchars($pesanan['nama_pelanggan']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan']))); ?></td>
                                        <td>Rp <?php echo number_format($pesanan['total_price'], 0, ',', '.'); // Menggunakan 'total_price' ?></td>
                                        <td>
                                            <?php 
                                            $status = htmlspecialchars($pesanan['status']); // Menggunakan 'status'
                                            $badge_class = 'bg-secondary'; // Default
                                            if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                            else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                            else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                            else if ($status == 'Selesai') $badge_class = 'bg-success';
                                            else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                                            echo "<span class='badge {$badge_class}'>{$status}</span>";
                                            ?>
                                        </td>
                                        <td>
                                            <a href="admin_detail_pesanan.php?id_order=<?php echo $pesanan['id']; ?>" class="btn btn-info btn-sm" title="Lihat Detail">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada pesanan.</td>
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
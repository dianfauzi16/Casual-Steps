<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$page_title = "Manajemen Pelanggan";
$daftar_pelanggan = [];
$error_message = '';

// Ambil semua data pelanggan dari tabel 'users', termasuk kolom account_status
$sql_pelanggan = "SELECT id, name, email, phone_number, account_status FROM users ORDER BY name ASC";

$result_pelanggan = $conn->query($sql_pelanggan);

if ($result_pelanggan) {
    while ($row = $result_pelanggan->fetch_assoc()) {
        $daftar_pelanggan[] = $row;
    }
} else {
    $error_message = "Gagal mengambil data pelanggan: " . $conn->error;
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
    <style>
        .status-badge {
            font-size: 0.8em;
            padding: 0.4em 0.6em;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php" class="active"><i class="fa fa-users me-2"></i>Pelanggan</a>
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
            if (isset($_SESSION['pesan_notifikasi_pelanggan']) && isset($_SESSION['tipe_notifikasi_pelanggan'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['tipe_notifikasi_pelanggan']) . ' alert-dismissible fade show mb-3" role="alert">';
                echo htmlspecialchars($_SESSION['pesan_notifikasi_pelanggan']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['pesan_notifikasi_pelanggan']);
                unset($_SESSION['tipe_notifikasi_pelanggan']);
            }
            ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i>Daftar Pelanggan Terdaftar</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>No. Telepon</th>
                                    <th class="text-center">Status Akun</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($daftar_pelanggan)): ?>
                                    <?php foreach ($daftar_pelanggan as $pelanggan): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($pelanggan['id']); ?></td>
                                            <td><?php echo htmlspecialchars($pelanggan['name']); ?></td>
                                            <td><?php echo htmlspecialchars($pelanggan['email']); ?></td>
                                            <td><?php echo htmlspecialchars($pelanggan['phone_number'] ?: '-'); ?></td>
                                            <td class="text-center">
                                                <?php if ($pelanggan['account_status'] == 'aktif'): ?>
                                                    <span class="badge bg-success status-badge">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger status-badge">Nonaktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="admin_detail_pelanggan.php?id_user=<?php echo $pelanggan['id']; ?>" class="btn btn-info btn-sm mb-1" title="Lihat Detail Pelanggan">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($pelanggan['account_status'] == 'aktif'): ?>
                                                    <a href="toggle_status_pelanggan_proses.php?id_user=<?php echo $pelanggan['id']; ?>&action=nonaktifkan" class="btn btn-warning btn-sm mb-1" title="Nonaktifkan Akun" onclick="return confirm('Anda yakin ingin menonaktifkan akun pelanggan ini? Pelanggan tidak akan bisa login.');">
                                                        <i class="fas fa-user-slash"></i> Nonaktifkan
                                                    </a>
                                                <?php else: ?>
                                                    <a href="toggle_status_pelanggan_proses.php?id_user=<?php echo $pelanggan['id']; ?>&action=aktifkan" class="btn btn-success btn-sm mb-1" title="Aktifkan Akun" onclick="return confirm('Anda yakin ingin mengaktifkan akun pelanggan ini?');">
                                                        <i class="fas fa-user-check"></i> Aktifkan
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada pelanggan terdaftar.</td>
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
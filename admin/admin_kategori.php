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
require_once 'db_connect.php';

// Mengambil data kategori dari database
$daftar_kategori = [];
$sql_kategori = "SELECT id_kategori, nama_kategori, created_at FROM categories ORDER BY nama_kategori ASC";
$hasil_kategori = $conn->query($sql_kategori);

if ($hasil_kategori && $hasil_kategori->num_rows > 0) {
    while ($baris = $hasil_kategori->fetch_assoc()) {
        $daftar_kategori[] = $baris;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manajemen Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>

<body>

    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
        <a href="admin_kategori.php" class="active"><i class="fa fa-tags me-2"></i>Kategori</a>
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
                <h1 class="h3">Manajemen Kategori</h1>
                <a href="tambah_kategori.php" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus me-1"></i> Tambah Kategori Baru
                </a>
            </div>

            <?php
            // Menampilkan pesan notifikasi dari session (jika ada)
            if (isset($_SESSION['form_message']) && isset($_SESSION['form_message_type'])) {
                echo '<div class="alert alert-' . htmlspecialchars($_SESSION['form_message_type']) . ' alert-dismissible fade show mb-3" role="alert">';
                echo htmlspecialchars($_SESSION['form_message']);
                echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                echo '</div>';
                unset($_SESSION['form_message']);
                unset($_SESSION['form_message_type']);
            }
            ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold"><i class="fa fa-tags me-2"></i>Daftar Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Kategori</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($daftar_kategori)): ?>
                                    <?php foreach ($daftar_kategori as $kategori): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($kategori['id_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($kategori['nama_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($kategori['created_at']))); ?></td>
                                            <td>
                                                <a href="edit_kategori.php?id=<?php echo $kategori['id_kategori']; ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                                                <a href="hapus_kategori.php?id=<?php echo $kategori['id_kategori']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Produk yang menggunakan kategori ini akan diatur ulang kategorinya.');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada kategori.</td>
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
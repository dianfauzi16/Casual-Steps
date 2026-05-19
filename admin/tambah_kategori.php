<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}
// Tidak perlu koneksi DB di sini karena hanya menampilkan form
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah Kategori Baru</title>
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
            <div class="row mb-3 align-items-center">
                <div class="col">
                    <h1 class="h3">Tambah Kategori Baru</h1>
                </div>
                <div class="col-auto">
                    <a href="admin_kategori.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Kategori
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="tambah_kategori_proses.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_kategori" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                            <div class="form-text">Masukkan nama kategori baru, contoh: Sneakers, Sepatu Formal.</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Tambah Kategori
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
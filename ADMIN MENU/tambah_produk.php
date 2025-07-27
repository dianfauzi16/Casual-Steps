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

// MODIFIKASI: Sertakan db_connect.php karena kita perlu mengambil daftar kategori
require_once 'db_connect.php'; 

// MODIFIKASI: Mengambil daftar kategori untuk dropdown
$daftar_kategori_dropdown = [];
$sql_get_categories = "SELECT id_kategori, nama_kategori FROM categories ORDER BY nama_kategori ASC";
$result_categories = $conn->query($sql_get_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row_cat = $result_categories->fetch_assoc()) {
        $daftar_kategori_dropdown[] = $row_cat;
    }
}
// Koneksi akan ditutup nanti di akhir file
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tambah Produk Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css"> 
</head>
<body>

<div class="sidebar">
    <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
    <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
    <a href="admin_produk.php" class="active"><i class="fa fa-box me-2"></i>Produk</a>
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
        <div class="row mb-3 align-items-center">
            <div class="col">
                <h1 class="h3">Tambah Produk Baru</h1>
            </div>
            <div class="col-auto">
                <a href="admin_dashboard.php#produk-section" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Produk
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <?php
                // Menampilkan pesan notifikasi dari sesi
                if (isset($_SESSION['form_message']) && isset($_SESSION['form_message_type'])) {
                    // ... (kode notifikasi tetap sama) ...
                    $form_message = $_SESSION['form_message'];
                    $form_message_type = $_SESSION['form_message_type'];
                    echo '<div class="alert alert-' . htmlspecialchars($form_message_type) . ' alert-dismissible fade show" role="alert">';
                    echo htmlspecialchars($form_message);
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                    unset($_SESSION['form_message']);
                    unset($_SESSION['form_message_type']);
                }
                ?>
                <form action="tambah_produk_proses.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_name" name="product_name" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_description" class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" id="product_description" name="product_description" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="product_brand" class="form-label">Merek</label>
                                <input type="text" class="form-control" id="product_brand" name="product_brand">
                            </div>

                            <div class="mb-3">
                                <label for="product_price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="product_price" name="product_price" step="1" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="product_stock" name="product_stock" step="1" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_size" class="form-label">Ukuran (pisahkan dengan koma jika banyak)</label>
                                <input type="text" class="form-control" id="product_size" name="product_size" placeholder="Contoh: 39,40,41">
                            </div>

                            <div class="mb-3">
                                <label for="id_kategori" class="form-label">Kategori Produk</label>
                                <select class="form-select" id="id_kategori" name="id_kategori">
                                    <option value="">-- Pilih Kategori (Opsional) --</option>
                                    <?php if (!empty($daftar_kategori_dropdown)): ?>
                                        <?php foreach ($daftar_kategori_dropdown as $kategori_item): ?>
                                            <option value="<?php echo htmlspecialchars($kategori_item['id_kategori']); ?>">
                                                <?php echo htmlspecialchars($kategori_item['nama_kategori']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Belum ada kategori ditambahkan</option>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">Pilih kategori yang sesuai untuk produk ini.</div>
                            </div>
                            </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="product_image" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/png, image/jpeg, image/gif">
                        <small class="form-text text-muted">Format yang didukung: JPG, PNG, GIF. Ukuran maks: 2MB (contoh).</small>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Simpan Produk
                    </button>
                    <a href="admin_dashboard.php#produk-section" class="btn btn-secondary">
                        Batal
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// MODIFIKASI: Pastikan koneksi ditutup jika sudah dibuka
if (isset($conn)) {
    $conn->close();
}
?>
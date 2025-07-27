<?php
// Memulai atau melanjutkan sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

// Sertakan file koneksi database
require_once 'db_connect.php'; //

$produk = null; // Variabel untuk menyimpan data produk
$product_id = null;
$error_message = "";

// MODIFIKASI: Mengambil daftar kategori untuk dropdown
$daftar_kategori_dropdown = [];
$sql_get_categories = "SELECT id_kategori, nama_kategori FROM categories ORDER BY nama_kategori ASC";
$result_categories = $conn->query($sql_get_categories);
if ($result_categories && $result_categories->num_rows > 0) {
    while ($row_cat = $result_categories->fetch_assoc()) {
        $daftar_kategori_dropdown[] = $row_cat;
    }
}

// Periksa apakah ID produk ada di URL
if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $product_id = trim($_GET['id']);

    // MODIFIKASI: Ambil juga id_kategori produk
    $sql = "SELECT id, name, brand, price, stock, size, image, description, id_kategori, discount_percent, discount_start_date, discount_end_date FROM product WHERE id = ?"; 

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $param_id);
        $param_id = $product_id;

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $produk = $result->fetch_assoc(); //
            } else {
                $error_message = "Produk tidak ditemukan."; //
            }
        } else {
            $error_message = "Oops! Terjadi kesalahan saat mengambil data produk."; //
        }
        $stmt->close();
    } else {
        $error_message = "Oops! Terjadi kesalahan pada persiapan query database."; //
    }
} else {
    $error_message = "ID Produk tidak valid."; //
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css"> <style>
        .current-product-image {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
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
                <h1 class="h3">Edit Produk</h1>
            </div>
            <div class="col-auto">
                <a href="admin_dashboard.php#produk-section" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Produk
                </a>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($produk): // Hanya tampilkan form jika data produk berhasil diambil ?>
        <div class="card">
            <div class="card-body">
                <?php
                // Menampilkan pesan notifikasi dari proses update (jika ada)
                if (isset($_SESSION['form_message']) && isset($_SESSION['form_message_type'])) {
                    echo '<div class="alert alert-' . htmlspecialchars($_SESSION['form_message_type']) . ' alert-dismissible fade show" role="alert">';
                    echo htmlspecialchars($_SESSION['form_message']);
                    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    echo '</div>';
                    unset($_SESSION['form_message']);
                    unset($_SESSION['form_message_type']);
                }
                ?>
                <form action="edit_produk_proses.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($produk['id']); ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="product_name" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($produk['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_description" class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" id="product_description" name="product_description" rows="5"><?php echo htmlspecialchars($produk['description']); ?></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="product_brand" class="form-label">Merek</label>
                                <input type="text" class="form-control" id="product_brand" name="product_brand" value="<?php echo htmlspecialchars($produk['brand']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="product_price" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="product_price" name="product_price" step="1" min="0" value="<?php echo htmlspecialchars($produk['price']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_stock" class="form-label">Stok <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="product_stock" name="product_stock" step="1" min="0" value="<?php echo htmlspecialchars($produk['stock']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="product_size" class="form-label">Ukuran (pisahkan dengan koma jika banyak)</label>
                                <input type="text" class="form-control" id="product_size" name="product_size" value="<?php echo htmlspecialchars($produk['size']); ?>" placeholder="Contoh: 39,40,41">
                            </div>

                            <div class="mb-3">
                                <label for="id_kategori" class="form-label">Kategori Produk</label>
                                <select class="form-select" id="id_kategori" name="id_kategori">
                                    <option value="">-- Pilih Kategori (Opsional) --</option>
                                    <?php if (!empty($daftar_kategori_dropdown)): ?>
                                        <?php foreach ($daftar_kategori_dropdown as $kategori_item): ?>
                                            <option value="<?php echo htmlspecialchars($kategori_item['id_kategori']); ?>"
                                                <?php 
                                                // Cek apakah produk ini memiliki id_kategori dan sama dengan id_kategori saat ini di loop
                                                if (isset($produk['id_kategori']) && $produk['id_kategori'] == $kategori_item['id_kategori']) {
                                                    echo 'selected';
                                                } 
                                                ?>>
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
                        <label for="product_image" class="form-label">Ganti Gambar Produk (Opsional)</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" accept="image/png, image/jpeg, image/gif">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti gambar. Format: JPG, PNG, GIF. Maks 2MB.</small>

                        <?php if (!empty($produk['image'])): ?>
                            <div class="mt-2">
                                <p class="mb-1">Gambar Saat Ini:</p>
                                <?php
                                    $current_image_path = "uploads/produk/" . htmlspecialchars($produk['image']); //
                                    if (file_exists($current_image_path)) { //
                                        echo '<img src="' . $current_image_path . '" alt="Gambar Produk Saat Ini" class="current-product-image">'; //
                                    } else {
                                        echo '<p class="text-danger">File gambar saat ini tidak ditemukan.</p>'; //
                                    }
                                ?>
                                <input type="hidden" name="old_product_image" value="<?php echo htmlspecialchars($produk['image']); ?>">
                            </div>
                        <?php endif; ?>
                    </div>

                    <fieldset id="promo-section" class="mb-4 p-3 border rounded">
                        <legend class="w-auto px-2 h6">Pengaturan Diskon</legend>
                         <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="discount_percent" class="form-label">Persentase Diskon (%)</label>
                                <input type="number" class="form-control" id="discount_percent" name="discount_percent" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($produk['discount_percent'] ?? '0'); ?>" placeholder="Contoh: 20">
                                <div class="form-text">Isi 0 jika tidak ada diskon.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="discount_start_date" class="form-label">Tanggal Mulai Diskon</label>
                                <input type="date" class="form-control" id="discount_start_date" name="discount_start_date" value="<?php echo htmlspecialchars($produk['discount_start_date'] ?? ''); ?>">
                                <div class="form-text">Biarkan kosong jika diskon tidak terbatas waktu.</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="discount_end_date" class="form-label">Tanggal Selesai Diskon</label>
                                <input type="date" class="form-control" id="discount_end_date" name="discount_end_date" value="<?php echo htmlspecialchars($produk['discount_end_date'] ?? ''); ?>">
                                <div class="form-text">Biarkan kosong jika diskon tidak terbatas waktu.</div>
                            </div>
                        </div>
                         <p class="text-muted small mb-0">
                            Untuk menghapus diskon, set persentase ke 0 dan kosongkan tanggal. Agar diskon aktif, persentase harus lebih dari 0 dan tanggal hari ini harus berada dalam rentang tanggal mulai dan selesai.
                        </p>
                    </fieldset>

                    <hr>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <a href="admin_dashboard.php#produk-section" class="btn btn-secondary">
                        Batal
                    </a>
                </form>
            </div> 
        </div> 
        <?php else: ?>
            <?php if (empty($error_message)): ?>
                <div class="alert alert-warning">Data produk tidak ditemukan atau ID tidak valid.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div> 
</div> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close(); //
}
?>
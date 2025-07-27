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
$page_title = "Manajemen Produk";

// --- AWAL LOGIKA PENCARIAN ---
$search_keyword = '';
$search_query_condition = '';
$search_params = [];
$search_types = '';

if (isset($_GET['search_keyword']) && !empty(trim($_GET['search_keyword']))) {
    $search_keyword = trim($_GET['search_keyword']);
    $search_query_condition = " WHERE (p.name LIKE ? OR p.brand LIKE ? OR c.nama_kategori LIKE ?) ";
    $search_like_keyword = "%" . $search_keyword . "%";
    $search_params = [$search_like_keyword, $search_like_keyword, $search_like_keyword];
    $search_types = "sss";
}
// --- AKHIR LOGIKA PENCARIAN ---

// Mengambil data produk dari database
$daftar_produk = []; 
$sql_produk_base = "SELECT p.id, p.name, p.brand, p.price, p.stock, p.image, c.nama_kategori 
                    FROM product p
                    LEFT JOIN categories c ON p.id_kategori = c.id_kategori";
$sql_produk_order = " ORDER BY p.created_at DESC";

// NANTI DI SINI KITA BISA MASUKKAN LOGIKA PAGINASI
// Untuk sekarang, kita ambil semua hasil filter
$sql_produk = $sql_produk_base . $search_query_condition . $sql_produk_order;

if ($stmt_produk = $conn->prepare($sql_produk)) {
    if (!empty($search_keyword)) {
        $stmt_produk->bind_param($search_types, ...$search_params);
    }
    $stmt_produk->execute();
    $hasil_produk = $stmt_produk->get_result();

    if ($hasil_produk && $hasil_produk->num_rows > 0) {
        while ($baris = $hasil_produk->fetch_assoc()) {
            $daftar_produk[] = $baris; 
        }
    }
    $stmt_produk->close();
} else {
    $_SESSION['form_message'] = "Gagal mengambil data produk (prepare): " . $conn->error;
    $_SESSION['form_message_type'] = "danger";
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
        .product-thumbnail { max-width: 60px; max-height: 60px; object-fit: cover; }
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
        ?>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa fa-box me-2"></i><?php echo htmlspecialchars($page_title); ?></h5>
                <a href="tambah_produk.php" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i> Tambah Produk Baru</a>
            </div>
            <div class="card-body">
                <form action="admin_produk.php" method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search_keyword" class="form-control" placeholder="Cari produk berdasarkan nama, merek, atau kategori..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                        <?php if (!empty($search_keyword)): ?>
                            <a href="admin_produk.php" class="btn btn-outline-secondary" title="Reset Pencarian"><i class="fas fa-undo"></i> Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
                <?php if (!empty($search_keyword) && isset($_GET['search_keyword'])): ?>
                    <p class="mb-2">Hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($search_keyword); ?>"</strong> 
                       (Ditemukan: <?php echo count($daftar_produk); ?> produk)
                    </p>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Merek</th>
                                <th>Kategori</th>
                                <th class="text-end">Harga</th>
                                <th class="text-center">Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($daftar_produk)): ?>
                                <?php foreach ($daftar_produk as $produk): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produk['id']); ?></td>
                                    <td>
                                        <?php 
                                        $nama_file_gambar = htmlspecialchars($produk['image'] ?? '');
                                        $path_gambar = "uploads/produk/" . $nama_file_gambar;
                                        if (!empty($nama_file_gambar) && file_exists($path_gambar)): 
                                        ?>
                                            <img src="<?php echo $path_gambar; ?>" alt="<?php echo htmlspecialchars($produk['name']); ?>" class="product-thumbnail">
                                        <?php else: ?>
                                            <img src="placeholder_image.png" alt="Tidak ada gambar" class="product-thumbnail"> 
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($produk['name']); ?></td>
                                    <td><?php echo htmlspecialchars($produk['brand'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($produk['nama_kategori'] ?? '-'); ?></td>
                                    <td class="text-end">Rp <?php echo number_format($produk['price'], 0, ',', '.'); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($produk['stock']); ?></td>
                                    <td class="text-center">
                                        <a href="edit_produk.php?id=<?php echo $produk['id']; ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></a>
                                        <a href="hapus_produk.php?id=<?php echo $produk['id']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <?php if (!empty($search_keyword)): ?>
                                            Produk dengan kata kunci "<?php echo htmlspecialchars($search_keyword); ?>" tidak ditemukan.
                                        <?php else: ?>
                                            Belum ada produk di database.
                                        <?php endif; ?>
                                    </td>
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
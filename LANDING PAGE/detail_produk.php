<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current_page_base = basename($_SERVER['PHP_SELF']); // Definisi $current_page_base

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Fungsi get_site_setting (pastikan ada di db_connect.php atau di-include terpisah)
if (!function_exists('get_site_setting')) {
    function get_site_setting($db_connection, $key)
    {
        $setting_value = null;
        $sql_setting = "SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1";
        if ($stmt_setting = $db_connection->prepare($sql_setting)) {
            $stmt_setting->bind_param("s", $key);
            if ($stmt_setting->execute()) {
                $result_setting = $stmt_setting->get_result();
                if ($result_setting->num_rows > 0) {
                    $row_setting = $result_setting->fetch_assoc();
                    $setting_value = $row_setting['setting_value'];
                }
            }
            $stmt_setting->close();
        }
        return $setting_value;
    }
}
$nama_toko_header = function_exists('get_site_setting') ? (get_site_setting($conn, 'nama_toko') ?: "Casual Steps") : "Casual Steps";


$product_detail = null;
$page_title = "Detail Produk"; // Judul default
$error_message = '';
$available_sizes = [];
$user_has_purchased = false;
$user_has_rated = false;
$user_id = $_SESSION['user_id'] ?? null;

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $product_id = $_GET['id'];

    $sql_product_detail = "SELECT p.id, p.name, p.brand, p.price, p.stock, p.image, p.description, p.size, p.id_kategori, c.nama_kategori, p.average_rating, p.rating_count, p.discount_percent, p.discount_start_date, p.discount_end_date
                           FROM product p
                           LEFT JOIN categories c ON p.id_kategori = c.id_kategori
                           WHERE p.id = ?";

    if ($stmt = $conn->prepare($sql_product_detail)) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $product_detail = $result->fetch_assoc();
            $page_title = htmlspecialchars($product_detail['name']);

            if (!empty($product_detail['size'])) {
                $available_sizes = array_map('trim', explode(',', $product_detail['size']));
            }
            // Cek apakah user yang login sudah pernah membeli produk ini
            if ($user_id) {
                $sql_check_purchase = "SELECT o.id FROM orders o JOIN order_items oi ON o.id = oi.id_order WHERE o.user_id = ? AND oi.id_produk = ? AND o.status = 'Selesai' LIMIT 1";
                if ($stmt_purchase = $conn->prepare($sql_check_purchase)) {
                    $stmt_purchase->bind_param("ii", $user_id, $product_id);
                    $stmt_purchase->execute();
                    if ($stmt_purchase->get_result()->num_rows > 0) {
                        $user_has_purchased = true;
                    }
                    $stmt_purchase->close();
                }

                // Cek apakah user sudah pernah memberikan rating untuk produk ini
                $sql_check_rating = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ? LIMIT 1";
                if ($stmt_rating_check = $conn->prepare($sql_check_rating)) {
                    $stmt_rating_check->bind_param("ii", $user_id, $product_id);
                    $stmt_rating_check->execute();
                    if ($stmt_rating_check->get_result()->num_rows > 0) $user_has_rated = true;
                    $stmt_rating_check->close();
                }
            }
        } else {
            $error_message = "Produk tidak ditemukan.";
        }
        $stmt->close();
    } else {
        $error_message = "Gagal mempersiapkan statement untuk mengambil detail produk: " . $conn->error;
    }
} else {
    $error_message = "ID produk tidak valid atau tidak disediakan.";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko_header); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        /* ... (style Anda yang sudah ada tetap di sini) ... */
        .product-detail-img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-height: 500px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .product-info h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .product-info .brand {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .product-info .category-info {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }

        .product-info .category-info a {
            color: #0d6efd;
            text-decoration: none;
        }

        .product-info .category-info a:hover {
            text-decoration: underline;
        }

        .product-info .price {
            font-size: 1.8rem;
            color: #000000;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .product-info .stock,
        .product-info .size-available-text {
            font-size: 0.95rem;
            margin-bottom: 0.75rem;
        }

        .product-info .stock .badge {
            font-size: 0.9rem;
        }

        .product-info .description {
            margin-top: 1.5rem;
            line-height: 1.7;
            color: #495057;
        }

        .add-to-cart-btn {
            width: 200px;
            /* atau ukuran sesuai keinginan */
            padding: 0.65rem 1.25rem;
            font-size: 1rem;
            border-radius: 0.3rem;
            font-weight: 500;
            text-align: center;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #0d6efd;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        .size-options .form-check-input:checked+.form-check-label {
            border-color: #0d6efd;
            background-color: #e7f1ff;
            color: #0d6efd;
            font-weight: 500;
        }

        .size-options .form-check-label {
            border: 1px solid #dee2e6;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .size-options .form-check-label:hover {
            border-color: #adb5bd;
        }

        .size-options .form-check-input {
            display: none;
        }

        /* Style untuk form rating */
        .rating-form-container {
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
            margin-top: 2rem;
        }

        .rating-stars {
            display: inline-block;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 1.8rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-stars input[type="radio"]:checked~label,
        .rating-stars label:hover,
        .rating-stars label:hover~label {
            color: #ffc107;
        }

        /* Arah sebaliknya untuk hover */
        .rating-stars {
            display: inline-flex;
            flex-direction: row-reverse;
        }

        .product-rating-display .fa-star {
            color: #ffc107;
        }

        .product-rating-display .fa-star-half-alt {
            color: #ffc107;
        }

        .product-rating-display .text-muted {
            font-size: 0.9rem;
        }

        /* Style untuk ikon sale */
        .product-image-wrapper {
            position: relative;
            display: inline-block;
            /* Agar wrapper pas dengan gambar */
        }

        .sale-badge-detail {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: #dc3545;
            color: white;
            z-index: 10;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($nama_toko_header); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavGlobal" aria-controls="navbarNavGlobal" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavGlobal">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link <?php echo ($current_page_base == 'index.php') ? 'active' : ''; ?>" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link <?php echo ($current_page_base == 'produk.php' || $current_page_base == 'detail_produk.php') ? 'active' : ''; ?>" href="produk.php">PRODUCT</a></li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'promo.php') ? 'active' : ''; ?>" href="promo.php">SALE</a>
                        </li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">
                    <form action="produk.php" method="GET" class="d-flex me-2" role="search">
                        <input class="form-control form-control-sm" type="search" id="searchInputNavbar" name="keyword_pencarian" placeholder="Cari produk..." aria-label="Cari produk" value="<?php echo htmlspecialchars($_GET['keyword_pencarian'] ?? ''); ?>">
                        <button class="btn btn-outline-primary btn-sm ms-1" type="submit" id="searchButtonNavbar"><i class="fas fa-search"></i></button>
                    </form>
                    </form>
                    <a href="keranjang.php" class="position-relative me-3 text-dark nav-link <?php echo ($current_page_base == 'keranjang.php' || $current_page_base == 'checkout.php') ? 'active' : ''; ?>" title="Keranjang Belanja">
                        <i class="fas fa-shopping-bag fs-5"></i>
                        <?php
                        $jumlah_total_kuantitas_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $cart_key_nav => $item_data_nav) {
                                if (is_array($item_data_nav) && isset($item_data_nav['kuantitas'])) {
                                    $jumlah_total_kuantitas_keranjang += (int)$item_data_nav['kuantitas'];
                                }
                            }
                        }
                        if ($jumlah_total_kuantitas_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65em; padding: 0.3em 0.5em;">
                                <?php echo $jumlah_total_kuantitas_keranjang; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Akun Saya"><i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item" href="akun_saya.php">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item" href="alamat_saya.php">Alamat Saya</a></li>
                                <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout_pelanggan.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login_pelanggan.php" class="nav-link text-dark me-2">Login</a>
                        <a href="register.php" class="btn btn-outline-dark btn-sm">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5" style="margin-top: 80px;">
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
                <p><a href="produk.php" class="alert-link">Kembali ke daftar produk</a></p>
            </div>
        <?php elseif ($product_detail): ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="produk.php">Produk</a></li>
                    <?php if (isset($product_detail['id_kategori']) && !empty($product_detail['nama_kategori'])): ?>
                        <li class="breadcrumb-item"><a href="produk.php?kategori=<?php echo $product_detail['id_kategori']; ?>"><?php echo htmlspecialchars($product_detail['nama_kategori']); ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product_detail['name']); ?></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0 text-center">
                    <div class="product-image-wrapper">
                        <?php
                        $sekarang = date('Y-m-d');
                        $sedang_diskon = ($product_detail['discount_percent'] > 0 && $sekarang >= $product_detail['discount_start_date'] && $sekarang <= $product_detail['discount_end_date']);

                        if ($sedang_diskon) {
                            echo '<span class="badge fs-5 p-2 sale-badge-detail">SALE ' . htmlspecialchars($product_detail['discount_percent']) . '%</span>';
                        }

                        $image_name = htmlspecialchars($product_detail['image'] ?? '');
                        $image_path_from_admin = "../ADMIN MENU/uploads/produk/" . $image_name;
                        $placeholder_path_from_admin = "../ADMIN MENU/placeholder_image.png";

                        if (!empty($image_name) && file_exists($image_path_from_admin)):
                        ?>
                            <img src="<?php echo $image_path_from_admin; ?>" alt="<?php echo htmlspecialchars($product_detail['name']); ?>" class="product-detail-img">
                        <?php else: ?>
                            <img src="<?php echo $placeholder_path_from_admin; ?>" alt="Gambar tidak tersedia" class="product-detail-img">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 product-info">
                    <h1><?php echo htmlspecialchars($product_detail['name']); ?></h1>

                    <?php if (!empty($product_detail['brand'])): ?>
                        <p class="brand">Merek: <?php echo htmlspecialchars($product_detail['brand']); ?></p>
                    <?php endif; ?>

                    <?php if (isset($product_detail['id_kategori']) && !empty($product_detail['nama_kategori'])): ?>
                        <p class="category-info mb-2">
                            Kategori:
                            <a href="produk.php?kategori=<?php echo htmlspecialchars($product_detail['id_kategori']); ?>">
                                <?php echo htmlspecialchars($product_detail['nama_kategori']); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                    <!-- Tampilan Rating Produk -->
                    <div class="product-rating-display mb-3">
                        <?php
                        $rating = $product_detail['average_rating'] ?? 0;
                        $rating_count = $product_detail['rating_count'] ?? 0;
                        for ($i = 1; $i <= 5; $i++) {
                            if ($rating >= $i) echo '<i class="fas fa-star"></i>'; // Bintang penuh
                            elseif ($rating > ($i - 1) && $rating < $i) echo '<i class="fas fa-star-half-alt"></i>'; // Bintang setengah
                            else echo '<i class="far fa-star"></i>'; // Bintang kosong
                        }
                        ?>
                        <span class="text-muted ms-2">(<?php echo number_format($rating, 1); ?> dari <?php echo $rating_count; ?> ulasan)</span>
                    </div>

                    <?php if ($sedang_diskon):
                        $harga_diskon_detail = $product_detail['price'] * (1 - ($product_detail['discount_percent'] / 100));
                    ?>
                        <h2 class="price text-danger mb-0">Rp <?php echo number_format($harga_diskon_detail, 0, ',', '.'); ?></h2>
                        <p class="mb-3 text-muted"><del>Rp <?php echo number_format($product_detail['price'], 0, ',', '.'); ?></del></p>
                    <?php else: ?>
                        <p class="price">Rp <?php echo number_format($product_detail['price'], 0, ',', '.'); ?></p>
                    <?php endif; ?>

                    <div class="stock mb-2">
                        Stok:
                        <?php if ($product_detail['stock'] > 0): ?>
                            <span class="badge bg-dark">Tersedia (<?php echo $product_detail['stock']; ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Habis</span>
                        <?php endif; ?>
                    </div>

                    <form action="tambah_ke_keranjang_proses.php" method="POST" class="mt-3">
                        <input type="hidden" name="id_produk" value="<?php echo htmlspecialchars($product_detail['id']); ?>">

                        <?php if (!empty($available_sizes)): ?>
                            <div class="mb-3 size-options">
                                <label class="form-label fw-bold d-block mb-2">Pilih Ukuran <span class="text-danger">*</span></label>
                                <?php foreach ($available_sizes as $index_size => $size_option): ?>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="ukuran_terpilih"
                                            id="size_<?php echo htmlspecialchars($size_option); ?>_<?php echo $product_detail['id']; ?>"
                                            value="<?php echo htmlspecialchars($size_option); ?>"
                                            <?php echo ($index_size === 0) ? 'checked' : ''; ?>
                                            required>
                                        <label class="form-check-label" for="size_<?php echo htmlspecialchars($size_option); ?>_<?php echo $product_detail['id']; ?>">
                                            <?php echo htmlspecialchars($size_option); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (!empty($product_detail['size'])): ?>
                            <p class="size-available-text">Ukuran: <?php echo htmlspecialchars($product_detail['size']); ?></p>
                            <input type="hidden" name="ukuran_terpilih" value="<?php echo htmlspecialchars($product_detail['size']); ?>">
                        <?php else: ?>
                            <input type="hidden" name="ukuran_terpilih" value="">
                        <?php endif; ?>

                        <?php if ($product_detail['stock'] > 0): ?>
                            <div class="row g-2 align-items-center mb-3">
                                <div class="col-auto">
                                    <label for="kuantitas_<?php echo $product_detail['id']; ?>" class="col-form-label fw-bold">Jumlah:</label>
                                </div>
                                <div class="col-auto" style="max-width: 100px;">
                                    <input type="number" class="form-control"
                                        name="kuantitas" id="kuantitas_<?php echo $product_detail['id']; ?>"
                                        value="1" min="1" max="<?php echo htmlspecialchars($product_detail['stock']); ?>"
                                        required style="width: 80px;">
                                </div>
                            </div>
                            <div class="description mt-3 mb-3">
                                <h4>Deskripsi Produk:</h4>
                                <p><?php echo nl2br(htmlspecialchars($product_detail['description'] ?? 'Tidak ada deskripsi untuk produk ini.')); ?></p>
                            </div>
                            <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-dark add-to-cart-btn flex-fill">
                                        <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                    </button>
                                    <button type="submit" formaction="proses_beli_sekarang.php" class="btn btn-dark add-to-cart-btn flex-fill">
                                        <i class="fas fa-bolt me-2"></i> Beli Sekarang
                                    </button>
                                </div>
                            <?php else: ?>
                                <?php
                                // Membuat URL untuk login dengan parameter redirect kembali ke halaman ini
                                // login_pelanggan.php akan menyimpan URL ini di session
                                // proses_login_pelanggan.php akan menggunakannya setelah login sukses
                                $login_url = 'login_pelanggan.php?redirect=' . urlencode($_SERVER['REQUEST_URI']);
                                ?>
                                <a href="<?php echo htmlspecialchars($login_url); ?>" class="btn btn-outline-dark add-to-cart-btn">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login untuk Tambah ke Keranjang
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary btn-lg mt-3" disabled>Stok Habis</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <!-- Bagian Rating dan Ulasan -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="rating-form-container">
                        <h4>Beri Ulasan Produk Ini</h4>
                        <?php if ($user_has_purchased && !$user_has_rated) : ?>
                            <form action="proses_rating.php" method="POST">
                                <input type="hidden" name="product_id" value="<?php echo $product_detail['id']; ?>">
                                <div class="mb-3">
                                    <label class="form-label d-block">Rating Anda:</label>
                                    <div class="rating-stars">
                                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="5 stars">★</label>
                                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">★</label>
                                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">★</label>
                                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">★</label>
                                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="review_text" class="form-label">Ulasan Anda (Opsional):</label>
                                    <textarea class="form-control" id="review_text" name="review_text" rows="4" placeholder="Bagaimana pendapat Anda tentang produk ini?"></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">Kirim Ulasan</button>
                            </form>
                        <?php elseif ($user_has_rated) : ?>
                            <div class="alert alert-info">Anda sudah memberikan ulasan untuk produk ini. Terima kasih!</div>
                        <?php else : ?>
                            <div class="alert alert-secondary">Anda harus membeli produk ini terlebih dahulu untuk memberikan ulasan.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center" role="alert">
                Produk tidak dapat ditampilkan. Silakan coba lagi.
                <p><a href="produk.php" class="alert-link">Kembali ke daftar produk</a></p>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© <?php echo date("Y"); ?> <?php echo htmlspecialchars($nama_toko_header); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputNavbar = document.getElementById('searchInputNavbar');
            const searchButtonNavbar = document.getElementById('searchButtonNavbar');

            function toggleSearchButton() {
                if (searchInputNavbar.value.trim() === '') {
                    searchButtonNavbar.disabled = true;
                } else {
                    searchButtonNavbar.disabled = false;
                }
            }

            // Panggil saat halaman dimuat untuk set kondisi awal tombol
            toggleSearchButton();

            // Tambahkan event listener untuk input
            searchInputNavbar.addEventListener('input', toggleSearchButton);
        });
    </script>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
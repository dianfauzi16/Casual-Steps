<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Fungsi get_site_setting (pastikan sudah ada di db_connect.php atau di-include)
if (!function_exists('get_site_setting')) {
    function get_site_setting($db_connection, $key)
    { /* ... (isi fungsi seperti respons sebelumnya) ... */
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
$page_title_default = "Produk Kami";
$page_title = $page_title_default;

$products_to_display = [];
$current_category_name = null;
$error_message = '';
$daftar_link_kategori = [];

// Ambil semua kategori untuk filter
$sql_get_all_categories = "SELECT id_kategori, nama_kategori FROM categories ORDER BY nama_kategori ASC";
$result_all_categories = $conn->query($sql_get_all_categories);
if ($result_all_categories && $result_all_categories->num_rows > 0) {
    while ($row_cat = $result_all_categories->fetch_assoc()) {
        $daftar_link_kategori[] = $row_cat;
    }
}

// --- Logika Pencarian & Filter Kategori ---
$filter_id_kategori = null;
$search_keyword = '';
$params = []; // Untuk menampung parameter yang akan di-bind
$types = "";  // Untuk menampung tipe data parameter

// Ambil keyword pencarian dari URL
if (isset($_GET['keyword_pencarian']) && !empty(trim($_GET['keyword_pencarian']))) {
    $search_keyword = trim($_GET['keyword_pencarian']);
    $page_title = "Hasil Pencarian untuk \"" . htmlspecialchars($search_keyword) . "\"";
}

// Ambil filter kategori dari URL
if (isset($_GET['kategori']) && filter_var($_GET['kategori'], FILTER_VALIDATE_INT)) {
    $filter_id_kategori = (int)$_GET['kategori'];
    $sql_cat_name = "SELECT nama_kategori FROM categories WHERE id_kategori = ?";
    if ($stmt_cat = $conn->prepare($sql_cat_name)) {
        $stmt_cat->bind_param("i", $filter_id_kategori);
        $stmt_cat->execute();
        $res_cat = $stmt_cat->get_result();
        if ($cat_data = $res_cat->fetch_assoc()) {
            $current_category_name = $cat_data['nama_kategori'];
            if (!empty($search_keyword)) {
                $page_title .= " dalam Kategori \"" . htmlspecialchars($current_category_name) . "\"";
            } else {
                $page_title = "Produk Kategori: " . htmlspecialchars($current_category_name);
            }
        } else {
            $filter_id_kategori = null;
            $error_message = "Kategori tidak ditemukan. Menampilkan semua produk.";
        }
        $stmt_cat->close();
    }
}


// Bangun query SQL dasar
$sql_products = "SELECT p.id, p.name, p.price, p.image, p.stock, c.nama_kategori AS nama_kategori_produk,
                        p.discount_percent, p.discount_start_date, p.discount_end_date
                 FROM product p
                 LEFT JOIN categories c ON p.id_kategori = c.id_kategori";

$conditions = [];

if ($filter_id_kategori !== null) {
    $conditions[] = "p.id_kategori = ?";
    $params[] = $filter_id_kategori;
    $types .= "i";
}

if (!empty($search_keyword)) {
    $search_like_keyword = "%" . $search_keyword . "%";
    // Cari di nama produk, deskripsi produk, merek produk, dan nama kategori
    $conditions[] = "(p.name LIKE ? OR p.description LIKE ? OR p.brand LIKE ? OR c.nama_kategori LIKE ?)";
    for ($i = 0; $i < 4; $i++) {
        $params[] = $search_like_keyword;
        $types .= "s";
    }
}



if (!empty($conditions)) {
    $sql_products .= " WHERE " . implode(" AND ", $conditions);
}
$sql_products .= " ORDER BY p.created_at DESC";


if ($stmt_products = $conn->prepare($sql_products)) {
    if (!empty($params)) {
        $stmt_products->bind_param($types, ...$params);
    }
    $stmt_products->execute();
    $result_products = $stmt_products->get_result();

    if ($result_products) {
        while ($row = $result_products->fetch_assoc()) {
            $products_to_display[] = $row;
        }
    } else {
        $error_message .= (empty($error_message) ? "" : " | ") . "Terjadi kesalahan saat mengambil produk: " . ($stmt_products->error ?: $conn->error);
    }
    if (isset($stmt_products)) $stmt_products->close(); // Pastikan statement ditutup
} else {
    $error_message .= (empty($error_message) ? "" : " | ") . "Gagal mempersiapkan statement produk: " . $conn->error;
}

$current_page_base = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko_header ?? 'Casual Steps'); // Tambahkan fallback untuk nama toko 
                                                            ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <style>
        .product-title-truncate {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.5em;
            font-size: 0.9rem;
        }

        .product-card img {
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }

        .category-filters .list-group-item.active {
            background-color: #000000;
            border-color: #000000;
            color: white;
        }

        .category-filters .list-group-item a {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .product-card .product-image-container {
            position: relative;
        }

        .sale-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: #dc3545;
            /* Bootstrap danger color */
            color: white;
            z-index: 10;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($nama_toko_header ?? 'Casual Steps'); ?></a>
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
                                <?php echo $jumlah_total_kuantitas_keranjang; ?></span>
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
                        <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5" style="margin-top: 80px;">
        <div class="row">
            <div class="col-lg-3 category-filters mb-4" >
                <h4 style="font-family: 'Special Gothic Expanded One', system-ui;">Kategori Produk</h4>
                <div class="list-group shadow-sm">
                    <a href="produk.php<?php if (!empty($search_keyword)) echo '?keyword_pencarian=' . urlencode($search_keyword); ?>"
                        class="list-group-item list-group-item-action <?php echo ($filter_id_kategori === null && empty($error_message) && !(isset($_GET['kategori']) && !empty($_GET['kategori']))) ? 'active' : ''; // Cek lebih teliti untuk active state 
                                                                        ?>">
                        Semua Kategori
                    </a>
                    <?php if (!empty($daftar_link_kategori)): ?>
                        <?php foreach ($daftar_link_kategori as $kategori_link): ?>
                            <a href="produk.php?kategori=<?php echo $kategori_link['id_kategori'];
                                                            if (!empty($search_keyword)) echo '&keyword_pencarian=' . urlencode($search_keyword); ?>"
                                class="list-group-item list-group-item-action <?php echo ($filter_id_kategori == $kategori_link['id_kategori']) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($kategori_link['nama_kategori']); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="list-group-item text-muted small">Tidak ada kategori tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0" style="font-family: 'Special Gothic Expanded One', system-ui;"><?php echo htmlspecialchars($page_title); ?></h2>
                    <span class="text-muted small">Menampilkan <?php echo count($products_to_display); ?> produk</span>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-warning mt-2"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['keyword_pencarian']) && !empty($search_keyword) && empty($products_to_display) && empty($error_message)): ?>
                    <div class="alert alert-info">
                        Tidak ada produk yang cocok dengan kata kunci pencarian "<strong><?php echo htmlspecialchars($search_keyword); ?></strong>"
                        <?php if ($current_category_name) echo " dalam kategori \"" . htmlspecialchars($current_category_name) . "\""; ?>.
                        Coba kata kunci lain atau <a href="produk.php<?php if ($filter_id_kategori) echo '?kategori=' . $filter_id_kategori; ?>" class="alert-link">lihat semua produk <?php if ($current_category_name) echo "dalam kategori ini"; ?></a>.
                    </div>
                <?php endif; ?>

                <div class="row">
                    <?php if (!empty($products_to_display)): ?>
                        <?php foreach ($products_to_display as $product): ?>
                            <div class="col-6 col-sm-6 col-md-4 mb-4">
                                <div class="product-card card h-100 shadow-sm">
                                    <a href="detail_produk.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark d-block">
                                        <div class="product-image-container">
                                            <?php
                                            // --- AWAL LOGIKA PROMO YANG DIPERBAIKI ---
                                            $punya_diskon = !empty($product['discount_percent']) && $product['discount_percent'] > 0;
                                            $sekarang = date('Y-m-d');

                                            // Cek apakah promo aktif. Promo aktif jika:
                                            // 1. Punya persen diskon.
                                            // 2. Tidak ada tanggal mulai ATAU hari ini setelah/sama dengan tanggal mulai.
                                            // 3. Tidak ada tanggal selesai ATAU hari ini sebelum/sama dengan tanggal selesai.
                                            $sedang_diskon = $punya_diskon &&
                                                (empty($product['discount_start_date']) || $sekarang >= $product['discount_start_date']) &&
                                                (empty($product['discount_end_date'])   || $sekarang <= $product['discount_end_date']);

                                            if ($sedang_diskon) {
                                                echo '<span class="badge sale-badge">SALE ' . htmlspecialchars($product['discount_percent']) . '%</span>';
                                            }
                                            // --- AKHIR LOGIKA PROMO YANG DIPERBAIKI ---

                                            $image_name = htmlspecialchars($product['image'] ?? '');
                                            $image_path_from_admin = "../ADMIN MENU/uploads/produk/" . $image_name;
                                            $placeholder_path_from_admin = "../ADMIN MENU/placeholder_image.png";

                                            if (!empty($image_name) && file_exists($image_path_from_admin)):
                                            ?>
                                                <img src="<?php echo $image_path_from_admin; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top">
                                            <?php else: ?>
                                                <img src="<?php echo $placeholder_path_from_admin; ?>" alt="Gambar tidak tersedia" class="card-img-top">
                                            <?php endif; ?>
                                        </div>

                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title product-title-truncate" ><?php echo htmlspecialchars($product['name']); ?></h5>
                                            <?php if (!empty($product['nama_kategori_produk'])): ?>
                                                <p class="card-text small text-muted mb-1" style="font-size: 0.75rem;">Kategori: <?php echo htmlspecialchars($product['nama_kategori_produk']); ?></p>
                                            <?php endif; ?>
                                            <div class="mt-auto">
                                                <?php if ($sedang_diskon):
                                                    $harga_diskon = $product['price'] * (1 - ($product['discount_percent'] / 100));
                                                ?>
                                                    <p class="card-text small text-muted mb-0"><del>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></del></p>
                                                    <p class="fw-bold card-text text-danger">Rp <?php echo number_format($harga_diskon, 0, ',', '.'); ?></p>
                                                <?php else: ?>
                                                    <p class="fw-bold card-text mt-2">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php elseif (empty($error_message) && !(isset($_GET['keyword_pencarian']) && !empty($search_keyword))): ?>
                        <div class="col-12">
                            <p class="text-center text-muted">Tidak ada produk yang ditemukan untuk kriteria ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 Casual Steps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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
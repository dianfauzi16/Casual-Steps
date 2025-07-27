<?php
// promo.php

// Memulai atau melanjutkan sesi jika diperlukan
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Ambil pengaturan nama toko
$nama_toko = function_exists('get_site_setting') ? (get_site_setting($conn, 'nama_toko') ?: "Casual Steps") : "Casual Steps";
$page_title = "Promo & Diskon Spesial";

// --- INTI LOGIKA: Mengambil produk yang diskonnya sedang aktif ---
$promo_products = [];
$sekarang_tanggal = date('Y-m-d'); // Tanggal hari ini

// Query ini mengambil produk dengan diskon > 0 dan periode waktunya valid
$sql_promo = "SELECT p.id, p.name, p.price, p.image, p.stock,
                     p.discount_percent, p.discount_start_date, p.discount_end_date
              FROM product p
              WHERE
                  p.discount_percent > 0
                  AND (p.discount_start_date IS NULL OR ? >= p.discount_start_date)
                  AND (p.discount_end_date IS NULL OR ? <= p.discount_end_date)
              ORDER BY p.discount_percent DESC, p.created_at DESC";

if ($stmt = $conn->prepare($sql_promo)) {
    $stmt->bind_param("ss", $sekarang_tanggal, $sekarang_tanggal);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $promo_products[] = $row;
    }
    $stmt->close();
}
// --- Akhir Logika ---

$current_page_base = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <style>
        .product-card img {
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }

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

        .product-image-container {
            position: relative;
        }

        .sale-badge {
            position: absolute;
            top: 10px;
            left: 10px;
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
                <a class="navbar-brand fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;" href="index.php"><?php echo htmlspecialchars($nama_toko); ?></a>
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNavGlobal"
                    aria-controls="navbarNavGlobal" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavGlobal">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'index.php') ? 'active' : ''; ?>" href="index.php">HOME</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">ABOUT</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page_base == 'produk.php' || $current_page_base == 'detail_produk.php') ? 'active' : ''; ?>" href="produk.php">PRODUCT</a>
                        </li>
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
                                <?php echo $jumlah_total_kuantitas_keranjang; ?>
                                <span class="visually-hidden">item di keranjang</span>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Akun Saya">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item <?php echo ($current_page_base == 'akun_saya.php') ? 'active' : ''; ?>" href="akun_saya.php">Profil Saya</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page_base == 'riwayat_pesanan.php' || $current_page_base == 'detail_order_pelanggan.php') ? 'active' : ''; ?>" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page_base == 'alamat_saya.php' || $current_page_base == 'tambah_alamat.php' || $current_page_base == 'edit_alamat.php') ? 'active' : ''; ?>" href="alamat_saya.php">Alamat Saya</a></li>
                                <li><a class="dropdown-item <?php echo ($current_page_base == 'ubah_password.php') ? 'active' : ''; ?>" href="ubah_password.php">Ubah Password</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="logout_pelanggan.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login_pelanggan.php" class="nav-link text-dark me-2 <?php echo ($current_page_base == 'login_pelanggan.php') ? 'active' : ''; ?>">Login</a>
                        <a href="register.php" class="btn btn-primary btn-sm <?php echo ($current_page_base == 'register.php') ? 'active' : ''; ?>">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5" style="margin-top: 80px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="display-5 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?php echo htmlspecialchars($page_title); ?></h2>
            <span class="text-muted small">Menampilkan <?php echo count($promo_products); ?> produk promo</span>
        </div>

        <div class="row">
            <?php if (!empty($promo_products)): ?>
                <?php foreach ($promo_products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <div class="product-card card h-100 shadow-sm">
                            <a href="detail_produk.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark d-block">
                                <div class="product-image-container">
                                    <?php
                                    // Logika badge diskon
                                    echo '<span class="badge sale-badge">SALE ' . htmlspecialchars($product['discount_percent']) . '%</span>';

                                    $image_name = htmlspecialchars($product['image'] ?? '');
                                    $image_path = "../ADMIN MENU/uploads/produk/" . $image_name;
                                    $placeholder_path = "../ADMIN MENU/placeholder_image.png";

                                    if (!empty($image_name) && file_exists($image_path)):
                                    ?>
                                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top">
                                    <?php else: ?>
                                        <img src="<?php echo $placeholder_path; ?>" alt="Gambar tidak tersedia" class="card-img-top">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title product-title-truncate"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <div class="mt-auto">
                                        <?php
                                        // Logika harga diskon
                                        $harga_asli = $product['price'];
                                        $persen_diskon = $product['discount_percent'];
                                        $harga_diskon = $harga_asli - ($harga_asli * $persen_diskon / 100);
                                        ?>
                                        <p class="card-text small text-muted mb-0"><del>Rp <?php echo number_format($harga_asli, 0, ',', '.'); ?></del></p>
                                        <p class="fw-bold card-text text-danger">Rp <?php echo number_format($harga_diskon, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h4>Belum Ada Promo</h4>
                    <p class="text-muted">Saat ini belum ada produk yang sedang promo. Cek kembali nanti!</p>
                    <a href="produk.php" class="btn btn-primary mt-2">Lihat Semua Produk</a>
                </div>
            <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
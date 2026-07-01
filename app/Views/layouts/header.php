<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'Casual Steps'; ?> - <?= htmlspecialchars($global_settings['nama_toko'] ?? 'Casual Steps') ?></title>
    <meta name="description" content="<?= htmlspecialchars($global_settings['deskripsi_toko'] ?? 'Menyediakan koleksi sepatu premium dan kasual dengan harga terbaik.') ?>">
    <link rel="icon" href="<?= BASE_URL ?>admin/Assets/logo.png" type="image/png">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="og:title" content="<?= isset($page_title) ? htmlspecialchars($page_title) : 'Casual Steps'; ?> - <?= htmlspecialchars($global_settings['nama_toko'] ?? 'Casual Steps') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($global_settings['deskripsi_toko'] ?? 'Menyediakan koleksi sepatu premium dan kasual dengan harga terbaik.') ?>">
    <meta property="og:image" content="<?= BASE_URL ?>admin/Assets/logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>">
    <meta property="twitter:title" content="<?= isset($page_title) ? htmlspecialchars($page_title) : 'Casual Steps'; ?> - <?= htmlspecialchars($global_settings['nama_toko'] ?? 'Casual Steps') ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($global_settings['deskripsi_toko'] ?? 'Menyediakan koleksi sepatu premium dan kasual dengan harga terbaik.') ?>">
    <meta property="twitter:image" content="<?= BASE_URL ?>admin/Assets/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Menggunakan BASE_URL agar pemuatan aset seperti CSS selalu berhasil walau path URL berubah -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { 
            background-color: #f8f9fa; 
            font-family: 'Inter', sans-serif;
            color: #333;
        }
        .font-special { font-family: 'Special Gothic Expanded One', system-ui; }
        .glass-navbar {
            background-color: rgba(255, 255, 255, 0.85) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.3s ease;
        }
        .nav-link {
            font-weight: 500;
            color: #6c757d !important;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-link:hover {
            color: #0d6efd !important;
        }
        .nav-link.active {
            color: #0d6efd !important;
            font-weight: 700;
        }
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background-color: #0d6efd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php
        $current_route = $_GET['url'] ?? '';
        $is_home = empty($current_route) || $current_route === 'Home/index';
        $is_about = $current_route === 'Home/about';
        $is_product = strpos($current_route, 'Product') === 0; // covers Product/index, Product/detail
        $is_promo = $current_route === 'Home/promo';
    ?>
    <?php if (isset($global_settings['announcement_active']) && $global_settings['announcement_active'] == '1' && !empty($global_settings['announcement_text'])): ?>
    <div class="bg-primary text-white text-center py-2 px-3 fw-medium position-relative z-3" style="font-size: 0.9rem; letter-spacing: 0.5px;">
        <?php if (!empty($global_settings['announcement_link'])): ?>
            <a href="<?= htmlspecialchars($global_settings['announcement_link']) ?>" class="text-white text-decoration-none d-block d-md-inline-block">
                <?= htmlspecialchars($global_settings['announcement_text']) ?>
                <i class="fas fa-arrow-right ms-2 fs-6 align-middle"></i>
            </a>
        <?php else: ?>
            <span><?= htmlspecialchars($global_settings['announcement_text']) ?></span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light glass-navbar <?= (isset($global_settings['announcement_active']) && $global_settings['announcement_active'] == '1' && !empty($global_settings['announcement_text'])) ? 'sticky-top border-bottom' : 'fixed-top' ?>">
            <div class="container">
                <a class="navbar-brand fw-bold text-uppercase" href="<?= BASE_URL ?>" style="letter-spacing: 1px; font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($global_settings['nama_toko'] ?? 'CASUAL STEPS') ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav gap-3">
                        <li class="nav-item"><a class="nav-link <?= $is_home ? 'active' : '' ?>" href="<?= BASE_URL ?>">HOME</a></li>
                        <li class="nav-item"><a class="nav-link <?= $is_about ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?url=Home/about">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link <?= $is_product ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?url=Product/index">PRODUCT</a></li>
                        <li class="nav-item"><a class="nav-link <?= $is_promo ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?url=Home/promo">SALE</a></li>
                    </ul>
                </div>
                
                <div class="d-flex align-items-center">
                    <form action="<?= BASE_URL ?>index.php" method="GET" class="d-flex me-3" role="search" id="headerSearchForm">
                        <input type="hidden" name="url" value="Product/index">
                        <input class="form-control form-control-sm me-1" type="search" name="keyword_pencarian" id="headerSearchInput" placeholder="Cari produk..." aria-label="Cari produk" value="<?= htmlspecialchars($_GET['keyword_pencarian'] ?? ''); ?>">
                        <button class="btn btn-outline-primary btn-sm" type="submit" id="headerSearchBtn" <?= empty($_GET['keyword_pencarian']) ? 'disabled' : '' ?>><i class="fas fa-search"></i></button>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const searchInput = document.getElementById('headerSearchInput');
                            const searchBtn = document.getElementById('headerSearchBtn');
                            
                            searchInput.addEventListener('input', function() {
                                if (this.value.trim().length > 0) {
                                    searchBtn.removeAttribute('disabled');
                                } else {
                                    searchBtn.setAttribute('disabled', 'disabled');
                                }
                            });
                        });
                    </script>

                    <a href="<?= BASE_URL ?>index.php?url=Cart/index" class="position-relative me-3 text-dark nav-link" title="Keranjang Belanja">
                        <i class="fas fa-shopping-bag fs-5"></i>
                        <?php
                        $jumlah_total_kuantitas_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $item_data_nav) {
                                if (is_array($item_data_nav) && isset($item_data_nav['kuantitas'])) {
                                    $jumlah_total_kuantitas_keranjang += (int)$item_data_nav['kuantitas'];
                                }
                            }
                        }
                        if ($jumlah_total_kuantitas_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65em; padding: 0.3em 0.5em;">
                                <?= $jumlah_total_kuantitas_keranjang; ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i> <?= htmlspecialchars($_SESSION['user_name'] ?? 'Akun'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?url=Profile/index">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?url=Order/history">Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php?url=Address/index">Alamat Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>index.php?url=Auth/logout">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a class="nav-link text-dark me-2" href="<?= BASE_URL ?>index.php?url=Auth/login">Login</a>
                        <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>index.php?url=Auth/register">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <!-- Mulai Konten Utama -->

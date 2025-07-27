<?php
// Memulai atau melanjutkan sesi jika diperlukan
if (session_status() == PHP_SESSION_NONE) {
  session_start(); // PASTIKAN INI ADA DI PALING ATAS SEBELUM OUTPUT APAPUN
}

// 1. Sertakan file koneksi database (yang juga berisi fungsi get_site_setting)
require_once __DIR__ . '/../ADMIN MENU/db_connect.php'; // Pastikan path ini benar sesuai struktur folder Anda

// Ambil pengaturan yang dibutuhkan menggunakan fungsi get_site_setting()
// Pastikan fungsi get_site_setting($conn, 'key') sudah ada di db_connect.php atau file functions.php yang di-include
$nama_toko = function_exists('get_site_setting') ? (get_site_setting($conn, 'nama_toko') ?: "Casual Steps") : "Casual Steps";
$email_kontak_toko = function_exists('get_site_setting') ? (get_site_setting($conn, 'email_kontak') ?: "kontak@example.com") : "kontak@example.com";
$telepon_toko_kontak = function_exists('get_site_setting') ? (get_site_setting($conn, 'telepon_toko') ?: "0812-xxxx-xxxx") : "0812-xxxx-xxxx";
$alamat_toko_kontak = function_exists('get_site_setting') ? (get_site_setting($conn, 'alamat_toko_lengkap') ?: "Alamat Toko Anda Belum Diatur") : "Alamat Toko Anda Belum Diatur";
$deskripsi_toko_meta = function_exists('get_site_setting') ? (get_site_setting($conn, 'deskripsi_toko') ?: "Pusat Sepatu Casual Kekinian dan Berkualitas.") : "Pusat Sepatu Casual Kekinian dan Berkualitas.";


// Logika untuk mengambil produk rekomendasi
$recommended_products = [];
$error_message_produk = '';
$sql_recommendations = "SELECT id, name, price, image FROM product ORDER BY created_at DESC LIMIT 8";
$result_recommendations = $conn->query($sql_recommendations);

if ($result_recommendations) {
  if ($result_recommendations->num_rows > 0) {
    while ($row = $result_recommendations->fetch_assoc()) {
      $recommended_products[] = $row;
    }
  } else {
    // Tidak ada produk rekomendasi, $recommended_products akan tetap kosong
  }
} else {
  // Gagal query, catat error jika perlu untuk debugging admin
  // $error_message_produk = "Gagal mengambil produk rekomendasi: " . $conn->error; 
}

// Logika untuk mengambil produk "best shoes"
$best_shoes_products = [];
// Mengambil 2 produk dengan rating tertinggi.
// Jika rating sama, utamakan yang jumlah ratingnya lebih banyak.
$sql_best_shoes = "SELECT id, name, price, image, average_rating, rating_count 
                   FROM product ORDER BY average_rating DESC, rating_count DESC LIMIT 2";
$result_best_shoes = $conn->query($sql_best_shoes);
if ($result_best_shoes) {
  while ($row = $result_best_shoes->fetch_assoc()) {
    $best_shoes_products[] = $row;
  }
}
// Tidak perlu pesan error untuk bagian ini, jika kosong akan ditangani di HTML
// Logika untuk mengambil daftar brand
$brands = [];
$sql_brands = "SELECT DISTINCT brand FROM product WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC LIMIT 12";
$result_brands = $conn->query($sql_brands);
if ($result_brands) {
  while ($row = $result_brands->fetch_assoc()) {
    $brands[] = $row['brand'];
  }
}

// Mapping brand ke logo untuk ditampilkan. Kunci harus huruf kecil.
$brand_logos = [
  'adidas' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Adidas_logo.png/800px-Adidas_logo.png',
  'nike' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png',
  'nb' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/New_Balance_logo.svg/640px-New_Balance_logo.svg.png',
  'new balance' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/New_Balance_logo.svg/640px-New_Balance_logo.svg.png',
  'puma' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Puma-logo-%28text%29.svg/640px-Puma-logo-%28text%29.svg.png',
  'vans' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Vans-logo.svg/640px-Vans-logo.svg.png',
  'converse' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Converse_logo.svg/640px-Converse_logo.svg.png',
  'salomon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Salomon_logo.svg/640px-Salomon_logo.svg.png', // Logo Salomon ditambahkan
  'hoka' => 'https://cdn.freelogovectors.net/wp-content/uploads/2022/07/hoka-logo-freelogovectors.net_.png'
];


$current_page_base = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($nama_toko); ?> - Shoes Culture For People Culture</title>
  <meta name="description" content="<?php echo htmlspecialchars($deskripsi_toko_meta); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="styles.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Special+Gothic+Expanded+One&display=swap" rel="stylesheet">
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


  <section class="hero-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="display-3 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">SHOES CULTURE FOR PEOPLE CULTURE</h1>
        </div>
        <div class="col-md-6">
          <img src="../ADMIN MENU/Assets/hoka.jpg" alt="Contoh Sepatu" class="img-fluid" />
        </div>
      </div>
    </div>
  </section>

  <!-- Carousel Section -->
  <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="../ADMIN MENU/Assets/1.png" class="d-block w-100 carousel-img" alt="Gamvar Carousel">
        <div class="carousel-caption">
          <h1 class="display-3 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">SHOES CULTURE FOR PEOPLE CULTURE</h1>
          <p class="lead">Temukan koleksi sepatu terbaik yang mendefinisikan gaya Anda.</p>
          <a href="produk.php" class="btn btn-primary btn-lg">Jelajahi Sekarang</a>
        </div>
      </div>
      <div class="carousel-item">
        <img src="../ADMIN MENU/Assets/2.png" class="d-block w-100 carousel-img" alt="Gamvar Carousel">
        <div class="carousel-caption">
          <h2 class="display-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">Koleksi Terbaru Telah Tiba</h2>
          <p class="lead">Jangan lewatkan desain paling trendi musim ini. Kualitas dan kenyamanan terjamin.</p>
          <a href="produk.php" class="btn btn-light btn-lg">Lihat Koleksi</a>
        </div>
      </div>
      <div class="carousel-item">
        <img src="../ADMIN MENU/Assets/3.png" class="d-block w-100 carousel-img" alt="Gamvar Carousel">
        <div class="carousel-caption">
          <h2 class="display-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">Penawaran Spesial</h2>
          <p class="lead">Dapatkan diskon eksklusif untuk produk-produk pilihan. Terbatas!</p>
          <a href="produk.php" class="btn btn-warning btn-lg">Klaim Penawaran</a>
        </div>
      </div>
    </div>
  </div>

  <section class="features-section py-5 bg-dark text-white">
    <div class="container">
      <div class="row">
        <div class="col-12 text-center mb-2">
          <h2 class="display-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">BEST FEATURE<br />IN SHOES</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="row">
            <div class="col-11 mx-auto mb-1">
              <img src="../ADMIN MENU/Assets/cs-removebg-preview.png" alt="Contoh Sepatu Fitur" class="img-fluid rounded" />
            </div>
          </div>
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center">
          <div class="feature-item mb-4">
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-dollar-sign feature-icon me-3"></i>
              <h3>Harga Berkualitas</h3>
            </div>
            <p>Kami menawarkan harga terbaik untuk produk berkualitas tinggi. Dapatkan sepatu branded dengan harga yang terjangkau dan bersaing di pasaran.</p>
          </div>
          <div class="feature-item mb-4">
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-headset feature-icon me-3"></i>
              <h3>Pelayanan Terbaik</h3>
            </div>
            <p>Tim customer service kami siap melayani Anda dengan respon cepat. Kami berkomitmen untuk memberikan pengalaman berbelanja yang menyenangkan.</p>
          </div>
          <div class="feature-item">
            <div class="d-flex align-items-center mb-2">
              <i class="fas fa-medal feature-icon me-3"></i>
              <h3>Kualitas Terjamin</h3>
            </div>
            <p>Semua produk kami melewati quality control yang ketat sebelum dikirim ke pelanggan. Kami memastikan Anda mendapatkan produk dengan kualitas terbaik.</p>
          </div>
        </div>
      </div>
    </div>
  </section>


  <section class="best-shoes-section py-5 bg-dark text-white">
    <div class="container">
      <div class="row">
        <div class="col-md-5">
          <h2 class="display-5 fw-bold mb-4" style="font-family:'Special Gothic Expanded One', system-ui;">THE BEST SHOES<br />FOR THE CULTURE<br />PEOPLE</h2>
          <p class="mb-4">Kami menyediakan berbagai pilihan sepatu casual berkualitas tinggi untuk pria dan wanita. Dengan desain yang trendy dan kekinian, sepatu kami cocok untuk berbagai aktivitas sehari-hari.</p>
          <p class="mb-4">Dibuat dengan bahan berkualitas tinggi, sepatu kami tidak hanya stylish tetapi juga nyaman dipakai. Kami berkomitmen untuk memberikan produk terbaik dengan harga yang terjangkau.</p>
          <a href="produk.php" class="btn btn-outline-light px-4 py-2">Lihat Semua Koleksi</a>
        </div>
        <div class="col-md-7 mt-4 mt-md-0">
          <div class="row">
            <?php if (!empty($best_shoes_products)): ?>
              <?php foreach ($best_shoes_products as $product): ?>
                <div class="col-md-6 mb-4">
                  <a href="detail_produk.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-white">
                    <div class="product-card card h-100 shadow-sm bg-dark border-secondary">
                      <?php
                      $image_name_best = htmlspecialchars($product['image'] ?? '');
                      $image_path_best = "../ADMIN MENU/uploads/produk/" . $image_name_best;
                      $placeholder_path_best = "../ADMIN MENU/placeholder_image.png";

                      if (!empty($image_name_best) && file_exists($image_path_best)):
                      ?>
                        <img src="<?php echo $image_path_best; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover;">
                      <?php else: ?>
                        <img src="<?php echo $placeholder_path_best; ?>" alt="Gambar tidak tersedia" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover;">
                      <?php endif; ?>

                      <div class="card-body d-flex flex-column text-start p-3">
                        <h5 class="card-title product-title-truncate text-light" style="min-height: 2.5em; font-size: 0.9rem;"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <div class="rating-display mb-2">
                          <?php
                          $rating_best = $product['average_rating'] ?? 0;
                          for ($i = 1; $i <= 5; $i++) {
                            if ($rating_best >= $i) echo '<i class="fas fa-star text-warning"></i>';
                            elseif ($rating_best > ($i - 1) && $rating_best < $i) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                            else echo '<i class="far fa-star text-warning"></i>';
                          }
                          ?>
                          <span class="text-white-50 small ms-1">(<?php echo $product['rating_count'] ?? 0; ?>)</span>
                        </div>
                        <div class="mt-auto">
                          <p class="text-white-50 mb-1 small">Start From</p>
                          <p class="fw-bold card-text mb-0 text-white">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="col-12">
                <p class="text-center">Produk terbaik akan segera hadir.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5 bg-light">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-3">
          <div class="text-center feature-card">
            <div class="feature-icon mx-auto"><i class="fas fa-truck-fast fa-2x text-primary"></i></div>
            <h5>Pengiriman Cepat</h5>
            <p class="text-muted">Pengiriman ke seluruh Indonesia dalam 1-3 hari kerja.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center feature-card">
            <div class="feature-icon mx-auto"><i class="fas fa-shield-alt fa-2x text-primary"></i></div>
            <h5>Garansi Produk</h5>
            <p class="text-muted">Garansi 30 hari untuk semua produk yang kami jual.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center feature-card">
            <div class="feature-icon mx-auto"><i class="fas fa-credit-card fa-2x text-primary"></i></div>
            <h5>Pembayaran Aman</h5>
            <p class="text-muted">Berbagai metode pembayaran yang aman dan terpercaya.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="text-center feature-card">
            <div class="feature-icon mx-auto"><i class="fas fa-headset fa-2x text-primary"></i></div>
            <h5>Layanan 24/7</h5>
            <p class="text-muted">Tim customer service kami siap membantu 24/7.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="product1" class="recommendation-section py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="display-5 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">RECOMENDATION</h2>
        <a href="produk.php" class="text-decoration-none">SEE ALL <i class="fas fa-arrow-right"></i></a>
      </div>
      <div class="row">
        <?php if (!empty($error_message_produk) && empty($recommended_products)): ?>
          <div class="col-12">
            <p class="text-center text-danger"><?php echo htmlspecialchars($error_message_produk); ?></p>
          </div>
        <?php elseif (!empty($recommended_products)): ?>
          <?php foreach ($recommended_products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4">
              <a href="detail_produk.php?id=<?php echo $product['id']; ?>" class="text-decoration-none text-dark">
                <div class="product-card card h-100 shadow-sm">
                  <?php
                  $image_name = htmlspecialchars($product['image'] ?? '');
                  $image_path = "../ADMIN MENU/uploads/produk/" . $image_name;
                  $placeholder_path = "../ADMIN MENU/placeholder_image.png"; // Pastikan placeholder ada

                  if (!empty($image_name) && file_exists($image_path)):
                  ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover;">
                  <?php else: ?>
                    <img src="<?php echo $placeholder_path; ?>" alt="Gambar tidak tersedia" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover;">
                  <?php endif; ?>

                  <div class="card-body d-flex flex-column">
                    <h5 class="card-title product-title-truncate" style="min-height: 2.5em; font-size: 0.9rem;"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="text-danger mb-1 small">Start From</p>
                    <p class="fw-bold card-text mt-auto">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-center">Belum ada produk untuk direkomendasikan saat ini.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- All Brands Section -->
  <section id="all-brands" class="all-brands-section py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="display-5 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">ALL BRANDS AVAILABLE</h2>
        <p class="lead text-muted">Jelajahi produk berdasarkan merek favorit Anda.</p>
      </div>
      <div class="row g-4 justify-content-center">
        <?php if (!empty($brands)): ?>
          <?php foreach ($brands as $brand_name): ?>
            <?php
            // Gunakan strtolower untuk mencocokkan kunci di array mapping
            $brand_key = strtolower($brand_name);
            // Tampilkan hanya jika logo tersedia di mapping
            if (array_key_exists($brand_key, $brand_logos)):
              $logo_url = $brand_logos[$brand_key];
            ?>
              <div class="col-6 col-md-4 col-lg-3">
                <a href="produk.php?keyword_pencarian=<?php echo urlencode($brand_name); ?>" class="brand-item-link">
                  <div class="card brand-item-card h-100">
                    <div class="card-body d-flex align-items-center justify-content-center" style="height:80px;">
                      <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Logo <?php echo htmlspecialchars($brand_name); ?>" class="img-fluid" style="max-height:60px; object-fit:contain;">
                    </div>
                  </div>
                </a>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; // Tidak perlu 'else' karena jika kosong, tidak ada yang ditampilkan 
        ?>
      </div>
    </div>
  </section>

  <section id="about1" class="about-section py-5 bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h2 class="display-4 fw-bold mb-4" style="font-family: 'Special Gothic Expanded One', system-ui;"><?php echo htmlspecialchars(strtoupper($nama_toko)); ?></h2>
          <p class="mb-4"><?php echo htmlspecialchars($deskripsi_toko_meta); ?></p>
          <p class="mb-4">Didirikan pada tahun 2020, <?php echo htmlspecialchars($nama_toko); ?> telah menjadi salah satu toko sepatu casual favorit anak muda di Yogyakarta. Kami terus berinovasi untuk memberikan pengalaman berbelanja yang menyenangkan bagi pelanggan kami.</p>
          <a href="medsos.php" class="btn btn-outline-dark px-4 py-2" target="_blank">Cek Media Sosial Kami</a>
        </div>
        <div class="col-md-6 mt-4 mt-md-0">
          <img src="../ADMIN MENU/Assets/brand-logo.png" alt="Tentang <?php echo htmlspecialchars($nama_toko); ?>" class="img-fluid" />
        </div>
      </div>
    </div>
  </section>

  <section id="about" class="about-section">
    <div class="brand-bar">
      <div class="brand-carousel">
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png" alt="Nike Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Adidas_logo.png/800px-Adidas_logo.png" alt="Adidas Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Vans-logo.svg/640px-Vans-logo.svg.png" alt="Vans Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Reebok_complete_logo_red.svg/640px-Reebok_complete_logo_red.svg.png" alt="Reebok Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Puma-logo-%28text%29.svg/640px-Puma-logo-%28text%29.svg.png" alt="Puma Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/New_Balance_logo.svg/640px-New_Balance_logo.svg.png" alt="New Balance Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Converse_logo.svg/640px-Converse_logo.svg.png" alt="Converse Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png" alt="Nike Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Adidas_logo.png/800px-Adidas_logo.png" alt="Adidas Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Vans-logo.svg/640px-Vans-logo.svg.png" alt="Vans Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/Reebok_complete_logo_red.svg/640px-Reebok_complete_logo_red.svg.png" alt="Reebok Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Puma-logo-%28text%29.svg/640px-Puma-logo-%28text%29.svg.png" alt="Puma Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ea/New_Balance_logo.svg/640px-New_Balance_logo.svg.png" alt="New Balance Logo" /></div>
        <div class="brand-logo"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Converse_logo.svg/640px-Converse_logo.svg.png" alt="Converse Logo" /></div>
      </div>
    </div>
  </section>

  <section id="contact1" class="contact-section py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="display-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">KONTAK KAMI</h2>
        <p>Hubungi kami untuk informasi lebih lanjut</p>
      </div>
      <div class="row">
        <div class="col-md-5">
          <h3 class="mb-4">Informasi Kontak</h3>
          <div class="contact-info mb-4">
            <div class="d-flex mb-3">
              <i class="fas fa-map-marker-alt fa-fw me-3 mt-1"></i>
              <div>
                <h5>Alamat Toko</h5>
                <p><?php echo nl2br(htmlspecialchars($alamat_toko_kontak)); ?><br />Buka setiap hari: 10:00 - 21:00 WIB</p>
              </div>
            </div>
            <div class="d-flex mb-3">
              <i class="fas fa-envelope fa-fw me-3 mt-1"></i>
              <div>
                <h5>Email</h5>
                <p><a href="mailto:<?php echo htmlspecialchars($email_kontak_toko); ?>"><?php echo htmlspecialchars($email_kontak_toko); ?></a></p>
              </div>
            </div>
            <div class="d-flex mb-3">
              <i class="fab fa-whatsapp fa-fw me-3 mt-1"></i>
              <div>
                <h5>Telepon/WhatsApp</h5>
                <p><a href="tel:<?php echo htmlspecialchars(str_replace([' ', '-'], '', $telepon_toko_kontak)); ?>"><?php echo htmlspecialchars($telepon_toko_kontak); ?></a></p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7">
          <div class="contact-form p-4 bg-white rounded shadow">
            <h4>Kirim Pesan Langsung</h4>
            <?php if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true): ?>
              <div class="alert alert-warning mb-0">Silakan <a href="login_pelanggan.php">login</a> untuk mengirim pesan.</div>
            <?php else: ?>
              <form action="proses_pesan_kontak.php" method="POST">
                <div class="row mb-3">
                  <div class="col-md-6 mb-3 mb-md-0">
                    <label for="contact_name_form" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="contact_name_form" name="contact_name" placeholder="Masukkan nama Anda" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required />
                  </div>
                  <div class="col-md-6">
                    <label for="contact_email_form" class="form-label">Email</label>
                    <input type="email" class="form-control" id="contact_email_form" name="contact_email" placeholder="Masukkan email Anda" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required />
                  </div>
                </div>
                <div class="mb-3">
                  <label for="contact_subject_form" class="form-label">Subjek</label>
                  <input type="text" class="form-control" id="contact_subject_form" name="contact_subject" placeholder="Subjek pesan" required />
                </div>
                <div class="mb-3">
                  <label for="contact_message_form" class="form-label">Pesan</label>
                  <textarea class="form-control" id="contact_message_form" name="contact_message" rows="4" placeholder="Tulis pesan Anda di sini" required></textarea>
                </div>
                <button type="submit" class="btn btn-dark px-4 py-2">Kirim Pesan</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer class="bg-dark text-white py-5 mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
          <h4>Tentang <?php echo htmlspecialchars($nama_toko); ?></h4>
          <p class="mt-3"><?php echo htmlspecialchars($deskripsi_toko_meta); ?></p>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
          <h4>Links</h4>
          <ul class="list-unstyled mt-3">
            <li class="mb-2"><a href="index.php" class="text-white text-decoration-none">Home</a></li>
            <li class="mb-2"><a href="about.php" class="text-white text-decoration-none">About</a></li>
            <li class="mb-2"><a href="produk.php" class="text-white text-decoration-none">Products</a></li>
            <li class="mb-2"><a href="index.php#contact1" class="text-white text-decoration-none">Contact</a></li>
          </ul>
        </div>
        <div class="col-md-4">
          <h4>Kontak Kami</h4>
          <ul class="list-unstyled mt-3">
            <li class="mb-2 d-flex"><i class="fas fa-map-marker-alt fa-fw me-2 mt-1"></i><span><?php echo nl2br(htmlspecialchars($alamat_toko_kontak)); ?></span></li>
            <li class="mb-2 d-flex"><i class="fas fa-envelope fa-fw me-2 mt-1"></i><a href="mailto:<?php echo htmlspecialchars($email_kontak_toko); ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($email_kontak_toko); ?></a></li>
            <li class="d-flex"><i class="fab fa-whatsapp fa-fw me-2 mt-1"></i><a href="tel:<?php echo htmlspecialchars(str_replace([' ', '-'], '', $telepon_toko_kontak)); ?>" class="text-white text-decoration-none"><?php echo htmlspecialchars($telepon_toko_kontak); ?></a></li>
          </ul>
        </div>
      </div>
      <hr class="my-4" />
      <div class="text-center">
        <p class="mb-0">© <?php echo date("Y"); ?> <?php echo htmlspecialchars($nama_toko); ?>. All rights reserved.</p>
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
<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<?php
$brand_logos = [
  'adidas' => BASE_URL . 'admin/Assets/adidas-logo-png_seeklogo-263852.png',
  'nike' => BASE_URL . 'admin/Assets/nike-logo-png_seeklogo-99478.png',
  'air jordan' => BASE_URL . 'admin/Assets/air-jordan-logo-png_seeklogo-380953.png',
  'new balance' => BASE_URL . 'admin/Assets/new-balance-logo-png_seeklogo-260827.png',
  'puma' => BASE_URL . 'admin/Assets/puma-logo-png_seeklogo-304000.png',
  'vans' => BASE_URL . 'admin/Assets/vans-logo-png_seeklogo-147507.png',
  'converse' => BASE_URL . 'admin/Assets/converse-logo-png_seeklogo-35061.png',
  'salomon' => BASE_URL . 'admin/Assets/salomon-logo-png_seeklogo-284093.png',
  'hoka' => BASE_URL . 'admin/Assets/hoka-logo-png_seeklogo-405619.png'
];
?>

<section class="hero-section" style="margin-top: 80px;" data-aos="fade-in">
    <div class="container">
        <div class="row align-items-center">
        <div class="col-md-6" data-aos="fade-right" data-aos-delay="200">
            <h1 class="display-3 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui; background: -webkit-linear-gradient(45deg, #0d6efd, #0dcaf0); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">SHOES CULTURE FOR PEOPLE CULTURE</h1>
            <p class="lead text-muted mt-3 mb-4">Temukan koleksi sepatu terbaik yang mendefinisikan gaya dan kenyamanan Anda setiap hari.</p>
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Jelajahi Sekarang</a>
        </div>
        <div class="col-md-6" data-aos="zoom-in" data-aos-delay="400">
            <img src="<?= BASE_URL ?>admin/Assets/hoka.jpg" alt="Contoh Sepatu" class="img-fluid rounded-4 shadow-lg" style="transform: perspective(1000px) rotateY(-15deg); transition: transform 0.5s ease;" onmouseover="this.style.transform='perspective(1000px) rotateY(0deg)'" onmouseout="this.style.transform='perspective(1000px) rotateY(-15deg)'"/>
        </div>
        </div>
    </div>
</section>

<!-- Carousel Section -->
<div id="heroCarousel" class="carousel slide mt-5" data-bs-ride="carousel" data-aos="fade-up">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner rounded-4 shadow-lg mx-auto" style="width: 95%; overflow: hidden;">
        <div class="carousel-item active">
        <img src="<?= BASE_URL ?>admin/Assets/1.png" class="d-block w-100 carousel-img" alt="Carousel 1">
        <div class="carousel-caption d-flex flex-column justify-content-center h-100 align-items-center">
            <h1 class="display-3 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;" data-aos="zoom-in" data-aos-delay="200">TRENDSETTER</h1>
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-light btn-lg rounded-pill px-5 shadow">Jelajahi</a>
        </div>
        </div>
        <div class="carousel-item">
        <img src="<?= BASE_URL ?>admin/Assets/2.png" class="d-block w-100 carousel-img" alt="Carousel 2">
        <div class="carousel-caption d-flex flex-column justify-content-center h-100 align-items-center">
            <h2 class="display-4 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">KOLEKSI TERBARU</h2>
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-light btn-lg rounded-pill px-5 shadow">Lihat Koleksi</a>
        </div>
        </div>
        <div class="carousel-item">
        <img src="<?= BASE_URL ?>admin/Assets/3.png" class="d-block w-100 carousel-img" alt="Carousel 3">
        <div class="carousel-caption d-flex flex-column justify-content-center h-100 align-items-center">
            <h2 class="display-4 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">PENAWARAN SPESIAL</h2>
            <a href="<?= BASE_URL ?>index.php?url=Home/promo" class="btn btn-warning btn-lg rounded-pill px-5 shadow">Klaim Promo</a>
        </div>
        </div>
    </div>
</div>

<section class="features-section py-5 mt-5 bg-dark text-white rounded-top-5">
    <div class="container">
        <div class="row" data-aos="fade-up">
        <div class="col-12 text-center mb-5">
            <h2 class="display-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui; background: -webkit-linear-gradient(45deg, #fff, #888); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">BEST FEATURE<br />IN SHOES</h2>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6" data-aos="fade-right" data-aos-delay="200">
            <div class="row">
            <div class="col-11 mx-auto mb-1 position-relative">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle, rgba(13,110,253,0.2) 0%, rgba(0,0,0,0) 70%);"></div>
                <img src="<?= BASE_URL ?>admin/Assets/cs-removebg-preview.png" alt="Fitur" class="img-fluid rounded" style="filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5));" />
            </div>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-center" data-aos="fade-left" data-aos-delay="400">
            <div class="feature-item mb-4 p-4 rounded-4" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: transform 0.3s;" onmouseover="this.style.transform='translateX(-10px)'" onmouseout="this.style.transform='translateX(0)'">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-dollar-sign feature-icon me-3 text-info"></i>
                <h3 class="mb-0">Harga Berkualitas</h3>
            </div>
            <p class="text-white-50 mb-0 mt-2">Dapatkan sepatu branded dengan harga yang terjangkau dan bersaing di pasaran tanpa mengorbankan kualitas.</p>
            </div>
            <div class="feature-item mb-4 p-4 rounded-4" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: transform 0.3s;" onmouseover="this.style.transform='translateX(-10px)'" onmouseout="this.style.transform='translateX(0)'">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-headset feature-icon me-3 text-info"></i>
                <h3 class="mb-0">Pelayanan Terbaik</h3>
            </div>
            <p class="text-white-50 mb-0 mt-2">Tim customer service kami siap melayani Anda dengan respon cepat. Kami berkomitmen untuk kepuasan Anda.</p>
            </div>
            <div class="feature-item p-4 rounded-4" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); transition: transform 0.3s;" onmouseover="this.style.transform='translateX(-10px)'" onmouseout="this.style.transform='translateX(0)'">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-medal feature-icon me-3 text-info"></i>
                <h3 class="mb-0">Kualitas Terjamin</h3>
            </div>
            <p class="text-white-50 mb-0 mt-2">Semua produk kami melewati quality control yang ketat sebelum dikirim ke pelanggan.</p>
            </div>
        </div>
        </div>
    </div>
</section>

<section class="best-shoes-section py-5 bg-dark text-white pb-5 rounded-bottom-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
        <div class="col-md-5" data-aos="fade-right">
            <h2 class="display-5 fw-bold mb-4" style="font-family:'Special Gothic Expanded One', system-ui;">THE BEST SHOES<br />FOR THE CULTURE<br />PEOPLE</h2>
            <p class="mb-4 text-white-50">Kami menyediakan berbagai pilihan sepatu casual berkualitas tinggi untuk pria dan wanita. Dengan desain yang trendy dan kekinian, cocok untuk aktivitas sehari-hari.</p>
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-outline-info rounded-pill px-4 py-2">Lihat Semua Koleksi</a>
        </div>
        <div class="col-md-7 mt-5 mt-md-0">
            <div class="row">
            <?php if (!empty($best_shoes_products)): ?>
                <?php foreach ($best_shoes_products as $index => $product): ?>
                <div class="col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 200 ?>">
                    <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-white">
                    <div class="product-card card h-100 shadow-lg bg-dark border-0 rounded-4 overflow-hidden" style="box-shadow: 0 15px 35px rgba(0,0,0,0.5) !important;">
                        <?php
                        $image_name_best = htmlspecialchars($product['image'] ?? '');
                        $is_url_best = filter_var($image_name_best, FILTER_VALIDATE_URL);
                        $image_url_best = $is_url_best ? $image_name_best : BASE_URL . "admin/uploads/produk/" . $image_name_best;
                        $placeholder_url_best = BASE_URL . "admin/placeholder_image.png";
                        ?>
                        <div style="overflow: hidden;">
                            <img src="<?= !empty($image_name_best) ? $image_url_best : $placeholder_url_best; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                        <div class="card-body d-flex flex-column text-start p-4" style="background: linear-gradient(180deg, transparent, rgba(0,0,0,0.8));">
                            <h5 class="card-title product-title-truncate text-light mb-3" style="min-height: 2.5em; font-size: 1.1rem; font-weight: 600;"><?= htmlspecialchars($product['name']); ?></h5>
                            <div class="rating-display mb-3">
                                <?php
                                $rating_best = $product['average_rating'] ?? 0;
                                for ($i = 1; $i <= 5; $i++) {
                                if ($rating_best >= $i) echo '<i class="fas fa-star text-warning"></i>';
                                elseif ($rating_best > ($i - 1) && $rating_best < $i) echo '<i class="fas fa-star-half-alt text-warning"></i>';
                                else echo '<i class="far fa-star text-secondary"></i>';
                                }
                                ?>
                                <span class="text-white-50 small ms-1">(<?= $product['rating_count'] ?? 0; ?>)</span>
                            </div>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-info mb-0 small text-uppercase fw-bold">Start From</p>
                                    <?php 
                                    $sekarang = date('Y-m-d');
                                    $is_discount = false;
                                    if (!empty($product['discount_percent']) && $product['discount_percent'] > 0 && !empty($product['discount_start_date']) && !empty($product['discount_end_date'])) {
                                        if ($sekarang >= $product['discount_start_date'] && $sekarang <= $product['discount_end_date']) {
                                            $is_discount = true;
                                        }
                                    }
                                    if ($is_discount): 
                                        $harga_diskon = $product['price'] - ($product['price'] * $product['discount_percent'] / 100);
                                    ?>
                                        <p class="fw-bold fs-5 mb-0 text-white">Rp <?= number_format($harga_diskon, 0, ',', '.') ?></p>
                                        <small class="text-white-50 text-decoration-line-through">Rp <?= number_format($product['price'], 0, ',', '.') ?></small>
                                    <?php else: ?>
                                        <p class="fw-bold fs-5 mb-0 text-white">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="btn btn-sm btn-light rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-arrow-right"></i></div>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12"><p class="text-center">Produk terbaik akan segera hadir.</p></div>
            <?php endif; ?>
            </div>
        </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="feature-icon mx-auto mb-3"><i class="fas fa-truck-fast fa-2x text-primary"></i></div>
            <h5 class="fw-bold">Pengiriman Cepat</h5>
            <p class="text-muted small mb-0">Pengiriman ke seluruh Indonesia dalam 1-3 hari kerja.</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="feature-icon mx-auto mb-3"><i class="fas fa-shield-alt fa-2x text-primary"></i></div>
            <h5 class="fw-bold">Garansi Produk</h5>
            <p class="text-muted small mb-0">Garansi 30 hari untuk semua produk yang kami jual.</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="300">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="feature-icon mx-auto mb-3"><i class="fas fa-credit-card fa-2x text-primary"></i></div>
            <h5 class="fw-bold">Pembayaran Aman</h5>
            <p class="text-muted small mb-0">Berbagai metode pembayaran yang aman dan terpercaya.</p>
            </div>
        </div>
        <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
            <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div class="feature-icon mx-auto mb-3"><i class="fas fa-headset fa-2x text-primary"></i></div>
            <h5 class="fw-bold">Layanan 24/7</h5>
            <p class="text-muted small mb-0">Tim customer service kami siap membantu 24/7.</p>
            </div>
        </div>
        </div>
    </div>
</section>

<section id="product1" class="recommendation-section py-5 bg-light mt-4 rounded-top-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
        <h2 class="display-6 fw-bold mb-0" style="font-family: 'Special Gothic Expanded One', system-ui;">RECOMMENDATION</h2>
        <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-outline-primary rounded-pill px-4">SEE ALL <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row">
        <?php if (!empty($recommended_products)): ?>
            <?php foreach ($recommended_products as $index => $product): ?>
            <div class="col-6 col-md-4 col-lg-3 mb-4" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
                <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-dark">
                <div class="product-card card h-100 border-0 rounded-4 overflow-hidden" style="box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: all 0.3s ease;">
                    <?php
                    $image_name = htmlspecialchars($product['image'] ?? '');
                    $is_url = filter_var($image_name, FILTER_VALIDATE_URL);
                    $image_url = $is_url ? $image_name : BASE_URL . "admin/uploads/produk/" . $image_name;
                    $placeholder_url = BASE_URL . "admin/placeholder_image.png";
                    ?>
                    <div style="overflow: hidden;">
                        <img src="<?= !empty($image_name) ? $image_url : $placeholder_url; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="card-img-top" style="aspect-ratio: 1 / 1; object-fit: cover; transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform='scale(1)'">
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title product-title-truncate mb-3" style="min-height: 2.5em; font-size: 1rem; font-weight: 600;"><?= htmlspecialchars($product['name']); ?></h5>
                        <p class="text-primary mb-1 small text-uppercase fw-bold">Start From</p>
                        <?php 
                        $sekarang = date('Y-m-d');
                        $is_discount = false;
                        if (!empty($product['discount_percent']) && $product['discount_percent'] > 0 && !empty($product['discount_start_date']) && !empty($product['discount_end_date'])) {
                            if ($sekarang >= $product['discount_start_date'] && $sekarang <= $product['discount_end_date']) {
                                $is_discount = true;
                            }
                        }
                        if ($is_discount): 
                            $harga_diskon = $product['price'] - ($product['price'] * $product['discount_percent'] / 100);
                        ?>
                            <p class="fw-bold fs-5 mb-0 mt-auto">
                                <span class="text-danger">Rp <?= number_format($harga_diskon, 0, ',', '.') ?></span> 
                                <br><small class="text-muted text-decoration-line-through" style="font-size: 0.8rem;">Rp <?= number_format($product['price'], 0, ',', '.') ?></small>
                                <span class="badge bg-danger ms-1">-<?= $product['discount_percent'] ?>%</span>
                            </p>
                        <?php else: ?>
                            <p class="fw-bold fs-5 mb-0 mt-auto">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                </a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12"><p class="text-center">Belum ada produk untuk direkomendasikan saat ini.</p></div>
        <?php endif; ?>
        </div>
    </div>
</section>

<!-- All Brands Section -->
<section id="all-brands" class="all-brands-section py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-6 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">ALL BRANDS AVAILABLE</h2>
        <p class="lead text-muted">Jelajahi produk berdasarkan merek favorit Anda.</p>
        </div>
        <div class="row g-4 justify-content-center">
        <?php if (!empty($brands)): ?>
            <?php foreach ($brands as $index => $brand_name): ?>
            <?php
            $brand_key = strtolower($brand_name);
            if (array_key_exists($brand_key, $brand_logos)):
                $logo_url = $brand_logos[$brand_key];
            ?>
                <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= ($index % 4) * 100 ?>">
                <a href="<?= BASE_URL ?>index.php?url=Product/index&keyword_pencarian=<?= urlencode($brand_name); ?>" class="brand-item-link">
                    <div class="card brand-item-card h-100 border-0 rounded-4 bg-light" style="box-shadow: 0 5px 15px rgba(0,0,0,0.03);">
                    <div class="card-body d-flex align-items-center justify-content-center" style="height:100px;">
                        <img src="<?= htmlspecialchars($logo_url); ?>" alt="Logo <?= htmlspecialchars($brand_name); ?>" class="img-fluid" style="max-height:50px; object-fit:contain; filter: grayscale(100%); transition: all 0.3s;" onmouseover="this.style.filter='grayscale(0%)'; this.style.transform='scale(1.1)'" onmouseout="this.style.filter='grayscale(100%)'; this.style.transform='scale(1)'">
                    </div>
                    </div>
                </a>
                </div>
            <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
    </div>
</section>

<section id="about" class="about-section py-4 border-top border-bottom">
    <div class="brand-bar" style="background: transparent;">
        <div class="brand-carousel">
            <?php foreach($brand_logos as $brand_name => $logo_url): ?>
                <div class="brand-logo mx-5"><img src="<?= $logo_url ?>" alt="<?= $brand_name ?> Logo" style="height: 40px; filter: grayscale(100%) opacity(50%);" /></div>
            <?php endforeach; ?>
            <?php foreach($brand_logos as $brand_name => $logo_url): ?>
                <div class="brand-logo mx-5"><img src="<?= $logo_url ?>" alt="<?= $brand_name ?> Logo" style="height: 40px; filter: grayscale(100%) opacity(50%);" /></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="about1" class="about-section py-5 bg-light">
    <div class="container my-4" data-aos="fade-up">
      <div class="row align-items-center bg-white p-5 rounded-5 shadow-sm">
        <div class="col-md-6">
          <h2 class="display-5 fw-bold mb-4" style="font-family: 'Special Gothic Expanded One', system-ui; background: -webkit-linear-gradient(45deg, #212529, #6c757d); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">CASUAL STEPS</h2>
          <p class="mb-4 text-muted" style="font-size: 1.1rem; line-height: 1.8;">Pusat Sepatu Casual Kekinian dan Berkualitas.</p>
          <p class="mb-4 text-muted" style="line-height: 1.8;">Didirikan pada tahun 2020, Casual Steps telah menjadi salah satu toko sepatu casual favorit anak muda di Yogyakarta. Kami terus berinovasi untuk memberikan pengalaman berbelanja yang menyenangkan bagi pelanggan kami.</p>
          <a href="<?= BASE_URL ?>index.php?url=Home/about" class="btn btn-outline-dark rounded-pill px-4 py-2 mt-2">Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
        <div class="col-md-6 mt-5 mt-md-0 text-center">
          <img src="<?= BASE_URL ?>admin/Assets/brand-logo.png" alt="Tentang Casual Steps" class="img-fluid rounded-circle shadow" style="max-width: 80%;" />
        </div>
      </div>
    </div>
</section>

<section id="contact1" class="contact-section py-5 bg-white">
    <div class="container my-4">
      <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-6 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">KONTAK KAMI</h2>
        <p class="text-muted">Hubungi kami untuk informasi lebih lanjut</p>
      </div>
      <div class="row">
        <div class="col-md-5" data-aos="fade-right">
          <h4 class="mb-4 fw-bold">Informasi Kontak</h4>
          <div class="contact-info mb-4">
            <div class="d-flex mb-4 align-items-center p-3 bg-light rounded-4">
              <div class="bg-white p-3 rounded-circle shadow-sm me-4 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                <i class="fas fa-map-marker-alt text-primary fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Alamat Toko</h6>
                <p class="text-muted mb-0 small">Yogyakarta, Indonesia<br />10:00 - 21:00 WIB</p>
              </div>
            </div>
            <div class="d-flex mb-4 align-items-center p-3 bg-light rounded-4">
              <div class="bg-white p-3 rounded-circle shadow-sm me-4 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                <i class="fas fa-envelope text-primary fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Email</h6>
                <p class="mb-0 small"><a href="mailto:kontak@casualsteps.com" class="text-muted text-decoration-none">kontak@casualsteps.com</a></p>
              </div>
            </div>
            <div class="d-flex mb-4 align-items-center p-3 bg-light rounded-4">
              <div class="bg-white p-3 rounded-circle shadow-sm me-4 d-flex justify-content-center align-items-center" style="width: 60px; height: 60px;">
                <i class="fab fa-whatsapp text-primary fs-4"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Telepon/WhatsApp</h6>
                <p class="mb-0 small"><a href="tel:081234567890" class="text-muted text-decoration-none">0812-3456-7890</a></p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-7" data-aos="fade-left">
          <div class="contact-form p-5 bg-white rounded-5 shadow-lg border-0" style="position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(13,110,253,0.1); border-radius: 50%;"></div>
            <h4 class="fw-bold mb-4">Kirim Pesan Langsung</h4>
            <?php if (isset($_SESSION['contact_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['contact_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['contact_success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['contact_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_SESSION['contact_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['contact_error']); ?>
            <?php endif; ?>
            
            <?php if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true): ?>
              <div class="alert alert-warning mb-0 rounded-3"><i class="fas fa-lock me-2"></i> Silakan <a href="<?= BASE_URL ?>index.php?url=Auth/login" class="fw-bold text-dark">login</a> untuk mengirim pesan.</div>
            <?php else: ?>
              <form action="<?= BASE_URL ?>index.php?url=Home/sendMessage" method="POST">
                <div class="row mb-4">
                  <div class="col-md-6 mb-4 mb-md-0">
                    <label for="contact_name_form" class="form-label small fw-bold text-muted">Nama Lengkap</label>
                    <input type="text" class="form-control form-control-lg bg-light border-0 fs-6" id="contact_name_form" name="contact_name" placeholder="John Doe" value="<?= htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" required />
                  </div>
                  <div class="col-md-6">
                    <label for="contact_email_form" class="form-label small fw-bold text-muted">Alamat Email</label>
                    <input type="email" class="form-control form-control-lg bg-light border-0 fs-6" id="contact_email_form" name="contact_email" placeholder="john@example.com" value="<?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" required />
                  </div>
                </div>
                <div class="mb-4">
                  <label for="contact_subject_form" class="form-label small fw-bold text-muted">Subjek Pesan</label>
                  <input type="text" class="form-control form-control-lg bg-light border-0 fs-6" id="contact_subject_form" name="contact_subject" placeholder="Tanya stok produk..." required />
                </div>
                <div class="mb-4">
                  <label for="contact_message_form" class="form-label small fw-bold text-muted">Isi Pesan</label>
                  <textarea class="form-control form-control-lg bg-light border-0 fs-6" id="contact_message_form" name="contact_message" rows="4" placeholder="Tulis pesan Anda dengan detail di sini..." required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Kirim Pesan <i class="fas fa-paper-plane ms-2"></i></button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
</section>


<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

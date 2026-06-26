<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .product-title-truncate {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 2.5em;
        font-size: 1rem;
        font-weight: 600;
    }
    .product-card img { aspect-ratio: 1 / 1; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover img { transform: scale(1.08); }
    .product-card { transition: all 0.3s ease; border-radius: 1rem; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .product-image-container { position: relative; overflow: hidden; }
    .sale-badge { position: absolute; top: 15px; left: 15px; background: linear-gradient(45deg, #dc3545, #ff4757); color: white; z-index: 10; font-weight: bold; padding: 5px 12px; border-radius: 20px; box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4); }
</style>

<?php 
    // Tentukan background hero. Jika ada promo_banner_image, gunakan itu
    $hero_bg_style = "background: linear-gradient(135deg, rgba(220,53,69,0.1), rgba(255,193,7,0.1));";
    if (!empty($global_settings['promo_banner_image'])) {
        $banner_url = BASE_URL . "admin/uploads/banners/" . htmlspecialchars($global_settings['promo_banner_image']);
        $hero_bg_style = "background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)), url('{$banner_url}') no-repeat center center; background-size: cover;";
    }
?>
<section class="promo-hero position-relative d-flex align-items-center justify-content-center" style="margin-top: 70px; min-height: 40vh; <?= $hero_bg_style ?> overflow: hidden;" data-aos="fade-in">
    <div style="position: absolute; top: -50px; right: 10%; width: 200px; height: 200px; background: rgba(220,53,69,0.15); border-radius: 50%; filter: blur(40px);"></div>
    <div style="position: absolute; bottom: -50px; left: 10%; width: 200px; height: 200px; background: rgba(255,193,7,0.15); border-radius: 50%; filter: blur(40px);"></div>
    <div class="container text-center py-5 z-1">
        <?php if (!empty($global_settings['promo_banner_image'])): ?>
            <h1 class="display-3 fw-bold mb-3 text-white text-shadow" style="font-family: 'Special Gothic Expanded One', system-ui;" data-aos="zoom-in" data-aos-delay="200">HOT PROMO SALE</h1>
            <p class="lead col-lg-8 mx-auto text-light fw-light text-shadow" style="font-size: 1.25rem;" data-aos="fade-up" data-aos-delay="400">Dapatkan penawaran terbaik dan diskon menarik untuk koleksi pilihan kami.</p>
        <?php else: ?>
            <h1 class="display-3 fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui; background: -webkit-linear-gradient(45deg, #dc3545, #fd7e14); -webkit-background-clip: text; -webkit-text-fill-color: transparent;" data-aos="zoom-in" data-aos-delay="200">HOT PROMO SALE</h1>
            <p class="lead text-muted col-lg-8 mx-auto" style="font-size: 1.25rem;" data-aos="fade-up" data-aos-delay="400">Dapatkan penawaran terbaik dan diskon menarik untuk koleksi pilihan kami.</p>
        <?php endif; ?>
    </div>
</section>

<main class="container py-5 my-3">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-right">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">Produk Sedang Diskon</h2>
        <span class="badge bg-danger text-white px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-fire me-1"></i> <?= count($promo_products); ?> Item</span>
    </div>

    <div class="row g-4">
        <?php if (!empty($promo_products)): ?>
            <?php foreach ($promo_products as $index => $product): ?>
                <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
                    <div class="product-card card h-100 bg-white">
                        <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-dark d-block h-100 d-flex flex-column">
                            <div class="product-image-container">
                                <?php
                                echo '<span class="badge sale-badge"><i class="fas fa-bolt me-1"></i> ' . htmlspecialchars($product['discount_percent']) . '% OFF</span>';
                                $image_name = htmlspecialchars($product['image'] ?? '');
                                $is_url = filter_var($image_name, FILTER_VALIDATE_URL);
                                $image_url = $is_url ? $image_name : BASE_URL . "admin/uploads/produk/" . $image_name;
                                $placeholder_url = BASE_URL . "admin/placeholder_image.png";
                                ?>
                                <img src="<?= !empty($image_name) ? $image_url : $placeholder_url; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="card-img-top">
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <h5 class="card-title product-title-truncate mb-3"><?= htmlspecialchars($product['name']); ?></h5>
                                <div class="mt-auto pt-3 border-top">
                                    <?php
                                    $harga_asli = $product['price'];
                                    $persen_diskon = $product['discount_percent'];
                                    $harga_diskon = $harga_asli - ($harga_asli * $persen_diskon / 100);
                                    ?>
                                    <p class="card-text small text-muted mb-1 text-decoration-line-through">Rp <?= number_format($harga_asli, 0, ',', '.'); ?></p>
                                    <p class="fw-bold fs-5 card-text text-danger mb-0">Rp <?= number_format($harga_diskon, 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12" data-aos="fade-in">
                <div class="text-center py-5 bg-white rounded-5 shadow-sm border p-5 mx-auto" style="max-width: 600px;">
                    <div class="mb-4">
                        <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-tags fa-3x text-muted"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-3">Belum Ada Promo Aktif</h3>
                    <p class="text-muted mb-4">Saat ini belum ada produk yang sedang dipromosikan. Tetap pantau halaman ini untuk mendapatkan penawaran spesial berikutnya!</p>
                    <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary btn-lg rounded-pill px-5 shadow" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Lihat Semua Produk</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

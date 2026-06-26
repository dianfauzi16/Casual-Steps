<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

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
        font-size: 1rem;
        font-weight: 600;
    }
    .product-card img { aspect-ratio: 1 / 1; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover img { transform: scale(1.08); }
    .product-card { transition: all 0.3s ease; border-radius: 1rem; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .category-filters .list-group-item { border: none; padding: 12px 20px; transition: all 0.2s; border-radius: 0.5rem !important; margin-bottom: 5px; }
    .category-filters .list-group-item:hover { background-color: #f8f9fa; transform: translateX(5px); }
    .category-filters .list-group-item.active { background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: white; border: none; box-shadow: 0 4px 10px rgba(13,110,253,0.3); }
    .category-filters .list-group-item a { text-decoration: none; color: inherit; display: block; }
    .product-card .product-image-container { position: relative; overflow: hidden; }
    .sale-badge { position: absolute; top: 15px; left: 15px; background: linear-gradient(45deg, #dc3545, #ff4757); color: white; z-index: 10; font-weight: bold; padding: 5px 12px; border-radius: 20px; box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4); }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-3 category-filters mb-5" data-aos="fade-right">
            <h4 class="mb-4 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">KATEGORI PRODUK</h4>
            <div class="list-group shadow-sm bg-white p-3 rounded-4">
                <a href="<?= BASE_URL ?>index.php?url=Product/index<?php if (!empty($keyword)) echo '&keyword_pencarian=' . urlencode($keyword); ?>"
                    class="list-group-item list-group-item-action <?= ($kategori_id === null) ? 'active fw-bold' : 'text-muted'; ?>">
                    <i class="fas fa-th-large me-2"></i> Semua Kategori
                </a>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?= BASE_URL ?>index.php?url=Product/index&kategori=<?= $cat['id_kategori']; ?><?php if (!empty($keyword)) echo '&keyword_pencarian=' . urlencode($keyword); ?>"
                            class="list-group-item list-group-item-action <?= ($kategori_id == $cat['id_kategori']) ? 'active fw-bold' : 'text-muted'; ?>">
                            <i class="fas fa-tag me-2"></i> <?= htmlspecialchars($cat['nama_kategori']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="list-group-item text-muted small border-0">Tidak ada kategori tersedia.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" data-aos="fade-left">
                <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm">Menampilkan <?= count($products); ?> produk</span>
            </div>

            <?php if (!empty($keyword) && empty($products)): ?>
                <div class="alert alert-info rounded-4 border-0 shadow-sm" data-aos="fade-up">
                    <i class="fas fa-info-circle me-2"></i> Tidak ada produk yang cocok dengan kata kunci pencarian "<strong><?= htmlspecialchars($keyword); ?></strong>"
                    <?php if ($current_category_name) echo " dalam kategori \"" . htmlspecialchars($current_category_name) . "\""; ?>.
                    Coba kata kunci lain atau <a href="<?= BASE_URL ?>index.php?url=Product/index<?= $kategori_id ? '&kategori=' . $kategori_id : ''; ?>" class="alert-link">lihat semua produk <?= $current_category_name ? "dalam kategori ini" : ""; ?></a>.
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <div class="col-6 col-sm-6 col-md-4" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 ?>">
                            <div class="product-card card h-100 bg-white">
                                <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-dark d-block h-100 d-flex flex-column">
                                    <div class="product-image-container">
                                        <?php
                                        $punya_diskon = !empty($product['discount_percent']) && $product['discount_percent'] > 0;
                                        $sekarang = date('Y-m-d');
                                        $sedang_diskon = $punya_diskon &&
                                            (empty($product['discount_start_date']) || $sekarang >= $product['discount_start_date']) &&
                                            (empty($product['discount_end_date'])   || $sekarang <= $product['discount_end_date']);

                                        if ($sedang_diskon) {
                                            echo '<span class="badge sale-badge"><i class="fas fa-fire me-1"></i> ' . htmlspecialchars($product['discount_percent']) . '% OFF</span>';
                                        }

                                        $image_name = htmlspecialchars($product['image'] ?? '');
                                        $is_url = filter_var($image_name, FILTER_VALIDATE_URL);
                                        $image_url = $is_url ? $image_name : BASE_URL . "admin/uploads/produk/" . $image_name;
                                        $placeholder_url = BASE_URL . "admin/placeholder_image.png";

                                        if (!empty($image_name)): ?>
                                            <img src="<?= $image_url; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="card-img-top" onerror="this.onerror=null; this.src='<?= $placeholder_url ?>'">
                                        <?php else: ?>
                                            <img src="<?= $placeholder_url; ?>" alt="Gambar tidak tersedia" class="card-img-top">
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-body d-flex flex-column p-4">
                                        <h5 class="card-title product-title-truncate mb-2"><?= htmlspecialchars($product['name']); ?></h5>
                                        <?php if (!empty($product['nama_kategori_produk'])): ?>
                                            <p class="card-text small text-info fw-bold mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?= htmlspecialchars($product['nama_kategori_produk']); ?></p>
                                        <?php endif; ?>
                                        <div class="mt-auto pt-3 border-top">
                                            <?php if ($sedang_diskon):
                                                $harga_diskon = $product['price'] * (1 - ($product['discount_percent'] / 100));
                                            ?>
                                                <p class="card-text small text-muted mb-1 text-decoration-line-through">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                                <p class="fw-bold fs-5 card-text text-danger mb-0">Rp <?= number_format($harga_diskon, 0, ',', '.'); ?></p>
                                            <?php else: ?>
                                                <p class="card-text small text-muted mb-1">Start From</p>
                                                <p class="fw-bold fs-5 card-text mb-0 text-dark">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (empty($keyword)): ?>
                    <div class="col-12" data-aos="fade-in">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <p class="text-muted fs-5 mb-0">Tidak ada produk yang ditemukan untuk kriteria ini.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

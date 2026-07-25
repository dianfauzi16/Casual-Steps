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
    .product-card img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; transition: transform 0.5s ease; }
    .product-card:hover img { transform: scale(1.08); }
    .product-card { transition: all 0.3s ease; border-radius: 1rem; overflow: hidden; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
    .product-card .product-image-container { position: relative; overflow: hidden; width: 100%; }
    .sale-badge { position: absolute; top: 15px; left: 15px; background: linear-gradient(45deg, #dc3545, #ff4757); color: white; z-index: 10; font-weight: bold; padding: 5px 12px; border-radius: 20px; box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4); }
</style>

<main class="container py-5" style="margin-top: 100px; min-height: 70vh;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" data-aos="fade-up">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;">WISHLIST SAYA</h2>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm"><?= count($products); ?> produk</span>
    </div>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $index => $product): ?>
                <div class="col-6 col-sm-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>" id="wishlist-item-<?= $product['id'] ?>">
                    <div class="product-card card h-100 bg-white">
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
                            ?>
                            
                            <button class="btn btn-light rounded-circle shadow-sm position-absolute" 
                                    style="top: 15px; right: 15px; z-index: 20; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
                                    onclick="removeWishlist(<?= $product['id'] ?>)">
                                <i class="fas fa-trash text-danger"></i>
                            </button>

                            <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-dark d-block">
                                <?php
                                $image_name = htmlspecialchars($product['image'] ?? '');
                                $is_url = filter_var($image_name, FILTER_VALIDATE_URL);
                                $image_url = $is_url ? \App\Core\CloudinaryHelper::optimizeUrl($image_name) : BASE_URL . "admin/uploads/produk/" . $image_name;
                                $placeholder_url = BASE_URL . "admin/placeholder_image.png";

                                if (!empty($image_name)): ?>
                                    <img src="<?= $image_url; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="card-img-top" onerror="this.onerror=null; this.src='<?= $placeholder_url ?>'">
                                <?php else: ?>
                                    <img src="<?= $placeholder_url; ?>" alt="Gambar tidak tersedia" class="card-img-top">
                                <?php endif; ?>
                            </a>
                        </div>

                        <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $product['id']; ?>" class="text-decoration-none text-dark d-block flex-grow-1 d-flex flex-column">
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
                                        <p class="card-text small text-muted mb-1">Harga</p>
                                        <p class="fw-bold fs-5 card-text mb-0 text-dark">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12" data-aos="fade-in">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border">
                    <i class="far fa-heart fa-4x text-muted mb-3"></i>
                    <h4 class="fw-bold text-secondary">Wishlist Kosong</h4>
                    <p class="text-muted fs-6 mb-4">Anda belum menambahkan produk apapun ke wishlist.</p>
                    <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm"><i class="fas fa-shopping-bag me-2"></i> Mulai Belanja</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function removeWishlist(productId) {
    if(confirm('Hapus produk dari wishlist?')) {
        fetch('<?= BASE_URL ?>index.php?url=Wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const item = document.getElementById('wishlist-item-' + productId);
                if (item) {
                    item.remove();
                }
                const counter = document.getElementById('wishlist-counter');
                if (counter) {
                    counter.innerText = data.count;
                    counter.style.display = data.count > 0 ? 'inline-block' : 'none';
                }
                if (data.count === 0) {
                    location.reload(); // Reload to show empty state
                }
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

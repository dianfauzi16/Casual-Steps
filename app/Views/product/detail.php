<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .product-detail-img { max-width: 100%; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-height: 500px; object-fit: contain; margin-bottom: 20px; }
    .product-info h1 { font-size: 2.2rem; font-weight: 700; margin-bottom: 0.5rem; }
    .product-info .brand { font-size: 0.9rem; color: #6c757d; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .product-info .category-info { font-size: 0.9rem; color: #6c757d; margin-bottom: 1rem; }
    .product-info .category-info a { color: #0d6efd; text-decoration: none; }
    .product-info .category-info a:hover { text-decoration: underline; }
    .product-info .price { font-size: 1.8rem; color: #000000; font-weight: 700; margin-bottom: 1.5rem; }
    .product-info .stock, .product-info .size-available-text { font-size: 0.95rem; margin-bottom: 0.75rem; }
    .product-info .stock .badge { font-size: 0.9rem; }
    .product-info .description { margin-top: 1.5rem; line-height: 1.7; color: #495057; }
    .add-to-cart-btn { padding: 0.65rem 1.25rem; font-size: 1rem; border-radius: 0.3rem; font-weight: 500; text-align: center; }
    .breadcrumb-item a { text-decoration: none; color: #0d6efd; }
    .breadcrumb-item.active { color: #6c757d; }
    .size-options .form-check-input:checked+.form-check-label { border-color: #0d6efd; background-color: #e7f1ff; color: #0d6efd; font-weight: 500; }
    .size-options .form-check-label { border: 1px solid #dee2e6; padding: 0.375rem 0.75rem; border-radius: 0.25rem; cursor: pointer; transition: all 0.2s ease-in-out; }
    .size-options .form-check-label:hover { border-color: #adb5bd; }
    .size-options .form-check-input { display: none; }
    .rating-form-container { border-top: 1px solid #eee; padding-top: 1.5rem; margin-top: 2rem; }
    .rating-stars { display: inline-flex; flex-direction: row-reverse; }
    .rating-stars input[type="radio"] { display: none; }
    .rating-stars label { font-size: 1.8rem; color: #ddd; cursor: pointer; transition: color 0.2s; }
    .rating-stars input[type="radio"]:checked~label, .rating-stars label:hover, .rating-stars label:hover~label { color: #ffc107; }
    .product-rating-display .fa-star, .product-rating-display .fa-star-half-alt { color: #ffc107; }
    .product-rating-display .text-muted { font-size: 0.9rem; }
    .product-image-wrapper { position: relative; display: inline-block; }
    .sale-badge-detail { position: absolute; top: 15px; left: 15px; background-color: #dc3545; color: white; z-index: 10; }
</style>

<main class="container py-5" style="margin-top: 80px;">
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger text-center" role="alert">
            <?= htmlspecialchars($error_message); ?>
            <p><a href="<?= BASE_URL ?>index.php?url=Product/index" class="alert-link">Kembali ke daftar produk</a></p>
        </div>
    <?php elseif ($product): ?>
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?url=Product/index">Produk</a></li>
                <?php if (!empty($product['nama_kategori'])): ?>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?url=Product/index&kategori=<?= $product['id_kategori']; ?>"><?= htmlspecialchars($product['nama_kategori']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-6 mb-4 mb-md-0 text-center">
                <div class="product-image-wrapper">
                    <?php
                    $sekarang = date('Y-m-d');
                    $sedang_diskon = (!empty($product['discount_percent']) && $product['discount_percent'] > 0 && 
                                     (empty($product['discount_start_date']) || $sekarang >= $product['discount_start_date']) && 
                                     (empty($product['discount_end_date']) || $sekarang <= $product['discount_end_date']));

                    if ($sedang_diskon) {
                        echo '<span class="badge fs-5 p-2 sale-badge-detail">SALE ' . htmlspecialchars($product['discount_percent']) . '%</span>';
                    }

                    $image_name = htmlspecialchars($product['image'] ?? '');
                    $is_url = filter_var($image_name, FILTER_VALIDATE_URL);
                    $image_url = $is_url ? $image_name : BASE_URL . "admin/uploads/produk/" . $image_name;
                    $placeholder_url = BASE_URL . "admin/placeholder_image.png";

                    if (!empty($image_name)): ?>
                        <img src="<?= $image_url; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-detail-img" onerror="this.onerror=null; this.src='<?= $placeholder_url ?>'">
                    <?php else: ?>
                        <img src="<?= $placeholder_url; ?>" alt="Gambar tidak tersedia" class="product-detail-img">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6 product-info">
                <h1><?= htmlspecialchars($product['name']); ?></h1>

                <?php if (!empty($product['brand'])): ?>
                    <p class="brand">Merek: <?= htmlspecialchars($product['brand']); ?></p>
                <?php endif; ?>

                <?php if (!empty($product['nama_kategori'])): ?>
                    <p class="category-info mb-2">
                        Kategori: <a href="<?= BASE_URL ?>index.php?url=Product/index&kategori=<?= htmlspecialchars($product['id_kategori']); ?>"><?= htmlspecialchars($product['nama_kategori']); ?></a>
                    </p>
                <?php endif; ?>

                <div class="product-rating-display mb-3">
                    <?php
                    $rating = $product['average_rating'] ?? 0;
                    $rating_count = $product['rating_count'] ?? 0;
                    for ($i = 1; $i <= 5; $i++) {
                        if ($rating >= $i) echo '<i class="fas fa-star"></i>';
                        elseif ($rating > ($i - 1) && $rating < $i) echo '<i class="fas fa-star-half-alt"></i>';
                        else echo '<i class="far fa-star"></i>';
                    }
                    ?>
                    <span class="text-muted ms-2">(<?= number_format($rating, 1); ?> dari <?= $rating_count; ?> ulasan)</span>
                </div>

                <?php if ($sedang_diskon):
                    $harga_diskon_detail = $product['price'] * (1 - ($product['discount_percent'] / 100));
                ?>
                    <h2 class="price text-danger mb-0">Rp <?= number_format($harga_diskon_detail, 0, ',', '.'); ?></h2>
                    <p class="mb-3 text-muted"><del>Rp <?= number_format($product['price'], 0, ',', '.'); ?></del></p>
                <?php else: ?>
                    <p class="price">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                <?php endif; ?>

                <div class="stock mb-2">
                    Stok:
                    <?php if ($product['stock'] > 0): ?>
                        <span class="badge bg-dark">Tersedia (<?= $product['stock']; ?>)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Habis</span>
                    <?php endif; ?>
                </div>

                <form action="<?= BASE_URL ?>index.php?url=Cart/add" method="POST" class="mt-3">
                    <input type="hidden" name="id_produk" value="<?= htmlspecialchars($product['id']); ?>">

                    <?php if (!empty($available_sizes)): ?>
                        <div class="mb-3 size-options">
                            <label class="form-label fw-bold d-block mb-2">Pilih Ukuran <span class="text-danger">*</span></label>
                            <?php foreach ($available_sizes as $index_size => $size_option): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="ukuran_terpilih"
                                        id="size_<?= htmlspecialchars($size_option); ?>_<?= $product['id']; ?>"
                                        value="<?= htmlspecialchars($size_option); ?>"
                                        <?= ($index_size === 0) ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="size_<?= htmlspecialchars($size_option); ?>_<?= $product['id']; ?>">
                                        <?= htmlspecialchars($size_option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif (!empty($product['size'])): ?>
                        <p class="size-available-text">Ukuran: <?= htmlspecialchars($product['size']); ?></p>
                        <input type="hidden" name="ukuran_terpilih" value="<?= htmlspecialchars($product['size']); ?>">
                    <?php else: ?>
                        <input type="hidden" name="ukuran_terpilih" value="">
                    <?php endif; ?>

                    <?php if ($product['stock'] > 0): ?>
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="kuantitas_<?= $product['id']; ?>" class="col-form-label fw-bold">Jumlah:</label>
                            </div>
                            <div class="col-auto" style="max-width: 100px;">
                                <input type="number" class="form-control" name="kuantitas" id="kuantitas_<?= $product['id']; ?>" value="1" min="1" max="<?= htmlspecialchars($product['stock']); ?>" required style="width: 80px;">
                            </div>
                        </div>
                        <div class="description mt-3 mb-3">
                            <h4>Deskripsi Produk:</h4>
                            <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Tidak ada deskripsi untuk produk ini.')); ?></p>
                        </div>
                        <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-outline-dark add-to-cart-btn flex-fill">
                                    <i class="fas fa-shopping-cart me-2"></i> Tambah ke Keranjang
                                </button>
                                <button type="submit" formaction="<?= BASE_URL ?>index.php?url=Checkout/buyNow" class="btn btn-dark add-to-cart-btn flex-fill">
                                    <i class="fas fa-bolt me-2"></i> Beli Sekarang
                                </button>
                            </div>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>index.php?url=Auth/login" class="btn btn-outline-dark add-to-cart-btn">
                                <i class="fas fa-sign-in-alt me-2"></i> Login untuk Membeli
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary btn-lg mt-3" disabled>Stok Habis</button>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12">
                <div class="rating-form-container">
                    <h4>Beri Ulasan Produk Ini</h4>
                    <?php if ($user_has_purchased && !$user_has_rated) : ?>
                        <form action="<?= BASE_URL ?>index.php?url=Product/addReview" method="POST">
                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
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

        <div class="row mt-4">
            <div class="col-12">
                <h4 class="mb-3">Ulasan Pelanggan</h4>
                <?php if (!empty($reviews)): ?>
                    <div class="list-group">
                        <?php foreach ($reviews as $review): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1 fw-bold"><?= htmlspecialchars($review['name']); ?></h5>
                                    <small class="text-muted"><?= date('d M Y', strtotime($review['created_at'])); ?></small>
                                </div>
                                <div class="mb-2 text-warning">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo ($review['rating'] >= $i) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($review['review_text'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border">Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!</div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

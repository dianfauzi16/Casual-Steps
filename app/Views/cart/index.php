<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .cart-item-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
    </div>

    <?php if (isset($_SESSION['pesan_notifikasi_keranjang'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['tipe_notifikasi_keranjang']); ?> alert-dismissible fade show rounded-4 shadow-sm" role="alert" data-aos="fade-in">
            <?= htmlspecialchars($_SESSION['pesan_notifikasi_keranjang']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['pesan_notifikasi_keranjang'], $_SESSION['tipe_notifikasi_keranjang']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message_checkout'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm" role="alert" data-aos="fade-in">
            <?= htmlspecialchars($_SESSION['error_message_checkout']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message_checkout']); ?>
    <?php endif; ?>

    <?php if (!empty($keranjang_items_detail)): ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" data-aos="fade-up">
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" colspan="2" class="py-3 ps-4 text-muted small text-uppercase">Produk</th>
                            <th scope="col" class="py-3 text-muted small text-uppercase">Ukuran</th>
                            <th scope="col" class="py-3 text-muted small text-uppercase">Harga Satuan</th>
                            <th scope="col" class="py-3 text-center text-muted small text-uppercase">Kuantitas</th>
                            <th scope="col" class="py-3 text-end text-muted small text-uppercase">Subtotal</th>
                            <th scope="col" class="py-3 text-center text-muted small text-uppercase pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($keranjang_items_detail as $cart_key => $item): ?>
                            <tr class="border-bottom">
                                <td style="width: 100px;" class="ps-4 py-3">
                                    <?php
                                    $image_name_cart = htmlspecialchars($item['image'] ?? '');
                                    $is_url_cart = filter_var($image_name_cart, FILTER_VALIDATE_URL);
                                    $image_url_cart = $is_url_cart ? $image_name_cart : BASE_URL . "admin/uploads/produk/" . $image_name_cart;
                                    $placeholder_url_cart = BASE_URL . "admin/placeholder_image.png";

                                    if (!empty($image_name_cart)): ?>
                                        <img src="<?= $image_url_cart; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="cart-item-img rounded-3 shadow-sm" onerror="this.onerror=null; this.src='<?= $placeholder_url_cart ?>'">
                                    <?php else: ?>
                                        <img src="<?= $placeholder_url_cart; ?>" alt="Gambar tidak tersedia" class="cart-item-img rounded-3 shadow-sm">
                                    <?php endif; ?>
                                </td>
                                <td class="py-3">
                                    <a href="<?= BASE_URL ?>index.php?url=Product/detail&id=<?= $item['id']; ?>" class="text-dark text-decoration-none fw-bold fs-6">
                                        <?= htmlspecialchars($item['name']); ?>
                                    </a>
                                </td>
                                <td class="py-3"><span class="badge bg-secondary rounded-pill px-3 py-2"><?= !empty($item['ukuran']) ? htmlspecialchars($item['ukuran']) : '-'; ?></span></td>
                                <td class="py-3 text-muted">
                                    <?php if (isset($item['is_discount']) && $item['is_discount']): ?>
                                        <span class="text-danger fw-bold">Rp <?= number_format($item['price'], 0, ',', '.') ?></span><br>
                                        <small class="text-decoration-line-through">Rp <?= number_format($item['original_price'], 0, ',', '.') ?></small>
                                    <?php else: ?>
                                        Rp <?= number_format($item['price'], 0, ',', '.'); ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center py-3" style="min-width: 170px;">
                                    <form action="<?= BASE_URL ?>index.php?url=Cart/update" method="POST" class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill p-1">
                                        <input type="hidden" name="cart_item_key" value="<?= htmlspecialchars($cart_key); ?>">
                                        <input type="number"
                                            name="kuantitas"
                                            value="<?= htmlspecialchars($item['kuantitas']); ?>"
                                            min="0"
                                            max="<?= htmlspecialchars($item['stock']); ?>"
                                            class="form-control form-control-sm bg-transparent border-0 text-center fw-bold"
                                            style="width: 60px;"
                                            required>
                                        <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" title="Update Kuantitas">
                                            <i class="fas fa-sync-alt text-primary" style="font-size: 0.75rem;"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="text-end py-3 fw-bold text-dark fs-6">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                <td class="text-center py-3 pe-4">
                                    <a href="<?= BASE_URL ?>index.php?url=Cart/remove&cart_item_key=<?= urlencode($cart_key); ?>" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Hapus item" onclick="return confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top-0 p-4 text-end">
                <span class="text-muted me-3 fs-5">Total Harga:</span>
                <span class="fw-bold fs-3 text-dark">Rp <?= number_format($total_harga_keranjang, 0, ',', '.'); ?></span>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4" data-aos="fade-up" data-aos-delay="200">
            <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-outline-secondary rounded-pill px-4 py-2 mb-3 mb-md-0 shadow-sm"><i class="fas fa-arrow-left me-2"></i> Lanjut Belanja</a>
            <a href="<?= BASE_URL ?>index.php?url=Checkout/index&mode=cart" class="btn btn-primary btn-lg rounded-pill px-5 shadow" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Lanjut ke Checkout <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    <?php else: ?>
        <div class="text-center py-5" data-aos="fade-in">
            <div class="bg-white rounded-5 shadow-sm p-5 mx-auto" style="max-width: 600px;">
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-3">Keranjang Anda Kosong</h3>
                <p class="text-muted mb-4">Sepertinya Anda belum menambahkan produk apapun ke dalam keranjang. Yuk temukan sepatu favorit Anda!</p>
                <a href="<?= BASE_URL ?>index.php?url=Product/index" class="btn btn-primary btn-lg rounded-pill px-5 shadow" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">Mulai Belanja</a>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .summary-item-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-shield-alt text-success me-1"></i> Checkout Aman</span>
    </div>

    <?php if (isset($_SESSION['checkout_errors'])): ?>
        <div class="alert alert-danger rounded-4 shadow-sm" data-aos="fade-in">
            <ul class="mb-0">
                <?php foreach ($_SESSION['checkout_errors'] as $error): ?>
                    <li><i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['checkout_errors']); ?>
    <?php endif; ?>

    <div class="row g-5">
        <div class="col-md-7 mb-4" data-aos="fade-right">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
                    <h5 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-primary me-2"></i> Informasi Pengiriman</h5>
                </div>
                <div class="card-body p-4">
                    <form id="checkoutForm" action="<?= BASE_URL ?>index.php?url=Checkout/processMidtrans" method="POST">
                        <input type="hidden" name="checkout_type" value="<?= $checkout_mode === 'direct' ? 'direct_checkout' : 'cart_checkout'; ?>">
                        
                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label text-muted small fw-bold text-uppercase">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 fs-6" id="nama_pelanggan" name="nama_pelanggan" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? ''); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_pelanggan" class="form-label text-muted small fw-bold text-uppercase">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control form-control-lg bg-light border-0 fs-6" id="email_pelanggan" name="email_pelanggan" required value="<?= htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telepon_pelanggan" class="form-label text-muted small fw-bold text-uppercase">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control form-control-lg bg-light border-0 fs-6" id="telepon_pelanggan" name="telepon_pelanggan" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_pengiriman_lengkap" class="form-label text-muted small fw-bold text-uppercase">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-lg bg-light border-0 fs-6" id="alamat_pengiriman_lengkap" name="alamat_pengiriman_lengkap" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kota_pengiriman" class="form-label text-muted small fw-bold text-uppercase">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg bg-light border-0 fs-6" id="kota_pengiriman" name="kota_pengiriman" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kode_pos_pengiriman" class="form-label text-muted small fw-bold text-uppercase">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg bg-light border-0 fs-6" id="kode_pos_pengiriman" name="kode_pos_pengiriman" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="catatan_pelanggan" class="form-label text-muted small fw-bold text-uppercase">Catatan Pesanan (Opsional)</label>
                            <textarea class="form-control form-control-lg bg-light border-0 fs-6" id="catatan_pelanggan" name="catatan_pelanggan" rows="2" placeholder="Contoh: Titip di pos satpam."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5" data-aos="fade-left" data-aos-delay="200">
            <div class="card shadow-lg border-0 rounded-4 mb-4 position-relative overflow-hidden">
                <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(13,110,253,0.1); border-radius: 50%;"></div>
                
                <div class="card-header bg-transparent border-bottom pt-4 pb-3 px-4 z-1">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-bag text-primary me-2"></i> Ringkasan Pesanan</h5>
                </div>
                <div class="card-body p-4 z-1">
                    <ul class="list-group list-group-flush mb-4">
                        <?php foreach ($items as $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 bg-transparent border-bottom">
                                <div class="d-flex align-items-center">
                                    <?php
                                    $image_name_checkout = htmlspecialchars($item['image'] ?? '');
                                    $is_url_checkout = filter_var($image_name_checkout, FILTER_VALIDATE_URL);
                                    $image_url_checkout = $is_url_checkout ? $image_name_checkout : BASE_URL . "admin/uploads/produk/" . $image_name_checkout;
                                    $placeholder_url_checkout = BASE_URL . "admin/placeholder_image.png";
                                    ?>
                                    <div class="position-relative me-3">
                                        <img src="<?= !empty($image_name_checkout) ? $image_url_checkout : $placeholder_url_checkout; ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="summary-item-img rounded-3 shadow-sm border" onerror="this.onerror=null; this.src='<?= $placeholder_url_checkout ?>'">
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary shadow-sm">
                                            <?= htmlspecialchars($item['kuantitas']); ?>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="my-0 fw-bold text-dark" style="font-size: 0.95rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted">
                                            <?php if (isset($item['is_discount']) && $item['is_discount']): ?>
                                                <span class="text-danger fw-bold">Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                                                <span class="text-decoration-line-through small">Rp <?= number_format($item['original_price'], 0, ',', '.') ?></span>
                                            <?php else: ?>
                                                Rp <?= number_format($item['price'], 0, ',', '.'); ?>
                                            <?php endif; ?>
                                            <?= !empty($item['ukuran']) ? ' | Size: <span class="fw-bold">' . htmlspecialchars($item['ukuran']) . '</span>' : ''; ?>
                                        </small>
                                    </div>
                                </div>
                                <span class="fw-bold text-dark ms-3">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></span>
                            </li>
                        <?php endforeach; ?>
                        
                        <li class="list-group-item d-flex justify-content-between px-0 pt-4 pb-2 bg-transparent border-0">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 py-2 bg-transparent border-bottom">
                            <span class="text-muted">Ongkos Kirim</span>
                            <?php 
                                $shipping_cost = (int)($global_settings['default_shipping_cost'] ?? 0); 
                            ?>
                            <?php if ($shipping_cost > 0): ?>
                                <span class="fw-bold">Rp <?= number_format($shipping_cost, 0, ',', '.'); ?></span>
                            <?php else: ?>
                                <span class="text-success fw-bold">Gratis</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0 pt-4 pb-0 bg-transparent border-0">
                            <span class="fs-5 fw-bold">Total Pembayaran</span>
                            <span class="text-danger fw-bold fs-4">Rp <?= number_format($total_harga + $shipping_cost, 0, ',', '.'); ?></span>
                        </li>
                    </ul>
                    
                    <button type="button" id="pay-button" class="btn btn-primary w-100 btn-lg mb-3 rounded-pill shadow" style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); border: none;">
                        <i class="fas fa-lock me-2"></i> Bayar Sekarang
                    </button>
                    <a href="<?= BASE_URL ?>index.php?url=Cart/index" class="btn btn-outline-secondary w-100 rounded-pill">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Keranjang
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-w6K_d1mY77l7xMvA"></script>
<script>
    document.getElementById('pay-button').onclick = function() {
        const form = document.getElementById('checkoutForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const payButton = document.getElementById('pay-button');
        
        payButton.disabled = true;
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';

        fetch('<?= BASE_URL ?>index.php?url=Checkout/processMidtrans', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.token) {
                window.snap.pay(data.token, {
                    onSuccess: function(result) {
                        window.location.href = "<?= BASE_URL ?>index.php?url=Checkout/paymentFinish&order_id=" + result.order_id + "&status=success";
                    },
                    onPending: function(result) {
                        window.location.href = "<?= BASE_URL ?>index.php?url=Checkout/paymentFinish&order_id=" + result.order_id + "&status=pending";
                    },
                    onError: function(result) {
                        window.location.href = "<?= BASE_URL ?>index.php?url=Checkout/paymentFinish&order_id=" + result.order_id + "&status=error";
                    },
                    onClose: function() {
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="fas fa-lock me-2"></i> Bayar Sekarang';
                        Swal.fire('Info', 'Anda menutup popup pembayaran sebelum menyelesaikannya.', 'info');
                    }
                });
            } else if (data.error) {
                Swal.fire('Error', data.error, 'error');
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock me-2"></i> Bayar Sekarang';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Terjadi kesalahan sistem saat menghubungi server pembayaran.', 'error');
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-lock me-2"></i> Bayar Sekarang';
        });
    };
</script>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

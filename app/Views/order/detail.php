<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .order-summary-card {
        border: none;
        border-radius: 1rem;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .order-summary-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-weight: 700;
        padding: 1.5rem;
    }
    .item-table th {
        background-color: transparent;
        border-bottom: 2px solid rgba(0,0,0,0.05) !important;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 1rem;
    }
    .item-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.03);
    }
    .order-status-badge {
        font-size: 0.75rem;
        padding: 0.5em 1em;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .breadcrumb-item a {
        color: #0d6efd;
        text-decoration: none;
        transition: color 0.2s;
    }
    .breadcrumb-item a:hover {
        color: #0a58ca;
        text-decoration: underline;
    }
</style>

<main class="container py-5" style="margin-top: 100px;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3" data-aos="fade-down">
        <h2 class="mb-0 fw-bold" style="font-family: 'Special Gothic Expanded One', system-ui;"><?= htmlspecialchars($page_title); ?></h2>
        <a href="<?= BASE_URL ?>index.php?url=Order/history" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <nav aria-label="breadcrumb" class="mb-5" data-aos="fade-right">
        <ol class="breadcrumb bg-light p-3 rounded-pill px-4 shadow-sm">
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?url=Profile/index">Akun Saya</a></li>
            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>index.php?url=Order/history">Riwayat Pesanan</a></li>
            <li class="breadcrumb-item active fw-bold" aria-current="page">Pesanan #<?= htmlspecialchars($order['id']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm order-summary-card mb-4" data-aos="fade-up">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                    <span class="fs-5 text-dark"><i class="fas fa-file-invoice text-primary me-2"></i> Informasi Pesanan</span>
                    <?php 
                        $status = htmlspecialchars($order['status']);
                        $badge_class = 'bg-secondary';
                        if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                        else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                        else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                        else if ($status == 'Selesai' || $status == 'settlement' || $status == 'capture') $badge_class = 'bg-success';
                        else if ($status == 'Dibatalkan' || $status == 'cancelled' || $status == 'deny' || $status == 'expire' || $status == 'cancel') $badge_class = 'bg-danger';
                    ?>
                    <span class="badge <?= $badge_class; ?> order-status-badge rounded-pill shadow-sm"><?= $status; ?></span>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="bg-light rounded-4 p-4 h-100">
                                <h6 class="text-muted small fw-bold text-uppercase mb-3"><i class="fas fa-user-tag text-primary me-2"></i> Detail Penerima</h6>
                                <p class="mb-2 text-dark"><span class="fw-medium">Nama:</span> <?= htmlspecialchars($order['nama_pelanggan']); ?></p>
                                <p class="mb-2 text-dark"><span class="fw-medium">Email:</span> <?= htmlspecialchars($order['email_pelanggan'] ?: '-'); ?></p>
                                <p class="mb-0 text-dark"><span class="fw-medium">Telepon:</span> <?= htmlspecialchars($order['telepon_pelanggan']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-light rounded-4 p-4 h-100">
                                <h6 class="text-muted small fw-bold text-uppercase mb-3"><i class="fas fa-map-marked-alt text-primary me-2"></i> Alamat Pengiriman</h6>
                                <p class="mb-2 text-dark" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($order['alamat_pengiriman_lengkap'])); ?></p>
                                <p class="mb-0 text-dark fw-medium"><?= htmlspecialchars($order['kota_pengiriman']); ?>, <?= htmlspecialchars($order['kode_pos_pengiriman']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4" style="opacity: 0.1;">
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small fw-bold text-uppercase">Tanggal Pesan</p>
                                    <p class="mb-0 fw-medium text-dark"><?= htmlspecialchars(date('d M Y, H:i', strtotime($order['tanggal_pesanan']))); ?></p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                    <i class="fas fa-credit-card text-success"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted small fw-bold text-uppercase">Metode Pembayaran</p>
                                    <p class="mb-0 fw-medium text-dark"><?= htmlspecialchars($order['metode_pembayaran']); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($order['catatan_pelanggan'])): ?>
                                <div class="bg-light rounded-4 p-4 border-start border-warning border-4 h-100">
                                    <h6 class="text-muted small fw-bold text-uppercase mb-2"><i class="fas fa-sticky-note text-warning me-2"></i> Catatan</h6>
                                    <p class="mb-0 text-dark" style="line-height: 1.6;"><?= nl2br(htmlspecialchars($order['catatan_pelanggan'])); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm order-summary-card mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-header border-bottom-0">
                    <span class="fs-5 text-dark"><i class="fas fa-box-open text-primary me-2"></i> Item yang Dipesan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 item-table align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="ps-4">Produk</th>
                                    <th class="text-center">Kuantitas</th>
                                    <th>Ukuran</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                <tr>
                                    <td style="width: 80px;" class="ps-4">
                                        <?php
                                        $image_name_item = htmlspecialchars($item['gambar_produk_terkini'] ?? '');
                                        $is_url_item = filter_var($image_name_item, FILTER_VALIDATE_URL);
                                        $image_url_item = $is_url_item ? $image_name_item : BASE_URL . "admin/uploads/produk/" . $image_name_item;
                                        $placeholder_url_item = BASE_URL . "admin/placeholder_image.png";
                                        ?>
                                        <div class="rounded-3 overflow-hidden shadow-sm border" style="width: 60px; height: 60px;">
                                            <img src="<?= !empty($image_name_item) ? $image_url_item : $placeholder_url_item; ?>" alt="<?= htmlspecialchars($item['nama_produk_saat_pesan']); ?>" class="w-100 h-100" style="object-fit:cover;">
                                        </div>
                                    </td>
                                    <td><span class="fw-bold text-dark"><?= htmlspecialchars($item['nama_produk_saat_pesan']); ?></span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark border px-2 py-1 rounded-pill"><?= htmlspecialchars($item['quantity']); ?></span></td>
                                    <td><span class="fw-medium text-dark"><?= htmlspecialchars($item['size'] ?: '-'); ?></span></td>
                                    <td class="text-end text-muted">Rp <?= number_format($item['harga_satuan_saat_pesan'], 0, ',', '.'); ?></td>
                                    <td class="text-end pe-4 fw-bold text-dark">Rp <?= number_format($item['subtotal_item'], 0, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-end py-4 fs-6 text-muted fw-bold text-uppercase">Total Keseluruhan:</td>
                                    <td class="text-end py-4 pe-4 fs-4 text-danger fw-bold">Rp <?= number_format($order['total_price'], 0, ',', '.'); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php if ($order['status'] == 'Menunggu Pembayaran' || $order['status'] == 'pending'): ?>
                <div class="card order-summary-card mb-4" data-aos="fade-up" data-aos-delay="200" style="background: linear-gradient(135deg, rgba(255,193,7,0.1), rgba(255,193,7,0.05)); border: 1px solid rgba(255,193,7,0.3);">
                    <div class="card-body text-center p-5">
                        <div class="mb-3">
                            <div class="bg-warning text-white rounded-circle d-inline-flex justify-content-center align-items-center shadow-sm" style="width: 80px; height: 80px;">
                                <i class="fas fa-hourglass-half fa-2x"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-warning mb-2">Menunggu Pembayaran</h4>
                        <p class="text-muted mb-0 mx-auto" style="max-width: 500px;">Pesanan Anda telah kami terima namun belum terbayar. Silakan selesaikan pembayaran Anda agar pesanan dapat segera kami proses.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

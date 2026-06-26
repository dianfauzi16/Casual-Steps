<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-file-invoice text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
    <a href="<?= BASE_URL ?>index.php?url=AdminOrder/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<?php if (isset($_SESSION['form_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3" role="alert">
        <?= htmlspecialchars($_SESSION['form_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-boxes text-primary me-2"></i> Item Produk Dipesan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 border-top">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Produk</th>
                                <th class="border-0 text-center">Ukuran</th>
                                <th class="border-0 text-center">Qty</th>
                                <th class="border-0 text-end">Harga Satuan</th>
                                <th class="border-0 text-end px-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="px-4 fw-medium text-dark"><?= htmlspecialchars($item['nama_produk_saat_pesan']) ?></td>
                                    <td class="text-center text-muted"><?= htmlspecialchars($item['size'] ?: '-') ?></td>
                                    <td class="text-center fw-bold"><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td class="text-end text-muted">Rp <?= number_format($item['harga_satuan_saat_pesan'], 0, ',', '.') ?></td>
                                    <td class="text-end px-4 fw-bold text-success">Rp <?= number_format($item['subtotal_item'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-shipping-fast text-primary me-2"></i> Alamat Pengiriman</h5>
            </div>
            <div class="card-body p-4 bg-light bg-opacity-50 rounded-bottom-4">
                <p class="mb-1 text-dark"><?= nl2br(htmlspecialchars($order['alamat_pengiriman_lengkap'])) ?></p>
                <p class="mb-0 text-muted fw-medium"><?= htmlspecialchars($order['kota_pengiriman']) ?>, <?= htmlspecialchars($order['kode_pos_pengiriman']) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-receipt text-primary me-2"></i> Ringkasan Pesanan</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Tanggal Pesan</span>
                    <span class="fw-medium text-dark"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Nama Pelanggan</span>
                    <span class="fw-medium text-dark"><?= htmlspecialchars($order['nama_pelanggan']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Telepon</span>
                    <span class="fw-medium text-dark"><?= htmlspecialchars($order['telepon_pelanggan']) ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted">Metode Pembayaran</span>
                    <span class="fw-medium text-dark"><?= htmlspecialchars($order['metode_pembayaran']) ?></span>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4 mt-4 p-3 bg-primary bg-opacity-10 rounded-3">
                    <span class="fw-bold text-primary mb-0">Total Harga</span>
                    <span class="fw-bold text-primary fs-5 mb-0">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></span>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Status Saat Ini</label>
                    <div>
                        <?php
                        $status = $order['status'];
                        $badge_class = 'bg-secondary';
                        if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                        else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                        else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                        else if ($status == 'Selesai') $badge_class = 'bg-success';
                        else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                        ?>
                        <span class="badge <?= $badge_class ?> fs-6 rounded-pill px-4 py-2 w-100 shadow-sm"><?= htmlspecialchars($status) ?></span>
                    </div>
                </div>

                <?php if (!empty($order['catatan_pelanggan'])): ?>
                    <div class="mt-4 p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                        <strong class="text-warning-emphasis d-block mb-1"><i class="fas fa-comment-dots me-1"></i> Catatan Pelanggan:</strong>
                        <span class="text-dark"><?= nl2br(htmlspecialchars($order['catatan_pelanggan'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-primary">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-sync-alt text-primary me-2"></i> Update Status Pesanan</h6>
                <form action="<?= BASE_URL ?>index.php?url=AdminOrder/updateStatus" method="POST">
                    <input type="hidden" name="id_order" value="<?= $order['id'] ?>">
                    <div class="input-group">
                        <select name="status_baru" class="form-select bg-light border-0">
                            <option value="Menunggu Pembayaran" <?= ($status == 'Menunggu Pembayaran' || $status == 'pending') ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                            <option value="Diproses" <?= ($status == 'Diproses' || $status == 'paid') ? 'selected' : '' ?>>Diproses</option>
                            <option value="Dikirim" <?= ($status == 'Dikirim' || $status == 'shipped') ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= ($status == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                            <option value="Dibatalkan" <?= ($status == 'Dibatalkan' || $status == 'cancelled') ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                        <button type="submit" class="btn btn-primary px-4 fw-medium">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

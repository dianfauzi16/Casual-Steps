<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-user text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
    <a href="<?= BASE_URL ?>index.php?url=AdminCustomer/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
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
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-center">
            <div class="card-body p-4 pt-5">
                <div class="mb-4 position-relative d-inline-block">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto border border-primary border-4" style="width: 100px; height: 100px; font-size: 2.5rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <?php if ($customer['account_status'] === 'aktif'): ?>
                        <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle border-3" title="Aktif"></span>
                    <?php else: ?>
                        <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-light rounded-circle border-3" title="Nonaktif"></span>
                    <?php endif; ?>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars($customer['name']) ?></h4>
                <p class="text-muted mb-4"><?= htmlspecialchars($customer['email']) ?></p>

                <div class="d-grid gap-2">
                    <form action="<?= BASE_URL ?>index.php?url=AdminCustomer/toggleStatus" method="POST">
                        <input type="hidden" name="id" value="<?= $customer['id'] ?>">
                        <?php if ($customer['account_status'] === 'aktif'): ?>
                            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill" onclick="return confirm('Apakah Anda yakin ingin menonaktifkan pelanggan ini?');">
                                <i class="fas fa-ban me-1"></i> Nonaktifkan Akun
                            </button>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success w-100 rounded-pill">
                                <i class="fas fa-check-circle me-1"></i> Aktifkan Akun
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <div class="card-footer bg-light border-0 py-3 text-muted small rounded-bottom-4">
                Bergabung sejak: <span class="fw-medium text-dark"><?= date('d M Y', strtotime($customer['created_at'])) ?></span>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-map-marker-alt text-primary me-2"></i> Alamat Tersimpan (<?= count($addresses) ?>)</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <?php if (!empty($addresses)): ?>
                    <div class="row g-4">
                        <?php foreach ($addresses as $address): ?>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <h6 class="fw-bold mb-2">
                                        <?= htmlspecialchars($address['label'] ?: 'Alamat') ?>
                                        <?php if ($address['is_primary']): ?>
                                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">Utama</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-1 small"><strong>Penerima:</strong> <?= htmlspecialchars($address['recipient_name']) ?></p>
                                    <p class="mb-1 small"><strong>Telepon:</strong> <?= htmlspecialchars($address['phone_number']) ?></p>
                                    <p class="mb-0 small text-muted">
                                        <?= nl2br(htmlspecialchars($address['street_address'])) ?><br>
                                        <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['province']) ?> <?= htmlspecialchars($address['postal_code']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">Pelanggan ini belum memiliki alamat tersimpan.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-shopping-bag text-primary me-2"></i> Riwayat Pesanan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">ID Pesanan</th>
                                <th class="border-0">Tanggal</th>
                                <th class="border-0 text-end">Total Harga</th>
                                <th class="border-0 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="px-4 fw-medium text-dark">
                                        <a href="<?= BASE_URL ?>index.php?url=AdminOrder/detail&id=<?= $order['id'] ?>" class="text-decoration-none">
                                            #<?= htmlspecialchars($order['id']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small"><?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?></td>
                                    <td class="text-end fw-medium">Rp <?= number_format($order['total_price'], 0, ',', '.') ?></td>
                                    <td class="text-center">
                                        <?php
                                        $status = $order['status'];
                                        $badge_class = 'bg-secondary';
                                        if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                        else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                        else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                        else if ($status == 'Selesai') $badge_class = 'bg-success';
                                        else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?= $badge_class ?> rounded-pill px-2 py-1" style="font-size: 0.75rem;"><?= htmlspecialchars($status) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Belum ada riwayat pesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

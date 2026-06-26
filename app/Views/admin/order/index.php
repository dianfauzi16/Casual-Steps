<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
    </div>
    
    <div class="card-body p-4">
        <?php if (isset($_SESSION['form_message'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3" role="alert">
                <?= htmlspecialchars($_SESSION['form_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 rounded-start">ID Pesanan</th>
                        <th class="border-0">Nama Pelanggan</th>
                        <th class="border-0">Tanggal Pesan</th>
                        <th class="border-0 text-end">Total Harga</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-center rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?= htmlspecialchars($order['id']) ?></td>
                            <td class="fw-medium text-dark">
                                <?= htmlspecialchars($order['nama_pelanggan']) ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])) ?>
                            </td>
                            <td class="text-end fw-bold text-success">
                                Rp <?= number_format($order['total_price'], 0, ',', '.') ?>
                            </td>
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
                                <span class="badge <?= $badge_class ?> rounded-pill px-3 py-2 fw-medium">
                                    <?= htmlspecialchars($status) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?url=AdminOrder/detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-light text-primary shadow-sm border" title="Lihat Detail">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-box-open fa-3x opacity-25"></i></div>
                                Belum ada pesanan yang masuk.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

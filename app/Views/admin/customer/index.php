<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-users text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
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
                        <th class="border-0 rounded-start">ID</th>
                        <th class="border-0">Nama Pelanggan</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Telepon</th>
                        <th class="border-0">Tanggal Bergabung</th>
                        <th class="border-0 text-center">Status Akun</th>
                        <th class="border-0 text-center rounded-end">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td class="text-muted fw-bold">#<?= htmlspecialchars($customer['id']) ?></td>
                            <td class="fw-medium text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <?= htmlspecialchars($customer['name']) ?>
                                </div>
                            </td>
                            <td class="text-muted">
                                <?= htmlspecialchars($customer['email']) ?>
                            </td>
                            <td class="text-muted">
                                <?= htmlspecialchars($customer['phone_number'] ?: '-') ?>
                            </td>
                            <td class="text-muted small">
                                <?= date('d M Y, H:i', strtotime($customer['created_at'])) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($customer['account_status'] === 'aktif'): ?>
                                    <span class="badge bg-success rounded-pill px-3 py-2 fw-medium"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill px-3 py-2 fw-medium"><i class="fas fa-ban me-1"></i> Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?url=AdminCustomer/detail&id=<?= $customer['id'] ?>" class="btn btn-sm btn-light text-primary shadow-sm border" title="Lihat Detail">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-3"><i class="fas fa-users-slash fa-3x opacity-25"></i></div>
                                Belum ada pelanggan yang terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

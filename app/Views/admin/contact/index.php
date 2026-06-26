<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-envelope text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
</div>

<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
        <h5 class="mb-0 fw-bold"><i class="fas fa-envelope-open-text text-primary me-2"></i> Daftar Pesan</h5>
    </div>
    
    <div class="card-body p-4">
        <?php if (isset($_SESSION['form_message'])): ?>
            <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
                <?= htmlspecialchars($_SESSION['form_message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
        <?php endif; ?>

        <?php if (!empty($messages)): ?>
            <div class="table-responsive border rounded-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-4">ID</th>
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">Pengirim</th>
                            <th class="border-0">Subjek</th>
                            <th class="border-0 text-center">Status</th>
                            <th class="border-0 text-center px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $row): ?>
                            <tr>
                                <td class="px-4 fw-medium text-muted">#<?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <div class="fw-medium text-dark"><?= date('d M Y', strtotime($row['tanggal_kirim'])) ?></div>
                                    <div class="small text-muted"><?= date('H:i', strtotime($row['tanggal_kirim'])) ?></div>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                    <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="small text-decoration-none text-muted"><i class="fas fa-envelope me-1"></i><?= htmlspecialchars($row['email']) ?></a>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($row['subjek']) ?>"><?= htmlspecialchars($row['subjek']) ?></div>
                                    <div class="small text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars(mb_strimwidth($row['pesan'], 0, 50, "...")) ?></div>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['status_baca'] == 'sudah dibaca'): ?>
                                        <span class="badge bg-info rounded-pill px-3 py-1 fw-medium"><i class="fas fa-check-double me-1"></i>Dibaca</span>
                                    <?php elseif ($row['status_baca'] == 'sudah dibalas'): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-1 shadow-sm fw-medium"><i class="fas fa-reply-all me-1"></i>Dibalas</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 shadow-sm fw-medium"><i class="fas fa-envelope me-1"></i>Baru</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center px-4">
                                    <div class="btn-group shadow-sm rounded-pill">
                                        <a href="<?= BASE_URL ?>index.php?url=AdminContact/detail&id=<?= $row['id'] ?>" class="btn btn-primary btn-sm rounded-start-pill px-3" title="Lihat Detail & Balas">
                                            <i class="fas <?= $row['status_baca'] == 'belum dibaca' ? 'fa-envelope-open' : 'fa-reply' ?>"></i>
                                        </a>
                                        <?php if ($row['status_baca'] == 'belum dibaca'): ?>
                                            <a href="<?= BASE_URL ?>index.php?url=AdminContact/markRead&id=<?= $row['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-end-pill px-3" title="Tandai Sudah Dibaca" onclick="return confirm('Tandai pesan ini sebagai sudah dibaca?');">
                                                <i class="fas fa-check"></i>
                                            </a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-end-pill px-3" disabled><i class="fas fa-check text-success"></i></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                <div class="mb-3 text-muted"><i class="fas fa-inbox fa-3x opacity-50"></i></div>
                <h5 class="fw-bold text-dark">Tidak Ada Pesan</h5>
                <p class="text-muted mb-0">Belum ada pesan kontak yang masuk dari pelanggan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

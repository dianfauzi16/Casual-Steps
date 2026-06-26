<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-0 text-dark"><i class="fas fa-reply text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h1>
        <p class="text-muted mb-0 mt-1">Balas pesan masuk dari pelanggan dan riwayat percakapan.</p>
    </div>
    <a href="<?= BASE_URL ?>index.php?url=AdminContact/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-bold">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<?php if (isset($_SESSION['form_message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['form_message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
<?php endif; ?>

<div class="row g-4">
    <!-- Detail Pesan Pelanggan -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-circle text-primary me-2"></i> Info Pesan</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-user fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($message['nama']) ?></h6>
                        <a href="mailto:<?= htmlspecialchars($message['email']) ?>" class="small text-decoration-none text-muted"><?= htmlspecialchars($message['email']) ?></a>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Subjek</small>
                    <p class="fw-medium text-dark mb-0"><?= htmlspecialchars($message['subjek']) ?></p>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Tanggal & Waktu</small>
                    <p class="fw-medium text-dark mb-0">
                        <?= date('d M Y, H:i', strtotime($message['tanggal_kirim'])) ?> WIB
                    </p>
                </div>

                <div class="mb-0">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Isi Pesan</small>
                    <div class="p-3 bg-light rounded-3 mt-1 text-dark border border-dashed" style="white-space: pre-wrap; font-size: 0.9rem; line-height: 1.6; max-height: 300px; overflow-y: auto;">
<?= htmlspecialchars($message['pesan']) ?>
                    </div>
                </div>
                
                <?php if ($message['status_baca'] == 'sudah dibalas' && !empty($message['admin_reply_message'])): ?>
                    <div class="mt-4 border-top pt-3">
                        <small class="text-muted text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            <i class="fas fa-check-circle me-1"></i> Telah Dibalas Pada: <?= date('d M Y, H:i', strtotime($message['admin_reply_timestamp'])) ?>
                        </small>
                        <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3 mt-2 text-dark" style="white-space: pre-wrap; font-size: 0.9rem; line-height: 1.6; max-height: 200px; overflow-y: auto;">
<?= htmlspecialchars($message['admin_reply_message']) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Form Balas Pesan -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-bottom-0 rounded-top-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-paper-plane text-primary me-2"></i> Balas ke Pelanggan</h5>
            </div>
            <div class="card-body p-4 pt-2">
                <div class="alert alert-info border-0 bg-info bg-opacity-10 text-dark rounded-3 d-flex align-items-center mb-4">
                    <i class="fas fa-info-circle text-info fs-4 me-3"></i>
                    <div class="small">
                        Balasan Anda akan langsung dikirimkan ke email <strong><?= htmlspecialchars($message['email']) ?></strong> menggunakan SMTP PHPMailer.
                    </div>
                </div>

                <form action="<?= BASE_URL ?>index.php?url=AdminContact/reply" method="POST" id="replyForm">
                    <input type="hidden" name="id_pesan" value="<?= $message['id'] ?>">
                    <input type="hidden" name="user_email" value="<?= htmlspecialchars($message['email']) ?>">
                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($message['nama']) ?>">
                    <input type="hidden" name="original_subject" value="<?= htmlspecialchars($message['subjek']) ?>">
                    <input type="hidden" name="original_message" value="<?= htmlspecialchars($message['pesan']) ?>">

                    <div class="mb-4">
                        <label for="admin_reply" class="form-label fw-medium text-dark">Pesan Balasan</label>
                        <textarea class="form-control bg-light border-0 px-3 py-2" id="admin_reply" name="admin_reply" rows="8" placeholder="Ketik balasan Anda di sini..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" id="btnSubmit">
                            <i class="fas fa-paper-plane me-2"></i> Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('replyForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...';
        btn.disabled = true;
    });
</script>

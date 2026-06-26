<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center rounded-top-4 border-bottom-0">
                <h5 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> <?= htmlspecialchars($page_title) ?></h5>
            </div>
            
            <div class="card-body p-4">
                <?php if (isset($_SESSION['form_message'])): ?>
                    <div class="alert alert-<?= htmlspecialchars($_SESSION['form_message_type']) ?> alert-dismissible fade show rounded-3" role="alert">
                        <?= htmlspecialchars($_SESSION['form_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['form_message'], $_SESSION['form_message_type']); ?>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>index.php?url=AdminCategory/store" method="POST">
                    <div class="mb-4">
                        <label for="nama_kategori" class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-tag text-muted"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Sepatu Pria" required autofocus>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>index.php?url=AdminCategory/index" class="btn btn-light rounded-pill px-4">Batal</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-1"></i> Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

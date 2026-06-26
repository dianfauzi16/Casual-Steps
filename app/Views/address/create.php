<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<main class="container py-5" style="margin-top: 80px;">
    <h1 class="mb-4"><?= htmlspecialchars($page_title); ?></h1>

    <?php if (isset($_SESSION['alamat_message'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['alamat_message_type']); ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['alamat_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['alamat_message'], $_SESSION['alamat_message_type']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">
                    Formulir Alamat Baru
                </div>
                <div class="card-body">
                    <form action="<?= BASE_URL ?>index.php?url=Address/store" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Label Alamat <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" placeholder="Rumah, Kantor, dll" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="recipient_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone_number" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="street_address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="province" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="city" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="postal_code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Negara <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="country" value="Indonesia" required>
                            </div>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1">
                            <label class="form-check-label" for="is_primary">Jadikan sebagai alamat utama</label>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>index.php?url=Address/index" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Alamat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

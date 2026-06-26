<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .form-card { background: #fff; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 5rem; margin-bottom: 5rem;}
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 60vh;">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="form-card mx-auto">
            <div class="text-center mb-4">
                <h2>Atur Ulang Password</h2>
                <p class="text-muted">Buat password baru yang kuat untuk akun Anda.</p>
            </div>

            <?php
            if (isset($_SESSION['reset_error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                    . htmlspecialchars($_SESSION['reset_error'])
                    . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['reset_error']);
            }
            ?>

            <form action="<?= BASE_URL ?>index.php?url=Auth/processResetPassword" method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '') ?>">
                <div class="mb-3">
                    <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password baru" required>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Atur Ulang Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

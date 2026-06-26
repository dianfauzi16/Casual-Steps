<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .form-card { 
        background: #fff; 
        padding: 3.5rem 3rem; 
        border-radius: 1.5rem; 
        box-shadow: 0 20px 40px rgba(0,0,0,0.08); 
        margin-top: 8rem; 
        margin-bottom: 5rem;
        position: relative;
        z-index: 1;
    }
    .form-control {
        background-color: #f8f9fa;
        border: 1px solid transparent;
        padding: 0.75rem 1rem;
        padding-left: 3rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.1);
    }
    .input-group-text-custom {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 4;
        color: #adb5bd;
        background: transparent;
        border: none;
    }
    .btn-reset { 
        background: linear-gradient(45deg, #0d6efd, #0dcaf0); 
        border: none; 
        padding: 0.75rem 1.5rem; 
        font-weight: 600; 
        border-radius: 50rem;
        box-shadow: 0 4px 15px rgba(13,110,253,0.2);
        transition: all 0.3s ease;
    }
    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13,110,253,0.3);
    }
    .icon-container {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,202,240,0.2));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem auto;
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="col-md-7 col-lg-6 col-xl-5 relative">
        <!-- Decorative blobs behind the card -->
        <div style="position: absolute; top: 100px; right: 0px; width: 250px; height: 250px; background: rgba(13, 202, 240, 0.15); border-radius: 50%; filter: blur(40px); z-index: 0;"></div>
        <div style="position: absolute; bottom: 50px; left: 0px; width: 200px; height: 200px; background: rgba(13, 110, 253, 0.1); border-radius: 50%; filter: blur(30px); z-index: 0;"></div>
        
        <div class="form-card mx-auto" data-aos="zoom-in" data-aos-duration="600">
            <div class="text-center mb-4">
                <div class="icon-container shadow-sm">
                    <i class="fas fa-lock-open fa-2x text-primary"></i>
                </div>
                <h3 class="fw-bold text-dark" style="font-family: 'Special Gothic Expanded One', system-ui;">Lupa Password?</h3>
                <p class="text-muted mt-2" style="font-size: 0.95rem;">Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
            </div>

            <?php
            if (isset($_SESSION['password_reset_message'])) {
                $type = htmlspecialchars($_SESSION['password_reset_message_type'] ?? 'info');
                $icon = $type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
                echo '<div class="alert alert-' . $type . ' alert-dismissible fade show rounded-3 shadow-sm border-0" role="alert" data-aos="fade-in">'
                    . '<i class="fas ' . $icon . ' me-2"></i>' . htmlspecialchars($_SESSION['password_reset_message'])
                    . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                unset($_SESSION['password_reset_message']);
                unset($_SESSION['password_reset_message_type']);
            }
            ?>

            <form action="<?= BASE_URL ?>index.php?url=Auth/processLupaPassword" method="POST" class="mt-4">
                <div class="mb-4">
                    <label for="email" class="form-label text-muted small fw-bold text-uppercase">Alamat Email</label>
                    <div class="position-relative">
                        <span class="input-group-text-custom"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required>
                    </div>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-reset">Kirim Tautan Reset</button>
                </div>
                <div class="text-center mt-4">
                    <a href="<?= BASE_URL ?>index.php?url=Auth/login" class="text-decoration-none fw-medium text-muted">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

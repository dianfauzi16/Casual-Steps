<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .login-container { 
        max-width: 900px; 
        margin-top: 120px; 
        margin-bottom: 80px; 
        background-color: #fff; 
        border-radius: 1.5rem; 
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); 
        overflow: hidden;
    }
    .login-image-side { 
        background: linear-gradient(135deg, rgba(13,110,253,0.9), rgba(13,202,240,0.8)), url('<?= BASE_URL ?>admin/Assets/1.png');
        background-size: cover; 
        background-position: center; 
        position: relative;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        text-align: center;
    }
    .login-form-side { 
        padding: 3rem 4rem; 
        background-color: #ffffff;
    }
    .form-control {
        background-color: #f8f9fa;
        border: 1px solid transparent;
        padding: 0.75rem 1rem;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        background-color: #fff;
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.1);
    }
    .btn-login { 
        background: linear-gradient(45deg, #0d6efd, #0dcaf0); 
        border: none; 
        padding: 0.75rem 1.5rem; 
        font-weight: 600; 
        border-radius: 50rem;
        box-shadow: 0 4px 15px rgba(13,110,253,0.2);
        transition: all 0.3s ease;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13,110,253,0.3);
    }
    .social-login-btn { 
        border-radius: 50%; 
        width: 45px; 
        height: 45px; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        margin: 0 8px; 
        border: 1px solid #e9ecef; 
        background-color: #fff;
        color: #555; 
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    .social-login-btn:hover { 
        background-color: #f8f9fa;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        color: #db4437;
    }
    @media (max-width: 768px) {
        .login-form-side { padding: 2rem; }
    }
</style>

<div class="container">
    <div class="login-container mx-auto row g-0" data-aos="fade-up">
        <div class="col-md-5 d-none d-md-flex login-image-side">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(30px);"></div>
            <div style="position: absolute; bottom: -50px; left: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(20px);"></div>
            
            <div class="position-relative z-1" data-aos="fade-right" data-aos-delay="200">
                <div class="mb-4 bg-white p-3 rounded-circle d-inline-block shadow">
                    <i class="fas fa-shoe-prints fa-3x text-primary"></i>
                </div>
                <h2 class="fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">Selamat Datang Kembali</h2>
                <p class="fs-6 fw-light opacity-75">Masuk ke akun Casual Steps Anda untuk melanjutkan belanja sepatu impian dengan pengalaman terbaik.</p>
            </div>
        </div>

        <div class="col-md-7 login-form-side">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-1">Masuk Akun</h3>
                <p class="text-muted">Silakan masukkan detail login Anda</p>
            </div>

            <?php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger rounded-3 border-0 shadow-sm" role="alert"><i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div class="alert alert-success rounded-3 border-0 shadow-sm" role="alert"><i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                unset($_SESSION['register_success']);
            }
            ?>

            <div id="login-error-message" class="alert alert-danger rounded-3 border-0 shadow-sm mt-3" role="alert" style="display: none;"></div>

            <form action="<?= BASE_URL ?>index.php?url=Auth/processLogin" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label text-muted small fw-bold text-uppercase">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required value="<?= htmlspecialchars($_SESSION['form_data_login']['email'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label text-muted small fw-bold text-uppercase mb-0">Password <span class="text-danger">*</span></label>
                        <a href="<?= BASE_URL ?>index.php?url=Auth/lupaPassword" class="text-primary text-decoration-none small fw-medium">Lupa password?</a>
                    </div>
                    <input type="password" class="form-control mt-2" id="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input mt-1" id="remember_me" name="remember_me">
                    <label class="form-check-label text-muted" for="remember_me">Ingat Saya</label>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-login btn-lg">Masuk Sekarang</button>
                </div>
                
                <div class="position-relative mb-4 text-center">
                    <hr class="text-muted opacity-25">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted small">Atau masuk dengan</span>
                </div>
                
                <div class="text-center mb-4">
                    <a href="#" id="tombol-login-google" class="social-login-btn" title="Login dengan Google"><i class="fab fa-google"></i></a>
                </div>
                <p class="text-center text-muted mb-0">Belum punya akun? <a href="<?= BASE_URL ?>index.php?url=Auth/register" class="text-primary text-decoration-none fw-bold">Daftar sekarang</a></p>
            </form>
            <?php unset($_SESSION['form_data_login']); ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<!-- Firebase Auth Logic Khusus Halaman Ini -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const googleLoginButton = document.getElementById('tombol-login-google');
    const errorMessageDiv = document.getElementById('login-error-message');
    let isLoginInProgress = false;

    if(googleLoginButton) {
        googleLoginButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (isLoginInProgress) return;
            isLoginInProgress = true;
            googleLoginButton.disabled = true;
            googleLoginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            errorMessageDiv.style.display = 'none';

            var provider = new firebase.auth.GoogleAuthProvider();

            firebase.auth().signInWithPopup(provider)
                .then((result) => {
                    return result.user.getIdToken().then(function(idToken) {
                        return fetch('<?= BASE_URL ?>index.php?url=Auth/firebaseLogin', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ idToken: idToken }),
                        });
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success', title: 'Login via Google berhasil!', timer: 1500, showConfirmButton: false
                        }).then(() => { window.location.href = data.redirect_url || '<?= BASE_URL ?>index.php'; });
                    } else {
                        errorMessageDiv.textContent = data.message || 'Terjadi kesalahan saat login.';
                        errorMessageDiv.style.display = 'block';
                    }
                })
                .catch((error) => {
                    if (error.code !== 'auth/cancelled-popup-request') {
                        errorMessageDiv.textContent = 'Gagal login dengan Google: ' + error.message;
                        errorMessageDiv.style.display = 'block';
                    }
                })
                .finally(() => {
                    isLoginInProgress = false;
                    googleLoginButton.disabled = false;
                    googleLoginButton.innerHTML = '<i class="fab fa-google"></i>';
                });
        });
    }
});
</script>

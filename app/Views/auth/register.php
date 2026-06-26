<?php require_once dirname(__DIR__) . '/layouts/header.php'; ?>

<style>
    .register-container { 
        max-width: 1000px; 
        margin-top: 120px; 
        margin-bottom: 50px; 
        background-color: #fff; 
        border-radius: 1.5rem; 
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08); 
        overflow: hidden;
    }
    .register-image-side { 
        background: linear-gradient(135deg, rgba(13,110,253,0.9), rgba(13,202,240,0.8)), url('<?= BASE_URL ?>assets/sp1.png');
        background-size: cover; 
        background-position: center; 
        position: relative;
        padding: 3rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        color: white;
    }
    .glass-feature-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-top: 2rem;
        transition: transform 0.3s ease;
    }
    .glass-feature-card:hover {
        transform: translateY(-5px);
    }
    .register-form-side { 
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
    .btn-register { 
        background: linear-gradient(45deg, #0d6efd, #0dcaf0); 
        border: none; 
        padding: 0.75rem 1.5rem; 
        font-weight: 600; 
        border-radius: 50rem;
        box-shadow: 0 4px 15px rgba(13,110,253,0.2);
        transition: all 0.3s ease;
    }
    .btn-register:hover {
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
        .register-form-side { padding: 2rem; }
    }
</style>

<div class="container">
    <div class="register-container mx-auto row g-0" data-aos="fade-up">
        <div class="col-md-5 d-none d-md-flex register-image-side">
            <div style="position: absolute; top: -50px; left: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(30px);"></div>
            <div style="position: absolute; bottom: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(20px);"></div>
            
            <div class="position-relative z-1" data-aos="fade-right" data-aos-delay="200">
                <h2 class="fw-bold mb-3" style="font-family: 'Special Gothic Expanded One', system-ui;">Langkah Awal Penampilan Sempurna</h2>
                <p class="fs-6 fw-light opacity-75 mb-4">Bergabunglah bersama ribuan pelanggan Casual Steps lainnya dan nikmati pengalaman berbelanja sepatu premium yang tak terlupakan.</p>
                
                <div class="glass-feature-card">
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-tags fs-4 mt-1 me-3 text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Diskon Eksklusif Member</h6>
                            <p class="small opacity-75 mb-0">Dapatkan akses ke promo khusus yang hanya tersedia untuk member terdaftar.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-truck-fast fs-4 mt-1 me-3 text-warning"></i>
                        <div>
                            <h6 class="fw-bold mb-1">Checkout Lebih Cepat</h6>
                            <p class="small opacity-75 mb-0">Simpan alamat pengiriman untuk proses pembelian kilat di masa mendatang.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7 register-form-side">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-dark mb-1">Buat Akun Baru</h3>
                <p class="text-muted">Isi data diri Anda di bawah ini</p>
            </div>

            <?php
            if (isset($_SESSION['register_error'])) {
                echo '<div class="alert alert-danger rounded-3 border-0 shadow-sm" role="alert"><i class="fas fa-exclamation-circle me-2"></i>' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                unset($_SESSION['register_error']);
            }
            if (isset($_SESSION['register_success'])) {
                echo '<div class="alert alert-success rounded-3 border-0 shadow-sm" role="alert"><i class="fas fa-check-circle me-2"></i>' . htmlspecialchars($_SESSION['register_success']) . ' Silakan <a href="' . BASE_URL . 'index.php?url=Auth/login" class="fw-bold text-success">login</a>.</div>';
                unset($_SESSION['register_success']);
            }
            ?>

            <div id="firebase-error-message" class="alert alert-danger rounded-3 border-0 shadow-sm" role="alert" style="display: none;"></div>

            <form action="<?= BASE_URL ?>index.php?url=Auth/processRegister" method="POST">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold text-uppercase">Nama Depan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" required value="<?= htmlspecialchars($_SESSION['form_data']['first_name'] ?? ''); ?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold text-uppercase">Nama Belakang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="last_name" required value="<?= htmlspecialchars($_SESSION['form_data']['last_name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Nomor Telepon <span class="text-secondary fw-normal text-lowercase">(opsional)</span></label>
                    <input type="tel" class="form-control" name="phone_number" value="<?= htmlspecialchars($_SESSION['form_data']['phone_number'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required>
                    <small class="form-text text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> Minimal 8 karakter, mengandung huruf & angka.</small>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="confirm_password" required>
                </div>
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input mt-1" name="agree_terms" required id="agreeTerms">
                    <label class="form-check-label text-muted" for="agreeTerms">Saya menyetujui <a href="#" class="text-primary text-decoration-none fw-medium">Syarat & Ketentuan</a> yang berlaku. <span class="text-danger">*</span></label>
                </div>
                <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-primary btn-register btn-lg">Daftar Sekarang</button>
                </div>
                
                <div class="position-relative mb-4 text-center">
                    <hr class="text-muted opacity-25">
                    <span class="position-absolute top-50 start-50 translate-middle px-3 bg-white text-muted small">Atau daftar instan dengan</span>
                </div>
                
                <div class="text-center mb-4">
                    <a href="#" id="tombol-login-google" class="social-login-btn" title="Daftar dengan Google"><i class="fab fa-google"></i></a>
                </div>
                
                <p class="text-center text-muted mb-0">Sudah punya akun? <a href="<?= BASE_URL ?>index.php?url=Auth/login" class="text-primary text-decoration-none fw-bold">Masuk di sini</a></p>
            </form>
            <?php unset($_SESSION['form_data']); ?>
        </div>
    </div>
</div>

<?php require_once dirname(__DIR__) . '/layouts/footer.php'; ?>

<!-- Script Firebase (Sama dengan login) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const googleLoginButton = document.getElementById('tombol-login-google');
    const errorMessageDiv = document.getElementById('firebase-error-message');
    let isLoginInProgress = false;

    if (googleLoginButton) {
        googleLoginButton.addEventListener('click', function(e) {
            e.preventDefault();
            if (isLoginInProgress) return;
            isLoginInProgress = true;
            googleLoginButton.disabled = true;
            googleLoginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            errorMessageDiv.style.display = 'none';

            var provider = new firebase.auth.GoogleAuthProvider();

            firebase.auth().signInWithPopup(provider)
                .then((result) => result.user.getIdToken())
                .then(function(idToken) {
                    return fetch('<?= BASE_URL ?>index.php?url=Auth/firebaseLogin', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ idToken: idToken }),
                    });
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Registrasi berhasil!', timer: 2000, showConfirmButton: false })
                            .then(() => { window.location.href = data.redirect_url || '<?= BASE_URL ?>index.php'; });
                    } else {
                        errorMessageDiv.textContent = data.message || 'Terjadi kesalahan.';
                        errorMessageDiv.style.display = 'block';
                    }
                })
                .catch((error) => {
                    if (error.code !== 'auth/cancelled-popup-request') {
                        errorMessageDiv.textContent = 'Gagal: ' + error.message;
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

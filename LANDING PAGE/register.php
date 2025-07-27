<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, arahkan ke halaman lain (misalnya, halaman utama atau akun)
if (isset($_SESSION['user_id'])) { // Asumsi 'user_id' disimpan di session saat login pelanggan
    header('Location: index.php'); // Atau halaman akun pelanggan
    exit;
}

// Sertakan db_connect.php jika Anda perlu melakukan pengecekan dinamis sebelum form tampil (opsional)
// require_once __DIR__ . '/../ADMIN MENU/db_connect.php';
$page_title = "Daftar Akun Baru";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background-color: #f8f9fa;
            /* Warna latar belakang halaman */
        }

        .register-container {
            max-width: 800px;
            /* Sesuaikan dengan layout gambar referensi */
            margin-top: 50px;
            margin-bottom: 50px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .register-image-side {
            /* Jika ingin ada gambar di sisi seperti referensi */
            /* background-image: url('path/to/your/shoe_image.jpg'); */
            background-size: cover;
            background-position: center;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .register-form-side {
            padding: 30px 40px;
            /* Padding untuk form */
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-register {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px 15px;
            font-weight: 500;
        }

        .social-login-btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border: 1px solid #ddd;
            color: #555;
        }

        .social-login-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="register-container mx-auto row g-0">
            <div class="col-md-5 d-none d-md-block register-image-side" style="background-image: url('sp1.png'); background-size: contain; background-repeat: no-repeat; background-position: center;">
            </div>

            <div class="col-md-7 register-form-side">
                <h2 class="text-center mb-1">Daftar Akun Baru</h2>
                <p class="text-center text-muted mb-4">Daftar untuk mulai berbelanja di Casual Steps</p>

                <?php
                // Menampilkan pesan error dari proses_register.php (jika ada)
                if (isset($_SESSION['register_error'])) {
                    echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['register_error']) . '</div>';
                    unset($_SESSION['register_error']); // Hapus setelah ditampilkan
                }
                if (isset($_SESSION['register_success'])) {
                    echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['register_success']) . ' Silakan <a href="login_pelanggan.php">login</a>.</div>';
                    unset($_SESSION['register_success']);
                }
                ?>

                <!-- Div untuk menampilkan error dari Firebase -->
                <div id="firebase-error-message" class="alert alert-danger" role="alert" style="display: none;"></div>

                <form action="proses_register.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">Nama Depan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Masukkan nama depan" required value="<?php echo htmlspecialchars($_SESSION['form_data']['first_name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Nama Belakang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Masukkan nama belakang" required value="<?php echo htmlspecialchars($_SESSION['form_data']['last_name'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Nomor Telepon (Opsional)</label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Masukkan nomor telepon" value="<?php echo htmlspecialchars($_SESSION['form_data']['phone_number'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        <small class="form-text text-muted">Password minimal 8 karakter, mengandung huruf dan angka.</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Konfirmasi password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                        <label class="form-check-label" for="agree_terms">Saya menyetujui <a href="#" target="_blank">Syarat & Ketentuan</a> dan <a href="#" target="_blank">Kebijakan Privasi</a>. <span class="text-danger">*</span></label>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-register">Daftar</button>
                    </div>
                    <p class="text-center text-muted mb-2">atau daftar dengan</p>
                    <div class="text-center mb-4">
                        <a href="#" id="tombol-login-google" class="social-login-btn"><i class="fab fa-google"></i></a>
                    </div>
                    <p class="text-center">Sudah punya akun? <a href="login_pelanggan.php">Masuk sekarang</a></p>
                </form>
                <?php unset($_SESSION['form_data']); // Hapus data form dari session setelah ditampilkan 
                ?>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 CasualSteps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Firebase SDK (pastikan versi sesuai dengan yang Anda gunakan) -->
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="firebase_config.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const googleLoginButton = document.getElementById('tombol-login-google');
            const errorMessageDiv = document.getElementById('firebase-error-message');
            let isLoginInProgress = false; // Flag untuk mencegah eksekusi ganda

            googleLoginButton.addEventListener('click', function(e) {
                e.preventDefault();

                if (isLoginInProgress) {
                    return;
                }

                isLoginInProgress = true;
                googleLoginButton.disabled = true;
                googleLoginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                errorMessageDiv.style.display = 'none';

                var provider = new firebase.auth.GoogleAuthProvider();

                firebase.auth().signInWithPopup(provider)
                    .then((result) => {
                        // Dapatkan ID Token dari pengguna yang berhasil login/daftar
                        return result.user.getIdToken();
                    })
                    .then(function(idToken) {
                        // Kirim token ke server Anda (proses_firebase_login.php sudah menangani ini)
                        return fetch('proses_firebase_login.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                idToken: idToken
                            }),
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Tampilkan popup sukses dengan SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Registrasi via Google berhasil!',
                                text: 'Akun Anda berhasil dibuat. Anda akan diarahkan ke halaman utama.',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url || 'index.php';
                            });
                        } else {
                            // Jika server merespon dengan error
                            errorMessageDiv.textContent = data.message || 'Terjadi kesalahan saat mendaftar/login.';
                            errorMessageDiv.style.display = 'block';
                        }
                    })
                    .catch((error) => {
                        console.error("Firebase login/register error:", error);
                        errorMessageDiv.textContent = 'Gagal mendaftar/login dengan Google: ' + error.message;
                        errorMessageDiv.style.display = 'block';
                    })
                    .finally(() => {
                        // Kembalikan kondisi tombol dan buka kunci
                        isLoginInProgress = false;
                        googleLoginButton.disabled = false;
                        googleLoginButton.innerHTML = '<i class="fab fa-google"></i>';
                    });
            });
        });
    </script>
</body>

</html>
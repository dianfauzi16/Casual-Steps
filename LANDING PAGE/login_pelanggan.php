<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Jika ada parameter 'redirect' di URL, simpan ke session untuk digunakan setelah login berhasil.
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = urldecode($_GET['redirect']); // urldecode untuk memastikan URL kembali normal
}

// Jika pengguna sudah login, arahkan ke halaman lain (misalnya, halaman utama atau akun)
if (isset($_SESSION['user_id'])) { // Asumsi 'user_id' disimpan di session saat login pelanggan
    header('Location: index.php'); // Atau halaman akun pelanggan
    exit;
}

$page_title = "Login Pelanggan";
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
        }

        .login-container {
            max-width: 450px;
            /* Lebar form login standar */
            margin-top: 80px;
            margin-bottom: 80px;
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-login {
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
            text-decoration: none;
        }

        .social-login-btn:hover {
            opacity: 0.8;
        }

        #login-error-message {
            display: none;
            /* Sembunyikan secara default */
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">Produk</a></li>
                        <li class="nav-item"><a class="nav-link active" href="login_pelanggan.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="login-container mx-auto">
            <h2 class="text-center mb-4">Login Pelanggan</h2>

            <?php
            // Menampilkan pesan error atau sukses dari proses_login_pelanggan.php atau register.php
            if (isset($_SESSION['login_error'])) {
                echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            if (isset($_SESSION['register_success'])) { // Pesan dari halaman registrasi
                echo '<div class="alert alert-success" role="alert">' . htmlspecialchars($_SESSION['register_success']) . '</div>';
                unset($_SESSION['register_success']);
            }
            ?>

            <!-- Div untuk menampilkan error dari Firebase -->
            <div id="login-error-message" class="alert alert-danger mt-3" role="alert" style="display: none;"></div>

            <form action="proses_login_pelanggan.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email Anda" required value="<?php echo htmlspecialchars($_SESSION['form_data_login']['email'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password Anda" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                    <label class="form-check-label" for="remember_me">Ingat Saya</label>
                </div>
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-primary btn-login">Login</button>
                </div>
                <p class="text-center"><a href="lupa_password.php">Lupa password?</a></p>
                <hr>
                <p class="text-center text-muted mb-2">atau login dengan</p>
                <div class="text-center mb-4">
                    <a href="#" id="tombol-login-google" class="social-login-btn"><i class="fab fa-google"></i></a>
                </div>
                <p class="text-center">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            </form>
            <?php unset($_SESSION['form_data_login']); // Hapus data form dari session setelah ditampilkan 
            ?>
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
    <!-- File konfigurasi Firebase Anda -->
    <script src="firebase_config.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const googleLoginButton = document.getElementById('tombol-login-google');
            const errorMessageDiv = document.getElementById('login-error-message');
            let isLoginInProgress = false; // <-- KUNCI UTAMA: Flag untuk mencegah eksekusi ganda

            googleLoginButton.addEventListener('click', function(e) {
                e.preventDefault();

                // Jika proses login sudah berjalan, jangan lakukan apa-apa.
                if (isLoginInProgress) {
                    return;
                }

                isLoginInProgress = true; // Kunci proses login
                googleLoginButton.disabled = true;
                // Tampilkan loading spinner untuk feedback ke pengguna
                googleLoginButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                errorMessageDiv.style.display = 'none'; // Sembunyikan pesan error lama

                var provider = new firebase.auth.GoogleAuthProvider();

                firebase.auth().signInWithPopup(provider)
                    .then((result) => {
                        // Pengguna berhasil login dengan Google
                        var user = result.user;

                        // Dapatkan ID Token
                        return user.getIdToken().then(function(idToken) {
                            // Kirim token ke server Anda
                            return fetch('proses_firebase_login.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    idToken: idToken
                                }),
                            });
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Login via Google berhasil!',
                                text: 'Anda akan diarahkan ke halaman utama.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = data.redirect_url || 'index.php';
                            });
                        } else {
                            // Jika server merespon dengan error
                            errorMessageDiv.textContent = data.message || 'Terjadi kesalahan saat login.';
                            errorMessageDiv.style.display = 'block';
                        }
                    })
                    .catch((error) => {
                        // Tangani error dari Firebase
                        console.error("Firebase login error:", error);

                        // Firebase akan melempar error 'auth/cancelled-popup-request' jika ada popup lain yang konflik.
                        // Kita tidak ingin menampilkan error ini ke pengguna karena proses login yang pertama kemungkinan besar sedang berjalan.
                        // Kita hanya akan menampilkan error lain yang relevan (misal: popup ditutup oleh pengguna, masalah jaringan, dll).
                        if (error.code !== 'auth/cancelled-popup-request') {
                            errorMessageDiv.textContent = 'Gagal login dengan Google: ' + error.message;
                            errorMessageDiv.style.display = 'block';
                        }
                    })
                    .finally(() => {
                        // Apapun hasilnya (sukses atau gagal), kembalikan kondisi tombol dan buka kunci
                        isLoginInProgress = false;
                        googleLoginButton.disabled = false;
                        googleLoginButton.innerHTML = '<i class="fab fa-google"></i>';
                    });
            });
        });
    </script>
</body>

</html>
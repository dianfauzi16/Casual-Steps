<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$page_title = "Lupa Password";
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';
$nama_toko = function_exists('get_site_setting') ? (get_site_setting($conn, 'nama_toko') ?: "Casual Steps") : "Casual Steps";
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($nama_toko); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="form-page-bg d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php"><?php echo htmlspecialchars($nama_toko); ?></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavGlobal"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNavGlobal">
                </div>
                <div class="d-flex align-items-center">
                    <a href="login_pelanggan.php" class="nav-link text-dark me-2">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="form-card">
                <div class="text-center mb-4">
                    <h2 class="form-card-title">Lupa Password?</h2>
                    <p class="text-muted">Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
                </div>

                <?php
                if (isset($_SESSION['password_reset_message'])) {
                    echo '<div class="alert alert-' . htmlspecialchars($_SESSION['password_reset_message_type']) . ' alert-dismissible fade show" role="alert">'
                        . htmlspecialchars($_SESSION['password_reset_message'])
                        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    unset($_SESSION['password_reset_message']);
                    unset($_SESSION['password_reset_message_type']);
                }
                ?>

                <form action="proses_lupa_password.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required>
                        </div>
                    </div>
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary btn-lg">Kirim Tautan Reset</button>
                    </div>
                    <div class="text-center">
                        <a href="login_pelanggan.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-0">© <?php echo date("Y"); ?> <?php echo htmlspecialchars($nama_toko); ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
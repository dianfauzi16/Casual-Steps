<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login, jika tidak, arahkan ke halaman login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Anda harus login untuk mengakses halaman ini.";
    header('Location: login_pelanggan.php');
    exit;
}

$page_title = "Ubah Password";
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
    <style>
        .account-info-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">PRODUCT</a></li>
                    </ul>
                </div>
                <div class="d-flex align-items-center">

                    <a href="keranjang.php" class="position-relative me-3 text-dark">
                        <i class="fas fa-shopping-bag"></i>
                        <?php
                        $jumlah_item_di_keranjang = 0;
                        if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $cart_key => $item_data) { // Mengganti $kuantitas_item menjadi $item_data
                                if (is_array($item_data) && isset($item_data['kuantitas'])) {
                                    $jumlah_item_di_keranjang += (int)$item_data['kuantitas']; // Mengakses 'kuantitas'
                                }
                            }
                        }
                        if ($jumlah_item_di_keranjang > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $jumlah_item_di_keranjang; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <?php if (isset($_SESSION['user_loggedin']) && $_SESSION['user_loggedin'] === true): ?>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Akun Saya'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUser">
                                <li><a class="dropdown-item" href="akun_saya.php">Profil Saya</a></li>
                                <li><a class="dropdown-item" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                                <li><a class="dropdown-item active" href="ubah_password.php">Ubah Password</a></li>
                                <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'alamat_saya.php' || basename($_SERVER['PHP_SELF']) == 'tambah_alamat.php' || basename($_SERVER['PHP_SELF']) == 'edit_alamat.php') ? 'active' : ''; ?>" href="alamat_saya.php">Alamat Saya</a></li>
                                <li><a class="dropdown-item" href="logout_pelanggan.php">Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="login_pelanggan.php" class="nav-link text-dark me-2">Login</a>
                        <a href="register.php" class="btn btn-primary btn-sm">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5" style="margin-top: 100px;">
        <h1 class="mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php
        if (isset($_SESSION['password_change_message']) && isset($_SESSION['password_change_type'])) {
            echo '<div class="alert alert-' . htmlspecialchars($_SESSION['password_change_type']) . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['password_change_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['password_change_message']);
            unset($_SESSION['password_change_type']);
        }
        ?>

        <div class="row">
            <div class="col-md-4">
                <div class="list-group mb-4">
                    <a href="akun_saya.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-circle me-2"></i>Profil Saya
                    </a>
                    <a href="riwayat_pesanan.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Riwayat Pesanan
                    </a>
                    <a href="alamat_saya.php" class="list-group-item list-group-item-action <?php echo (basename($_SERVER['PHP_SELF']) == 'alamat_saya.php' || basename($_SERVER['PHP_SELF']) == 'tambah_alamat.php' || basename($_SERVER['PHP_SELF']) == 'edit_alamat.php') ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Saya
                    </a>
                    <a href="ubah_password.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </a>
                    <a href="logout_pelanggan.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card account-info-card">
                    <div class="card-header">
                        Formulir Ubah Password
                    </div>
                    <div class="card-body">
                        <form action="proses_ubah_password.php" method="POST">
                            <div class="mb-3">
                                <label for="old_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <small class="form-text text-muted">Password minimal 8 karakter, mengandung huruf dan angka.</small>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_new_password" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Ubah Password</button>
                            <a href="akun_saya.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
            <hr class="my-4" />
            <div class="text-center">
                <p class="mb-0">© 2025 CasualSteps. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
// Tidak perlu koneksi DB di sini.
?>
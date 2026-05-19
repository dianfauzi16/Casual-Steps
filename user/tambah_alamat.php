<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['login_error'] = "Anda harus login untuk mengakses halaman ini.";
    header('Location: login_pelanggan.php');
    exit;
}

// Tidak memerlukan koneksi DB hanya untuk menampilkan form ini,
// kecuali jika ada data default yang ingin diambil dari DB.
$page_title = "Tambah Alamat Baru";
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
                                <li><a class="dropdown-item active" href="alamat_saya.php">Alamat Saya</a></li>
                                <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                                <li><hr class="dropdown-divider"></li>
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
        <div class="row">
            <div class="col-md-4">
                <div class="list-group mb-4">
                    <a href="akun_saya.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-circle me-2"></i>Profil Saya
                    </a>
                    <a href="riwayat_pesanan.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i>Riwayat Pesanan
                    </a>
                    <a href="alamat_saya.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Saya
                    </a>
                     <a href="ubah_password.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </a>
                    <a href="logout_pelanggan.php" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </div>
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="h3 mb-0"><?php echo htmlspecialchars($page_title); ?></h1>
                    <a href="alamat_saya.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Alamat
                    </a>
                </div>

                <div class="card account-info-card">
                    <div class="card-header">
                        Formulir Alamat Baru
                    </div>
                    <div class="card-body">
                        <?php
                        // Menampilkan pesan error dari proses sebelumnya (jika ada dan disimpan di session)
                        if (isset($_SESSION['alamat_form_error'])) {
                            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_SESSION['alamat_form_error']) . '</div>';
                            unset($_SESSION['alamat_form_error']); 
                        }
                        ?>
                        <form action="proses_tambah_alamat.php" method="POST">
                            <div class="mb-3">
                                <label for="label" class="form-label">Label Alamat <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="label" name="label" placeholder="Contoh: Rumah, Kantor, Apartemen" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['label'] ?? ''); ?>">
                                <small class="form-text text-muted">Beri nama untuk alamat ini agar mudah diingat.</small>
                            </div>
                            <div class="mb-3">
                                <label for="recipient_name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="recipient_name" name="recipient_name" placeholder="Nama lengkap penerima" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['recipient_name'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Nomor Telepon Penerima <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" placeholder="Nomor telepon aktif" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['phone_number'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="street_address" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="street_address" name="street_address" rows="3" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan/desa, kecamatan" required><?php echo htmlspecialchars($_SESSION['form_data_alamat']['street_address'] ?? ''); ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['city'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="province" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="province" name="province" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['province'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" required value="<?php echo htmlspecialchars($_SESSION['form_data_alamat']['postal_code'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="country" class="form-label">Negara</label>
                                    <input type="text" class="form-control" id="country" name="country" value="Indonesia" readonly>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_primary" name="is_primary" value="1">
                                <label class="form-check-label" for="is_primary">Jadikan sebagai alamat utama</label>
                                <small class="form-text text-muted d-block">Jika dicentang, alamat lain yang sebelumnya utama akan diubah menjadi tidak utama.</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Alamat</button>
                            <a href="alamat_saya.php" class="btn btn-secondary">Batal</a>
                        </form>
                        <?php unset($_SESSION['form_data_alamat']); // Hapus data form dari session setelah ditampilkan ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-5 mt-auto">
        <div class="container">
             <hr class="my-4" /><div class="text-center"><p class="mb-0">© 2025 CasualSteps. All rights reserved.</p></div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
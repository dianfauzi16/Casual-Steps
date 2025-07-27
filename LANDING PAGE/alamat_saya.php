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

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Alamat Saya";
$user_id = $_SESSION['user_id'];
$daftar_alamat = [];
$error_message = '';

// 3. Ambil semua alamat untuk pengguna yang sedang login
// Kolom yang diambil: id, recipient_name, phone_number, street_address, city, province, postal_code, country, is_primary, label
$sql_get_addresses = "SELECT id, recipient_name, phone_number, street_address, city, province, postal_code, country, is_primary, label 
                      FROM addresses 
                      WHERE user_id = ? 
                      ORDER BY is_primary DESC, id DESC"; // Alamat utama di atas

if ($stmt = $conn->prepare($sql_get_addresses)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $daftar_alamat[] = $row;
    }
    $stmt->close();
} else {
    $error_message = "Gagal mengambil daftar alamat: " . $conn->error;
}

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
        .address-card {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem; /* Bootstrap's default border-radius */
            margin-bottom: 1rem;
        }
        .address-card .card-body {
            padding: 1.25rem;
        }
        .address-card .primary-badge {
            font-size: 0.75em;
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
        <h1 class="mb-4"><?php echo htmlspecialchars($page_title); ?></h1>

        <?php
        // Menampilkan pesan notifikasi dari proses tambah/edit/hapus alamat (jika ada)
        if (isset($_SESSION['alamat_message']) && isset($_SESSION['alamat_message_type'])) {
            echo '<div class="alert alert-' . htmlspecialchars($_SESSION['alamat_message_type']) . ' alert-dismissible fade show" role="alert">';
            echo htmlspecialchars($_SESSION['alamat_message']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['alamat_message']);
            unset($_SESSION['alamat_message_type']);
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
                    <h4 class="mb-0">Daftar Alamat Tersimpan</h4>
                    <a href="tambah_alamat.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Alamat Baru
                    </a>
                </div>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <?php if (empty($daftar_alamat) && empty($error_message)): ?>
                    <div class="alert alert-info">Anda belum memiliki alamat tersimpan.</div>
                <?php else: ?>
                    <?php foreach ($daftar_alamat as $alamat): ?>
                        <div class="card address-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title mb-1">
                                            <?php echo htmlspecialchars($alamat['label'] ?: 'Alamat'); ?>
                                            <?php if ($alamat['is_primary'] == 1): ?>
                                                <span class="badge bg-success primary-badge ms-2">Utama</span>
                                            <?php endif; ?>
                                        </h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($alamat['recipient_name']); ?></h6>
                                        <p class="card-text small mb-1"><?php echo htmlspecialchars($alamat['phone_number']); ?></p>
                                        <p class="card-text small mb-0">
                                            <?php echo nl2br(htmlspecialchars($alamat['street_address'])); ?><br>
                                            <?php echo htmlspecialchars($alamat['city']); ?>, <?php echo htmlspecialchars($alamat['province']); ?><br>
                                            <?php echo htmlspecialchars($alamat['postal_code']); ?> <?php echo htmlspecialchars($alamat['country']); ?>
                                        </p>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" id="dropdownMenuButton_<?php echo $alamat['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton_<?php echo $alamat['id']; ?>">
                                            <li><a class="dropdown-item" href="edit_alamat.php?id=<?php echo $alamat['id']; ?>"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                            <?php if ($alamat['is_primary'] == 0): ?>
                                                <li><a class="dropdown-item" href="set_alamat_utama_proses.php?id=<?php echo $alamat['id']; ?>" onclick="return confirm('Jadikan alamat ini sebagai alamat utama?')"><i class="fas fa-star me-2"></i>Set Utama</a></li>
                                            <?php endif; ?>
                                            <li><a class="dropdown-item text-danger" href="hapus_alamat_proses.php?id=<?php echo $alamat['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')"><i class="fas fa-trash me-2"></i>Hapus</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
<?php
if (isset($conn)) {
    $conn->close();
}
?>
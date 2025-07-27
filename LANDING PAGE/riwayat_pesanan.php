<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    $_SESSION['login_error'] = "Anda harus login untuk mengakses halaman ini.";
    header('Location: login_pelanggan.php');
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Riwayat Pesanan Saya";
$user_id = $_SESSION['user_id']; // Ambil ID pengguna yang sedang login
$daftar_riwayat_pesanan = [];
$error_message = '';

// 3. Ambil riwayat pesanan untuk pengguna ini dari tabel 'orders'
// Pastikan nama kolom 'user_id', 'id', 'tanggal_pesanan', 'total_price', dan 'status'
// sesuai dengan struktur tabel 'orders' Anda.
$sql_get_orders = "SELECT id, tanggal_pesanan, total_price, status 
                   FROM orders 
                   WHERE user_id = ? 
                   ORDER BY tanggal_pesanan DESC";

if ($stmt = $conn->prepare($sql_get_orders)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $daftar_riwayat_pesanan[] = $row;
        }
    }
    // Tidak perlu pesan error jika tidak ada pesanan, cukup tampilkan pesan di HTML.
    $stmt->close();
} else {
    $error_message = "Terjadi kesalahan saat mengambil riwayat pesanan Anda: " . $conn->error;
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
        .account-info-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

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
                                <li><a class="dropdown-item active" href="riwayat_pesanan.php">Riwayat Pesanan </a></li>
                                <li><a class="dropdown-item <?php echo (basename($_SERVER['PHP_SELF']) == 'ubah_password.php' ? 'active' : ''); ?>" href="ubah_password.php">Ubah Password</a></li>
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

        <div class="row">
            <div class="col-md-4">
                <div class="list-group mb-4">
                    <a href="akun_saya.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-user-circle me-2"></i>Profil Saya
                    </a>
                    <a href="riwayat_pesanan.php" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="fas fa-history me-2"></i>Riwayat Pesanan
                    </a>
                    <a href="alamat_saya.php" class="list-group-item list-group-item-action <?php echo (basename($_SERVER['PHP_SELF']) == 'alamat_saya.php' || basename($_SERVER['PHP_SELF']) == 'tambah_alamat.php' || basename($_SERVER['PHP_SELF']) == 'edit_alamat.php') ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt me-2"></i>Alamat Saya
                    </a>
                    <a href="ubah_password.php" class="list-group-item list-group-item-action <?php echo (basename($_SERVER['PHP_SELF']) == 'ubah_password.php' ? 'active' : ''); ?>">
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
                        Daftar Pesanan Anda
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php elseif (!empty($daftar_riwayat_pesanan)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID Pesanan</th>
                                            <th scope="col">Tanggal</th>
                                            <th scope="col" class="text-end">Total</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($daftar_riwayat_pesanan as $pesanan): ?>
                                            <tr>
                                                <td>#<?php echo htmlspecialchars($pesanan['id']); ?></td>
                                                <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan']))); ?></td>
                                                <td class="text-end">Rp <?php echo number_format($pesanan['total_price'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <?php
                                                    $status = htmlspecialchars($pesanan['status']);
                                                    $badge_class = 'bg-secondary';
                                                    if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                                    else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                                    else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                                    else if ($status == 'Selesai') $badge_class = 'bg-success';
                                                    else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                                                    echo "<span class='badge {$badge_class}'>{$status}</span>";
                                                    ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="detail_pesanan_pelanggan.php?id_order=<?php echo $pesanan['id']; ?>" class="btn btn-sm btn-outline-info mb-1">
                                                        <i class="fas fa-eye"></i> Lihat Detail
                                                    </a>
                                                    <?php if (strtolower($pesanan['status']) == 'selesai'): ?>
                                                        <?php
                                                        // Ambil produk pada pesanan ini
                                                        $sql_items = "SELECT id_produk, nama_produk_saat_pesan FROM order_items WHERE id_order = ?";
                                                        $stmt_items = $conn->prepare($sql_items);
                                                        $stmt_items->bind_param("i", $pesanan['id']);
                                                        $stmt_items->execute();
                                                        $result_items = $stmt_items->get_result();
                                                        while ($item = $result_items->fetch_assoc()):
                                                        ?>
                                                            <a href="detail_produk.php?id=<?php echo $item['id_produk']; ?>" class="btn btn-sm btn-success mb-1">
                                                                <i class="fas fa-star"></i> Berikan Ulasan
                                                            </a>
                                                        <?php endwhile;
                                                        $stmt_items->close(); ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" role="alert">
                                Anda belum memiliki riwayat pesanan. <a href="produk.php">Mulai belanja sekarang!</a>
                            </div>
                        <?php endif; ?>
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
if (isset($conn)) {
    $conn->close();
}
?>
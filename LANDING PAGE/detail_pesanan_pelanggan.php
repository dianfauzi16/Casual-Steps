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

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$page_title = "Detail Pesanan";
$user_id = $_SESSION['user_id']; // ID pelanggan yang sedang login
$order_details = null;
$order_items = [];
$error_message = '';
$id_order_dari_url = null;

// 3. Ambil id_order dari URL dan validasi
if (isset($_GET['id_order']) && filter_var($_GET['id_order'], FILTER_VALIDATE_INT)) {
    $id_order_dari_url = (int)$_GET['id_order'];

    // 4. Ambil data utama pesanan dari tabel 'orders'
    //    Pastikan pesanan ini milik pengguna yang sedang login
    //    Sesuaikan nama kolom 'id', 'user_id', 'total_price', 'status' jika berbeda di tabel Anda
    $sql_order = "SELECT id, nama_pelanggan, email_pelanggan, telepon_pelanggan, 
                         alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, 
                         total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan 
                  FROM orders 
                  WHERE id = ? AND user_id = ?";
    
    if ($stmt_order = $conn->prepare($sql_order)) {
        $stmt_order->bind_param("ii", $id_order_dari_url, $user_id);
        $stmt_order->execute();
        $result_order = $stmt_order->get_result();
        if ($result_order->num_rows == 1) {
            $order_details = $result_order->fetch_assoc();
            $page_title = "Detail Pesanan #" . htmlspecialchars($order_details['id']);
        } else {
            $error_message = "Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.";
        }
        $stmt_order->close();
    } else {
        $error_message = "Gagal mempersiapkan statement untuk detail pesanan: " . $conn->error;
    }

    // 5. Jika data utama pesanan ditemukan, ambil item-item produknya dari 'order_items'
    //    Sesuaikan nama kolom 'id_order', 'id_produk', 'quantity', 'size', 'harga_satuan_saat_pesan', 'subtotal_item' jika berbeda
    if ($order_details && empty($error_message)) {
        $sql_items = "SELECT oi.id_produk, oi.nama_produk_saat_pesan, oi.quantity, oi.size, 
                             oi.harga_satuan_saat_pesan, oi.subtotal_item, p.image AS gambar_produk_terkini
                      FROM order_items oi
                      LEFT JOIN product p ON oi.id_produk = p.id 
                      WHERE oi.id_order = ?";
        if ($stmt_items = $conn->prepare($sql_items)) {
            $stmt_items->bind_param("i", $id_order_dari_url);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            while ($row_item = $result_items->fetch_assoc()) {
                $order_items[] = $row_item;
            }
            $stmt_items->close();
            if (empty($order_items) && $result_order->num_rows == 1) { // Pesanan ada tapi tidak ada item (jarang terjadi)
                 $error_message = "Tidak ada item ditemukan untuk pesanan ini.";
            }
        } else {
            $error_message = "Gagal mengambil item pesanan: " . $conn->error;
        }
    }

} else {
    $error_message = "ID Pesanan tidak valid atau tidak disediakan.";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($page_title); ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .order-summary-card .card-header { background-color: #f8f9fa; font-weight: 600;}
        .address-details p, .customer-details p { margin-bottom: 0.5rem; }
        .item-table th { background-color: #e9ecef; }
        .item-table img { width: 60px; height: 60px; object-fit: cover; margin-right: 10px; }
        .breadcrumb-item a { text-decoration: none; color: #0d6efd; }
        .breadcrumb-item.active { color: #6c757d; }
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
                                <li><a class="dropdown-item active" href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="mb-0"><?php echo htmlspecialchars($page_title); ?></h1>
            <a href="riwayat_pesanan.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Kembali ke Riwayat Pesanan
            </a>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif ($order_details && !empty($order_items)): ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="akun_saya.php">Akun Saya</a></li>
                    <li class="breadcrumb-item"><a href="riwayat_pesanan.php">Riwayat Pesanan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pesanan #<?php echo htmlspecialchars($order_details['id']); ?></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="card shadow-sm order-summary-card mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Informasi Pesanan</span>
                                <span class="badge <?php 
                                    $status = htmlspecialchars($order_details['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                    else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                    else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                    else if ($status == 'Selesai') $badge_class = 'bg-success';
                                    else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                                    echo $badge_class;
                                ?> fs-6"><?php echo $status; ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 customer-details mb-3 mb-md-0">
                                    <h5>Detail Penerima:</h5>
                                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order_details['nama_pelanggan']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email_pelanggan'] ?: '-'); ?></p>
                                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order_details['telepon_pelanggan']); ?></p>
                                </div>
                                <div class="col-md-6 address-details">
                                    <h5>Alamat Pengiriman:</h5>
                                    <p><?php echo nl2br(htmlspecialchars($order_details['alamat_pengiriman_lengkap'])); ?></p>
                                    <p><?php echo htmlspecialchars($order_details['kota_pengiriman']); ?>, <?php echo htmlspecialchars($order_details['kode_pos_pengiriman']); ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Tanggal Pesan:</strong> <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($order_details['tanggal_pesanan']))); ?></p>
                                    <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order_details['metode_pembayaran']); ?></p>
                                </div>
                                <div class="col-md-6">
                                     <?php if (!empty($order_details['catatan_pelanggan'])): ?>
                                        <p><strong>Catatan dari Anda:</strong> <?php echo nl2br(htmlspecialchars($order_details['catatan_pelanggan'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">
                            Item yang Dipesan
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0 item-table">
                                    <thead>
                                        <tr>
                                            <th colspan="2">Produk</th>
                                            <th class="text-center">Kuantitas</th>
                                            <th>Ukuran</th>
                                            <th class="text-end">Harga Satuan</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td style="width: 70px;">
                                                <?php
                                                $image_name_item = htmlspecialchars($item['gambar_produk_terkini'] ?? '');
                                                $image_path_item = "../ADMIN MENU/uploads/produk/" . $image_name_item;
                                                $placeholder_path_item = "../ADMIN MENU/placeholder_image.png";

                                                if (!empty($image_name_item) && file_exists($image_path_item)):
                                                ?>
                                                    <img src="<?php echo $image_path_item; ?>" alt="<?php echo htmlspecialchars($item['nama_produk_saat_pesan']); ?>" class="img-thumbnail">
                                                <?php else: ?>
                                                    <img src="<?php echo $placeholder_path_item; ?>" alt="Gambar tidak tersedia" class="img-thumbnail">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['nama_produk_saat_pesan']); ?></td>
                                            <td class="text-center"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                            <td><?php echo htmlspecialchars($item['size'] ?: '-'); ?></td>
                                            <td class="text-end">Rp <?php echo number_format($item['harga_satuan_saat_pesan'], 0, ',', '.'); ?></td>
                                            <td class="text-end">Rp <?php echo number_format($item['subtotal_item'], 0, ',', '.'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="5" class="text-end border-top-2 pt-3 fs-5">Total Keseluruhan:</td>
                                            <td class="text-end border-top-2 pt-3 fs-5">Rp <?php echo number_format($order_details['total_price'], 0, ',', '.'); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($order_details['status'] == 'Menunggu Pembayaran' || $order_details['status'] == 'pending'): ?>
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h5 class="card-title">Menunggu Pembayaran</h5>
                            <p>Silakan lakukan pembayaran sejumlah <strong>Rp <?php echo number_format($order_details['total_price'], 0, ',', '.'); ?></strong> ke salah satu rekening kami.</p>
                            <p>Pesanan Anda akan diproses setelah pembayaran dikonfirmasi.</p>
                            </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php elseif(empty($error_message)): ?>
            <div class="alert alert-warning text-center" role="alert">
                Detail pesanan tidak dapat ditampilkan.
            </div>
        <?php endif; ?>
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
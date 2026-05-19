<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$page_title = "Detail Pelanggan";
$user_details = null;
$user_orders = []; // Untuk menyimpan pesanan pelanggan
$user_addresses = []; // Untuk menyimpan alamat pelanggan
$error_message = '';
$id_user_dari_url = null;

if (isset($_GET['id_user']) && filter_var($_GET['id_user'], FILTER_VALIDATE_INT)) {
    $id_user_dari_url = (int)$_GET['id_user'];

    // 1. Ambil data utama pelanggan dari tabel 'users'
    $sql_user = "SELECT id, name, email, phone_number FROM users WHERE id = ?";
    if ($stmt_user = $conn->prepare($sql_user)) {
        $stmt_user->bind_param("i", $id_user_dari_url);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows == 1) {
            $user_details = $result_user->fetch_assoc();
            $page_title = "Detail Pelanggan: " . htmlspecialchars($user_details['name']);
        } else {
            $error_message = "Pelanggan tidak ditemukan.";
        }
        $stmt_user->close();
    } else {
        $error_message = "Gagal mempersiapkan statement untuk detail pelanggan: " . $conn->error;
    }

    // 2. Jika data pelanggan ditemukan, ambil riwayat pesanannya dari tabel 'orders'
    if ($user_details && empty($error_message)) {
        // Pastikan nama kolom di tabel 'orders' Anda sesuai (id, tanggal_pesanan, total_price, status)
        $sql_user_orders = "SELECT id, tanggal_pesanan, total_price, status 
                            FROM orders 
                            WHERE user_id = ? 
                            ORDER BY tanggal_pesanan DESC";
        if ($stmt_orders = $conn->prepare($sql_user_orders)) {
            $stmt_orders->bind_param("i", $id_user_dari_url);
            $stmt_orders->execute();
            $result_orders = $stmt_orders->get_result();
            while ($row_order = $result_orders->fetch_assoc()) {
                $user_orders[] = $row_order;
            }
            $stmt_orders->close();
        } else {
            $error_message .= " Gagal mengambil riwayat pesanan pelanggan: " . $conn->error;
        }

        // 3. Ambil daftar alamat pelanggan dari tabel 'addresses'
        // Pastikan nama kolom di tabel 'addresses' Anda sesuai
        $sql_user_addresses = "SELECT id, label, recipient_name, phone_number, street_address, city, province, postal_code, country, is_primary 
                               FROM addresses 
                               WHERE user_id = ? 
                               ORDER BY is_primary DESC, id DESC";
        if ($stmt_addresses = $conn->prepare($sql_user_addresses)) {
            $stmt_addresses->bind_param("i", $id_user_dari_url);
            $stmt_addresses->execute();
            $result_addresses = $stmt_addresses->get_result();
            while ($row_address = $result_addresses->fetch_assoc()) {
                $user_addresses[] = $row_address;
            }
            $stmt_addresses->close();
        } else {
             $error_message .= " Gagal mengambil alamat pelanggan: " . $conn->error;
        }
    }

} else {
    $error_message = "ID Pelanggan tidak valid atau tidak disediakan.";
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .detail-section .card-header { background-color: #f8f9fa; border-bottom: 1px solid #dee2e6 !important;}
        .table-sm th, .table-sm td { padding: 0.5rem; font-size: 0.9rem;}
        .address-item { border-bottom: 1px solid #eee; padding-bottom: 0.75rem; margin-bottom: 0.75rem; }
        .address-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0;}
        .address-item p { margin-bottom: 0.25rem;}
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php" class="active"><i class="fa fa-users me-2"></i>Pelanggan</a>
        <a href="admin_kategori.php"><i class="fa fa-tags me-2"></i>Kategori</a>
        <a href="admin_laporan.php"><i class="fa fa-chart-line me-2"></i>Laporan</a>
        <a href="admin_pengaturan.php"><i class="fa fa-cog me-2"></i>Pengaturan</a>
        <a href="admin-pesan-kontak.php"><i class="fa fa-envelope me-2"></i>Kontak Masuk</a>
         <a href="admin_promo.php"><i class="fa fa-gift me-2"></i>Promo</a>
        <hr class="text-secondary">
        <a href="logout.php"><i class="fa fa-sign-out-alt me-2"></i>Logout</a>
    </div>

    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3"><?php echo htmlspecialchars($page_title); ?></h1>
                <a href="admin_pelanggan.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Pelanggan
                </a>
            </div>

            <?php if (!empty($error_message) && !$user_details): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php elseif ($user_details): ?>
                <div class="card detail-section shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profil Pelanggan</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>ID Pelanggan:</strong> <?php echo htmlspecialchars($user_details['id']); ?></p>
                        <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($user_details['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user_details['email']); ?></p>
                        <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($user_details['phone_number'] ?: '-'); ?></p>
                    </div>
                </div>

                <div class="card detail-section shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Pesanan (Total: <?php echo count($user_orders); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($user_orders)): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID Pesanan</th>
                                            <th>Tanggal</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($user_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                            <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($order['tanggal_pesanan']))); ?></td>
                                            <td class="text-end">Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <?php 
                                                $status = htmlspecialchars($order['status']);
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
                                                <a href="admin_detail_pesanan.php?id_order=<?php echo $order['id']; ?>" class="btn btn-outline-info btn-xs py-0 px-1" title="Lihat Detail Pesanan Ini">
                                                    <i class="fas fa-eye fa-xs"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Pelanggan ini belum memiliki riwayat pesanan.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card detail-section shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Alamat Tersimpan (Total: <?php echo count($user_addresses); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($user_addresses)): ?>
                            <?php foreach ($user_addresses as $address): ?>
                                <div class="address-item">
                                    <h6>
                                        <?php echo htmlspecialchars($address['label'] ?: 'Alamat'); ?>
                                        <?php if ($address['is_primary'] == 1): ?>
                                            <span class="badge bg-success ms-2" style="font-size: 0.7em;">Utama</span>
                                        <?php endif; ?>
                                    </h6>
                                    <p class="mb-0">
                                        <strong>Penerima:</strong> <?php echo htmlspecialchars($address['recipient_name']); ?> <br>
                                        <strong>Telepon:</strong> <?php echo htmlspecialchars($address['phone_number']); ?>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <?php echo nl2br(htmlspecialchars($address['street_address'])); ?><br>
                                        <?php echo htmlspecialchars($address['city']); ?>, <?php echo htmlspecialchars($address['province']); ?> <?php echo htmlspecialchars($address['postal_code']); ?>
                                        (<?php echo htmlspecialchars($address['country']); ?>)
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Pelanggan ini belum memiliki alamat tersimpan.</p>
                        <?php endif; ?>
                         <?php if (!empty($error_message) && $user_details && (strpos($error_message, "Pelanggan tidak ditemukan") === false )): ?>
                            <div class="alert alert-warning mt-3">Sebagian data mungkin gagal dimuat: <?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>
                    </div>
                </div>

            <?php elseif(empty($error_message)): ?>
                <div class="alert alert-warning">Data pelanggan tidak dapat ditampilkan.</div>
            <?php endif; ?>
        </div>
    </div> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
if (isset($conn)) {
    $conn->close();
}
?>
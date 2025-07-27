<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$page_title = "Detail Pesanan";
$order_details = null;
$order_items = [];
$error_message = '';
$id_order_dari_url = null;

if (isset($_GET['id_order']) && filter_var($_GET['id_order'], FILTER_VALIDATE_INT)) {
    $id_order_dari_url = $_GET['id_order'];

    // 1. Ambil data utama pesanan dari tabel 'orders'
    // Sesuaikan nama kolom jika berbeda (misal, total_price menjadi total_harga_pesanan, status menjadi status_pesanan, dll.)
    $sql_order = "SELECT id, user_id, nama_pelanggan, email_pelanggan, telepon_pelanggan, 
                         alamat_pengiriman_lengkap, kota_pengiriman, kode_pos_pengiriman, 
                         total_price, metode_pembayaran, status, catatan_pelanggan, tanggal_pesanan 
                  FROM orders 
                  WHERE id = ?";

    if ($stmt_order = $conn->prepare($sql_order)) {
        $stmt_order->bind_param("i", $id_order_dari_url);
        $stmt_order->execute();
        $result_order = $stmt_order->get_result();
        if ($result_order->num_rows == 1) {
            $order_details = $result_order->fetch_assoc();
            $page_title = "Detail Pesanan #" . htmlspecialchars($order_details['id']);
        } else {
            $error_message = "Pesanan tidak ditemukan.";
        }
        $stmt_order->close();
    } else {
        $error_message = "Gagal mempersiapkan statement untuk detail pesanan: " . $conn->error;
    }

    // 2. Jika data utama pesanan ditemukan, ambil item-item produknya dari 'order_items'
    if ($order_details && empty($error_message)) {
        // Sesuaikan nama kolom jika berbeda (misal, id_produk, quantity, harga_satuan_saat_pesan, subtotal_item)
        $sql_items = "SELECT id_produk, nama_produk_saat_pesan, quantity, size, harga_satuan_saat_pesan, subtotal_item 
                      FROM order_items 
                      WHERE id_order = ?";
        if ($stmt_items = $conn->prepare($sql_items)) {
            $stmt_items->bind_param("i", $id_order_dari_url);
            $stmt_items->execute();
            $result_items = $stmt_items->get_result();
            while ($row_item = $result_items->fetch_assoc()) {
                $order_items[] = $row_item;
            }
            $stmt_items->close();
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
    <style>
        .order-details-card .card-header {
            background-color: #f8f9fa;
        }

        .item-summary img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h3 class="text-center my-3 fw-bold">CASUAL STEPS</h3>
        <a href="admin_dashboard.php"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
        <a href="admin_produk.php"><i class="fa fa-box me-2"></i>Produk</a>
        <a href="admin_pesanan.php" class="active"><i class="fa fa-file-invoice me-2"></i>Pesanan</a>
        <a href="admin_pelanggan.php"><i class="fa fa-users me-2"></i>Pelanggan</a>
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
                <a href="admin_pesanan.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Pesanan
                </a>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php elseif ($order_details && !empty($order_items)): ?>
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card order-details-card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Nama:</strong> <?php echo htmlspecialchars($order_details['nama_pelanggan']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email_pelanggan'] ?: '-'); ?></p>
                                <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order_details['telepon_pelanggan']); ?></p>
                                <?php if ($order_details['user_id']): ?>
                                    <p><strong>ID Pelanggan:</strong> <?php echo htmlspecialchars($order_details['user_id']); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card order-details-card shadow-sm mt-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Alamat Pengiriman</h5>
                            </div>
                            <div class="card-body">
                                <p><?php echo nl2br(htmlspecialchars($order_details['alamat_pengiriman_lengkap'])); ?></p>
                                <p><?php echo htmlspecialchars($order_details['kota_pengiriman']); ?>, <?php echo htmlspecialchars($order_details['kode_pos_pengiriman']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card order-details-card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Tanggal Pesan:</strong> <?php echo htmlspecialchars(date('d M Y, H:i', strtotime($order_details['tanggal_pesanan']))); ?></p>
                                <p><strong>Total Harga:</strong> <span class="fw-bold">Rp <?php echo number_format($order_details['total_price'], 0, ',', '.'); ?></span></p>
                                <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($order_details['metode_pembayaran']); ?></p>
                                <p><strong>Status Pesanan:</strong>
                                    <?php
                                    $status = htmlspecialchars($order_details['status']);
                                    $badge_class = 'bg-secondary'; // Default
                                    if ($status == 'Menunggu Pembayaran' || $status == 'pending') $badge_class = 'bg-warning text-dark';
                                    else if ($status == 'Diproses' || $status == 'paid') $badge_class = 'bg-info text-dark';
                                    else if ($status == 'Dikirim' || $status == 'shipped') $badge_class = 'bg-primary';
                                    else if ($status == 'Selesai') $badge_class = 'bg-success';
                                    else if ($status == 'Dibatalkan' || $status == 'cancelled') $badge_class = 'bg-danger';
                                    echo "<span class='badge {$badge_class} fs-6'>{$status}</span>";
                                    ?>
                                </p>
                                <?php if (!empty($order_details['catatan_pelanggan'])): ?>
                                    <p><strong>Catatan Pelanggan:</strong> <?php echo nl2br(htmlspecialchars($order_details['catatan_pelanggan'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <h5 class="mb-2">Update Status Pesanan</h5>
                                <form action="update_status_pesanan_proses.php" method="POST">
                                    <input type="hidden" name="id_order" value="<?php echo $order_details['id']; ?>">
                                    <div class="input-group">
                                        <select name="status_baru" class="form-select">
                                            <option value="Menunggu Pembayaran" <?php if ($order_details['status'] == 'Menunggu Pembayaran' || $order_details['status'] == 'pending') echo 'selected'; ?>>Menunggu Pembayaran</option>
                                            <option value="Diproses" <?php if ($order_details['status'] == 'Diproses' || $order_details['status'] == 'paid') echo 'selected'; ?>>Diproses</option>
                                            <option value="Dikirim" <?php if ($order_details['status'] == 'Dikirim' || $order_details['status'] == 'shipped') echo 'selected'; ?>>Dikirim</option>
                                            <option value="Selesai" <?php if ($order_details['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                            <option value="Dibatalkan" <?php if ($order_details['status'] == 'Dibatalkan' || $order_details['status'] == 'cancelled') echo 'selected'; ?>>Dibatalkan</option>
                                        </select>
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-boxes me-2"></i>Item Produk Dipesan</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Produk</th>
                                                <th class="text-center">Kuantitas</th>
                                                <th>Ukuran</th>
                                                <th class="text-end">Harga Satuan</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order_items as $item): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['nama_produk_saat_pesan']); ?></td>
                                                    <td class="text-center"><?php echo htmlspecialchars($item['quantity']); // Sesuai nama kolom Anda 
                                                                            ?></td>
                                                    <td><?php echo htmlspecialchars($item['size'] ?: '-'); // Sesuai nama kolom Anda 
                                                        ?></td>
                                                    <td class="text-end">Rp <?php echo number_format($item['harga_satuan_saat_pesan'], 0, ',', '.'); ?></td>
                                                    <td class="text-end">Rp <?php echo number_format($item['subtotal_item'], 0, ',', '.'); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif (empty($error_message)): // Jika $order_details null tapi tidak ada error spesifik 
            ?>
                <div class="alert alert-warning">Detail pesanan tidak dapat ditampilkan.</div>
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
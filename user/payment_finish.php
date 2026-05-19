<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$order_id = htmlspecialchars($_GET['order_id'] ?? 'Tidak diketahui');
$status = htmlspecialchars($_GET['status'] ?? 'Tidak diketahui');

$page_title = "Status Pembayaran";
$message = "Status pembayaran Anda sedang diproses.";
$icon = "fa-hourglass-half";
$color = "info";

if ($status === 'success') {
    $page_title = "Pembayaran Berhasil";
    $message = "Terima kasih! Pembayaran Anda telah berhasil kami terima. Pesanan Anda akan segera kami proses.";
    $icon = "fa-check-circle";
    $color = "success";
    // Bersihkan keranjang setelah pembayaran sukses
    unset($_SESSION['keranjang']);
    unset($_SESSION['direct_checkout_item']);
} elseif ($status === 'pending') {
    $page_title = "Menunggu Pembayaran";
    $message = "Pesanan Anda telah kami terima. Silakan selesaikan pembayaran Anda.";
    $icon = "fa-clock";
    $color = "warning";
} elseif ($status === 'error') {
    $page_title = "Pembayaran Gagal";
    $message = "Maaf, terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi atau hubungi customer service kami.";
    $icon = "fa-times-circle";
    $color = "danger";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Casual Steps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .status-container { max-width: 600px; margin-top: 100px; text-align: center; }
        .status-icon { font-size: 4rem; margin-bottom: 20px; }
    </style>
</head>
<body>
    <main class="container">
        <div class="status-container mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <i class="fas <?php echo $icon; ?> status-icon text-<?php echo $color; ?>"></i>
                    <h2 class="mb-3"><?php echo $page_title; ?></h2>
                    <p class="lead"><?php echo $message; ?></p>
                    <p>Nomor Pesanan Anda: <strong><?php echo $order_id; ?></strong></p>
                    <a href="riwayat_pesanan.php" class="btn btn-primary mt-4">Lihat Riwayat Pesanan</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

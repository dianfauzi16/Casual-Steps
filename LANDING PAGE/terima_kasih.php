<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Sertakan koneksi database jika Anda ingin mengambil detail pesanan lagi,
// namun untuk halaman terima kasih sederhana, ID pesanan dari session mungkin cukup.
// require_once __DIR__ . '/../ADMIN MENU/db_connect.php'; 

$page_title = "Pesanan Diterima";
$id_order_sukses = null;
$pesan_konfirmasi = "Terima kasih! Pesanan Anda telah berhasil kami terima.";

if (isset($_SESSION['id_order_sukses'])) {
    $id_order_sukses = $_SESSION['id_order_sukses'];
    // Anda bisa menambahkan query di sini untuk mengambil detail pesanan berdasarkan $id_order_sukses jika diperlukan
    // Misalnya, untuk menampilkan ringkasan pesanan lagi atau nama pelanggan.
    // Untuk saat ini, kita hanya tampilkan ID ordernya.
    
    // Hapus session id_order_sukses agar tidak muncul lagi jika halaman di-refresh
    unset($_SESSION['id_order_sukses']); 
} else {
    // Jika tidak ada ID order sukses di session, mungkin pengguna mengakses halaman ini secara langsung
    // Arahkan ke halaman utama atau tampilkan pesan umum
    $pesan_konfirmasi = "Terima kasih atas kunjungan Anda.";
    // header('Location: index.php'); // Opsional: redirect jika tidak ada order ID
    // exit;
}

// Jika ada pesan notifikasi lain dari proses sebelumnya (jarang diperlukan di sini)
$notifikasi_lain = $_SESSION['pesan_notifikasi'] ?? null;
$tipe_notifikasi_lain = $_SESSION['tipe_notifikasi'] ?? null;
if ($notifikasi_lain) {
    unset($_SESSION['pesan_notifikasi']);
    unset($_SESSION['tipe_notifikasi']);
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
</head>
<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">CASUAL STEPS</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
                        <li class="nav-item"><a class="nav-link" href="about.php">ABOUT</a></li>
                        <li class="nav-item"><a class="nav-link" href="produk.php">PRODUCT</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php#contact1">CONTACT</a></li>
                    </ul>
                </div>
                <div class="d-flex">
                    <a href="#" class="me-3"><i class="fas fa-search"></i></a>
                    <a href="keranjang.php" class="position-relative me-3"> 
                        <i class="fas fa-shopping-bag"></i>
                        <?php 
                        // Logika untuk menampilkan jumlah item keranjang (session keranjang sudah di-unset)
                        // Jadi, di halaman ini keranjang akan selalu 0.
                        // Jika Anda ingin menampilkan jumlah sebelum di-unset, sessionnya harus di-unset setelah halaman ini.
                        // Tapi umumnya keranjang sudah kosong saat sampai di "terima kasih".
                        $jumlah_item_di_keranjang = 0; 
                        if (isset($_SESSION['keranjang_sebelum_checkout']) && is_array($_SESSION['keranjang_sebelum_checkout'])) {
                             foreach ($_SESSION['keranjang_sebelum_checkout'] as $kuantitas_item) {
                                 $jumlah_item_di_keranjang += $kuantitas_item;
                             }
                             // unset($_SESSION['keranjang_sebelum_checkout']); // Hapus jika sudah ditampilkan
                        }
                        if ($jumlah_item_di_keranjang > 0): 
                        ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?php echo $jumlah_item_di_keranjang; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main class="container py-5 text-center" style="margin-top: 100px; margin-bottom: 50px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <?php if ($notifikasi_lain && $tipe_notifikasi_lain === 'success'): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($notifikasi_lain); ?>
                    </div>
                <?php elseif($notifikasi_lain && $tipe_notifikasi_lain === 'danger'): ?>
                     <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($notifikasi_lain); ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-5">
                        <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                        <h1 class="display-5 mb-3"><?php echo htmlspecialchars($page_title); ?></h1>
                        <p class="lead"><?php echo htmlspecialchars($pesan_konfirmasi); ?></p>
                        
                        <?php if ($id_order_sukses): ?>
                            <p>Nomor Pesanan Anda adalah: <strong>#<?php echo htmlspecialchars($id_order_sukses); ?></strong></p>
                            <hr>
                            <h5 class="mt-4 mb-3">Instruksi Pembayaran (Transfer Bank)</h5>
                            <p class="text-start">Silakan lakukan pembayaran sejumlah total pesanan Anda ke salah satu rekening berikut:</p>
                            <ul class="list-unstyled text-start ps-3">
                                <li><strong>Bank ABC:</strong> 123-456-7890 a.n. Casual Steps</li>
                                <li><strong>Bank XYZ:</strong> 987-654-3210 a.n. Casual Steps</li>
                            </ul>
                            <p class="text-start">Mohon segera lakukan pembayaran dan konfirmasi agar pesanan Anda dapat kami proses. (Fitur konfirmasi pembayaran akan ditambahkan kemudian).</p>
                            <p class="text-start"><small class="text-muted">Simpan nomor pesanan Anda untuk referensi.</small></p>
                        <?php endif; ?>

                        <div class="mt-5">
                            <a href="produk.php" class="btn btn-outline-primary me-2"><i class="fas fa-shopping-bag me-2"></i> Lanjut Belanja</a>
                            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-home me-2"></i> Kembali ke Halaman Utama</a>
                            </div>
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
<?php
// Tidak perlu tutup koneksi $conn di sini jika tidak dibuka di awal file ini.
// if (isset($conn) && $conn instanceof mysqli) { 
//     $conn->close();
// }
?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Pastikan pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    // Simpan URL tujuan setelah login
    // Ambil URL halaman produk dari HTTP_REFERER atau kirim sebagai hidden input jika perlu
    $redirect_url_after_login = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    if (isset($_POST['id_produk'])) { // Jika ada ID produk, coba buat URL detail produk
        $redirect_url_after_login = 'detail_produk.php?id=' . $_POST['id_produk'];
    }
    $_SESSION['redirect_after_login'] = $redirect_url_after_login . (strpos($redirect_url_after_login, '?') ? '&' : '?') . 'action=buy_now'; // Tambahkan parameter action

    $_SESSION['login_error'] = "Anda harus login untuk melanjutkan pembelian.";
    header('Location: login_pelanggan.php?redirect=' . urlencode($redirect_url_after_login));
    exit;
}




if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produk'])) {
    $id_produk = filter_var($_POST['id_produk'], FILTER_VALIDATE_INT);
    $kuantitas = isset($_POST['kuantitas']) && filter_var($_POST['kuantitas'], FILTER_VALIDATE_INT) && $_POST['kuantitas'] > 0 ? (int)$_POST['kuantitas'] : 1;
    $ukuran_terpilih = trim($_POST['ukuran_terpilih'] ?? '');

    if ($id_produk) {
        // (Sangat Penting) Lakukan pengecekan stok ke database di sini sebelum menambahkan ke keranjang
        require_once __DIR__ . '/../ADMIN MENU/db_connect.php'; // Jika belum di-include
        $sql_check_stock = "SELECT stock FROM product WHERE id = ?";
        if ($stmt_stock = $conn->prepare($sql_check_stock)) {
            $stmt_stock->bind_param("i", $id_produk);
            $stmt_stock->execute();
            $result_stock = $stmt_stock->get_result();
            if ($prod_db = $result_stock->fetch_assoc()) {
                if ($kuantitas > $prod_db['stock']) {
                    $_SESSION['pesan_notifikasi_produk'] = "Maaf, stok produk tidak mencukupi untuk jumlah yang Anda minta.";
                    $_SESSION['tipe_notifikasi_produk'] = "warning";
                    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'detail_produk.php?id=' . $id_produk));
                    exit;
                }
            } else {
                $_SESSION['pesan_notifikasi_produk'] = "Produk tidak ditemukan.";
                $_SESSION['tipe_notifikasi_produk'] = "danger";
                header("Location: produk.php");
                exit;
            }
            $stmt_stock->close();
        }
        if (isset($conn)) $conn->close(); // Tutup koneksi jika dibuka di sini


        // Tambahkan atau update item di keranjang
        // Untuk "Beli Sekarang", kita bisa memilih untuk selalu menimpa item yang sama di keranjang
        // atau membuat entri baru. Untuk kesederhanaan, kita akan menimpa/menambahkan seperti biasa.
        // Jika Anda ingin item "Beli Sekarang" terpisah, Anda bisa menggunakan session key yang berbeda,
        // misal $_SESSION['buy_now_item'].

        // Simpan item untuk direct checkout ke session khusus
        $_SESSION['direct_checkout_item'] = [
            'id_produk' => $id_produk,
            'ukuran' => $ukuran_terpilih,
            'kuantitas' => $kuantitas
        ];

        // Langsung arahkan ke halaman checkout
         header("Location: checkout.php?mode=direct");
        exit;
    } else {
        // ID Produk tidak valid
        $_SESSION['pesan_notifikasi_produk'] = "ID Produk tidak valid.";
        $_SESSION['tipe_notifikasi_produk'] = "danger";
    }
} else {
    // Metode request tidak valid atau ID produk tidak ada
    $_SESSION['pesan_notifikasi_produk'] = "Aksi tidak valid.";
    $_SESSION['tipe_notifikasi_produk'] = "danger";
}

// Jika terjadi error sebelum redirect ke checkout, kembali ke halaman produk
$url_sebelumnya = $_SERVER['HTTP_REFERER'] ?? 'produk.php';
if (isset($_POST['id_produk'])) {
    $url_sebelumnya = 'detail_produk.php?id=' . $_POST['id_produk'];
}
header("Location: " . $url_sebelumnya);
exit;

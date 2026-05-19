<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan ada parameter cart_item_key dan session keranjang ada
if (isset($_GET['cart_item_key']) && isset($_SESSION['keranjang'])) {
    $cart_item_key_hapus = $_GET['cart_item_key']; // cart_item_key adalah string

    // Hapus item dari array keranjang jika kunci item ada di keranjang
    if (isset($_SESSION['keranjang'][$cart_item_key_hapus])) {
        unset($_SESSION['keranjang'][$cart_item_key_hapus]);
        // (Opsional) Anda bisa menambahkan pesan sukses ke session jika mau
        // $_SESSION['pesan_notifikasi_keranjang'] = "Item berhasil dihapus dari keranjang.";
        // $_SESSION['tipe_notifikasi_keranjang'] = "success";
    } else {
        // (Opsional) Pesan jika item tidak ditemukan di keranjang
        // $_SESSION['pesan_notifikasi_keranjang'] = "Item tidak ditemukan di keranjang untuk dihapus.";
        // $_SESSION['tipe_notifikasi_keranjang'] = "warning";
    }
} else {
    // (Opsional) Pesan jika aksi tidak valid atau keranjang kosong
    // $_SESSION['pesan_notifikasi_keranjang'] = "Aksi penghapusan tidak valid atau keranjang kosong.";
    // $_SESSION['tipe_notifikasi_keranjang'] = "danger";
}

// Arahkan kembali ke halaman keranjang
header("Location: keranjang.php");
exit;
?>
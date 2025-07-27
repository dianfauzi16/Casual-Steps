<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// (Opsional) Sertakan koneksi DB jika Anda ingin melakukan pengecekan stok
// require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Pastikan ada parameter cart_item_key, kuantitas, dan session keranjang ada
if ($_SERVER["REQUEST_METHOD"] == "POST" && 
    isset($_POST['cart_item_key']) && 
    isset($_POST['kuantitas']) && 
    isset($_SESSION['keranjang'])) {

    $cart_item_key = $_POST['cart_item_key']; // Ini adalah string, contoh: "PRODUCTID_SIZE" atau "PRODUCTID_"
    $kuantitas_baru = filter_var($_POST['kuantitas'], FILTER_VALIDATE_INT);

    // Validasi dasar
    if ($kuantitas_baru !== false && $kuantitas_baru >= 0) { // Izinkan 0 untuk menghapus
        // Cek apakah item dengan kunci tersebut ada di keranjang
        if (isset($_SESSION['keranjang'][$cart_item_key])) {
            
            // --- (OPSIONAL TAPI PENTING: Pengecekan Stok) ---
            // Jika Anda ingin mengecek stok sebelum update:
            // 1. Ambil id_produk dari $cart_item_key atau dari $_SESSION['keranjang'][$cart_item_key]['id_produk']
            // $id_produk_untuk_cek_stok = $_SESSION['keranjang'][$cart_item_key]['id_produk'];
            // 2. Lakukan query ke database untuk mendapatkan stok produk tersebut.
            //    Contoh:
            //    $stok_saat_ini = 0;
            //    $sql_get_stock = "SELECT stock FROM product WHERE id = ?";
            //    if ($stmt_stock = $conn->prepare($sql_get_stock)) {
            //        $stmt_stock->bind_param("i", $id_produk_untuk_cek_stok);
            //        $stmt_stock->execute();
            //        $result_stock = $stmt_stock->get_result();
            //        if ($prod_stock_data = $result_stock->fetch_assoc()) {
            //            $stok_saat_ini = $prod_stock_data['stock'];
            //        }
            //        $stmt_stock->close();
            //    }
            // 3. Bandingkan $kuantitas_baru dengan $stok_saat_ini
            //    if ($kuantitas_baru > $stok_saat_ini) {
            //        $_SESSION['pesan_notifikasi_keranjang'] = "Kuantitas melebihi stok yang tersedia (" . $stok_saat_ini . ").";
            //        $_SESSION['tipe_notifikasi_keranjang'] = "warning";
            //        header('Location: keranjang.php');
            //        exit;
            //    }
            // --- (AKHIR OPSIONAL Pengecekan Stok) ---


            if ($kuantitas_baru > 0) {
                $_SESSION['keranjang'][$cart_item_key]['kuantitas'] = $kuantitas_baru;
                // (Opsional) Pesan sukses
                // $_SESSION['pesan_notifikasi_keranjang'] = "Kuantitas produk berhasil diperbarui.";
                // $_SESSION['tipe_notifikasi_keranjang'] = "success";
            } else { // Jika kuantitas baru adalah 0
                unset($_SESSION['keranjang'][$cart_item_key]); // Hapus item dari keranjang
                // (Opsional) Pesan sukses
                // $_SESSION['pesan_notifikasi_keranjang'] = "Item berhasil dihapus dari keranjang.";
                // $_SESSION['tipe_notifikasi_keranjang'] = "success";
            }
        } else {
            // (Opsional) Pesan jika item tidak ditemukan di keranjang untuk diperbarui
            // $_SESSION['pesan_notifikasi_keranjang'] = "Item tidak ditemukan di keranjang untuk diperbarui.";
            // $_SESSION['tipe_notifikasi_keranjang'] = "warning";
        }
    } else {
        // (Opsional) Pesan jika kuantitas tidak valid
        // $_SESSION['pesan_notifikasi_keranjang'] = "Kuantitas yang dimasukkan tidak valid.";
        // $_SESSION['tipe_notifikasi_keranjang'] = "danger";
    }
} else {
    // (Opsional) Pesan jika aksi tidak valid
    // $_SESSION['pesan_notifikasi_keranjang'] = "Aksi tidak valid.";
    // $_SESSION['tipe_notifikasi_keranjang'] = "danger";
}

// (Opsional) Tutup koneksi jika dibuka untuk cek stok
// if (isset($conn)) {
//     $conn->close();
// }

// Arahkan kembali ke halaman keranjang
header("Location: keranjang.php");
exit;
?>
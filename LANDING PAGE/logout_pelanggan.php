<?php
// Memulai atau melanjutkan sesi yang sudah ada.
// Ini diperlukan untuk bisa mengakses dan menghancurkan variabel session.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Menghapus semua variabel sesi yang terkait dengan login pelanggan.
// Anda bisa memilih untuk menghapus variabel spesifik atau menghancurkan seluruh session.
// Menghapus variabel spesifik lebih aman jika Anda memiliki variabel session lain yang tidak terkait login pelanggan.
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_loggedin']);

// Alternatif: Jika Anda ingin menghancurkan semua data session (termasuk keranjang, dll.)
// Hati-hati jika menggunakan ini, karena keranjang belanja juga akan hilang.
// session_unset(); // Menghapus semua variabel session
// session_destroy(); // Menghancurkan session itu sendiri

// (Opsional) Jika Anda ingin menghancurkan cookie sesi juga (lebih bersih):
// Cek apakah cookie sesi digunakan.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// (Opsional) Hancurkan sesi secara keseluruhan jika Anda tidak memerlukan data session lain.
// Jika Anda hanya ingin logout pelanggan tapi mempertahankan keranjang, jangan gunakan session_destroy().
// Cukup unset variabel session pelanggan seperti di atas.
// Jika Anda memutuskan untuk menghancurkan seluruh session:
// session_destroy(); // Baris ini akan menghapus SEMUA data di $_SESSION, termasuk keranjang.

// (Opsional) Tambahkan pesan bahwa logout berhasil (disimpan di session sebelum di-destroy, atau sebagai parameter GET)
// Jika Anda menggunakan session_destroy(), pesan ini tidak akan berfungsi jika disimpan di session.
// Salah satu cara adalah dengan parameter GET:
// header("Location: index.php?logout=success");

// 4. Mengarahkan pengguna kembali ke halaman utama (index.php).
header("Location: index.php");
exit; // Pastikan untuk keluar setelah redirect.
?>
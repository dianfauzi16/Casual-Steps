<?php
// Memulai sesi. Ini diperlukan untuk bisa mengakses dan menghancurkan sesi.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Menghapus semua variabel sesi.
$_SESSION = array(); // Ini akan mengosongkan array $_SESSION.

// 2. Jika diinginkan untuk menghancurkan cookie sesi juga (opsional tapi lebih bersih):
// Cek apakah cookie sesi digunakan.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params(); // Dapatkan parameter cookie sesi.
    // Set cookie sesi dengan waktu kedaluwarsa di masa lalu agar dihapus oleh browser.
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Menghancurkan sesi secara keseluruhan.
session_destroy();

// 4. Mengarahkan pengguna kembali ke halaman login.
header("location: admin_login.php");
exit; // Pastikan untuk keluar setelah redirect.
?>
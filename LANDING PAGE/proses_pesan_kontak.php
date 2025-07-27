<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Inisialisasi pesan untuk session
$message = '';
$message_type = ''; // 'success' atau 'danger'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan pengguna sudah login untuk mengirim pesan
    if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
        $message = "Anda harus login untuk mengirim pesan.";
        $message_type = "danger";
    } else {
        $user_id = $_SESSION['user_id'] ?? 0; // Ambil user_id pelanggan
        $nama = trim($_POST['contact_name'] ?? '');
        $email = trim($_POST['contact_email'] ?? '');
        $subjek = trim($_POST['contact_subject'] ?? '');
        $pesan = trim($_POST['contact_message'] ?? '');

        if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
            $message = "Semua field wajib diisi.";
            $message_type = "danger";
        } else {
            // Query INSERT ke tabel pesan_kontak
            $stmt = $conn->prepare("INSERT INTO pesan_kontak (user_id, nama, email, subjek, pesan) VALUES (?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issss", $user_id, $nama, $email, $subjek, $pesan);
                if ($stmt->execute()) {
                    $message = "Pesan Anda berhasil dikirim! Kami akan segera meresponnya.";
                    $message_type = "success";
                } else {
                    $message = "Gagal mengirim pesan. Silakan coba lagi. Error: " . $stmt->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                $message = "Gagal mempersiapkan statement. Error: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
} else {
    // Jika diakses bukan melalui POST request
    $message = "Akses tidak valid.";
    $message_type = "danger";
}

// Simpan pesan ke session untuk ditampilkan di halaman konfirmasi
$_SESSION['pesan_kontak_status'] = $message_type;
$_SESSION['pesan_kontak_message'] = $message;

if (isset($conn)) {
    $conn->close();
}

// Arahkan ke halaman konfirmasi
header("Location: terima_kasih_pesan.php");
exit;
?>

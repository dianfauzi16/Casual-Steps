<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

require_once 'db_connect.php';

$message = "";
$message_type = ""; // "success" atau "danger"

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kategori = trim($_POST['nama_kategori']);

    if (empty($nama_kategori)) {
        $message = "Nama kategori tidak boleh kosong.";
        $message_type = "danger";
    } else {
        // Cek apakah kategori sudah ada (opsional, karena DB sudah ada UNIQUE constraint)
        $sql_check = "SELECT id_kategori FROM categories WHERE nama_kategori = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $nama_kategori);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $message = "Nama kategori sudah ada.";
                $message_type = "warning";
            }
            $stmt_check->close();
        }

        if (empty($message_type)) { // Hanya lanjut jika tidak ada error dari pengecekan di atas
            $sql_insert = "INSERT INTO categories (nama_kategori) VALUES (?)";
            if ($stmt_insert = $conn->prepare($sql_insert)) {
                $stmt_insert->bind_param("s", $nama_kategori);
                if ($stmt_insert->execute()) {
                    $message = "Kategori baru berhasil ditambahkan!";
                    $message_type = "success";
                } else {
                    $message = "Error: Gagal menambahkan kategori. " . $stmt_insert->error;
                    if ($stmt_insert->errno == 1062) { // Error code for duplicate entry
                         $message = "Nama kategori sudah ada (dari database constraint).";
                    }
                    $message_type = "danger";
                }
                $stmt_insert->close();
            } else {
                $message = "Error: Gagal mempersiapkan statement insert. " . $conn->error;
                $message_type = "danger";
            }
        }
    }
} else {
    $message = "Metode request tidak valid.";
    $message_type = "danger";
}

$_SESSION['form_message'] = $message;
$_SESSION['form_message_type'] = $message_type;

$conn->close();
header("location: admin_kategori.php"); // Kembali ke halaman daftar kategori
exit;
?>
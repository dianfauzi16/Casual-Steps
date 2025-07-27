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
    if (isset($_POST['id_kategori'], $_POST['nama_kategori'])) {
        $id_kategori = trim($_POST['id_kategori']);
        $nama_kategori_baru = trim($_POST['nama_kategori']);

        if (empty($nama_kategori_baru)) {
            $message = "Nama kategori tidak boleh kosong.";
            $message_type = "danger";
        } elseif (!filter_var($id_kategori, FILTER_VALIDATE_INT)) {
            $message = "ID Kategori tidak valid.";
            $message_type = "danger";
        } else {
            // Cek apakah nama kategori baru sudah ada untuk ID LAIN
            $sql_check = "SELECT id_kategori FROM categories WHERE nama_kategori = ? AND id_kategori != ?";
            if ($stmt_check = $conn->prepare($sql_check)) {
                $stmt_check->bind_param("si", $nama_kategori_baru, $id_kategori);
                $stmt_check->execute();
                $stmt_check->store_result();
                if ($stmt_check->num_rows > 0) {
                    $message = "Nama kategori '" . htmlspecialchars($nama_kategori_baru) . "' sudah digunakan oleh kategori lain.";
                    $message_type = "warning";
                }
                $stmt_check->close();
            }

            if (empty($message_type)) { // Lanjut jika tidak ada error dari pengecekan di atas
                $sql_update = "UPDATE categories SET nama_kategori = ?, updated_at = NOW() WHERE id_kategori = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    $stmt_update->bind_param("si", $nama_kategori_baru, $id_kategori);
                    if ($stmt_update->execute()) {
                        if ($stmt_update->affected_rows > 0) {
                            $message = "Kategori berhasil diperbarui!";
                            $message_type = "success";
                        } else {
                            $message = "Tidak ada perubahan pada data kategori atau kategori tidak ditemukan.";
                            $message_type = "info"; // Atau warning, karena tidak ada error tapi tidak ada update
                        }
                    } else {
                        $message = "Error: Gagal memperbarui kategori. " . $stmt_update->error;
                         if ($stmt_update->errno == 1062) { // Error code for duplicate entry
                            $message = "Nama kategori '" . htmlspecialchars($nama_kategori_baru) . "' sudah ada.";
                        }
                        $message_type = "danger";
                    }
                    $stmt_update->close();
                } else {
                    $message = "Error: Gagal mempersiapkan statement update. " . $conn->error;
                    $message_type = "danger";
                }
            }
        }
    } else {
        $message = "Data form tidak lengkap.";
        $message_type = "danger";
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
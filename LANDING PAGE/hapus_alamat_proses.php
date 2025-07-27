<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['alamat_message'] = "Anda harus login untuk melakukan aksi ini.";
    $_SESSION['alamat_message_type'] = "danger";
    header('Location: login_pelanggan.php');
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $address_id_to_delete = (int)$_GET['id'];

    // Pastikan alamat yang akan dihapus adalah milik user yang login
    $sql_check_owner = "SELECT id, is_primary FROM addresses WHERE id = ? AND user_id = ?";
    if ($stmt_check = $conn->prepare($sql_check_owner)) {
        $stmt_check->bind_param("ii", $address_id_to_delete, $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 1) {
            $address_to_delete = $result_check->fetch_assoc();

            // Periksa apakah alamat yang akan dihapus adalah alamat utama
            if ($address_to_delete['is_primary'] == 1) {
                $_SESSION['alamat_message'] = "Tidak dapat menghapus alamat utama. Silakan set alamat lain sebagai utama terlebih dahulu.";
                $_SESSION['alamat_message_type'] = "warning";
            } else {
                // Lanjutkan dengan menghapus alamat
                $sql_delete = "DELETE FROM addresses WHERE id = ? AND user_id = ?"; // Double check user_id for safety
                if ($stmt_delete = $conn->prepare($sql_delete)) {
                    $stmt_delete->bind_param("ii", $address_id_to_delete, $user_id);
                    if ($stmt_delete->execute()) {
                        if ($stmt_delete->affected_rows > 0) {
                            $_SESSION['alamat_message'] = "Alamat berhasil dihapus.";
                            $_SESSION['alamat_message_type'] = "success";
                        } else {
                            $_SESSION['alamat_message'] = "Alamat tidak ditemukan atau gagal dihapus.";
                            $_SESSION['alamat_message_type'] = "warning";
                        }
                    } else {
                        $_SESSION['alamat_message'] = "Gagal menghapus alamat: " . $stmt_delete->error;
                        $_SESSION['alamat_message_type'] = "danger";
                    }
                    $stmt_delete->close();
                } else {
                    $_SESSION['alamat_message'] = "Gagal mempersiapkan statement hapus: " . $conn->error;
                    $_SESSION['alamat_message_type'] = "danger";
                }
            }
        } else {
            $_SESSION['alamat_message'] = "Alamat tidak ditemukan atau Anda tidak memiliki izin untuk menghapusnya.";
            $_SESSION['alamat_message_type'] = "warning";
        }
        $stmt_check->close();
    } else {
        $_SESSION['alamat_message'] = "Gagal mempersiapkan statement pengecekan alamat: " . $conn->error;
        $_SESSION['alamat_message_type'] = "danger";
    }
} else {
    $_SESSION['alamat_message'] = "ID Alamat tidak valid atau tidak disediakan.";
    $_SESSION['alamat_message_type'] = "danger";
}

if (isset($conn)) {
    $conn->close();
}

header('Location: alamat_saya.php');
exit;
?>
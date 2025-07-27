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
    $address_id_to_set_primary = (int)$_GET['id'];

    $conn->begin_transaction();
    try {
        // Verifikasi dulu apakah alamat ini benar-benar milik user yang login
        $sql_check_owner = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
        $stmt_check = $conn->prepare($sql_check_owner);
        $stmt_check->bind_param("ii", $address_id_to_set_primary, $user_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $stmt_check->close();

        if ($result_check->num_rows == 0) {
            throw new Exception("Anda tidak memiliki izin untuk mengatur alamat ini atau alamat tidak ditemukan.");
        }

        // Set semua alamat lain milik user ini menjadi TIDAK utama (is_primary = 0)
        $sql_unset_others = "UPDATE addresses SET is_primary = 0 WHERE user_id = ? AND id != ?";
        if ($stmt_unset = $conn->prepare($sql_unset_others)) {
            $stmt_unset->bind_param("ii", $user_id, $address_id_to_set_primary);
            if (!$stmt_unset->execute()) {
                throw new Exception("Gagal mengatur ulang status alamat utama lainnya: " . $stmt_unset->error);
            }
            $stmt_unset->close();
        } else {
            throw new Exception("Gagal mempersiapkan statement untuk unset alamat lain: " . $conn->error);
        }

        // Set alamat yang dipilih menjadi UTAMA (is_primary = 1)
        $sql_set_primary = "UPDATE addresses SET is_primary = 1 WHERE id = ? AND user_id = ?";
        if ($stmt_set = $conn->prepare($sql_set_primary)) {
            $stmt_set->bind_param("ii", $address_id_to_set_primary, $user_id);
            if ($stmt_set->execute()) {
                if ($stmt_set->affected_rows > 0) {
                    $conn->commit();
                    $_SESSION['alamat_message'] = "Alamat berhasil diatur sebagai utama.";
                    $_SESSION['alamat_message_type'] = "success";
                } else {
                    // Bisa jadi alamat memang sudah utama, atau ID tidak ditemukan (sudah dicek di awal)
                    $conn->rollback(); // Tidak ada perubahan, bisa rollback atau commit saja
                    $_SESSION['alamat_message'] = "Tidak ada perubahan atau alamat sudah menjadi utama.";
                    $_SESSION['alamat_message_type'] = "info";
                }
            } else {
                throw new Exception("Gagal mengatur alamat sebagai utama: " . $stmt_set->error);
            }
            $stmt_set->close();
        } else {
            throw new Exception("Gagal mempersiapkan statement untuk set alamat utama: " . $conn->error);
        }

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['alamat_message'] = "Terjadi kesalahan: " . $e->getMessage();
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
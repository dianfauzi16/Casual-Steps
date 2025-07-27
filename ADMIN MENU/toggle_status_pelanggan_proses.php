<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    $_SESSION['pesan_notifikasi_pelanggan'] = "Akses ditolak. Silakan login terlebih dahulu.";
    $_SESSION['tipe_notifikasi_pelanggan'] = "danger";
    header("location: admin_login.php");
    exit;
}

// 2. Sertakan file koneksi database
require_once 'db_connect.php';

$pesan_notifikasi = "";
$tipe_notifikasi = "";

// 3. Ambil dan validasi parameter dari URL
if (isset($_GET['id_user']) && filter_var($_GET['id_user'], FILTER_VALIDATE_INT) && isset($_GET['action'])) {
    $id_user_toggle = (int)$_GET['id_user'];
    $action = $_GET['action']; // 'aktifkan' atau 'nonaktifkan'

    $status_baru = '';
    if ($action === 'aktifkan') {
        $status_baru = 'aktif';
    } elseif ($action === 'nonaktifkan') {
        $status_baru = 'nonaktif';
    } else {
        $pesan_notifikasi = "Aksi tidak valid.";
        $tipe_notifikasi = "danger";
    }

    if (!empty($status_baru)) {
        // Update status akun di database
        // Pastikan nama kolom 'account_status' dan 'id' sesuai dengan tabel 'users' Anda
        $sql_update_status = "UPDATE users SET account_status = ? WHERE id = ?";
        
        if ($stmt_update = $conn->prepare($sql_update_status)) {
            $stmt_update->bind_param("si", $status_baru, $id_user_toggle);
            if ($stmt_update->execute()) {
                if ($stmt_update->affected_rows > 0) {
                    $pesan_notifikasi = "Status akun pelanggan berhasil diubah menjadi '" . htmlspecialchars($status_baru) . "'.";
                    $tipe_notifikasi = "success";
                } else {
                    $pesan_notifikasi = "Tidak ada perubahan pada status akun (mungkin status sudah sama atau pelanggan tidak ditemukan).";
                    $tipe_notifikasi = "info";
                }
            } else {
                $pesan_notifikasi = "Gagal memperbarui status akun: " . $stmt_update->error;
                $tipe_notifikasi = "danger";
            }
            $stmt_update->close();
        } else {
            $pesan_notifikasi = "Gagal mempersiapkan statement untuk update status: " . $conn->error;
            $tipe_notifikasi = "danger";
        }
    }

} else {
    $pesan_notifikasi = "Parameter tidak valid atau tidak lengkap.";
    $tipe_notifikasi = "danger";
}

$_SESSION['pesan_notifikasi_pelanggan'] = $pesan_notifikasi;
$_SESSION['tipe_notifikasi_pelanggan'] = $tipe_notifikasi;

if (isset($conn)) {
    $conn->close();
}

header("Location: admin_pelanggan.php");
exit;
?>
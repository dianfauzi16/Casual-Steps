<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Jika belum login, hentikan atau beri respon error
    $_SESSION['pesan_notifikasi_order'] = "Akses ditolak. Silakan login terlebih dahulu.";
    $_SESSION['tipe_notifikasi_order'] = "danger";
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php'; // Path ke db_connect.php

$pesan_notifikasi = "";
$tipe_notifikasi = "";
$id_order_redirect = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_order'], $_POST['status_baru'])) {
    $id_order = filter_var($_POST['id_order'], FILTER_VALIDATE_INT);
    $status_baru = trim($_POST['status_baru']);
    $id_order_redirect = $id_order; // Untuk redirect kembali ke halaman detail yang benar

    // Daftar status yang valid (sesuaikan dengan ENUM di tabel orders Anda)
    $status_valid = ['Menunggu Pembayaran', 'pending', 'Diproses', 'paid', 'Dikirim', 'shipped', 'Selesai', 'Dibatalkan', 'cancelled'];

    if ($id_order === false || $id_order <= 0) {
        $pesan_notifikasi = "ID Pesanan tidak valid.";
        $tipe_notifikasi = "danger";
    } elseif (empty($status_baru) || !in_array($status_baru, $status_valid)) {
        $pesan_notifikasi = "Status baru pesanan tidak valid.";
        $tipe_notifikasi = "danger";
    } else {
        // Update status pesanan dan tanggal_update_status di database
        // Pastikan nama kolom 'status' dan 'id' sesuai dengan tabel 'orders' Anda
        $sql_update_status = "UPDATE orders SET status = ?, tanggal_update_status = NOW() WHERE id = ?";
        
        if ($stmt_update = $conn->prepare($sql_update_status)) {
            $stmt_update->bind_param("si", $status_baru, $id_order);
            if ($stmt_update->execute()) {
                if ($stmt_update->affected_rows > 0) {
                    $pesan_notifikasi = "Status pesanan berhasil diperbarui menjadi '" . htmlspecialchars($status_baru) . "'.";
                    $tipe_notifikasi = "success";
                } else {
                    $pesan_notifikasi = "Tidak ada perubahan pada status pesanan (mungkin status sudah sama atau pesanan tidak ditemukan).";
                    $tipe_notifikasi = "info";
                }
            } else {
                $pesan_notifikasi = "Gagal memperbarui status pesanan: " . $stmt_update->error;
                $tipe_notifikasi = "danger";
            }
            $stmt_update->close();
        } else {
            $pesan_notifikasi = "Gagal mempersiapkan statement untuk update status: " . $conn->error;
            $tipe_notifikasi = "danger";
        }
    }
} else {
    $pesan_notifikasi = "Permintaan tidak valid.";
    $tipe_notifikasi = "danger";
    // Jika request tidak valid, mungkin lebih baik arahkan ke daftar pesanan umum
    header("Location: admin_pesanan.php");
    exit;
}

$_SESSION['pesan_notifikasi_order'] = $pesan_notifikasi;
$_SESSION['tipe_notifikasi_order'] = $tipe_notifikasi;

if (isset($conn)) {
    $conn->close();
}

// Arahkan kembali ke halaman detail pesanan yang baru saja diupdate (jika id_order valid)
// atau ke halaman daftar pesanan jika id_order tidak ada/tidak valid dari awal
if ($id_order_redirect && $tipe_notifikasi !== "danger" && $pesan_notifikasi !== "ID Pesanan tidak valid.") { // Hanya redirect ke detail jika ID valid
    header("Location: admin_detail_pesanan.php?id_order=" . $id_order_redirect);
} else {
    header("Location: admin_pesanan.php");
}
exit;
?>
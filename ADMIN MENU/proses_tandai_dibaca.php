<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'db_connect.php';

$message = "";
$message_type = "";

if (isset($_GET['id_pesan']) && filter_var($_GET['id_pesan'], FILTER_VALIDATE_INT)) {
    $id_pesan = (int)$_GET['id_pesan'];

    // Update status pesan menjadi 'sudah dibaca'
    $sql_update = "UPDATE pesan_kontak SET status_baca = 'sudah dibaca' WHERE id = ?";
    if ($stmt = $conn->prepare($sql_update)) {
        $stmt->bind_param("i", $id_pesan);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $message = "Pesan #" . $id_pesan . " berhasil ditandai sebagai sudah dibaca.";
                $message_type = "success";
            } else {
                $message = "Pesan #" . $id_pesan . " tidak ditemukan atau statusnya tidak berubah.";
                $message_type = "warning";
            }
        } else {
            $message = "Gagal mengupdate status pesan: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } else {
        $message = "Gagal mempersiapkan statement: " . $conn->error;
        $message_type = "danger";
    }
} else {
    $message = "ID Pesan tidak valid atau tidak disediakan.";
    $message_type = "danger";
}

$_SESSION['pesan_kontak_message'] = $message;
$_SESSION['pesan_kontak_message_type'] = $message_type;

if (isset($conn)) {
    $conn->close();
}

header("Location: admin-pesan-kontak.php");
exit;
?>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    // Simpan pesan error di session jika mau, atau langsung die
    $_SESSION['form_message'] = "Akses ditolak. Silakan login terlebih dahulu.";
    $_SESSION['form_message_type'] = "danger";
    header("location: admin_login.php"); // Arahkan ke login jika belum login
    exit;
}

require_once 'db_connect.php';

$message = "";
$message_type = "";

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id_kategori = $_GET['id'];

    // (Opsional) Verifikasi apakah kategori ada sebelum mencoba menghapus
    $sql_check_exist = "SELECT id_kategori FROM categories WHERE id_kategori = ?";
    if ($stmt_check = $conn->prepare($sql_check_exist)) {
        $stmt_check->bind_param("i", $id_kategori);
        $stmt_check->execute();
        $stmt_check->store_result();
        $kategori_ada = $stmt_check->num_rows > 0;
        $stmt_check->close();

        if ($kategori_ada) {
            // Karena kita sudah set ON DELETE SET NULL pada foreign key di tabel product,
            // kita bisa langsung hapus kategorinya. Produk terkait akan memiliki id_kategori = NULL.
            $sql_delete = "DELETE FROM categories WHERE id_kategori = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id_kategori);
                if ($stmt_delete->execute()) {
                    if ($stmt_delete->affected_rows > 0) {
                        $message = "Kategori berhasil dihapus.";
                        $message_type = "success";
                    } else {
                        // Seharusnya tidak terjadi jika $kategori_ada true dan query delete berhasil
                        $message = "Kategori tidak ditemukan atau gagal dihapus.";
                        $message_type = "warning";
                    }
                } else {
                    $message = "Error: Gagal menghapus kategori. " . $stmt_delete->error;
                    $message_type = "danger";
                }
                $stmt_delete->close();
            } else {
                $message = "Error: Gagal mempersiapkan statement hapus. " . $conn->error;
                $message_type = "danger";
            }
        } else {
            $message = "Kategori tidak ditemukan.";
            $message_type = "warning";
        }
    } else {
         $message = "Error: Gagal mempersiapkan statement pengecekan. " . $conn->error;
         $message_type = "danger";
    }


} else {
    $message = "ID Kategori tidak valid atau tidak disediakan.";
    $message_type = "danger";
}

$_SESSION['form_message'] = $message;
$_SESSION['form_message_type'] = $message_type;

$conn->close();
header("location: admin_kategori.php");
exit;
?>
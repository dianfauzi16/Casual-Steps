<?php
// Memulai atau melanjutkan sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    $_SESSION['form_message'] = "Akses ditolak. Silakan login terlebih dahulu.";
    $_SESSION['form_message_type'] = "danger";
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

$message = "";
$message_type = ""; 

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) { // Validasi ID sebagai integer
    $product_id = (int)$_GET['id'];

    // Langkah 0: Cek apakah produk ini ada dalam order_items
    $sql_check_orders = "SELECT COUNT(*) as count FROM order_items WHERE id_produk = ?";
    $order_count = 0;
    if ($stmt_check = $conn->prepare($sql_check_orders)) {
        $stmt_check->bind_param("i", $product_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $order_count = $row_check['count'];
        $stmt_check->close();
    } else {
        $message = "Error saat memeriksa riwayat pesanan produk: " . $conn->error;
        $message_type = "danger";
    }

    if (empty($message) && $order_count > 0) { // Jika ada error sebelumnya, jangan set pesan ini
        $message = "Produk ini tidak dapat dihapus karena sudah ada dalam riwayat pesanan pelanggan.";
        $message_type = "warning";
    } else if (empty($message)) { // Hanya lanjut jika tidak ada error dan produk tidak ada di order_items
        // Langkah 1: Ambil nama file gambar produk sebelum menghapus data dari database
        $sql_select_image = "SELECT image FROM product WHERE id = ?";
        $image_to_delete = null;

        if ($stmt_select = $conn->prepare($sql_select_image)) {
            $stmt_select->bind_param("i", $product_id);
            if ($stmt_select->execute()) {
                $result_image = $stmt_select->get_result();
                if ($result_image->num_rows == 1) {
                    $row_image = $result_image->fetch_assoc();
                    if (!empty($row_image['image'])) {
                        $image_to_delete = $row_image['image'];
                    }
                } else {
                    // Produk tidak ditemukan untuk mengambil gambar, mungkin sudah terhapus
                    $message = "Produk tidak ditemukan untuk mengambil detail gambar.";
                    $message_type = "warning";
                }
            }
            $stmt_select->close();
        }

        // Lanjutkan hanya jika tidak ada pesan error dari pengambilan gambar
        if (empty($message) || $message_type !== "danger") {
            // Langkah 2: Hapus data produk dari database
            $sql_delete_product = "DELETE FROM product WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete_product)) {
                $stmt_delete->bind_param("i", $product_id);

                if ($stmt_delete->execute()) {
                    if ($stmt_delete->affected_rows > 0) {
                        $message = "Produk berhasil dihapus!";
                        $message_type = "success";

                        if ($image_to_delete) {
                            $target_dir = "uploads/produk/";
                            $file_path_to_delete = $target_dir . $image_to_delete;
                            if (file_exists($file_path_to_delete)) {
                                if (!unlink($file_path_to_delete)) {
                                    $_SESSION['form_message_extra'] = "Namun, file gambar terkait gagal dihapus dari server.";
                                }
                            }
                        }
                    } else {
                        // Jika pesan error sebelumnya bukan karena produk tidak ditemukan untuk gambar
                        if (empty($message) || strpos($message, "Produk tidak ditemukan untuk mengambil detail gambar") === false) {
                            $message = "Produk tidak ditemukan atau sudah dihapus sebelumnya.";
                            $message_type = "warning";
                        }
                    }
                } else {
                    // Jika error karena foreign key, tangkap di sini
                    if ($conn->errno == 1451) { // Error code for foreign key constraint
                         $message = "Produk ini tidak dapat dihapus karena sudah ada dalam riwayat pesanan pelanggan.";
                         $message_type = "danger";
                    } else {
                        $message = "Error: Tidak bisa mengeksekusi statement hapus. " . $stmt_delete->error;
                        $message_type = "danger";
                    }
                }
                $stmt_delete->close();
            } else {
                $message = "Error: Tidak bisa mempersiapkan statement hapus. " . $conn->error;
                $message_type = "danger";
            }
        }
    }
} else {
    $message = "ID Produk tidak valid atau tidak disediakan.";
    $message_type = "danger";
}

$_SESSION['form_message'] = $message;
$_SESSION['form_message_type'] = $message_type;

$conn->close();

header("location: admin_dashboard.php#produk-section");
exit;
?>
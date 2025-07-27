<?php
// Memulai atau melanjutkan sesi
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Sertakan file koneksi database
require_once 'db_connect.php';

$message = "";
$message_type = ""; // "success" atau "danger"
$product_id_to_redirect = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $product_id = trim($_POST['product_id']);
    $product_id_to_redirect = $product_id; // Simpan ID untuk redirect

    $product_name = trim($_POST['product_name']);
    $product_description = trim($_POST['product_description']);
    $product_brand = trim($_POST['product_brand']);
    $product_price = trim($_POST['product_price']);
    $product_stock = trim($_POST['product_stock']);
    $product_size = trim($_POST['product_size']);
    $old_product_image = trim($_POST['old_product_image'] ?? '');
    $id_kategori_input = isset($_POST['id_kategori']) && !empty($_POST['id_kategori']) ? trim($_POST['id_kategori']) : NULL;

    // Ambil data diskon
    $discount_percent = !empty($_POST['discount_percent']) ? trim($_POST['discount_percent']) : 0;
    $discount_start_date = !empty($_POST['discount_start_date']) ? trim($_POST['discount_start_date']) : NULL;
    $discount_end_date = !empty($_POST['discount_end_date']) ? trim($_POST['discount_end_date']) : NULL;
    // Validasi dasar
    if (empty($product_id) || empty($product_name) || empty($product_price) || empty($product_stock)) {
        $message = "ID Produk, Nama Produk, Harga, dan Stok tidak boleh kosong.";
        $message_type = "danger";
    } else {
        $product_image_name_to_save = $old_product_image;
        $upload_ok = 1; 

        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0 && !empty($_FILES['product_image']['name'])) {
            $target_dir = "uploads/produk/";
            $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            $new_product_image_name = uniqid('produk_', true) . '.' . $image_file_type;
            $target_file = $target_dir . $new_product_image_name;
            
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if ($check === false) {
                $message = "File yang diunggah bukan gambar."; $message_type = "danger"; $upload_ok = 0;
            }
            if ($upload_ok && $_FILES["product_image"]["size"] > 2000000) { 
                $message = "Maaf, ukuran file gambar terlalu besar (maks 2MB)."; $message_type = "danger"; $upload_ok = 0;
            }
            if ($upload_ok && !in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                $message = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan."; $message_type = "danger"; $upload_ok = 0;
            }

            if ($upload_ok == 1) {
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    if (!empty($old_product_image) && file_exists($target_dir . $old_product_image)) {
                        unlink($target_dir . $old_product_image);
                    }
                    $product_image_name_to_save = $new_product_image_name;
                } else {
                    $message = "Maaf, terjadi error saat mengunggah file gambar baru Anda.";
                    $message_type = "danger";
                    $upload_ok = 0; 
                }
            }
        } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['product_image']['error'] != 0) {
            $message = "Terjadi error pada file gambar yang diupload. Kode Error: " . $_FILES['product_image']['error'];
            $message_type = "danger";
            $upload_ok = 0;
        }

        if ($message_type !== "danger") {
            // PERBAIKAN SQL: Hapus `updated_at = NOW()` karena kolomnya tidak ada
            $sql = "UPDATE product SET name = ?, description = ?, brand = ?, price = ?, stock = ?, size = ?, image = ?, id_kategori = ?, 
                           discount_percent = ?, discount_start_date = ?, discount_end_date = ?
                    WHERE id = ?";

            if ($stmt = $conn->prepare($sql)) {
                // bind_param untuk 8 kolom SET + 1 ID WHERE
               // Tipe:   s(name), s(desc), s(brand), d(price), i(stock), s(size), s(image), i(id_kategori), d(disc%), s(start), s(end), i(id_where)
                $stmt->bind_param("sssdissidssi",
                    $product_name, 
                    $product_description, 
                    $product_brand, 
                    $product_price, 
                    $product_stock,
                    $product_size,
                    $product_image_name_to_save, 
                    $id_kategori_input,
                    $discount_percent,
                    $discount_start_date,
                    $discount_end_date,
                    $product_id
                );

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $message = "Produk berhasil diperbarui!";
                        $message_type = "success";
                    } else {
                        $message = "Tidak ada perubahan pada data produk atau produk tidak ditemukan.";
                        $message_type = "info"; 
                    }
                } else {
                    $message = "Error: Tidak bisa mengeksekusi statement update. " . $stmt->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                $message = "Error: Tidak bisa mempersiapkan statement update. " . $conn->error;
                $message_type = "danger";
            }
        }
    }
} else {
    $message = "Metode request tidak valid.";
    $message_type = "danger";
    // Untuk request non-POST, lebih baik redirect ke halaman sebelumnya atau dashboard
    // daripada menampilkan pesan di halaman kosong ini.
    // Jika $product_id_to_redirect ada, bisa redirect ke edit_produk.php?id=...
    // jika tidak, ke dashboard.
    if ($product_id_to_redirect) {
        header("location: edit_produk.php?id=" . $product_id_to_redirect);
    } else {
        header("location: admin_dashboard.php#produk-section");
    }
    exit;
}

$_SESSION['form_message'] = $message;
$_SESSION['form_message_type'] = $message_type;

if (isset($conn)) {
    $conn->close();
}

if ($message_type === "success") {
    header("location: admin_promo.php");
} else {
    if ($product_id_to_redirect) {
        header("location: edit_produk.php?id=" . $product_id_to_redirect);
    } else {
        header("location: admin_dashboard.php#produk-section"); 
    }
}
exit;
?>
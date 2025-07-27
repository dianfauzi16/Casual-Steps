<?php
// Memulai atau melanjutkan sesi yang sudah ada.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    die("Akses ditolak. Silakan login terlebih dahulu."); 
}

// Menyertakan file koneksi database
require_once 'db_connect.php';

// Inisialisasi variabel untuk pesan
$message = "";
$message_type = ""; // "success" atau "danger"

// Memeriksa apakah form sudah di-submit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form dan membersihkannya (trim)
    $product_name = trim($_POST['product_name'] ?? '');
    $product_description = trim($_POST['product_description'] ?? '');
    $product_brand = trim($_POST['product_brand'] ?? '');
    $product_price = trim($_POST['product_price'] ?? '');
    $product_stock = trim($_POST['product_stock'] ?? '');
    $product_size = trim($_POST['product_size'] ?? ''); 

    // Mengambil id_kategori dari form
    // Jika kosong (karena "-- Pilih Kategori --" memiliki value=""), set sebagai NULL
    $id_kategori_input = isset($_POST['id_kategori']) && !empty($_POST['id_kategori']) ? trim($_POST['id_kategori']) : NULL;

    // Validasi dasar
    if (empty($product_name) || empty($product_price) || empty($product_stock)) {
        $message = "Nama Produk, Harga, dan Stok tidak boleh kosong.";
        $message_type = "danger";
    } else {
        // Proses upload gambar
        $product_image_name_to_save = null; // Nama file gambar yang akan disimpan ke DB
        $upload_ok = 1; // Inisialisasi status upload

        // Cek apakah ada file gambar yang diupload dan tidak ada error pada upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0 && !empty($_FILES['product_image']['name'])) {
            $target_dir = "uploads/produk/"; // Pastikan folder ini ada dan writable

            // Membuat folder jika belum ada (sebaiknya dibuat manual dengan permission yang benar)
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
            // Buat nama file unik untuk menghindari penimpaan file
            $product_image_name_to_save = uniqid('produk_', true) . '.' . $image_file_type;
            $target_file = $target_dir . $product_image_name_to_save;
            
            // Cek apakah file adalah gambar sungguhan
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if ($check === false) {
                $message = "File yang diunggah bukan gambar.";
                $message_type = "danger";
                $upload_ok = 0;
            }

            // Cek ukuran file (misalnya, maks 2MB)
            if ($upload_ok && $_FILES["product_image"]["size"] > 2000000) { 
                $message = "Maaf, ukuran file gambar terlalu besar (maks 2MB).";
                $message_type = "danger";
                $upload_ok = 0;
            }

            // Izinkan format file tertentu
            if ($upload_ok && !in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                $message = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
                $message_type = "danger";
                $upload_ok = 0;
            }

            if ($upload_ok == 0) {
                // Pesan error sudah diatur di atas, $product_image_name_to_save akan tetap null
            } else {
                // Jika semua oke, coba upload file
                if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    $message = "Maaf, terjadi error saat mengunggah file gambar Anda.";
                    $message_type = "danger";
                    $product_image_name_to_save = null; // Set kembali jadi null jika gagal upload
                }
            }
        } elseif (isset($_FILES['product_image']) && $_FILES['product_image']['error'] != UPLOAD_ERR_NO_FILE && $_FILES['product_image']['error'] != 0) {
            // Ada file yang diupload tapi ada error selain 'tidak ada file'
            $message = "Terjadi error pada file gambar yang diupload. Kode Error: " . $_FILES['product_image']['error'];
            $message_type = "danger";
        }
        // Jika tidak ada file gambar yang diupload, $product_image_name_to_save akan tetap null.

        // Hanya lanjut jika tidak ada error dari validasi input atau upload gambar sejauh ini
        if ($message_type !== "danger") {
            // SQL INSERT dengan kolom id_kategori
            // Diasumsikan kolom created_at di tabel product Anda di-set DEFAULT CURRENT_TIMESTAMP
            $sql = "INSERT INTO product (name, description, brand, price, stock, size, image, id_kategori) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                // Bind variabel ke prepared statement sebagai parameter
                // Tipe data: s(name), s(desc), s(brand), d(price), i(stock), s(size), s(image), i(id_kategori)
                $stmt->bind_param("sssdissi", 
                    $product_name, 
                    $product_description, 
                    $product_brand, 
                    $product_price, 
                    $product_stock,
                    $product_size,
                    $product_image_name_to_save, // Nama file gambar (bisa NULL jika tidak ada upload/gagal)
                    $id_kategori_input // ID kategori (bisa NULL jika opsional)
                );

                if ($stmt->execute()) {
                    $message = "Produk baru berhasil ditambahkan!";
                    $message_type = "success";
                } else {
                    $message = "Error: Tidak bisa mengeksekusi statement. " . $stmt->error;
                    $message_type = "danger";
                }
                $stmt->close();
            } else {
                $message = "Error: Tidak bisa mempersiapkan statement. " . $conn->error;
                $message_type = "danger";
            }
        }
    }
} else {
    // Jika halaman diakses tidak melalui metode POST
    $message = "Metode request tidak valid.";
    $message_type = "danger";
    // Sebaiknya redirect ke halaman form jika diakses langsung
    header("location: tambah_produk.php");
    exit;
}

// Simpan pesan ke session untuk ditampilkan di halaman tambah_produk.php atau halaman lain
$_SESSION['form_message'] = $message;
$_SESSION['form_message_type'] = $message_type;

// Tutup koneksi database
if (isset($conn)) {
    $conn->close();
}

// Arahkan kembali, jika sukses ke daftar produk, jika gagal kembali ke form tambah
if ($message_type === "success") {
    header("location: admin_dashboard.php#produk-section");
} else {
    // Jika gagal, kembali ke form tambah produk agar pengguna bisa memperbaiki
    // Data form yang sudah diinput bisa disimpan di session untuk diisi ulang di tambah_produk.php
    // $_SESSION['form_data_produk'] = $_POST; // Contoh
    header("location: tambah_produk.php");
}
exit;
?>
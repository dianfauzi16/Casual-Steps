<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login dan ada user_id di session
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    // Jika tidak, mungkin arahkan ke login atau tampilkan error.
    // Untuk skrip proses, biasanya kita langsung hentikan atau beri respon error jika akses tidak sah.
    // Namun, karena ini dipanggil dari form, idealnya form tidak akan bisa diakses jika belum login.
    // Kita akan mengandalkan redirect di halaman form jika belum login.
    // Jika sampai sini tanpa user_id, itu masalah.
    $_SESSION['alamat_message'] = "Sesi tidak valid atau Anda belum login.";
    $_SESSION['alamat_message_type'] = "danger";
    header('Location: alamat_saya.php'); // Kembali ke halaman daftar alamat
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Ambil dan bersihkan data input
    $label = trim($_POST['label'] ?? '');
    $recipient_name = trim($_POST['recipient_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $street_address = trim($_POST['street_address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'Indonesia'); // Default ke Indonesia jika tidak diisi
    $is_primary = isset($_POST['is_primary']) ? 1 : 0; // 1 jika dicentang, 0 jika tidak

    // 4. Validasi Input
    if (empty($label)) $errors[] = "Label alamat wajib diisi.";
    if (empty($recipient_name)) $errors[] = "Nama penerima wajib diisi.";
    if (empty($phone_number)) $errors[] = "Nomor telepon penerima wajib diisi.";
    if (empty($street_address)) $errors[] = "Alamat lengkap wajib diisi.";
    if (empty($city)) $errors[] = "Kota/Kabupaten wajib diisi.";
    if (empty($province)) $errors[] = "Provinsi wajib diisi.";
    if (empty($postal_code)) $errors[] = "Kode pos wajib diisi.";
    if (empty($country)) $errors[] = "Negara wajib diisi."; // Meskipun ada default, validasi tetap baik

    // Jika tidak ada error validasi
    if (empty($errors)) {
        $conn->begin_transaction(); // Mulai transaksi

        try {
            // 5. Jika alamat baru ini akan dijadikan utama, set semua alamat lain user ini menjadi tidak utama (is_primary = 0)
            if ($is_primary == 1) {
                $sql_update_others = "UPDATE addresses SET is_primary = 0 WHERE user_id = ?";
                if ($stmt_update_others = $conn->prepare($sql_update_others)) {
                    $stmt_update_others->bind_param("i", $user_id);
                    if (!$stmt_update_others->execute()) {
                        throw new Exception("Gagal mengatur ulang alamat utama lainnya: " . $stmt_update_others->error);
                    }
                    $stmt_update_others->close();
                } else {
                    throw new Exception("Gagal mempersiapkan statement untuk update alamat lain: " . $conn->error);
                }
            }

            // 6. Simpan alamat baru ke database
            $sql_insert_address = "INSERT INTO addresses (user_id, label, recipient_name, phone_number, street_address, city, province, postal_code, country, is_primary) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            if ($stmt_insert = $conn->prepare($sql_insert_address)) {
                $stmt_insert->bind_param("issssssssi", 
                    $user_id, 
                    $label, 
                    $recipient_name, 
                    $phone_number, 
                    $street_address, 
                    $city, 
                    $province, 
                    $postal_code, 
                    $country, 
                    $is_primary
                );

                if ($stmt_insert->execute()) {
                    $conn->commit(); // Commit transaksi jika semua berhasil
                    $_SESSION['alamat_message'] = "Alamat baru berhasil ditambahkan.";
                    $_SESSION['alamat_message_type'] = "success";
                    unset($_SESSION['form_data_alamat']); // Hapus data form dari session jika sukses
                } else {
                    throw new Exception("Gagal menyimpan alamat baru: " . $stmt_insert->error);
                }
                $stmt_insert->close();
            } else {
                throw new Exception("Gagal mempersiapkan statement untuk insert alamat: " . $conn->error);
            }

        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaksi jika ada error
            $_SESSION['alamat_message'] = "Terjadi kesalahan: " . $e->getMessage();
            $_SESSION['alamat_message_type'] = "danger";
            $_SESSION['form_data_alamat'] = $_POST; // Simpan kembali data form
        }

    } else {
        // Jika ada error validasi, simpan error dan data form ke session
        $_SESSION['alamat_message'] = implode("<br>", $errors);
        $_SESSION['alamat_message_type'] = "danger";
        // Data form sudah disimpan di awal script
    }

    // Tutup koneksi database
    if (isset($conn)) {
        $conn->close();
    }

    // Redirect kembali ke halaman tambah alamat (jika error) atau daftar alamat (jika sukses/info)
    if (!empty($errors)) {
        header('Location: tambah_alamat.php');
    } else {
        header('Location: alamat_saya.php');
    }
    exit;

} else {
    // Jika bukan metode POST, arahkan ke halaman daftar alamat
    $_SESSION['alamat_message'] = "Permintaan tidak valid.";
    $_SESSION['alamat_message_type'] = "danger";
    header('Location: alamat_saya.php');
    exit;
}
?>
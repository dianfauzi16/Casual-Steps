<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login dan ada user_id di session
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    $_SESSION['alamat_message'] = "Sesi tidak valid atau Anda belum login.";
    $_SESSION['alamat_message_type'] = "danger";
    header('Location: alamat_saya.php'); // Kembali ke halaman daftar alamat
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['address_id'])) {
    // 3. Ambil dan bersihkan data input
    $address_id = filter_var($_POST['address_id'], FILTER_VALIDATE_INT);
    $label = trim($_POST['label'] ?? '');
    $recipient_name = trim($_POST['recipient_name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $street_address = trim($_POST['street_address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? 'Indonesia'); // Default jika tidak diubah
    $is_primary = isset($_POST['is_primary']) ? 1 : 0;

    // 4. Validasi Input
    if ($address_id === false || $address_id <= 0) {
        $errors[] = "ID Alamat tidak valid.";
    }
    if (empty($label)) $errors[] = "Label alamat wajib diisi.";
    if (empty($recipient_name)) $errors[] = "Nama penerima wajib diisi.";
    if (empty($phone_number)) $errors[] = "Nomor telepon penerima wajib diisi.";
    if (empty($street_address)) $errors[] = "Alamat lengkap wajib diisi.";
    if (empty($city)) $errors[] = "Kota/Kabupaten wajib diisi.";
    if (empty($province)) $errors[] = "Provinsi wajib diisi.";
    if (empty($postal_code)) $errors[] = "Kode pos wajib diisi.";
    if (empty($country)) $errors[] = "Negara wajib diisi.";

    // Jika tidak ada error validasi
    if (empty($errors)) {
        $conn->begin_transaction(); // Mulai transaksi

        try {
            // Verifikasi dulu apakah alamat ini benar-benar milik user yang login
            $sql_check_owner = "SELECT id FROM addresses WHERE id = ? AND user_id = ?";
            $stmt_check = $conn->prepare($sql_check_owner);
            $stmt_check->bind_param("ii", $address_id, $user_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $stmt_check->close();

            if ($result_check->num_rows == 0) {
                throw new Exception("Anda tidak memiliki izin untuk mengedit alamat ini atau alamat tidak ditemukan.");
            }

            // Jika alamat ini akan dijadikan utama, set semua alamat lain user ini menjadi tidak utama
            if ($is_primary == 1) {
                $sql_update_others = "UPDATE addresses SET is_primary = 0 WHERE user_id = ? AND id != ?";
                if ($stmt_update_others = $conn->prepare($sql_update_others)) {
                    $stmt_update_others->bind_param("ii", $user_id, $address_id);
                    if (!$stmt_update_others->execute()) {
                        throw new Exception("Gagal mengatur ulang alamat utama lainnya: " . $stmt_update_others->error);
                    }
                    $stmt_update_others->close();
                } else {
                    throw new Exception("Gagal mempersiapkan statement untuk update alamat lain: " . $conn->error);
                }
            }

            // Update alamat di database
            $sql_update_address = "UPDATE addresses SET 
                                       label = ?, recipient_name = ?, phone_number = ?, 
                                       street_address = ?, city = ?, province = ?, 
                                       postal_code = ?, country = ?, is_primary = ?
                                   WHERE id = ? AND user_id = ?"; // Pastikan update hanya untuk user ini
            
            if ($stmt_update = $conn->prepare($sql_update_address)) {
                $stmt_update->bind_param("ssssssssiii", 
                    $label, 
                    $recipient_name, 
                    $phone_number, 
                    $street_address, 
                    $city, 
                    $province, 
                    $postal_code, 
                    $country, 
                    $is_primary,
                    $address_id,
                    $user_id 
                );

                if ($stmt_update->execute()) {
                    if ($stmt_update->affected_rows > 0 || $is_primary == 1) { // Jika ada baris terupdate atau jika is_primary diubah
                        $conn->commit(); 
                        $_SESSION['alamat_message'] = "Alamat berhasil diperbarui.";
                        $_SESSION['alamat_message_type'] = "success";
                    } else {
                        $conn->commit(); // Tetap commit jika tidak ada error, mungkin tidak ada perubahan data
                        $_SESSION['alamat_message'] = "Tidak ada perubahan pada data alamat.";
                        $_SESSION['alamat_message_type'] = "info";
                    }
                } else {
                    throw new Exception("Gagal memperbarui alamat: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                throw new Exception("Gagal mempersiapkan statement untuk update alamat: " . $conn->error);
            }

        } catch (Exception $e) {
            $conn->rollback(); 
            $_SESSION['alamat_message'] = "Terjadi kesalahan: " . $e->getMessage();
            $_SESSION['alamat_message_type'] = "danger";
            // Simpan input kembali ke session agar form bisa diisi ulang jika perlu (opsional)
            // $_SESSION['form_data_edit_alamat'] = $_POST; 
        }

    } else {
        // Jika ada error validasi, simpan error ke session
        $_SESSION['alamat_message'] = implode("<br>", $errors);
        $_SESSION['alamat_message_type'] = "danger";
        // $_SESSION['form_data_edit_alamat'] = $_POST;
    }

    // Tutup koneksi database
    if (isset($conn)) {
        $conn->close();
    }

    // Redirect kembali ke halaman daftar alamat
    // Jika ada error validasi, kembali ke form edit dengan ID yang sama
    if (!empty($errors) && $address_id) {
        header('Location: edit_alamat.php?id=' . $address_id);
    } else {
        header('Location: alamat_saya.php');
    }
    exit;

} else {
    $_SESSION['alamat_message'] = "Permintaan tidak valid.";
    $_SESSION['alamat_message_type'] = "danger";
    header('Location: alamat_saya.php');
    exit;
}
?>
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    // Jika tidak login, tidak ada yang bisa diproses
    header('Location: login_pelanggan.php');
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$user_id = $_SESSION['user_id'];
$errors = []; // Untuk menyimpan pesan error validasi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 3. Ambil dan bersihkan data input
    $name = trim($_POST['name'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $date_of_birth = trim($_POST['date_of_birth'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $profile_picture_file = $_FILES['profile_picture'] ?? null;

    // 4. Validasi Input
    if (empty($name)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }
    if (!empty($date_of_birth)) {
        $d = DateTime::createFromFormat('Y-m-d', $date_of_birth);
        if (!$d || $d->format('Y-m-d') !== $date_of_birth) {
            $errors[] = "Format tanggal lahir tidak valid.";
        }
    }

    // Validasi nomor telepon (opsional, bisa lebih kompleks jika diperlukan)
    // Jika diisi, pastikan formatnya wajar. Jika dikosongkan, kita akan simpan sebagai NULL.
    if (!empty($phone_number) && !preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $phone_number)) {
        // $errors[] = "Format nomor telepon tidak valid."; 
        // Untuk sementara, kita biarkan format sederhana. Jika ingin lebih ketat, aktifkan baris error ini.
    }
    $profile_picture_db_path = null;
    // Handle file upload
    if (isset($profile_picture_file) && $profile_picture_file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../ADMIN MENU/uploads/profile_pictures/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_info = pathinfo($profile_picture_file['name']);
        $file_ext = strtolower($file_info['extension']);
        $allowed_exts = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_ext, $allowed_exts)) {
            $errors[] = "Format file foto profil tidak diizinkan. Hanya JPG, PNG, GIF.";
        } elseif ($profile_picture_file['size'] > 2097152) { // 2MB
            $errors[] = "Ukuran file foto profil tidak boleh lebih dari 2MB.";
        } else {
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
            $destination = $upload_dir . $new_filename;
            if (move_uploaded_file($profile_picture_file['tmp_name'], $destination)) {
                $profile_picture_db_path = 'uploads/profile_pictures/' . $new_filename;
            } else {
                $errors[] = "Gagal mengunggah foto profil.";
            }
        }
    } elseif (isset($profile_picture_file) && $profile_picture_file['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = "Terjadi kesalahan saat mengunggah file.";
    }

    // Jika tidak ada error validasi
    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // 1. Update tabel `users`
            $sql_update_user = "UPDATE users SET name = ?, phone_number = ? WHERE id = ?";
            $stmt_user = $conn->prepare($sql_update_user);
            if (!$stmt_user) throw new Exception("Gagal mempersiapkan update user: " . $conn->error);

            $phone_to_save = !empty($phone_number) ? $phone_number : null;
            $stmt_user->bind_param("ssi", $name, $phone_to_save, $user_id);
            if (!$stmt_user->execute()) throw new Exception("Gagal update user: " . $stmt_user->error);
            $stmt_user->close();

            // 2. Dapatkan path foto profil lama untuk dihapus jika ada foto baru
            $old_pic_path = null;
            if ($profile_picture_db_path) {
                $sql_get_old_pic = "SELECT profile_picture_url FROM user_profiles WHERE user_id = ?";
                $stmt_get_pic = $conn->prepare($sql_get_old_pic);
                $stmt_get_pic->bind_param("i", $user_id);
                $stmt_get_pic->execute();
                $result_pic = $stmt_get_pic->get_result();
                if ($row_pic = $result_pic->fetch_assoc()) {
                    $old_pic_path = $row_pic['profile_picture_url'];
                }
                $stmt_get_pic->close();
            }

            // 3. Insert atau Update tabel `user_profiles`
            // Jika ada foto baru, gunakan path baru. Jika tidak, JANGAN ubah kolom foto.
            if ($profile_picture_db_path) {
                $sql_profile = "INSERT INTO user_profiles (user_id, date_of_birth, bio, profile_picture_url) VALUES (?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), bio = VALUES(bio), profile_picture_url = VALUES(profile_picture_url)";
                $stmt_profile = $conn->prepare($sql_profile);
                if (!$stmt_profile) throw new Exception("Gagal mempersiapkan update profil (dengan foto): " . $conn->error);
                $dob_to_save = !empty($date_of_birth) ? $date_of_birth : null;
                $stmt_profile->bind_param("isss", $user_id, $dob_to_save, $bio, $profile_picture_db_path);
            } else {
                // Query tanpa mengubah foto profil
                $sql_profile = "INSERT INTO user_profiles (user_id, date_of_birth, bio) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), bio = VALUES(bio)";
                $stmt_profile = $conn->prepare($sql_profile);
                if (!$stmt_profile) throw new Exception("Gagal mempersiapkan update profil (tanpa foto): " . $conn->error);
                $dob_to_save = !empty($date_of_birth) ? $date_of_birth : null;
                $stmt_profile->bind_param("iss", $user_id, $dob_to_save, $bio);
            }

            if (!$stmt_profile->execute()) throw new Exception("Gagal update profil: " . $stmt_profile->error);
            $stmt_profile->close();

            // Jika semua query berhasil, commit transaksi
            $conn->commit();

            // Hapus foto lama jika ada foto baru yang berhasil diupload dan disimpan
            if ($profile_picture_db_path && $old_pic_path && file_exists(__DIR__ . '/../ADMIN MENU/' . $old_pic_path)) {
                unlink(__DIR__ . '/../ADMIN MENU/' . $old_pic_path);
            }

            $_SESSION['profil_message'] = "Profil Anda berhasil diperbarui.";
            $_SESSION['profil_message_type'] = "success";
            $_SESSION['user_name'] = $name; // Update session nama

        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['profil_message'] = "Terjadi kesalahan: " . $e->getMessage();
            $_SESSION['profil_message_type'] = "danger";

            // Jika file sudah terupload tapi transaksi gagal, hapus file yang baru diupload
            if ($profile_picture_db_path && file_exists(__DIR__ . '/../ADMIN MENU/' . $profile_picture_db_path)) {
                unlink(__DIR__ . '/../ADMIN MENU/' . $profile_picture_db_path);
            }
        }
    } else {
        // Jika ada error validasi, simpan error ke session
        $_SESSION['profil_message'] = implode("<br>", $errors);
        $_SESSION['profil_message_type'] = "danger";
        // Simpan input kembali ke session agar form bisa diisi ulang jika kembali ke mode edit
        $_SESSION['form_data_edit_profil'] = $_POST;
    }
    // Tutup koneksi database
    if (isset($conn)) {
        $conn->close();
    }

    // Redirect kembali ke halaman akun saya
    // Jika ada error validasi, arahkan kembali ke mode edit. Jika sukses/info, ke mode tampilan.
    if (!empty($errors)) {
        header('Location: akun_saya.php?action=edit');
    } else {
        header('Location: akun_saya.php');
    }
    exit;
} else {
    // Jika bukan metode POST, arahkan ke halaman akun saya
    header('Location: akun_saya.php');
    exit;
}

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: login_pelanggan.php');
    exit;
}

// 2. Sertakan file koneksi database
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

$user_id = $_SESSION['user_id'];
$errors = []; // Untuk menyimpan pesan error validasi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    // Validasi input
    if (empty($old_password)) {
        $errors[] = "Password lama wajib diisi.";
    }
    if (empty($new_password)) {
        $errors[] = "Password baru wajib diisi.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Password baru minimal harus 8 karakter.";
    } elseif (!preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $errors[] = "Password baru harus mengandung setidaknya satu huruf dan satu angka.";
    }
    if (empty($confirm_new_password)) {
        $errors[] = "Konfirmasi password baru wajib diisi.";
    } elseif ($new_password !== $confirm_new_password) {
        $errors[] = "Password baru dan konfirmasi password baru tidak cocok.";
    }

    // Jika tidak ada error validasi awal
    if (empty($errors)) {
        // Ambil password hash saat ini dari database
        $sql_get_current_password = "SELECT password FROM users WHERE id = ?";
        if ($stmt_get = $conn->prepare($sql_get_current_password)) {
            $stmt_get->bind_param("i", $user_id);
            $stmt_get->execute();
            $result_current_password = $stmt_get->get_result();
            
            if ($result_current_password->num_rows == 1) {
                $user = $result_current_password->fetch_assoc();
                $current_hashed_password = $user['password'];

                // Verifikasi password lama
                if (password_verify($old_password, $current_hashed_password)) {
                    // Password lama cocok, hash password baru dan update database
                    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                    $sql_update_password = "UPDATE users SET password = ? WHERE id = ?";
                    if ($stmt_update = $conn->prepare($sql_update_password)) {
                        $stmt_update->bind_param("si", $new_hashed_password, $user_id);
                        if ($stmt_update->execute()) {
                            $_SESSION['password_change_message'] = "Password Anda berhasil diperbarui. Silakan login kembali dengan password baru Anda jika sesi ini terputus.";
                            $_SESSION['password_change_type'] = "success";
                            // Opsional: Hancurkan session saat ini dan paksa login ulang untuk keamanan tambahan
                            // unset($_SESSION['user_id']);
                            // unset($_SESSION['user_name']);
                            // unset($_SESSION['user_email']);
                            // unset($_SESSION['user_loggedin']);
                            // session_destroy();
                            // header('Location: login_pelanggan.php?password_updated=1');
                            // exit;
                        } else {
                            $_SESSION['password_change_message'] = "Gagal memperbarui password di database: " . $stmt_update->error;
                            $_SESSION['password_change_type'] = "danger";
                        }
                        $stmt_update->close();
                    } else {
                        $_SESSION['password_change_message'] = "Gagal mempersiapkan statement update password: " . $conn->error;
                        $_SESSION['password_change_type'] = "danger";
                    }
                } else {
                    // Password lama tidak cocok
                    $_SESSION['password_change_message'] = "Password lama yang Anda masukkan salah.";
                    $_SESSION['password_change_type'] = "danger";
                }
            } else {
                // User tidak ditemukan (seharusnya tidak terjadi jika sudah login)
                $_SESSION['password_change_message'] = "Pengguna tidak ditemukan.";
                $_SESSION['password_change_type'] = "danger";
            }
            $stmt_get->close();
        } else {
            $_SESSION['password_change_message'] = "Gagal mengambil data pengguna: " . $conn->error;
            $_SESSION['password_change_type'] = "danger";
        }
    } else {
        // Jika ada error validasi awal, simpan error ke session
        $_SESSION['password_change_message'] = implode("<br>", $errors);
        $_SESSION['password_change_type'] = "danger";
    }

    if (isset($conn)) {
        $conn->close();
    }

    header('Location: ubah_password.php'); // Selalu kembali ke halaman ubah password untuk lihat pesan
    exit;

} else {
    header('Location: ubah_password.php');
    exit;
}
?>
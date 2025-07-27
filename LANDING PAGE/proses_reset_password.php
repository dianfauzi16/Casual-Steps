<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = trim($_POST['token'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    $errors = [];
    $user_id = null;

    // 1. Validasi token (ulang untuk keamanan)
    if (empty($token)) {
        $errors[] = "Token reset password tidak ditemukan.";
    } else {
        $sql_check_token = "SELECT user_id, expires_at FROM password_resets WHERE token = ?";
        if ($stmt = $conn->prepare($sql_check_token)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $expires_at = strtotime($row['expires_at']);
                $current_time = time();

                if ($current_time < $expires_at) {
                    $user_id = $row['user_id'];
                } else {
                    $errors[] = "Tautan reset password sudah kadaluarsa.";
                }
            } else {
                $errors[] = "Tautan reset password tidak valid.";
            }
            $stmt->close();
        } else {
            $errors[] = "Terjadi kesalahan database saat memverifikasi token.";
        }
    }

    // 2. Validasi password baru
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

    if (empty($errors) && $user_id) {
        $conn->begin_transaction();
        try {
            // 3. Hash password baru
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // 4. Update password di tabel users
            $sql_update_password = "UPDATE users SET password = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_password)) {
                $stmt_update->bind_param("si", $hashed_password, $user_id);
                if (!$stmt_update->execute()) {
                    throw new Exception("Gagal memperbarui password: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                throw new Exception("Gagal mempersiapkan statement update password: " . $conn->error);
            }

            // 5. Hapus token dari tabel password_resets (atau tandai sebagai tidak aktif)
            $sql_delete_token = "DELETE FROM password_resets WHERE token = ?";
            if ($stmt_delete = $conn->prepare($sql_delete_token)) {
                $stmt_delete->bind_param("s", $token);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Gagal menghapus token reset password: " . $stmt_delete->error);
                }
                $stmt_delete->close();
            } else {
                throw new Exception("Gagal mempersiapkan statement hapus token: " . $conn->error);
            }

            $conn->commit();
            $_SESSION['password_reset_message'] = "Password Anda berhasil diatur ulang. Silakan login dengan password baru Anda.";
            $_SESSION['password_reset_message_type'] = "success";
            header('Location: reset_sukses.php');
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Terjadi kesalahan saat mengatur ulang password: " . $e->getMessage();
        }
    }

    // Jika ada error, simpan ke session dan redirect kembali ke halaman reset password
    $_SESSION['password_reset_message'] = implode("<br>", $errors);
    $_SESSION['password_reset_message_type'] = "danger";
    header('Location: reset_password.php?token=' . urlencode($token));
    exit;
} else {
    header('Location: lupa_password.php');
    exit;
}

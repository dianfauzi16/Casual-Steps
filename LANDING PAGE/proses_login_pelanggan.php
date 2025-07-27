<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\IdTokenVerificationFailed; 

// Sertakan file koneksi database (pastikan path ini benar)
require_once __DIR__ . '/../ADMIN MENU/db_connect.php';

// Jika pengguna sudah login, arahkan ke halaman utama
if (isset($_SESSION['user_id'])) { // Anda bisa juga cek $_SESSION['user_loggedin']
    header('Location: index.php');
    exit;
}

// Simpan data form untuk diisi ulang jika login gagal (opsional, tapi baik untuk UX)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['form_data_login'] = $_POST;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email dan password wajib diisi.";
        header('Location: login_pelanggan.php');
        exit;
    }

    // MODIFIKASI SQL: Ambil juga kolom 'account_status'
    $sql_get_user = "SELECT id, name, email, password, account_status FROM users WHERE email = ?";

    if ($stmt = $conn->prepare($sql_get_user)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // PENAMBAHAN: Cek status akun sebelum verifikasi password
            if (isset($user['account_status']) && $user['account_status'] === 'nonaktif') {
                $_SESSION['login_error'] = "Akun Anda saat ini tidak aktif. Silakan hubungi administrator.";
                // Data form sudah disimpan di session di awal, jadi tidak perlu unset di sini
                header('Location: login_pelanggan.php');
                exit;
            }
            // AKHIR PENAMBAHAN

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password cocok, login berhasil
                unset($_SESSION['form_data_login']); // Hapus data form dari session karena sudah berhasil

                // Simpan informasi pengguna ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_loggedin'] = true; // Penanda bahwa pelanggan sudah login


                // Arahkan ke URL yang disimpan di session jika ada, jika tidak ke halaman utama
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect_url = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']); // Hapus dari session setelah digunakan
                    header('Location: ' . $redirect_url);
                } else {
                    header('Location: index.php'); // Fallback ke halaman utama
                }
                exit;
            } else {
                // Password tidak cocok
                $_SESSION['login_error'] = "Email atau password salah.";
            }
        } else {
            // Email tidak ditemukan
            $_SESSION['login_error'] = "Email atau password salah.";
        }
        $stmt->close();
    } else {
        $_SESSION['login_error'] = "Terjadi kesalahan pada database: " . $conn->error;
        // Sebaiknya catat error ini ke log server untuk debugging lebih lanjut
        // error_log("Database prepare error in proses_login_pelanggan.php: " . $conn->error);
    }

    // Tutup koneksi database hanya jika statement tidak error dan koneksi ada
    if (isset($conn) && empty($conn->error) && (isset($stmt) && empty($stmt->error))) {
        $conn->close();
    }

    // Jika login gagal, arahkan kembali ke halaman login
    header('Location: login_pelanggan.php');
    exit;
} else {
    // Jika bukan metode POST, arahkan ke halaman login
    header('Location: login_pelanggan.php');
    exit;
}

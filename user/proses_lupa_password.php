<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/admin/db_connect.php';
require_once dirname(__DIR__) . '/admin/PHPMailer/PHPMailer.php';
require_once dirname(__DIR__) . '/admin/PHPMailer/SMTP.php';
require_once dirname(__DIR__) . '/admin/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['password_reset_message'] = "Email tidak valid.";
        $_SESSION['password_reset_message_type'] = "danger";
        header('Location: lupa_password.php');
        exit;
    }

    // 1. Cek apakah email terdaftar di database
    $sql_get_user = "SELECT id, name FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql_get_user)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $user_name = $user['name'];

            // 2. Hasilkan token unik dan waktu kadaluarsa
            $token = bin2hex(random_bytes(32)); // Token acak 64 karakter hex
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Kadaluarsa dalam 1 jam

            // 3. Simpan token ke tabel password_resets (atau update jika sudah ada)
            $sql_insert_token = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)";
            if ($stmt_token = $conn->prepare($sql_insert_token)) {
                $stmt_token->bind_param("iss", $user_id, $token, $expires_at);
                $stmt_token->execute();
                $stmt_token->close();

                // Dapatkan protokol (http/https) secara dinamis
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https" : "http";
                // Dapatkan host (misal: localhost atau alamat ngrok) secara dinamis
                $host = $_SERVER['HTTP_HOST'];
                // Dapatkan path direktori dari skrip saat ini secara dinamis
                $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\'); // Menghasilkan /FP/LANDING PAGE
                // Bangun base URL yang lengkap dan dinamis
                $base_url = $protocol . '://' . $host . $path . '/';
                // 4. Kirim email dengan tautan reset
                $mail = new PHPMailer(true);
                try {
                    // Konfigurasi Server SMTP (Ganti dengan detail SMTP Anda)
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Contoh: smtp.gmail.com untuk Gmail
                    $mail->SMTPAuth = true;
                    $smtp_user = getenv('SMTP_USER') ?: ($_ENV['SMTP_USER'] ?? ($_SERVER['SMTP_USER'] ?? ''));
                    $smtp_pass = getenv('SMTP_PASS') ?: ($_ENV['SMTP_PASS'] ?? ($_SERVER['SMTP_PASS'] ?? ''));
                    
                    // Fallback manual membaca file .env jika getenv/$_ENV gagal di Windows/Laragon
                    if (empty($smtp_user) || empty($smtp_pass)) {
                        $envPath = dirname(__DIR__) . '/.env';
                        if (file_exists($envPath)) {
                            $envLines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                            foreach ($envLines as $line) {
                                if (strpos(trim($line), 'SMTP_USER=') === 0) {
                                    $smtp_user = trim(str_replace('"', '', substr(trim($line), 10)));
                                }
                                if (strpos(trim($line), 'SMTP_PASS=') === 0) {
                                    $smtp_pass = trim(str_replace('"', '', substr(trim($line), 10)));
                                }
                            }
                        }
                    }

                    $mail->Username = $smtp_user; // Diambil dari .env
                    $mail->Password = $smtp_pass; // Diambil dari .env
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Atau ENCRYPTION_SMTPS untuk port 465
                    $mail->Port = 587; // Port SMTP, biasanya 587 untuk STARTTLS, 465 untuk SMTPS

                    // Pengaturan Pengirim dan Penerima
                    $mail->setFrom('no-reply@casualsteps.com', 'Casual Steps No-Reply');
                    $mail->addAddress($email, $user_name);

                    // Konten Email
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password Akun Casual Steps Anda';
                    $mail->Body    = 'Halo ' . htmlspecialchars($user_name) . ',<br><br>'
                        . 'Anda telah meminta untuk mengatur ulang password akun Casual Steps Anda.<br>'
                        . 'Silakan klik tautan di bawah ini untuk melanjutkan:<br><br>'
                        . '<a href="' . $base_url . 'reset_password.php?token=' . $token . '">'
                        . 'Atur Ulang Password Anda</a><br><br>'
                        . 'Tautan ini akan kadaluarsa dalam 1 jam.<br>'
                        . 'Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.<br><br>'
                        . 'Terima kasih,<br>'
                        . 'Tim Casual Steps';
                    $mail->AltBody = 'Halo ' . htmlspecialchars($user_name) . ',' . "\n\n"
                        . 'Anda telah meminta untuk mengatur ulang password akun Casual Steps Anda.' . "\n"
                        . 'Silakan salin dan tempel tautan berikut di browser Anda untuk melanjutkan:' . "\n"
                        . $base_url . 'reset_password.php?token=' . $token . "\n\n"
                        . 'Tautan ini akan kadaluarsa dalam 1 jam.' . "\n"
                        . 'Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini.' . "\n\n"
                        . 'Terima kasih,' . "\n"
                        . 'Tim Casual Steps';

                    $mail->send();
                    $_SESSION['password_reset_message'] = "Tautan reset password telah dikirimkan ke email Anda.";
                    $_SESSION['password_reset_message_type'] = "success";
                } catch (Exception $e) {
                    $_SESSION['password_reset_message'] = "Gagal mengirim email reset password. Error: " . $mail->ErrorInfo;
                    $_SESSION['password_reset_message_type'] = "danger";
                    // Log error lebih detail di server
                    error_log("PHPMailer Error: " . $mail->ErrorInfo);
                }
            } else {
                $_SESSION['password_reset_message'] = "Terjadi kesalahan database saat menyimpan token.";
                $_SESSION['password_reset_message_type'] = "danger";
            }
        } else {
            // Email tidak ditemukan, berikan pesan generik untuk keamanan
            $_SESSION['password_reset_message'] = "Jika email Anda terdaftar, tautan reset password telah dikirimkan ke email Anda.";
            $_SESSION['password_reset_message_type'] = "success";
        }
        $stmt->close();
    } else {
        $_SESSION['password_reset_message'] = "Terjadi kesalahan database: " . $conn->error;
        $_SESSION['password_reset_message_type'] = "danger";
    }

    $conn->close();
    header('Location: lupa_password.php');
    exit;
} else {
    header('Location: lupa_password.php');
    exit;
}

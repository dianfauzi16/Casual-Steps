<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek jika admin sudah login
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    $_SESSION['pesan_kontak_message'] = "Akses ditolak. Silakan login terlebih dahulu.";
    $_SESSION['pesan_kontak_message_type'] = "danger";
    header("location: admin_login.php");
    exit;
}

require_once 'db_connect.php';

// Sertakan PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Sesuaikan path ini dengan lokasi PHPMailer Anda
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$message = "";
$message_type = "";
$redirect_id_pesan = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_pesan = filter_var($_POST['id_pesan'] ?? '', FILTER_VALIDATE_INT);
    $user_email = trim($_POST['user_email'] ?? '');
    $user_name = trim($_POST['user_name'] ?? '');
    $original_subject = trim($_POST['original_subject'] ?? '');
    $original_message = trim($_POST['original_message'] ?? '');
    $admin_reply = trim($_POST['admin_reply'] ?? '');

    $redirect_id_pesan = $id_pesan; // Untuk redirect kembali ke halaman detail pesan

    if (!$id_pesan || empty($user_email) || empty($admin_reply)) {
        $message = "Data tidak lengkap atau tidak valid.";
        $message_type = "danger";
    } else {
        // Mulai transaksi database
        $conn->begin_transaction();
        try {
            // 1. Update pesan_kontak di database dengan balasan admin
            $sql_update_pesan = "UPDATE pesan_kontak SET admin_reply_message = ?, admin_reply_timestamp = NOW(), status_baca = 'sudah dibalas' WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_pesan)) {
                $stmt_update->bind_param("si", $admin_reply, $id_pesan);
                if (!$stmt_update->execute()) {
                    throw new Exception("Gagal menyimpan balasan ke database: " . $stmt_update->error);
                }
                $stmt_update->close();
            } else {
                throw new Exception("Gagal mempersiapkan statement update pesan: " . $conn->error);
            }

            // 2. Kirim email balasan ke pengguna
            $mail = new PHPMailer(true);
            // Konfigurasi SMTP (Ganti dengan detail Gmail Anda)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Server SMTP Gmail
            $mail->SMTPAuth = true;
            $mail->Username = 'dianfauzi16@students.amikom.ac.id'; // Ganti dengan email Gmail Anda
            $mail->Password = 'mzzzrgudlmiujniv'; // Ganti dengan App Password Gmail Anda
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Gunakan TLS
            $mail->Port = 587; // Port untuk TLS
            $mail->CharSet = 'UTF-8'; // Penting untuk karakter non-ASCII

            // Pengaturan Email
            $mail->setFrom('dianfauzi16@students.amikom.ac.id', 'Admin Casual Steps'); // Ganti dengan email Gmail Anda
            $mail->addAddress($user_email, $user_name); // Email dan nama penerima
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Balasan Pesan Anda dari Casual Steps: ' . $original_subject;
            $mail->Body    = 'Halo ' . htmlspecialchars($user_name) . ',<br><br>'
                . 'Terima kasih telah menghubungi kami. Berikut adalah balasan dari tim Casual Steps:<br><br>'
                . '---<br>'
                . '<strong>Pesan Anda Sebelumnya:</strong><br>'
                . nl2br(htmlspecialchars($original_message)) . '<br>'
                . '---<br><br>'
                . '<strong>Balasan dari Admin:</strong><br>'
                . nl2br(htmlspecialchars($admin_reply)) . '<br><br>'
                . 'Hormat kami,<br>'
                . 'Tim Casual Steps';
            $mail->AltBody = 'Halo ' . htmlspecialchars($user_name) . ",\n\n"
                . "Terima kasih telah menghubungi kami. Berikut adalah balasan dari tim Casual Steps:\n\n"
                . "--- Pesan Anda Sebelumnya ---\n"
                . htmlspecialchars($original_message) . "\n"
                . "--- Balasan dari Admin ---\n"
                . htmlspecialchars($admin_reply) . "\n\n"
                . "Hormat kami,\n"
                . "Tim Casual Steps";

            $mail->send();

            // Commit transaksi jika semua berhasil
            $conn->commit();
            $message = "Balasan berhasil dikirim dan email telah terkirim ke " . htmlspecialchars($user_email) . ".";
            $message_type = "success";
        } catch (Exception $e) {
            // Rollback transaksi jika ada error
            $conn->rollback();
            $message = "Gagal mengirim balasan. Error: " . $e->getMessage();
            $message_type = "danger";
            // Anda bisa log error PHPMailer lebih detail di sini: error_log("PHPMailer Error: " . $e->getMessage());
        }
    }
} else {
    $message = "Metode request tidak valid.";
    $message_type = "danger";
}

$_SESSION['pesan_kontak_message'] = $message;
$_SESSION['pesan_kontak_message_type'] = $message_type;

if (isset($conn)) {
    $conn->close();
}

// Redirect kembali ke halaman detail pesan atau daftar pesan
if ($redirect_id_pesan) {
    header("Location: admin_balas_pesan.php?id_pesan=" . $redirect_id_pesan);
} else {
    header("Location: admin-pesan-kontak.php");
}
exit;

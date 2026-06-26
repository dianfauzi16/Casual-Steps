<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class AdminContactController extends AdminBaseController {
    
    public function index() {
        $contactModel = $this->model('ContactModel');
        $messages = $contactModel->getAllMessages();
        
        $data = [
            'page_title' => 'Pesan Kontak Masuk',
            'messages' => $messages
        ];
        
        $this->renderAdminView('admin/contact/index', $data);
    }

    public function detail() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminContact/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $contactModel = $this->model('ContactModel');
        $message = $contactModel->getMessageById($id);

        if (!$message) {
            $_SESSION['form_message'] = "Pesan tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminContact/index");
            exit;
        }
        
        // Tandai sudah dibaca saat dibuka jika masih "belum dibaca"
        if ($message['status_baca'] == 'belum dibaca') {
            $contactModel->markAsRead($id);
            $message['status_baca'] = 'sudah dibaca'; // Update for view
        }

        $data = [
            'page_title' => 'Detail Pesan dari: ' . htmlspecialchars($message['nama']),
            'message' => $message
        ];

        $this->renderAdminView('admin/contact/detail', $data);
    }

    public function markRead() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $contactModel = $this->model('ContactModel');
            
            if ($contactModel->markAsRead($id)) {
                $_SESSION['form_message'] = "Pesan berhasil ditandai sudah dibaca.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal menandai pesan atau pesan sudah dibaca.";
                $_SESSION['form_message_type'] = "warning";
            }
        }
        header("location: " . BASE_URL . "index.php?url=AdminContact/index");
        exit;
    }

    public function reply() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_pesan = filter_var($_POST['id_pesan'] ?? '', FILTER_VALIDATE_INT);
            $user_email = trim($_POST['user_email'] ?? '');
            $user_name = trim($_POST['user_name'] ?? '');
            $original_subject = trim($_POST['original_subject'] ?? '');
            $original_message = trim($_POST['original_message'] ?? '');
            $admin_reply = trim($_POST['admin_reply'] ?? '');

            if (!$id_pesan || empty($user_email) || empty($admin_reply)) {
                $_SESSION['form_message'] = "Data tidak lengkap atau tidak valid.";
                $_SESSION['form_message_type'] = "danger";
                header("Location: " . BASE_URL . "index.php?url=AdminContact/detail&id=" . $id_pesan);
                exit;
            }

            $contactModel = $this->model('ContactModel');
            
            try {
                // Require manual PHPMailer files from admin folder
                require_once __DIR__ . '/../../admin/PHPMailer/Exception.php';
                require_once __DIR__ . '/../../admin/PHPMailer/PHPMailer.php';
                require_once __DIR__ . '/../../admin/PHPMailer/SMTP.php';

                // Gunakan PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'dianfauzi16@students.amikom.ac.id'; 
                $mail->Password = 'mzzzrgudlmiujniv'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587; 
                $mail->CharSet = 'UTF-8'; 

                $mail->setFrom('dianfauzi16@students.amikom.ac.id', 'Admin Casual Steps');
                $mail->addAddress($user_email, $user_name);
                $mail->isHTML(true);
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
                
                // Simpan ke database setelah sukses kirim email
                if ($contactModel->saveReply($id_pesan, $admin_reply)) {
                    $_SESSION['form_message'] = "Balasan berhasil dikirim dan email telah terkirim ke " . htmlspecialchars($user_email) . ".";
                    $_SESSION['form_message_type'] = "success";
                } else {
                    throw new Exception("Gagal menyimpan riwayat balasan ke database.");
                }
            } catch (Exception $e) {
                $_SESSION['form_message'] = "Gagal mengirim balasan. Error: " . $e->getMessage();
                $_SESSION['form_message_type'] = "danger";
            }
            
            header("Location: " . BASE_URL . "index.php?url=AdminContact/detail&id=" . $id_pesan);
            exit;
        }
    }
}

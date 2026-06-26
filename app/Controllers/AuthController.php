<?php

namespace App\Controllers;

use App\Core\Controller;
use Kreait\Firebase\Factory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController extends Controller {

    public function login() {
        $data = ['page_title' => 'Login Pelanggan'];
        $this->view('auth/login', $data);
    }

    public function register() {
        $data = ['page_title' => 'Registrasi Akun'];
        $this->view('auth/register', $data);
    }

    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $_SESSION['login_error'] = "Email dan password wajib diisi.";
                header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                exit;
            }

            $userModel = $this->model('UserModel');
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                if (isset($user['account_status']) && $user['account_status'] === 'nonaktif') {
                    $_SESSION['login_error'] = "Akun Anda saat ini tidak aktif. Silakan hubungi administrator.";
                    header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                    exit;
                }

                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_loggedin'] = true;

                    $redirect_url = $_SESSION['redirect_after_login'] ?? BASE_URL;
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect_url);
                    exit;
                }
            }
            
            $_SESSION['login_error'] = "Email atau password salah.";
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }
    }

    public function processRegister() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone_number = trim($_POST['phone_number'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $agree_terms = isset($_POST['agree_terms']);

            $full_name = trim($first_name . ' ' . $last_name);
            $errors = [];

            if (empty($first_name) || empty($last_name)) $errors[] = "Nama depan dan belakang wajib diisi.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Format email tidak valid.";
            
            $userModel = $this->model('UserModel');
            if ($userModel->checkEmailExists($email)) {
                $errors[] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
            }

            if (empty($password) || strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                $errors[] = "Password minimal 8 karakter dan mengandung setidaknya satu huruf serta angka.";
            }
            if ($password !== $confirm_password) $errors[] = "Konfirmasi password tidak cocok.";
            if (!$agree_terms) $errors[] = "Anda harus menyetujui Syarat & Ketentuan.";

            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $data = [
                    'name' => $full_name,
                    'email' => $email,
                    'password' => $hashed_password,
                    'phone_number' => $phone_number
                ];

                if ($userModel->createUser($data)) {
                    $_SESSION['register_success'] = "Registrasi berhasil! Silakan login dengan akun Anda.";
                    unset($_SESSION['form_data']); 
                    header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                    exit;
                } else {
                    $errors[] = "Gagal menyimpan data ke sistem.";
                }
            }

            $_SESSION['register_error'] = implode("<br>", $errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . 'index.php?url=Auth/register');
            exit;
        }
    }

    // MIGRATION: Firebase Google Login
    public function firebaseLogin() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $idToken = $input['idToken'] ?? null;

            if (!$idToken) {
                echo json_encode(['success' => false, 'message' => 'ID Token tidak ditemukan.']);
                exit;
            }

            try {
                // Koneksi ke Firebase SDK
                $factory = (new Factory())->withServiceAccount(dirname(dirname(__DIR__)) . '/casual-steps-118-firebase-adminsdk-fbsvc-adf0d10297.json');
                $auth = $factory->createAuth();
                $verifiedIdToken = $auth->verifyIdToken($idToken);

                $email = $verifiedIdToken->claims()->get('email');
                $name = $verifiedIdToken->claims()->get('name') ?? 'Pengguna Google';

                $userModel = $this->model('UserModel');
                $result = $userModel->processGoogleUser($name, $email);

                if ($result) {
                    if ($result['user']['account_status'] === 'nonaktif') {
                        echo json_encode(['success' => false, 'message' => 'Akun Anda telah dinonaktifkan.']);
                        exit;
                    }

                    $_SESSION['user_id'] = $result['user']['id'];
                    $_SESSION['user_name'] = $result['user']['name'];
                    $_SESSION['user_email'] = $result['user']['email'];
                    $_SESSION['user_loggedin'] = true;

                    echo json_encode([
                        'success' => true, 
                        'message' => 'Login berhasil.', 
                        'redirect_url' => BASE_URL
                    ]);
                    exit;
                } else {
                    throw new \Exception("Gagal memproses pengguna Google di database lokal.");
                }
            } catch (\Kreait\Firebase\Exception\Auth\IdTokenVerificationFailed $e) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Verifikasi token gagal.']);
                exit;
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Kesalahan server: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
        exit;
    }

    // MIGRATION: Tampilan Lupa Password
    public function lupaPassword() {
        $data = ['page_title' => 'Lupa Password'];
        $this->view('auth/lupa_password', $data);
    }

    // MIGRATION: Proses Lupa Password (Kirim Email SMTP)
    public function processLupaPassword() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['password_reset_message'] = "Email tidak valid.";
                $_SESSION['password_reset_message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?url=Auth/lupaPassword');
                exit;
            }

            $userModel = $this->model('UserModel');
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $token = bin2hex(random_bytes(32)); 
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour')); 

                if ($userModel->createPasswordResetToken($user['id'], $token, $expires_at)) {
                    
                    // Include PHPMailer manually (karena di old sistem pakai require_once)
                    require_once dirname(dirname(__DIR__)) . '/admin/PHPMailer/PHPMailer.php';
                    require_once dirname(dirname(__DIR__)) . '/admin/PHPMailer/SMTP.php';
                    require_once dirname(dirname(__DIR__)) . '/admin/PHPMailer/Exception.php';

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com'; 
                        $mail->SMTPAuth = true;
                        
                        // Load credentials dari .env
                        $smtp_user = $_ENV['SMTP_USER'] ?? '';
                        $smtp_pass = $_ENV['SMTP_PASS'] ?? '';

                        $mail->Username = $smtp_user; 
                        $mail->Password = $smtp_pass; 
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                        $mail->Port = 587; 

                        $mail->setFrom('no-reply@casualsteps.com', 'Casual Steps No-Reply');
                        $mail->addAddress($email, $user['name']);

                        $reset_link = BASE_URL . 'index.php?url=Auth/resetPassword&token=' . $token;

                        $mail->isHTML(true);
                        $mail->Subject = 'Reset Password Akun Casual Steps Anda';
                        $mail->Body    = "Halo {$user['name']},<br><br>Klik tautan berikut untuk mereset password: <a href='{$reset_link}'>Atur Ulang Password</a><br><br>Berlaku 1 jam.";

                        $mail->send();
                        $_SESSION['password_reset_message'] = "Tautan reset password telah dikirimkan ke email Anda.";
                        $_SESSION['password_reset_message_type'] = "success";
                    } catch (Exception $e) {
                        $_SESSION['password_reset_message'] = "Gagal mengirim email reset password.";
                        $_SESSION['password_reset_message_type'] = "danger";
                    }
                } else {
                    $_SESSION['password_reset_message'] = "Terjadi kesalahan database.";
                    $_SESSION['password_reset_message_type'] = "danger";
                }
            } else {
                $_SESSION['password_reset_message'] = "Jika email terdaftar, tautan reset akan dikirimkan.";
                $_SESSION['password_reset_message_type'] = "success";
            }
            header('Location: ' . BASE_URL . 'index.php?url=Auth/lupaPassword');
            exit;
        }
    }

    // MIGRATION: Tampilan Reset Password
    public function resetPassword() {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }

        $userModel = $this->model('UserModel');
        $reset_data = $userModel->verifyPasswordResetToken($token);

        if (!$reset_data || strtotime($reset_data['expires_at']) < time()) {
            $_SESSION['login_error'] = "Tautan reset password tidak valid atau sudah kadaluarsa.";
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }

        $data = ['page_title' => 'Reset Password', 'token' => $token];
        $this->view('auth/reset_password', $data);
    }

    // MIGRATION: Proses Ganti Password Baru
    public function processResetPassword() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($token)) {
                header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                exit;
            }

            if ($password !== $confirm_password || strlen($password) < 8) {
                $_SESSION['reset_error'] = "Password tidak cocok atau kurang dari 8 karakter.";
                header('Location: ' . BASE_URL . 'index.php?url=Auth/resetPassword&token=' . $token);
                exit;
            }

            $userModel = $this->model('UserModel');
            $reset_data = $userModel->verifyPasswordResetToken($token);

            if ($reset_data && strtotime($reset_data['expires_at']) > time()) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                if ($userModel->updatePasswordAndClearToken($reset_data['user_id'], $hashed_password)) {
                    $_SESSION['register_success'] = "Password berhasil diubah. Silakan login.";
                    header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                    exit;
                } else {
                    $_SESSION['reset_error'] = "Gagal memperbarui password di database.";
                    header('Location: ' . BASE_URL . 'index.php?url=Auth/resetPassword&token=' . $token);
                    exit;
                }
            } else {
                $_SESSION['login_error'] = "Tautan reset password tidak valid atau sudah kadaluarsa.";
                header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                exit;
            }
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
        exit;
    }
}

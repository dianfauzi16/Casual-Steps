<?php

namespace App\Controllers;

use App\Core\Controller;
use Exception;
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

class ProfileController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }
    }

    public function index() {
        $userModel = $this->model('UserModel');
        $user_id = $_SESSION['user_id'];
        
        $edit_mode = isset($_GET['action']) && $_GET['action'] == 'edit';
        $user_data = $userModel->getUserProfile($user_id);
        
        $data = [
            'page_title' => $edit_mode ? 'Ubah Profil Saya' : 'Akun Saya',
            'edit_mode' => $edit_mode,
            'user_data' => $user_data
        ];

        $this->view('profile/index', $data);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone_number'] ?? '');
            $dob = trim($_POST['date_of_birth'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $pic_file = $_FILES['profile_picture'] ?? null;
            
            $errors = [];
            
            if (empty($name)) $errors[] = "Nama lengkap wajib diisi.";
            
            $phone_to_save = !empty($phone) ? $phone : null;
            $dob_to_save = !empty($dob) ? $dob : null;
            
            $pic_url = null;
            
            if (isset($pic_file) && $pic_file['error'] === UPLOAD_ERR_OK) {
                if ($pic_file['size'] > 2097152) {
                    $errors[] = "Ukuran file foto profil tidak boleh lebih dari 2MB.";
                } else {
                    try {
                        $cloudinary_url = \App\Core\CloudinaryHelper::upload($pic_file['tmp_name'], 'casual_steps_user_profiles');
                        if ($cloudinary_url) {
                            $pic_url = $cloudinary_url;
                        }
                    } catch (\Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }
            
            if (empty($errors)) {
                $userModel = $this->model('UserModel');
                
                // Get old pic path for cleanup if it's local
                $old_data = $userModel->getUserProfile($user_id);
                $old_pic = $old_data['profile_picture_url'] ?? null;
                
                if ($userModel->updateProfile($user_id, $name, $phone_to_save, $dob_to_save, $bio, $pic_url)) {
                    $_SESSION['profil_message'] = "Profil Anda berhasil diperbarui.";
                    $_SESSION['profil_message_type'] = "success";
                    $_SESSION['user_name'] = $name;
                    
                    // Cleanup local file if replaced
                    if ($pic_url && $old_pic && !filter_var($old_pic, FILTER_VALIDATE_URL) && file_exists(dirname(__DIR__) . '/../admin/' . $old_pic)) {
                        unlink(dirname(__DIR__) . '/../admin/' . $old_pic);
                    }
                    
                    header('Location: ' . BASE_URL . 'index.php?url=Profile/index');
                    exit;
                } else {
                    $errors[] = "Gagal memperbarui profil di database.";
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['profil_message'] = implode("<br>", $errors);
                $_SESSION['profil_message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?url=Profile/index&action=edit');
                exit;
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Profile/index');
        exit;
    }
    public function changePassword() {
        $data = [
            'page_title' => 'Ubah Password'
        ];
        $this->view('profile/change_password', $data);
    }

    public function processChangePassword() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $errors = [];
            
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $errors[] = "Semua field harus diisi.";
            } elseif ($new_password !== $confirm_password) {
                $errors[] = "Password baru dan konfirmasi password tidak cocok.";
            } elseif (strlen($new_password) < 8) {
                $errors[] = "Password baru harus memiliki minimal 8 karakter.";
            } else {
                $userModel = $this->model('UserModel');
                $user = $userModel->getPasswordHashById($user_id);
                
                if (!$user) {
                    $errors[] = "Pengguna tidak ditemukan.";
                } elseif (!password_verify($current_password, $user)) {
                    $errors[] = "Password saat ini salah.";
                } else {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    if ($userModel->updatePassword($user_id, $hashed_password)) {
                        $_SESSION['password_message'] = "Password berhasil diubah.";
                        $_SESSION['password_message_type'] = "success";
                        header('Location: ' . BASE_URL . 'index.php?url=Profile/changePassword');
                        exit;
                    } else {
                        $errors[] = "Gagal mengubah password. Silakan coba lagi.";
                    }
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['password_message'] = implode("<br>", $errors);
                $_SESSION['password_message_type'] = "danger";
                header('Location: ' . BASE_URL . 'index.php?url=Profile/changePassword');
                exit;
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Profile/changePassword');
        exit;
    }
}

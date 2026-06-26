<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminAuthController extends Controller {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login() {
        // Jika sudah login, alihkan ke dashboard
        if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
            header("location: " . BASE_URL . "index.php?url=AdminDashboard/index");
            exit;
        }

        $data = [
            'page_title' => 'Admin Login'
        ];
        $this->view('admin/auth/login', $data);
    }

    public function processLogin() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = "Username dan Password harus diisi.";
                header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
                exit;
            }

            $adminModel = $this->model('AdminModel');
            $admin = $adminModel->verifyLogin($username, $password);

            if ($admin) {
                // Berhasil login
                unset($_SESSION['login_error']);
                $_SESSION['admin_loggedin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header("location: " . BASE_URL . "index.php?url=AdminDashboard/index");
                exit;
            } else {
                $_SESSION['login_error'] = "Username atau password yang Anda masukkan salah.";
                header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
                exit;
            }
        } else {
            header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
            exit;
        }
    }

    public function logout() {
        // Hapus semua variabel sesi admin
        unset($_SESSION['admin_loggedin']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        
        // Alihkan ke halaman login admin
        header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
        exit;
    }
}

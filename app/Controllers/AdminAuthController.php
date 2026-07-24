<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller untuk Autentikasi Admin
 * Menangani proses masuk (login), validasi kredensial, dan keluar (logout) dari panel admin.
 */
class AdminAuthController extends Controller {
    
    /**
     * Constructor: Inisialisasi sesi jika belum aktif
     */
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Menampilkan Form Login Admin
     * Jika admin sudah terautentikasi (login), akan otomatis dialihkan ke Dashboard.
     */
    public function login() {
        // Redireksi jika sudah dalam status login aktif
        if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
            header("location: " . BASE_URL . "index.php?url=AdminDashboard/index");
            exit;
        }

        $data = [
            'page_title' => 'Admin Login'
        ];
        $this->view('admin/auth/login', $data);
    }

    /**
     * Memproses Data Login Admin (POST)
     * Memeriksa username dan password, serta mencatat status login di sesi.
     */
    public function processLogin() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Validasi input kosong
            if (empty($username) || empty($password)) {
                $_SESSION['login_error'] = "Username dan Password harus diisi.";
                header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
                exit;
            }

            // Inisialisasi model admin dan memverifikasi kredensial
            $adminModel = $this->model('AdminModel');
            $admin = $adminModel->verifyLogin($username, $password);

            if ($admin) {
                // Jika login sukses, simpan detail admin ke dalam session
                unset($_SESSION['login_error']);
                $_SESSION['admin_loggedin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Alihkan ke halaman dashboard admin
                header("location: " . BASE_URL . "index.php?url=AdminDashboard/index");
                exit;
            } else {
                // Jika gagal, kembalikan ke form login dengan pesan kesalahan
                $_SESSION['login_error'] = "Username atau password yang Anda masukkan salah.";
                header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
                exit;
            }
        } else {
            // Jika bukan request POST, alihkan ke form login
            header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
            exit;
        }
    }

    /**
     * Memproses Logout Admin
     * Membersihkan semua data session admin dan mengembalikannya ke form login.
     */
    public function logout() {
        // Hapus variabel sesi yang spesifik untuk admin
        unset($_SESSION['admin_loggedin']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_username']);
        
        // Alihkan ke halaman login admin
        header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
        exit;
    }
}


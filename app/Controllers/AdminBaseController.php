<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller Dasar untuk Seluruh Fitur Admin
 * Bertindak sebagai middleware keamanan (autentikasi) dan utilitas layout render admin.
 */
class AdminBaseController extends Controller {
    
    /**
     * Constructor: Melakukan pengecekan sesi login admin secara otomatis (Middleware Keamanan).
     * Jika admin belum login, maka alihkan ke halaman login dengan pesan peringatan.
     */
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Cek apakah variabel session admin_loggedin telah diatur dan bernilai true
        if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
            $_SESSION['form_message'] = "Sesi Anda telah berakhir. Silakan login kembali.";
            $_SESSION['form_message_type'] = "warning";
            header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
            exit;
        }
    }

    /**
     * Fungsi bantuan untuk me-render layout lengkap panel admin
     * Menggabungkan bagian header, sidebar, konten utama (view), dan footer.
     * 
     * @param string $view Nama file view yang akan di-load (tanpa ekstensi .php)
     * @param array $data Array data opsional yang akan dikirim ke view
     */
    public function renderAdminView($view, $data = []) {
        $this->view('admin/layouts/header', $data); // Bagian atas halaman (meta, css)
        $this->view('admin/layouts/sidebar', $data); // Menu navigasi kiri admin
        $this->view($view, $data);                  // Konten halaman spesifik
        $this->view('admin/layouts/footer', $data);  // Bagian penutup halaman (js)
    }
}


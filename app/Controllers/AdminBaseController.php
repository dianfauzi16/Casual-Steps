<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminBaseController extends Controller {
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Middleware: Cek apakah admin sudah login
        if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
            $_SESSION['form_message'] = "Sesi Anda telah berakhir. Silakan login kembali.";
            $_SESSION['form_message_type'] = "warning";
            header("location: " . BASE_URL . "index.php?url=AdminAuth/login");
            exit;
        }
    }

    // Fungsi bantuan untuk me-render layout admin
    public function renderAdminView($view, $data = []) {
        $this->view('admin/layouts/header', $data);
        $this->view('admin/layouts/sidebar', $data);
        $this->view($view, $data);
        $this->view('admin/layouts/footer', $data);
    }
}

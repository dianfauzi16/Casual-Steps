<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller {
    public function index() {
        $productModel = $this->model('ProductModel');
        
        $recommended_products = $productModel->getRecommendedProducts(8);
        $best_shoes_products = $productModel->getBestShoes(2);
        $brands = $productModel->getAllBrands(12);
        
        $data = [
            'page_title' => 'Home',
            'recommended_products' => $recommended_products,
            'best_shoes_products' => $best_shoes_products,
            'brands' => $brands
        ];
        
        $this->view('home/index', $data);
    }

    public function about() {
        $data = [
            'page_title' => 'Tentang Kami'
        ];
        $this->view('home/about', $data);
    }

    public function promo() {
        $productModel = $this->model('ProductModel');
        $promo_products = $productModel->getPromoProducts();
        
        $data = [
            'page_title' => 'Promo & Diskon Spesial',
            'promo_products' => $promo_products
        ];
        $this->view('home/promo', $data);
    }

    public function sendMessage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
                $_SESSION['contact_error'] = "Anda harus login untuk mengirim pesan.";
            } else {
                $user_id = $_SESSION['user_id'] ?? 0;
                $nama = trim($_POST['contact_name'] ?? '');
                $email = trim($_POST['contact_email'] ?? '');
                $subjek = trim($_POST['contact_subject'] ?? '');
                $pesan = trim($_POST['contact_message'] ?? '');

                if (empty($nama) || empty($email) || empty($subjek) || empty($pesan)) {
                    $_SESSION['contact_error'] = "Semua field wajib diisi.";
                } else {
                    $db = \App\Core\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("INSERT INTO pesan_kontak (user_id, nama, email, subjek, pesan) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt) {
                        $stmt->bind_param("issss", $user_id, $nama, $email, $subjek, $pesan);
                        if ($stmt->execute()) {
                            $_SESSION['contact_success'] = "Pesan Anda berhasil dikirim! Kami akan segera meresponnya.";
                        } else {
                            $_SESSION['contact_error'] = "Gagal mengirim pesan. Silakan coba lagi.";
                        }
                        $stmt->close();
                    } else {
                        $_SESSION['contact_error'] = "Gagal memproses pesan.";
                    }
                }
            }
        }
        
        header('Location: ' . BASE_URL . '#contact1');
        exit;
    }
}

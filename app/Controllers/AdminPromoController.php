<?php

namespace App\Controllers;

class AdminPromoController extends AdminBaseController {
    
    public function index() {
        $productModel = $this->model('ProductModel');
        $products = $productModel->getProducts();
        
        $data = [
            'page_title' => 'Manajemen Promo',
            'products' => $products
        ];
        
        $this->renderAdminView('admin/promo/index', $data);
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminPromo/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);

        if (!$product) {
            $_SESSION['form_message'] = "Produk tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminPromo/index");
            exit;
        }

        $data = [
            'page_title' => 'Atur Diskon Produk: ' . $product['name'],
            'product' => $product
        ];

        $this->renderAdminView('admin/promo/edit', $data);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produk'])) {
            $id = (int)$_POST['id_produk'];
            $discount_percent = isset($_POST['discount_percent']) && $_POST['discount_percent'] !== '' ? (int)$_POST['discount_percent'] : 0;
            $discount_start = !empty($_POST['discount_start_date']) ? $_POST['discount_start_date'] : null;
            $discount_end = !empty($_POST['discount_end_date']) ? $_POST['discount_end_date'] : null;

            // Validasi
            if ($discount_percent < 0 || $discount_percent > 100) {
                $_SESSION['form_message'] = "Persentase diskon harus antara 0 dan 100.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/edit&id=" . $id);
                exit;
            }

            if ($discount_percent > 0 && (!$discount_start || !$discount_end)) {
                $_SESSION['form_message'] = "Tanggal mulai dan selesai harus diisi jika diskon lebih dari 0%.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/edit&id=" . $id);
                exit;
            }

            if ($discount_start && $discount_end && strtotime($discount_end) < strtotime($discount_start)) {
                $_SESSION['form_message'] = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/edit&id=" . $id);
                exit;
            }

            $productModel = $this->model('ProductModel');
            if ($productModel->updatePromo($id, $discount_percent, $discount_start, $discount_end)) {
                $_SESSION['form_message'] = "Diskon produk berhasil diperbarui.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/index");
                exit;
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui diskon produk.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/edit&id=" . $id);
                exit;
            }
        }
    }

    public function massEdit() {
        $data = [
            'page_title' => 'Atur Diskon Massal (Semua Produk)'
        ];

        $this->renderAdminView('admin/promo/mass_edit', $data);
    }

    public function massUpdate() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $discount_percent = isset($_POST['discount_percent']) && $_POST['discount_percent'] !== '' ? (int)$_POST['discount_percent'] : 0;
            $discount_start = !empty($_POST['discount_start_date']) ? $_POST['discount_start_date'] : null;
            $discount_end = !empty($_POST['discount_end_date']) ? $_POST['discount_end_date'] : null;

            // Validasi
            if ($discount_percent < 0 || $discount_percent > 100) {
                $_SESSION['form_message'] = "Persentase diskon harus antara 0 dan 100.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/massEdit");
                exit;
            }

            if ($discount_percent > 0 && (!$discount_start || !$discount_end)) {
                $_SESSION['form_message'] = "Tanggal mulai dan selesai harus diisi jika diskon lebih dari 0%.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/massEdit");
                exit;
            }

            if ($discount_start && $discount_end && strtotime($discount_end) < strtotime($discount_start)) {
                $_SESSION['form_message'] = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/massEdit");
                exit;
            }

            $productModel = $this->model('ProductModel');
            if ($productModel->updatePromoAll($discount_percent, $discount_start, $discount_end)) {
                $_SESSION['form_message'] = "Diskon massal berhasil diterapkan ke SEMUA produk.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/index");
                exit;
            } else {
                $_SESSION['form_message'] = "Gagal menerapkan diskon massal.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminPromo/massEdit");
                exit;
            }
        }
    }
}

<?php

namespace App\Controllers;

class AdminCustomerController extends AdminBaseController {
    
    public function index() {
        $userModel = $this->model('UserModel');
        $customers = $userModel->getAllCustomersAdmin();
        
        $data = [
            'page_title' => 'Manajemen Pelanggan',
            'customers' => $customers
        ];
        
        $this->renderAdminView('admin/customer/index', $data);
    }

    public function detail() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminCustomer/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $userModel = $this->model('UserModel');
        $result = $userModel->getCustomerDetailsAdmin($id);
        
        if (!$result) {
            $_SESSION['form_message'] = "Pelanggan tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminCustomer/index");
            exit;
        }
        
        $data = [
            'page_title' => 'Detail Pelanggan: ' . $result['customer']['name'],
            'customer' => $result['customer'],
            'orders' => $result['orders'],
            'addresses' => $result['addresses']
        ];
        
        $this->renderAdminView('admin/customer/detail', $data);
    }

    public function toggleStatus() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            $userModel = $this->model('UserModel');
            $new_status = $userModel->toggleCustomerStatus($id);
            
            if ($new_status) {
                $_SESSION['form_message'] = "Status pelanggan berhasil diubah menjadi '$new_status'.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal mengubah status pelanggan.";
                $_SESSION['form_message_type'] = "danger";
            }
            
            header("location: " . BASE_URL . "index.php?url=AdminCustomer/detail&id=" . $id);
            exit;
        }
        
        header("location: " . BASE_URL . "index.php?url=AdminCustomer/index");
        exit;
    }
}

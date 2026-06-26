<?php

namespace App\Controllers;

class AdminOrderController extends AdminBaseController {
    
    public function index() {
        $orderModel = $this->model('OrderModel');
        $orders = $orderModel->getAllOrdersAdmin();
        
        $data = [
            'page_title' => 'Manajemen Pesanan',
            'orders' => $orders
        ];
        
        $this->renderAdminView('admin/order/index', $data);
    }

    public function detail() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminOrder/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $orderModel = $this->model('OrderModel');
        $result = $orderModel->getOrderDetailsAdmin($id);
        
        if (!$result) {
            $_SESSION['form_message'] = "Pesanan tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminOrder/index");
            exit;
        }
        
        $data = [
            'page_title' => 'Detail Pesanan #' . $result['order']['id'],
            'order' => $result['order'],
            'items' => $result['items']
        ];
        
        $this->renderAdminView('admin/order/detail', $data);
    }

    public function updateStatus() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_order']) && isset($_POST['status_baru'])) {
            $id = (int)$_POST['id_order'];
            $status = trim($_POST['status_baru']);

            $orderModel = $this->model('OrderModel');
            if ($orderModel->updateOrderStatus($id, $status)) {
                $_SESSION['form_message'] = "Status pesanan berhasil diperbarui menjadi '$status'.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui status pesanan.";
                $_SESSION['form_message_type'] = "danger";
            }
            
            header("location: " . BASE_URL . "index.php?url=AdminOrder/detail&id=" . $id);
            exit;
        }
        
        header("location: " . BASE_URL . "index.php?url=AdminOrder/index");
        exit;
    }
}

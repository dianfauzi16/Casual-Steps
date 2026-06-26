<?php

namespace App\Controllers;

use App\Core\Controller;

class OrderController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }
    }

    public function history() {
        $user_id = $_SESSION['user_id'];
        $orderModel = $this->model('OrderModel');
        $orders = $orderModel->getUserOrders($user_id);
        
        $data = [
            'page_title' => 'Riwayat Pesanan',
            'orders' => $orders
        ];
        
        $this->view('order/history', $data);
    }

    public function detail() {
        $id_order = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id_order <= 0) {
            header('Location: ' . BASE_URL . 'index.php?url=Order/history');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $orderModel = $this->model('OrderModel');
        $orderDetails = $orderModel->getOrderDetails($id_order, $user_id);

        if (!$orderDetails) {
            header('Location: ' . BASE_URL . 'index.php?url=Order/history');
            exit;
        }

        $data = [
            'page_title' => 'Detail Pesanan #' . $orderDetails['order']['id'],
            'order' => $orderDetails['order'],
            'items' => $orderDetails['items']
        ];
        
        $this->view('order/detail', $data);
    }
}

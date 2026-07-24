<?php

namespace App\Controllers;

use App\Core\Controller;

class WishlistController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $wishlistModel = $this->model('WishlistModel');
        $products = $wishlistModel->getWishlist($user_id);
        
        $wishlisted_ids = array_column($products, 'id');

        $data = [
            'page_title' => 'Wishlist Saya',
            'products' => $products,
            'wishlisted_ids' => $wishlisted_ids
        ];

        $this->view('wishlist/index', $data);
    }

    public function toggle() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            exit;
        }
        
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            echo json_encode(['error' => 'unauthorized']);
            exit;
        }

        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $user_id = $_SESSION['user_id'];

        if ($product_id > 0) {
            $wishlistModel = $this->model('WishlistModel');
            $is_now_wishlisted = $wishlistModel->toggle($user_id, $product_id);
            $count = $wishlistModel->countWishlist($user_id);
            
            echo json_encode([
                'status' => 'success',
                'wishlisted' => $is_now_wishlisted,
                'count' => $count
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid product ID'
            ]);
        }
    }
}

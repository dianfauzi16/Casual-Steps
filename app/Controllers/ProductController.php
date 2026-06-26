<?php

namespace App\Controllers;

use App\Core\Controller;

class ProductController extends Controller {

    public function index() {
        $productModel = $this->model('ProductModel');
        
        $kategori_id = isset($_GET['kategori']) && filter_var($_GET['kategori'], FILTER_VALIDATE_INT) ? (int)$_GET['kategori'] : null;
        $keyword = isset($_GET['keyword_pencarian']) ? trim($_GET['keyword_pencarian']) : '';
        
        $categories = $productModel->getAllCategories();
        $products = $productModel->getProducts($kategori_id, $keyword);
        
        $current_category_name = null;
        $page_title = "Produk Kami";
        
        if ($kategori_id) {
            $cat = $productModel->getCategoryById($kategori_id);
            if ($cat) {
                $current_category_name = $cat['nama_kategori'];
                $page_title = "Produk Kategori: " . $current_category_name;
            }
        }
        
        if (!empty($keyword)) {
            $page_title = "Hasil Pencarian: " . htmlspecialchars($keyword);
        }

        $data = [
            'page_title' => $page_title,
            'products' => $products,
            'categories' => $categories,
            'kategori_id' => $kategori_id,
            'keyword' => $keyword,
            'current_category_name' => $current_category_name
        ];

        $this->view('product/index', $data);
    }

    public function detail() {
        if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
            header('Location: ' . BASE_URL . 'index.php?url=Product/index');
            exit;
        }

        $product_id = (int)$_GET['id'];
        $productModel = $this->model('ProductModel');
        
        $product = $productModel->getProductById($product_id);
        if (!$product) {
            $data = ['error_message' => "Produk tidak ditemukan.", 'page_title' => 'Produk Tidak Ditemukan'];
            $this->view('product/detail', $data);
            return;
        }

        $available_sizes = [];
        if (!empty($product['size'])) {
            $available_sizes = array_map('trim', explode(',', $product['size']));
        }

        $user_id = $_SESSION['user_id'] ?? null;
        $user_has_purchased = false;
        $user_has_rated = false;

        if ($user_id) {
            $user_has_purchased = $productModel->hasUserPurchasedProduct($user_id, $product_id);
            $user_has_rated = $productModel->hasUserRatedProduct($user_id, $product_id);
        }

        $reviews = $productModel->getProductReviews($product_id);

        $data = [
            'page_title' => $product['name'],
            'product' => $product,
            'available_sizes' => $available_sizes,
            'user_has_purchased' => $user_has_purchased,
            'user_has_rated' => $user_has_rated,
            'reviews' => $reviews
        ];

        $this->view('product/detail', $data);
    }

    public function addReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'] ?? null;
            if (!$user_id) {
                header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
                exit;
            }

            $product_id = $_POST['product_id'] ?? 0;
            $rating = (int)($_POST['rating'] ?? 0);
            $review_text = trim($_POST['review_text'] ?? '');

            if ($product_id && $rating >= 1 && $rating <= 5) {
                $productModel = $this->model('ProductModel');
                
                if ($productModel->hasUserPurchasedProduct($user_id, $product_id) && !$productModel->hasUserRatedProduct($user_id, $product_id)) {
                    $productModel->addProductReview($user_id, $product_id, $rating, $review_text);
                }
            }

            header('Location: ' . BASE_URL . 'index.php?url=Product/detail&id=' . $product_id);
            exit;
        }
    }
}

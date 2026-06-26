<?php

namespace App\Controllers;

use App\Core\Controller;
use Exception;

class CheckoutController extends Controller {

    public function index() {
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }

        $mode = $_GET['mode'] ?? 'cart';
        $items = [];
        $total_harga = 0;

        if ($mode === 'cart') {
            unset($_SESSION['direct_checkout_item']);
        }

        $cartModel = $this->model('CartModel');

        if ($mode === 'direct' && isset($_SESSION['direct_checkout_item']) && !empty($_SESSION['direct_checkout_item'])) {
            $direct_item = $_SESSION['direct_checkout_item'];
            $products = $cartModel->getCartProducts([$direct_item['id_produk']]);
            
            if (isset($products[$direct_item['id_produk']])) {
                $product_data = $products[$direct_item['id_produk']];
                if ($direct_item['kuantitas'] > $product_data['stock']) {
                    $_SESSION['error_message_checkout'] = "Maaf, stok untuk produk tidak mencukupi.";
                    unset($_SESSION['direct_checkout_item']);
                    header('Location: ' . BASE_URL . 'index.php?url=Product/detail&id=' . $direct_item['id_produk']);
                    exit;
                }
                $sekarang = date('Y-m-d');
                $is_discount = (!empty($product_data['discount_percent']) && $product_data['discount_percent'] > 0 && 
                               (empty($product_data['discount_start_date']) || $sekarang >= $product_data['discount_start_date']) && 
                               (empty($product_data['discount_end_date']) || $sekarang <= $product_data['discount_end_date']));
                
                $harga_final = $product_data['price'];
                if ($is_discount) {
                    $harga_final = $product_data['price'] - ($product_data['price'] * $product_data['discount_percent'] / 100);
                }

                $subtotal = $harga_final * $direct_item['kuantitas'];
                $items['direct_checkout'] = [
                    'id' => $product_data['id'],
                    'name' => $product_data['name'],
                    'price' => $harga_final,
                    'original_price' => $product_data['price'],
                    'is_discount' => $is_discount,
                    'image' => $product_data['image'],
                    'kuantitas' => $direct_item['kuantitas'],
                    'ukuran' => $direct_item['ukuran'],
                    'subtotal' => $subtotal
                ];
                $total_harga = $subtotal;
            }
        } else {
            if (empty($_SESSION['keranjang'])) {
                header('Location: ' . BASE_URL . 'index.php?url=Cart/index');
                exit;
            }
            $array_id_produk = array_column($_SESSION['keranjang'], 'id_produk');
            $products = $cartModel->getCartProducts(array_values($array_id_produk));
            
            foreach ($_SESSION['keranjang'] as $cart_key => $item_data) {
                $id_produk = $item_data['id_produk'];
                if (isset($products[$id_produk])) {
                    $prod = $products[$id_produk];
                    
                    $sekarang = date('Y-m-d');
                    $is_discount = (!empty($prod['discount_percent']) && $prod['discount_percent'] > 0 && 
                                   (empty($prod['discount_start_date']) || $sekarang >= $prod['discount_start_date']) && 
                                   (empty($prod['discount_end_date']) || $sekarang <= $prod['discount_end_date']));
                    
                    $harga_final = $prod['price'];
                    if ($is_discount) {
                        $harga_final = $prod['price'] - ($prod['price'] * $prod['discount_percent'] / 100);
                    }

                    $subtotal = $harga_final * $item_data['kuantitas'];
                    $items[$cart_key] = [
                        'id' => $id_produk,
                        'name' => $prod['name'],
                        'price' => $harga_final,
                        'original_price' => $prod['price'],
                        'is_discount' => $is_discount,
                        'image' => $prod['image'],
                        'kuantitas' => $item_data['kuantitas'],
                        'ukuran' => $item_data['ukuran'],
                        'subtotal' => $subtotal
                    ];
                    $total_harga += $subtotal;
                }
            }
        }

        if (empty($items)) {
            header('Location: ' . BASE_URL . 'index.php?url=Product/index');
            exit;
        }

        $this->view('checkout/index', [
            'page_title' => 'Checkout',
            'checkout_mode' => $mode,
            'items' => $items,
            'total_harga' => $total_harga
        ]);
    }

    public function buyNow() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produk'])) {
            $_SESSION['direct_checkout_item'] = [
                'id_produk' => filter_var($_POST['id_produk'], FILTER_VALIDATE_INT),
                'kuantitas' => isset($_POST['kuantitas']) ? (int)$_POST['kuantitas'] : 1,
                'ukuran' => trim($_POST['ukuran_terpilih'] ?? '')
            ];
            header("Location: " . BASE_URL . "index.php?url=Checkout/index&mode=direct");
            exit;
        }
        header("Location: " . BASE_URL . "index.php?url=Product/index");
        exit;
    }

    public function processMidtrans() {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') exit;
        
        // Matikan warning yang merusak JSON
        error_reporting(0);
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_loggedin'])) {
            echo json_encode(['error' => 'Harap login']);
            exit;
        }

        require_once dirname(__DIR__) . '/../vendor/autoload.php';
        \Midtrans\Config::$serverKey = 'SB-Mid-server-7rXZtaLcNc8M3I9VZYtj9eoE';
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $checkout_type = $_POST['checkout_type'] ?? 'cart_checkout';
        $cartModel = $this->model('CartModel');
        $orderModel = $this->model('OrderModel');
        $settingModel = $this->model('SettingModel');

        try {
            $items_for_stock_check = [];
            if ($checkout_type === 'direct_checkout' && isset($_SESSION['direct_checkout_item'])) {
                $items_for_stock_check[] = $_SESSION['direct_checkout_item'];
            } elseif ($checkout_type === 'cart_checkout' && isset($_SESSION['keranjang'])) {
                $items_for_stock_check = array_values($_SESSION['keranjang']);
            }

            if (empty($items_for_stock_check)) throw new Exception('Keranjang kosong');

            $product_ids = array_column($items_for_stock_check, 'id_produk');
            $products_from_db = $cartModel->getCartProducts($product_ids);

            $total_harga_final = 0;
            $item_details_midtrans = [];
            $items_to_save_in_db = [];
            
            $shipping_cost = (int)($settingModel->getSetting('default_shipping_cost') ?? 0);

            foreach ($items_for_stock_check as $item) {
                $product_id = $item['id_produk'];
                $quantity = $item['kuantitas'];
                if (!isset($products_from_db[$product_id])) throw new Exception('Produk tak ditemukan');
                $prod = $products_from_db[$product_id];
                if ($quantity > $prod['stock']) throw new Exception('Stok tidak cukup untuk: ' . $prod['name']);

                $sekarang = date('Y-m-d');
                $is_discount = (!empty($prod['discount_percent']) && $prod['discount_percent'] > 0 && 
                               (empty($prod['discount_start_date']) || $sekarang >= $prod['discount_start_date']) && 
                               (empty($prod['discount_end_date']) || $sekarang <= $prod['discount_end_date']));
                
                $harga_final = $prod['price'];
                if ($is_discount) {
                    $harga_final = $prod['price'] - ($prod['price'] * $prod['discount_percent'] / 100);
                }

                $subtotal = $harga_final * $quantity;
                $total_harga_final += $subtotal;

                $item_details_midtrans[] = [
                    'id' => (string)$product_id,
                    'price' => (int)$harga_final,
                    'quantity' => (int)$quantity,
                    'name' => $prod['name'] . (!empty($item['ukuran']) ? ' (' . $item['ukuran'] . ')' : '')
                ];

                $items_to_save_in_db[] = [
                    'id_produk' => $product_id,
                    'nama_produk' => $prod['name'],
                    'kuantitas' => $quantity,
                    'ukuran' => $item['ukuran'],
                    'harga_satuan' => $harga_final
                ];
            }

            if ($shipping_cost > 0) {
                $total_harga_final += $shipping_cost;
                $item_details_midtrans[] = [
                    'id' => 'SHIPPING',
                    'price' => $shipping_cost,
                    'quantity' => 1,
                    'name' => 'Biaya Pengiriman'
                ];
            }

            $midtrans_id = $orderModel->createOrder(
                $_SESSION['user_id'],
                $_POST,
                $items_to_save_in_db,
                $total_harga_final,
                'Payment Gateway',
                'pending'
            );

            $params = [
                'transaction_details' => [
                    'order_id' => $midtrans_id,
                    'gross_amount' => (int)$total_harga_final,
                ],
                'customer_details' => [
                    'first_name' => trim($_POST['nama_pelanggan'] ?? ''),
                    'email' => trim($_POST['email_pelanggan'] ?? ''),
                    'phone' => trim($_POST['telepon_pelanggan'] ?? ''),
                ],
                'item_details' => $item_details_midtrans,
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            if ($checkout_type === 'direct_checkout') {
                unset($_SESSION['direct_checkout_item']);
            } else {
                unset($_SESSION['keranjang']);
            }

            echo json_encode(['token' => $snapToken]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function paymentFinish() {
        $order_id = htmlspecialchars($_GET['order_id'] ?? 'Tidak diketahui');
        $status = htmlspecialchars($_GET['status'] ?? 'Tidak diketahui');

        $page_title = "Status Pembayaran";
        $message = "Status pembayaran Anda sedang diproses.";
        $icon = "fa-hourglass-half";
        $color = "info";

        if ($status === 'success' || $status === 'settlement' || $status === 'capture') {
            $page_title = "Pembayaran Berhasil";
            $message = "Terima kasih! Pembayaran Anda telah berhasil kami terima. Pesanan Anda akan segera kami proses.";
            $icon = "fa-check-circle";
            $color = "success";
            unset($_SESSION['keranjang']);
            unset($_SESSION['direct_checkout_item']);
        } elseif ($status === 'pending') {
            $page_title = "Menunggu Pembayaran";
            $message = "Pesanan Anda telah kami terima. Silakan selesaikan pembayaran Anda.";
            $icon = "fa-clock";
            $color = "warning";
        } elseif ($status === 'error' || $status === 'deny' || $status === 'cancel') {
            $page_title = "Pembayaran Gagal";
            $message = "Maaf, terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.";
            $icon = "fa-times-circle";
            $color = "danger";
        }

        $this->view('checkout/success', [
            'page_title' => $page_title,
            'message' => $message,
            'order_id' => $order_id,
            'icon' => $icon,
            'color' => $color
        ]);
    }
}

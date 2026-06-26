<?php

namespace App\Controllers;

use App\Core\Controller;

class CartController extends Controller {

    public function index() {
        $data = ['page_title' => 'Keranjang Belanja Anda'];
        $cart_items_detail = [];
        $total_harga = 0;

        if (isset($_SESSION['keranjang']) && !empty($_SESSION['keranjang'])) {
            $array_id_produk = [];
            foreach ($_SESSION['keranjang'] as $item) {
                if (isset($item['id_produk'])) {
                    $array_id_produk[$item['id_produk']] = $item['id_produk'];
                }
            }

            if (!empty($array_id_produk)) {
                $cartModel = $this->model('CartModel');
                $produk_details = $cartModel->getCartProducts(array_values($array_id_produk));

                foreach ($_SESSION['keranjang'] as $cart_key => $item) {
                    $id_produk = $item['id_produk'];
                    if (isset($produk_details[$id_produk])) {
                        $info = $produk_details[$id_produk];
                        
                        $sekarang = date('Y-m-d');
                        $is_discount = (!empty($info['discount_percent']) && $info['discount_percent'] > 0 && 
                                       (empty($info['discount_start_date']) || $sekarang >= $info['discount_start_date']) && 
                                       (empty($info['discount_end_date']) || $sekarang <= $info['discount_end_date']));
                        
                        $harga_final = $info['price'];
                        if ($is_discount) {
                            $harga_final = $info['price'] - ($info['price'] * $info['discount_percent'] / 100);
                        }

                        $subtotal = $harga_final * $item['kuantitas'];

                        $cart_items_detail[$cart_key] = [
                            'id' => $id_produk,
                            'cart_key' => $cart_key,
                            'name' => $info['name'],
                            'price' => $harga_final,
                            'original_price' => $info['price'],
                            'is_discount' => $is_discount,
                            'image' => $info['image'],
                            'stock' => $info['stock'],
                            'kuantitas' => $item['kuantitas'],
                            'ukuran' => $item['ukuran'],
                            'subtotal' => $subtotal
                        ];
                        $total_harga += $subtotal;
                    }
                }
            }
        }
        $data['keranjang_items_detail'] = $cart_items_detail;
        $data['total_harga_keranjang'] = $total_harga;

        $this->view('cart/index', $data);
    }

    public function add() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_produk'])) {
            $id_produk = filter_var($_POST['id_produk'], FILTER_VALIDATE_INT);
            $kuantitas = isset($_POST['kuantitas']) && filter_var($_POST['kuantitas'], FILTER_VALIDATE_INT) && $_POST['kuantitas'] > 0 ? (int)$_POST['kuantitas'] : 1;
            $ukuran_terpilih = trim($_POST['ukuran_terpilih'] ?? '');

            if ($id_produk) {
                if (!isset($_SESSION['keranjang'])) {
                    $_SESSION['keranjang'] = [];
                }
                
                $cart_item_key = $id_produk;
                if (!empty($ukuran_terpilih)) {
                    $safe_size_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $ukuran_terpilih);
                    $cart_item_key .= "_" . $safe_size_key;
                }

                if (isset($_SESSION['keranjang'][$cart_item_key])) {
                    $_SESSION['keranjang'][$cart_item_key]['kuantitas'] += $kuantitas;
                } else {
                    $_SESSION['keranjang'][$cart_item_key] = [
                        'id_produk' => $id_produk,
                        'ukuran' => $ukuran_terpilih,
                        'kuantitas' => $kuantitas
                    ];
                }
            }
        }
        
        $url_sebelumnya = $_SERVER['HTTP_REFERER'] ?? BASE_URL . 'index.php?url=Product/index';
        header("Location: " . $url_sebelumnya);
        exit;
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cart_item_key']) && isset($_POST['kuantitas'])) {
            $cart_item_key = $_POST['cart_item_key'];
            $kuantitas_baru = filter_var($_POST['kuantitas'], FILTER_VALIDATE_INT);

            if ($kuantitas_baru !== false && $kuantitas_baru >= 0 && isset($_SESSION['keranjang'][$cart_item_key])) {
                if ($kuantitas_baru == 0) {
                    unset($_SESSION['keranjang'][$cart_item_key]);
                } else {
                    $id_produk = $_SESSION['keranjang'][$cart_item_key]['id_produk'];
                    $cartModel = $this->model('CartModel');
                    $stok_tersedia = $cartModel->getProductStock($id_produk);

                    if ($kuantitas_baru <= $stok_tersedia) {
                        $_SESSION['keranjang'][$cart_item_key]['kuantitas'] = $kuantitas_baru;
                    }
                }
            }
        }
        header("Location: " . BASE_URL . "index.php?url=Cart/index");
        exit;
    }

    public function remove() {
        if (isset($_GET['cart_item_key'])) {
            $cart_item_key = $_GET['cart_item_key'];
            if (isset($_SESSION['keranjang'][$cart_item_key])) {
                unset($_SESSION['keranjang'][$cart_item_key]);
            }
        }
        header("Location: " . BASE_URL . "index.php?url=Cart/index");
        exit;
    }
}

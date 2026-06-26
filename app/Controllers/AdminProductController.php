<?php

namespace App\Controllers;

class AdminProductController extends AdminBaseController {
    
    public function index() {
        $productModel = $this->model('ProductModel');
        $keyword = isset($_GET['search_keyword']) ? trim($_GET['search_keyword']) : '';
        
        // Memanfaatkan metode yang sudah ada di ProductModel (digunakan juga di frontend)
        $products = $productModel->getProducts(null, $keyword);
        
        $data = [
            'page_title' => 'Manajemen Produk',
            'products' => $products,
            'search_keyword' => $keyword
        ];
        
        $this->renderAdminView('admin/product/index', $data);
    }
    
    public function delete() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $productModel = $this->model('ProductModel');
            
            $result = $productModel->deleteProduct($id);
            
            if ($result === true) {
                $_SESSION['form_message'] = "Produk berhasil dihapus.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = $result; // Pesan error dari model
                $_SESSION['form_message_type'] = "danger";
            }
        }
        
        header("location: " . BASE_URL . "index.php?url=AdminProduct/index");
        exit;
    }
    
    public function create() {
        $productModel = $this->model('ProductModel');
        $categories = $productModel->getAllCategories();
        
        $data = [
            'page_title' => 'Tambah Produk Baru',
            'categories' => $categories
        ];
        
        $this->renderAdminView('admin/product/create', $data);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $data = [
                'name' => trim($_POST['product_name'] ?? ''),
                'description' => trim($_POST['product_description'] ?? ''),
                'brand' => trim($_POST['product_brand'] ?? ''),
                'price' => trim($_POST['product_price'] ?? 0),
                'stock' => trim($_POST['product_stock'] ?? 0),
                'size' => trim($_POST['product_size'] ?? ''),
                'id_kategori' => !empty($_POST['id_kategori']) ? trim($_POST['id_kategori']) : NULL,
                'image' => null
            ];

            if (empty($data['name']) || empty($data['price']) || $data['stock'] === '') {
                $_SESSION['form_message'] = "Nama Produk, Harga, dan Stok tidak boleh kosong.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminProduct/create");
                exit;
            }

            // Proses Upload Gambar ke Cloudinary
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0 && !empty($_FILES['product_image']['name'])) {
                $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
                
                if (in_array($image_file_type, ['jpg', 'png', 'jpeg', 'webp', 'gif']) && $_FILES["product_image"]["size"] <= 2000000) {
                    try {
                        $cloudinary_url = \App\Core\CloudinaryHelper::upload($_FILES["product_image"]["tmp_name"], 'casual_steps_products');
                        if ($cloudinary_url) {
                            $data['image'] = $cloudinary_url;
                        }
                    } catch (\Exception $e) {
                        $_SESSION['form_message'] = "Gagal upload gambar ke Cloudinary: " . $e->getMessage();
                        $_SESSION['form_message_type'] = "danger";
                        header("location: " . BASE_URL . "index.php?url=AdminProduct/create");
                        exit;
                    }
                } else {
                    $_SESSION['form_message'] = "Format gambar tidak didukung atau ukuran melebihi 2MB.";
                    $_SESSION['form_message_type'] = "danger";
                    header("location: " . BASE_URL . "index.php?url=AdminProduct/create");
                    exit;
                }
            }

            $productModel = $this->model('ProductModel');
            if ($productModel->insertProduct($data)) {
                $_SESSION['form_message'] = "Produk berhasil ditambahkan.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminProduct/index");
            } else {
                $_SESSION['form_message'] = "Gagal menyimpan produk ke database.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminProduct/create");
            }
            exit;
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminProduct/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $productModel = $this->model('ProductModel');
        $product = $productModel->getProductById($id);
        
        if (!$product) {
            $_SESSION['form_message'] = "Produk tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminProduct/index");
            exit;
        }

        $categories = $productModel->getAllCategories();
        
        $data = [
            'page_title' => 'Edit Produk',
            'product' => $product,
            'categories' => $categories
        ];
        
        $this->renderAdminView('admin/product/edit', $data);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            $data = [
                'name' => trim($_POST['product_name'] ?? ''),
                'description' => trim($_POST['product_description'] ?? ''),
                'brand' => trim($_POST['product_brand'] ?? ''),
                'price' => trim($_POST['product_price'] ?? 0),
                'stock' => trim($_POST['product_stock'] ?? 0),
                'size' => trim($_POST['product_size'] ?? ''),
                'id_kategori' => !empty($_POST['id_kategori']) ? trim($_POST['id_kategori']) : NULL
            ];

            // Proses Upload Gambar Baru jika ada (ke Cloudinary)
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0 && !empty($_FILES['product_image']['name'])) {
                $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));

                if (in_array($image_file_type, ['jpg', 'png', 'jpeg', 'webp', 'gif']) && $_FILES["product_image"]["size"] <= 2000000) {
                    try {
                        $cloudinary_url = \App\Core\CloudinaryHelper::upload($_FILES["product_image"]["tmp_name"], 'casual_steps_products');
                        if ($cloudinary_url) {
                            $data['image'] = $cloudinary_url;
                        }
                    } catch (\Exception $e) {
                        $_SESSION['form_message'] = "Gagal upload gambar ke Cloudinary: " . $e->getMessage();
                        $_SESSION['form_message_type'] = "danger";
                        header("location: " . BASE_URL . "index.php?url=AdminProduct/edit&id=" . $id);
                        exit;
                    }
                } else {
                    $_SESSION['form_message'] = "Format gambar tidak didukung atau ukuran melebihi 2MB.";
                    $_SESSION['form_message_type'] = "danger";
                    header("location: " . BASE_URL . "index.php?url=AdminProduct/edit&id=" . $id);
                    exit;
                }
            }

            $productModel = $this->model('ProductModel');
            if ($productModel->updateProduct($id, $data)) {
                $_SESSION['form_message'] = "Produk berhasil diperbarui.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminProduct/index");
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui produk.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminProduct/edit&id=" . $id);
            }
            exit;
        }
    }
}

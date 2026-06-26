<?php

namespace App\Controllers;

class AdminCategoryController extends AdminBaseController {
    
    public function index() {
        $categoryModel = $this->model('CategoryModel');
        $categories = $categoryModel->getAllCategories();
        
        $data = [
            'page_title' => 'Manajemen Kategori',
            'categories' => $categories
        ];
        
        $this->renderAdminView('admin/category/index', $data);
    }

    public function create() {
        $data = [
            'page_title' => 'Tambah Kategori Baru'
        ];
        
        $this->renderAdminView('admin/category/create', $data);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');

            if (empty($nama_kategori)) {
                $_SESSION['form_message'] = "Nama Kategori tidak boleh kosong.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/create");
                exit;
            }

            $categoryModel = $this->model('CategoryModel');
            if ($categoryModel->insertCategory($nama_kategori)) {
                $_SESSION['form_message'] = "Kategori berhasil ditambahkan.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/index");
            } else {
                $_SESSION['form_message'] = "Gagal menyimpan kategori ke database.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/create");
            }
            exit;
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header("location: " . BASE_URL . "index.php?url=AdminCategory/index");
            exit;
        }

        $id = (int)$_GET['id'];
        $categoryModel = $this->model('CategoryModel');
        $category = $categoryModel->getCategoryById($id);
        
        if (!$category) {
            $_SESSION['form_message'] = "Kategori tidak ditemukan.";
            $_SESSION['form_message_type'] = "danger";
            header("location: " . BASE_URL . "index.php?url=AdminCategory/index");
            exit;
        }
        
        $data = [
            'page_title' => 'Edit Kategori',
            'category' => $category
        ];
        
        $this->renderAdminView('admin/category/edit', $data);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');

            if (empty($nama_kategori)) {
                $_SESSION['form_message'] = "Nama Kategori tidak boleh kosong.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/edit&id=" . $id);
                exit;
            }

            $categoryModel = $this->model('CategoryModel');
            if ($categoryModel->updateCategory($id, $nama_kategori)) {
                $_SESSION['form_message'] = "Kategori berhasil diperbarui.";
                $_SESSION['form_message_type'] = "success";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/index");
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui kategori.";
                $_SESSION['form_message_type'] = "danger";
                header("location: " . BASE_URL . "index.php?url=AdminCategory/edit&id=" . $id);
            }
            exit;
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $categoryModel = $this->model('CategoryModel');
            
            if ($categoryModel->deleteCategory($id)) {
                $_SESSION['form_message'] = "Kategori berhasil dihapus. Produk yang sebelumnya berada di kategori ini telah diatur ulang.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal menghapus kategori.";
                $_SESSION['form_message_type'] = "danger";
            }
        }
        
        header("location: " . BASE_URL . "index.php?url=AdminCategory/index");
        exit;
    }
}

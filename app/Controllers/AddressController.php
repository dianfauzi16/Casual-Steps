<?php

namespace App\Controllers;

use App\Core\Controller;

class AddressController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
            header('Location: ' . BASE_URL . 'index.php?url=Auth/login');
            exit;
        }
    }

    public function index() {
        $user_id = $_SESSION['user_id'];
        $addressModel = $this->model('AddressModel');
        $addresses = $addressModel->getUserAddresses($user_id);
        
        $data = [
            'page_title' => 'Alamat Saya',
            'daftar_alamat' => $addresses
        ];
        
        $this->view('address/index', $data);
    }

    public function create() {
        $data = [
            'page_title' => 'Tambah Alamat Baru'
        ];
        $this->view('address/create', $data);
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            
            $data = [
                'user_id' => $user_id,
                'label' => trim($_POST['label'] ?? ''),
                'recipient_name' => trim($_POST['recipient_name'] ?? ''),
                'phone_number' => trim($_POST['phone_number'] ?? ''),
                'street_address' => trim($_POST['street_address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'province' => trim($_POST['province'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? ''),
                'country' => trim($_POST['country'] ?? 'Indonesia'),
                'is_primary' => isset($_POST['is_primary']) ? 1 : 0
            ];
            
            $errors = [];
            foreach (['label', 'recipient_name', 'phone_number', 'street_address', 'city', 'province', 'postal_code', 'country'] as $field) {
                if (empty($data[$field])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . " wajib diisi.";
                }
            }
            
            if (empty($errors)) {
                $addressModel = $this->model('AddressModel');
                if ($addressModel->addAddress($data)) {
                    $_SESSION['alamat_message'] = "Alamat baru berhasil ditambahkan.";
                    $_SESSION['alamat_message_type'] = "success";
                    header('Location: ' . BASE_URL . 'index.php?url=Address/index');
                    exit;
                } else {
                    $errors[] = "Gagal menyimpan alamat.";
                }
            }
            
            $_SESSION['alamat_message'] = implode("<br>", $errors);
            $_SESSION['alamat_message_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?url=Address/create');
            exit;
        }
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . 'index.php?url=Address/index');
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $addressModel = $this->model('AddressModel');
        $address = $addressModel->getAddressById($id, $user_id);

        if (!$address) {
            $_SESSION['alamat_message'] = "Alamat tidak ditemukan.";
            $_SESSION['alamat_message_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?url=Address/index');
            exit;
        }

        $data = [
            'page_title' => 'Edit Alamat',
            'alamat' => $address
        ];
        $this->view('address/edit', $data);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $user_id = $_SESSION['user_id'];
            $id = filter_var($_POST['id'], FILTER_VALIDATE_INT);
            
            if (!$id) {
                header('Location: ' . BASE_URL . 'index.php?url=Address/index');
                exit;
            }

            $data = [
                'label' => trim($_POST['label'] ?? ''),
                'recipient_name' => trim($_POST['recipient_name'] ?? ''),
                'phone_number' => trim($_POST['phone_number'] ?? ''),
                'street_address' => trim($_POST['street_address'] ?? ''),
                'city' => trim($_POST['city'] ?? ''),
                'province' => trim($_POST['province'] ?? ''),
                'postal_code' => trim($_POST['postal_code'] ?? ''),
                'country' => trim($_POST['country'] ?? 'Indonesia'),
                'is_primary' => isset($_POST['is_primary']) ? 1 : 0
            ];
            
            $errors = [];
            foreach (['label', 'recipient_name', 'phone_number', 'street_address', 'city', 'province', 'postal_code', 'country'] as $field) {
                if (empty($data[$field])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . " wajib diisi.";
                }
            }
            
            if (empty($errors)) {
                $addressModel = $this->model('AddressModel');
                if ($addressModel->updateAddress($id, $user_id, $data)) {
                    $_SESSION['alamat_message'] = "Alamat berhasil diperbarui.";
                    $_SESSION['alamat_message_type'] = "success";
                    header('Location: ' . BASE_URL . 'index.php?url=Address/index');
                    exit;
                } else {
                    $errors[] = "Gagal memperbarui alamat.";
                }
            }
            
            $_SESSION['alamat_message'] = implode("<br>", $errors);
            $_SESSION['alamat_message_type'] = "danger";
            header('Location: ' . BASE_URL . 'index.php?url=Address/edit&id=' . $id);
            exit;
        }
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $user_id = $_SESSION['user_id'];
            $addressModel = $this->model('AddressModel');
            
            if ($addressModel->deleteAddress($id, $user_id)) {
                $_SESSION['alamat_message'] = "Alamat berhasil dihapus.";
                $_SESSION['alamat_message_type'] = "success";
            } else {
                $_SESSION['alamat_message'] = "Gagal menghapus alamat.";
                $_SESSION['alamat_message_type'] = "danger";
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Address/index');
        exit;
    }

    public function setPrimary() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $user_id = $_SESSION['user_id'];
            $addressModel = $this->model('AddressModel');
            
            if ($addressModel->setPrimaryAddress($id, $user_id)) {
                $_SESSION['alamat_message'] = "Alamat utama berhasil diubah.";
                $_SESSION['alamat_message_type'] = "success";
            } else {
                $_SESSION['alamat_message'] = "Gagal mengatur alamat utama.";
                $_SESSION['alamat_message_type'] = "danger";
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Address/index');
        exit;
    }
}

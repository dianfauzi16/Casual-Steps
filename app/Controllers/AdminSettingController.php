<?php

namespace App\Controllers;

class AdminSettingController extends AdminBaseController {
    
    public function index() {
        $settingModel = $this->model('SettingModel');
        
        // Ambil data pengaturan yang dibutuhkan untuk Informasi Toko & Sosial Media
        $keys = [
            'nama_toko', 
            'deskripsi_toko', 
            'alamat_toko_lengkap', 
            'telepon_toko', 
            'email_kontak',
            'social_instagram',
            'social_facebook',
            'social_tiktok'
        ];
        
        $settings = $settingModel->getSettings($keys);
        
        $data = [
            'page_title' => 'Pengaturan Informasi Toko',
            'settings' => $settings
        ];
        
        $this->renderAdminView('admin/setting/index', $data);
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $settingModel = $this->model('SettingModel');
            
            $data_to_update = [
                'nama_toko' => $_POST['nama_toko'] ?? '',
                'deskripsi_toko' => $_POST['deskripsi_toko'] ?? '',
                'alamat_toko_lengkap' => $_POST['alamat_toko_lengkap'] ?? '',
                'telepon_toko' => $_POST['telepon_toko'] ?? '',
                'email_kontak' => $_POST['email_kontak'] ?? '',
                'social_instagram' => $_POST['social_instagram'] ?? '',
                'social_facebook' => $_POST['social_facebook'] ?? '',
                'social_tiktok' => $_POST['social_tiktok'] ?? ''
            ];

            if ($settingModel->updateMultipleSettings($data_to_update)) {
                $_SESSION['form_message'] = "Pengaturan toko berhasil diperbarui.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui pengaturan toko.";
                $_SESSION['form_message_type'] = "danger";
            }
            
            header("Location: " . BASE_URL . "index.php?url=AdminSetting/index");
            exit;
        }
    }

    public function system() {
        $settingModel = $this->model('SettingModel');
        
        $keys = [
            'announcement_active', 
            'announcement_text', 
            'announcement_link', 
            'default_shipping_cost', 
            'promo_banner_image'
        ];
        
        $settings = $settingModel->getSettings($keys);
        
        $data = [
            'page_title' => 'Pengaturan Sistem & Banner',
            'settings' => $settings
        ];
        
        $this->renderAdminView('admin/setting/system', $data);
    }

    public function updateSystem() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $settingModel = $this->model('SettingModel');
            
            $data_to_update = [
                'announcement_active' => isset($_POST['announcement_active']) ? '1' : '0',
                'announcement_text' => $_POST['announcement_text'] ?? '',
                'announcement_link' => $_POST['announcement_link'] ?? '',
                'default_shipping_cost' => $_POST['default_shipping_cost'] ?? '0'
            ];

            // Handle Image Upload for promo banner (ke Cloudinary)
            if (isset($_FILES['promo_banner_image']) && $_FILES['promo_banner_image']['error'] == 0) {
                $file_extension = strtolower(pathinfo($_FILES["promo_banner_image"]["name"], PATHINFO_EXTENSION));

                // Validate image
                $check = getimagesize($_FILES["promo_banner_image"]["tmp_name"]);
                if($check !== false && in_array($file_extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    try {
                        $cloudinary_url = \App\Core\CloudinaryHelper::upload($_FILES["promo_banner_image"]["tmp_name"], 'casual_steps_banners');
                        if ($cloudinary_url) {
                            $data_to_update['promo_banner_image'] = $cloudinary_url;
                        }
                    } catch (\Exception $e) {
                        $_SESSION['form_message'] = "Gagal upload gambar banner ke Cloudinary: " . $e->getMessage();
                        $_SESSION['form_message_type'] = "danger";
                        header("Location: " . BASE_URL . "index.php?url=AdminSetting/system");
                        exit;
                    }
                } else {
                    $_SESSION['form_message'] = "Format gambar banner tidak didukung.";
                    $_SESSION['form_message_type'] = "danger";
                    header("Location: " . BASE_URL . "index.php?url=AdminSetting/system");
                    exit;
                }
            }

            if ($settingModel->updateMultipleSettings($data_to_update)) {
                $_SESSION['form_message'] = "Pengaturan sistem & banner berhasil diperbarui.";
                $_SESSION['form_message_type'] = "success";
            } else {
                $_SESSION['form_message'] = "Gagal memperbarui pengaturan sistem.";
                $_SESSION['form_message_type'] = "danger";
            }
            
            header("Location: " . BASE_URL . "index.php?url=AdminSetting/system");
            exit;
        }
    }
}

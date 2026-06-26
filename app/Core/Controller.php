<?php

namespace App\Core;

class Controller {
    // Fungsi untuk memanggil model
    public function model($model) {
        $modelClass = 'App\\Models\\' . $model;
        if (class_exists($modelClass)) {
            return new $modelClass();
        }
        return null;
    }

    // Fungsi untuk memanggil view
    public function view($view, $data = []) {
        $viewFile = dirname(dirname(__DIR__)) . '/app/Views/' . $view . '.php';
        if (file_exists($viewFile)) {
            // Injeksi pengaturan global secara otomatis
            if (!isset($data['global_settings'])) {
                $settingModel = $this->model('SettingModel');
                if ($settingModel) {
                    $keys = [
                        'nama_toko', 'deskripsi_toko', 'alamat_toko_lengkap', 'telepon_toko', 'email_kontak', 
                        'social_instagram', 'social_facebook', 'social_tiktok',
                        'announcement_active', 'announcement_text', 'announcement_link',
                        'default_shipping_cost', 'promo_banner_image'
                    ];
                    $data['global_settings'] = $settingModel->getSettings($keys);
                }
            }

            // Ekstrak data menjadi variabel agar bisa langsung dipakai di view
            extract($data);
            require_once $viewFile;
        } else {
            die("View {$viewFile} tidak ditemukan.");
        }
    }
}

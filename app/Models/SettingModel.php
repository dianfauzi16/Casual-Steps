<?php

namespace App\Models;

use App\Core\Model;

class SettingModel extends Model {
    
    /**
     * Mengambil satu nilai pengaturan berdasarkan key
     */
    public function getSetting($key) {
        $sql = "SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                return $result->fetch_assoc()['setting_value'];
            }
            $stmt->close();
        }
        return null;
    }

    /**
     * Mengambil banyak pengaturan sekaligus
     */
    public function getSettings($keys) {
        $settings = [];
        foreach ($keys as $key) {
            $settings[$key] = $this->getSetting($key);
        }
        return $settings;
    }

    /**
     * Memperbarui atau menyimpan pengaturan baru (upsert)
     */
    public function updateSetting($key, $value) {
        $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ss", $key, $value);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    /**
     * Update beberapa pengaturan secara bersamaan dengan Transaction
     */
    public function updateMultipleSettings($data) {
        $this->db->begin_transaction();
        try {
            foreach ($data as $key => $value) {
                if (!$this->updateSetting($key, trim($value))) {
                    throw new \Exception("Gagal menyimpan pengaturan: $key");
                }
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
}

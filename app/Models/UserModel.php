<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class UserModel extends Model {

    // 1. Mengambil data pengguna berdasarkan Email
    public function getUserByEmail($email) {
        $sql = "SELECT id, name, email, password, account_status FROM users WHERE email = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            }
            $stmt->close();
        }
        return false;
    }

    // 2. Mengecek apakah Email sudah terdaftar
    public function checkEmailExists($email) {
        $sql = "SELECT id FROM users WHERE email = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            $exists = $stmt->num_rows > 0;
            $stmt->close();
            return $exists;
        }
        return false;
    }

    // 3. Menambahkan pengguna baru (Registrasi Biasa)
    public function createUser($data) {
        $this->db->begin_transaction();
        try {
            $sql_user = "INSERT INTO users (name, email, password, phone_number) VALUES (?, ?, ?, ?)";
            $stmt_user = $this->db->prepare($sql_user);
            $phone = !empty($data['phone_number']) ? $data['phone_number'] : null;
            $stmt_user->bind_param("ssss", $data['name'], $data['email'], $data['password'], $phone);
            $stmt_user->execute();
            $new_user_id = $stmt_user->insert_id;
            
            $sql_profile = "INSERT INTO user_profiles (user_id) VALUES (?)";
            $stmt_profile = $this->db->prepare($sql_profile);
            $stmt_profile->bind_param("i", $new_user_id);
            $stmt_profile->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // 4. Memproses pengguna dari Google Firebase
    public function processGoogleUser($name, $email) {
        $user = $this->getUserByEmail($email);
        if ($user) {
            // Update nama jika berbeda
            if ($user['name'] !== $name) {
                $sql = "UPDATE users SET name = ? WHERE id = ?";
                if ($stmt = $this->db->prepare($sql)) {
                    $stmt->bind_param("si", $name, $user['id']);
                    $stmt->execute();
                }
                $user['name'] = $name;
            }
            return ['status' => 'exists', 'user' => $user];
        } else {
            // Pengguna baru via Google
            $this->db->begin_transaction();
            try {
                $dummy_password = password_hash(uniqid('firebase_user_', true), PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO users (name, email, password, account_status) VALUES (?, ?, ?, 'aktif')";
                $stmt_in = $this->db->prepare($sql_insert);
                $stmt_in->bind_param("sss", $name, $email, $dummy_password);
                $stmt_in->execute();
                $new_id = $stmt_in->insert_id;
                
                $sql_prof = "INSERT INTO user_profiles (user_id) VALUES (?)";
                $stmt_prof = $this->db->prepare($sql_prof);
                $stmt_prof->bind_param("i", $new_id);
                $stmt_prof->execute();
                
                $this->db->commit();
                return ['status' => 'new', 'user' => ['id' => $new_id, 'name' => $name, 'email' => $email, 'account_status' => 'aktif']];
            } catch (Exception $e) {
                $this->db->rollback();
                return false;
            }
        }
    }

    // 5. Menyimpan token reset password
    public function createPasswordResetToken($user_id, $token, $expires_at) {
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("iss", $user_id, $token, $expires_at);
            return $stmt->execute();
        }
        return false;
    }

    // 6. Memverifikasi token reset password
    public function verifyPasswordResetToken($token) {
        $sql = "SELECT user_id, expires_at FROM password_resets WHERE token = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) return $result->fetch_assoc();
        }
        return false;
    }

    // 7. Mengupdate password dan menghapus token reset
    public function updatePasswordAndClearToken($user_id, $new_password) {
        $this->db->begin_transaction();
        try {
            $sql_update = "UPDATE users SET password = ? WHERE id = ?";
            $stmt_up = $this->db->prepare($sql_update);
            $stmt_up->bind_param("si", $new_password, $user_id);
            $stmt_up->execute();

            $sql_del = "DELETE FROM password_resets WHERE user_id = ?";
            $stmt_del = $this->db->prepare($sql_del);
            $stmt_del->bind_param("i", $user_id);
            $stmt_del->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    // 8. Mendapatkan profil lengkap pengguna (users + user_profiles)
    public function getUserProfile($user_id) {
        $sql = "SELECT u.id, u.name, u.email, u.phone_number, 
                       up.date_of_birth, up.profile_picture_url, up.bio
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            }
        }
        return false;
    }

    // 9. Update profil pengguna
    public function updateProfile($user_id, $name, $phone, $dob, $bio, $pic_url = null) {
        $this->db->begin_transaction();
        try {
            $sql_user = "UPDATE users SET name = ?, phone_number = ? WHERE id = ?";
            $stmt_user = $this->db->prepare($sql_user);
            $stmt_user->bind_param("ssi", $name, $phone, $user_id);
            if (!$stmt_user->execute()) throw new Exception("Gagal update user");
            
            if ($pic_url !== null) {
                $sql_prof = "INSERT INTO user_profiles (user_id, date_of_birth, bio, profile_picture_url) VALUES (?, ?, ?, ?)
                             ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), bio = VALUES(bio), profile_picture_url = VALUES(profile_picture_url)";
                $stmt_prof = $this->db->prepare($sql_prof);
                $stmt_prof->bind_param("isss", $user_id, $dob, $bio, $pic_url);
            } else {
                $sql_prof = "INSERT INTO user_profiles (user_id, date_of_birth, bio) VALUES (?, ?, ?)
                             ON DUPLICATE KEY UPDATE date_of_birth = VALUES(date_of_birth), bio = VALUES(bio)";
                $stmt_prof = $this->db->prepare($sql_prof);
                $stmt_prof->bind_param("iss", $user_id, $dob, $bio);
            }
            if (!$stmt_prof->execute()) throw new Exception("Gagal update profile");
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // 10. Update Password
    public function updatePassword($user_id, $hashed_password) {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("si", $hashed_password, $user_id);
            return $stmt->execute();
        }
        return false;
    }

    // 11. Dapatkan password hash berdasarkan id
    public function getPasswordHashById($user_id) {
        $sql = "SELECT password FROM users WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row['password'];
            }
        }
        return false;
    }

    // --- Admin Methods ---

    public function getAllCustomersAdmin() {
        $customers = [];
        $sql = "SELECT id, name, email, phone_number, created_at, account_status 
                FROM users 
                ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        return $customers;
    }

    public function getCustomerDetailsAdmin($id) {
        $sql = "SELECT id, name, email, phone_number, created_at, account_status
                FROM users
                WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($customer = $result->fetch_assoc()) {
                // Ambil history pesanan
                $orders = [];
                $sql_orders = "SELECT id, tanggal_pesanan, total_price, status 
                               FROM orders 
                               WHERE user_id = ? 
                               ORDER BY tanggal_pesanan DESC";
                if ($stmt_orders = $this->db->prepare($sql_orders)) {
                    $stmt_orders->bind_param("i", $id);
                    $stmt_orders->execute();
                    $res_orders = $stmt_orders->get_result();
                    while ($row_order = $res_orders->fetch_assoc()) {
                        $orders[] = $row_order;
                    }
                    $stmt_orders->close();
                }

                // Ambil alamat pelanggan
                $addresses = [];
                $sql_addresses = "SELECT label, recipient_name, phone_number, street_address, city, province, postal_code, is_primary 
                                  FROM addresses 
                                  WHERE user_id = ? 
                                  ORDER BY is_primary DESC, id DESC";
                if ($stmt_addresses = $this->db->prepare($sql_addresses)) {
                    $stmt_addresses->bind_param("i", $id);
                    $stmt_addresses->execute();
                    $res_addresses = $stmt_addresses->get_result();
                    while ($row_address = $res_addresses->fetch_assoc()) {
                        $addresses[] = $row_address;
                    }
                    $stmt_addresses->close();
                }

                return ['customer' => $customer, 'orders' => $orders, 'addresses' => $addresses];
            }
            $stmt->close();
        }
        return false;
    }


    public function toggleCustomerStatus($id) {
        $sql = "SELECT account_status FROM users WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $new_status = ($row['account_status'] === 'aktif') ? 'nonaktif' : 'aktif';
                
                $sql_update = "UPDATE users SET account_status = ? WHERE id = ?";
                if ($stmt_update = $this->db->prepare($sql_update)) {
                    $stmt_update->bind_param("si", $new_status, $id);
                    $success = $stmt_update->execute();
                    $stmt_update->close();
                    return $success ? $new_status : false;
                }
            }
            $stmt->close();
        }
        return false;
    }
}

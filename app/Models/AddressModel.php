<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class AddressModel extends Model {

    public function getUserAddresses($user_id) {
        $sql = "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_primary DESC, id DESC";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $addresses = [];
            while ($row = $result->fetch_assoc()) {
                $addresses[] = $row;
            }
            return $addresses;
        }
        return [];
    }

    public function getAddressById($id, $user_id) {
        $sql = "SELECT * FROM addresses WHERE id = ? AND user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row;
            }
        }
        return false;
    }

    public function addAddress($data) {
        $this->db->begin_transaction();
        try {
            if (!empty($data['is_primary'])) {
                $this->resetPrimaryAddresses($data['user_id']);
            }

            $sql = "INSERT INTO addresses (user_id, label, recipient_name, phone_number, street_address, city, province, postal_code, country, is_primary) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("issssssssi", 
                $data['user_id'], $data['label'], $data['recipient_name'], $data['phone_number'], 
                $data['street_address'], $data['city'], $data['province'], $data['postal_code'], 
                $data['country'], $data['is_primary']
            );
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function updateAddress($id, $user_id, $data) {
        $this->db->begin_transaction();
        try {
            if (!empty($data['is_primary'])) {
                $this->resetPrimaryAddresses($user_id);
            }

            $sql = "UPDATE addresses SET label = ?, recipient_name = ?, phone_number = ?, street_address = ?, city = ?, province = ?, postal_code = ?, country = ?, is_primary = ? WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssssssssiii", 
                $data['label'], $data['recipient_name'], $data['phone_number'], 
                $data['street_address'], $data['city'], $data['province'], $data['postal_code'], 
                $data['country'], $data['is_primary'], $id, $user_id
            );
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function deleteAddress($id, $user_id) {
        $sql = "DELETE FROM addresses WHERE id = ? AND user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("ii", $id, $user_id);
            return $stmt->execute();
        }
        return false;
    }

    public function setPrimaryAddress($id, $user_id) {
        $this->db->begin_transaction();
        try {
            $this->resetPrimaryAddresses($user_id);
            
            $sql = "UPDATE addresses SET is_primary = 1 WHERE id = ? AND user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    private function resetPrimaryAddresses($user_id) {
        $sql = "UPDATE addresses SET is_primary = 0 WHERE user_id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
    }
}

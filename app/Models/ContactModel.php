<?php

namespace App\Models;

use App\Core\Model;

class ContactModel extends Model {
    
    public function getAllMessages() {
        $messages = [];
        $sql = "SELECT * FROM pesan_kontak ORDER BY tanggal_kirim DESC";
        $result = $this->db->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }
        }
        return $messages;
    }

    public function getMessageById($id) {
        $sql = "SELECT * FROM pesan_kontak WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                return $result->fetch_assoc();
            }
            $stmt->close();
        }
        return false;
    }

    public function markAsRead($id) {
        $sql = "UPDATE pesan_kontak SET status_baca = 'sudah dibaca' WHERE id = ? AND status_baca = 'belum dibaca'";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }

    public function saveReply($id, $reply) {
        $sql = "UPDATE pesan_kontak SET admin_reply_message = ?, admin_reply_timestamp = NOW(), status_baca = 'sudah dibalas' WHERE id = ?";
        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("si", $reply, $id);
            $success = $stmt->execute();
            $stmt->close();
            return $success;
        }
        return false;
    }
}

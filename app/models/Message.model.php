<?php

require_once "../config/Database.config.php";

class Message {
    private $db;

    public function __construct() {
        $this->db = Database::connectToDb();
    }

    public function sendMessage($messageData) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO message_table (message_content, message_date, message_private, conv_id, id_user)
                VALUES (:content, :date, :private, :conv_id, :user_id)"
            );
            $stmt->bindParam(':content', $messageData['content']);
            $stmt->bindParam(':date', $messageData['message_date']);
            $stmt->bindParam(':private', $messageData['message_private']);
            $stmt->bindParam(':conv_id', $messageData['conv_id']);
            $stmt->bindParam(':user_id', $messageData['user_id']);
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message : " . $e->getMessage());
        }
    }

    public function sendMessageWithAttachment($messageData) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO message_table (message_content, message_date, message_private, conv_id, id_user)
                VALUES (:content, :date, :private, :conv_id, :user_id, :type)"
            );
            $stmt->bindParam(':content', $messageData['content']);
            $stmt->bindParam(':date', $messageData['message_date']);
            $stmt->bindParam(':private', $messageData['message_private']);
            $stmt->bindParam(':conv_id', $messageData['conv_id']);
            $stmt->bindParam(':user_id', $messageData['user_id']);
            $stmt->execute();
            $message_id = $this->db->lastInsertId();
            return true;

            // TODO: Envoi du fichier
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message : " . $e->getMessage());
        }
    }

    public function sendAttachment($messageData) {
        // TODO: Envoi du fichier sans message
    }

    public function getConversationMessages($conv_id, $user_id, $limit, $offset) {}

    public function markAsRead($message_id, $user_id) {}

    public function deleteMessage($message_id, $user_id) {}

    public function editMessage($message_id, $content, $user_id) {}

    public function getUnreadMessagesCount($user_id) {}
    
    public function getLastMessage($conv_id) {}
}
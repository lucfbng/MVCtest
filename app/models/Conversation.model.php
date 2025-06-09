<?php
require_once "../config/Database.config.php";

class Conversation {
    //DB
    private $db;
    // Paramètres
    private $id;
    private $user1;
    private $user2;
    private $conv_private;
    private $conv_name;


    // Injection de déprendance
    public function __construct() {
        $this->db = Database::connectToDb();
    }
    
    // SETTER
    public function setConversationData($conversationData) {
        if (isset($conversationData['user1'])) $this->user1 = $conversationData['user1'];
        if (isset($conversationData['user2'])) $this->user2 = $conversationData['user2'];
        if (isset($conversationData['conv_private'])) $this->conv_private = $conversationData['conv_private'] ?? 1;
        if (isset($conversationData['conv_name'])) $this->conv_name = $conversationData['conv_name'] ?? null;
    }

    // GETTERS
    public function getId() { return $this->id; }
    public function getUser1() { return $this->user1; }
    public function getUser2() { return $this->user2; }
    public function getConvPrivate() { return $this->conv_private; }
    public function getConvName() { return $this->conv_name; }

    // Méthodes
    
    public function conversationExists($conversationData) {
        $this->setConversationData($conversationData);
        if ($this->user1 == $this->user2) {
            return false;
        }
        // Vérification si les utilisateurs sont dans une conversation
        try {
            $stmt = $this->db->prepare(
                "SELECT uc1.id_user, uc1.conv_id, uc2.id_user, uc2.conv_id
                FROM user_conversation uc1
                INNER JOIN user_conversation uc2 ON uc1.conv_id = uc2.conv_id
                WHERE uc1.id_user = :user1 AND uc2.id_user = :user2"
            );
            $stmt->bindParam(':user1', $this->user1);
            $stmt->bindParam(':user2', $this->user2);
            $stmt->execute();
            $count = $stmt->rowCount();
            if ($count > 1) {
                return true;
            } else {
                $result = $this->createPrivateConversation($conversationData);
                return $result;
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la vérification des utilisateurs");
        }
    }

    public function createPrivateConversation($conversationData) {
        $this->setConversationData($conversationData);
        // Requête préparée qui créer une conversation en base de donnée
        try {
            // Création de la conversation
            $stmt = $this->db->prepare(
                "INSERT INTO conversation_table (conv_private, conv_name)
                VALUES (:conv_private, :conv_name)"
            );
            $stmt->bindParam(':conv_private', $this->conv_private);
            $stmt->bindParam(':conv_name', $this->conv_name);
            $stmt->execute();
            $convId = $this->db->lastInsertId();

            // Ajout des utilisateurs à la conversation
            $stmt2 = $this->db->prepare(
                "INSERT INTO user_conversation (id_user, conv_id) VALUES (:user_id, :conv_id)"
            );
            foreach ([$this->user1, $this->user2] as $userId) {
                $stmt2->bindParam(':user_id', $userId);
                $stmt2->bindParam(':conv_id', $convId);
                $stmt2->execute();
                return true;
            }
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur inatendue de la base de donnée',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function findById($conv_id) {}

    public function getUserConversations($user_id) {}

    public function getConversationParticipants($conv_id) {}

    public function deleteConversation($conv_id, $user_id) {}

    public function updateConversationName($conv_id, $name) {}

    public function addUserToConversation($conv_id, $user_id) {}
}
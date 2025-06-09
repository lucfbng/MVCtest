<?php
require_once "../services/MessageService/DataValidator.service.php";

class MessageService {

    private $messageModel;
    private $authService;
    private $dataValidator;

    public function __construct() {
        $this->messageModel = new Message();
        $this->authService = new AuthService();
        $this->dataValidator = new DataValidatorService();
    }

    // Fonction pour définir le type de message
    public function setMessageType($messageData) {
        if ($messageData['type'] === 'text') {
            $result = $this->sendMessage($messageData);
            return $result;
        } else if ($messageData['type'] === 'file' && $messageData['content'] === null) {
            $result = $this->sendAttachment($messageData);
            return $result;
        } else if ($messageData['type'] === 'file' && $messageData['content'] !== null) {
            $result = $this->sendMessageWithAttachment($messageData);
            return $result;
        }
    }

    public function sendMessage($messageData) {
        try {
            $messageData = $this->dataValidator->cleaningMessageData($messageData);
            if ($this->authService->isAuthenticated()) {
                // Vérification si l'utilisateur est le même que celui qui envoie le message
                $userVerification = $this->authService->getCurrentUser();
                
                if ($userVerification['id'] === $messageData['user_id']) {
                    $result = $this->messageModel->sendMessage($messageData);
                    return $result;
                } else {
                    throw new Exception("Utilisateur non autorisé");
                }
            } else {
                throw new Exception("Utilisateur non authentifié");
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message : " . $e->getMessage());
        }
    }

    public function sendMessageWithAttachment($messageData) {
        try {
            if ($this->authService->isAuthenticated()) {
                $userVerification = $this->authService->getCurrentUser();
                if ($userVerification['id'] === $messageData['user_id']) {
                    $result = $this->messageModel->sendMessageWithAttachment($messageData);
                    return $result;
                } else {
                    throw new Exception("Utilisateur non autorisé");
                }
            } else {
                throw new Exception("Utilisateur non authentifié");
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'envoi du message : " . $e->getMessage());
        }
    }

    public function getConversationMessages($conv_id, $user_id) {}

    public function markMessageAsRead($message_id, $user_id) {}

    public function deleteMessage($message_id, $user_id) {}

    public function getUnreadCount($user_id) {}
}
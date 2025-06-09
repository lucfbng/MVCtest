<?php
require_once "../services/MessageService/Message.service.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

class MessageController {

    private $messageService;
    private $dataValidator;

    public function __construct() {
        $this->messageService = new MessageService();
        $this->dataValidator = new DataValidatorService();
    }

    public function sendMessage() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Traitement des messages texte
                $messageData = [
                    'type' => $_POST['type'] ?? 'text',
                    'content' => $_POST['content'],
                    'message_date' => date('Y-m-d H:i:s'),
                    'conv_id' => $_POST['conv_id'],
                    'user_id' => $_POST['user_id'],
                    'user2_id' => $_POST['user2_id'] ?? null,
                    'message_private' => $_POST['message_private'] ?? false
                ];
                $result = $this->dataValidator->cleaningMessageData($messageData);
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Message envoyé avec succès',
                        'data' => $result
                    ]);
                }
            }   

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function getMessages() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $conv_id = $_POST['conv_id'];
                $user_id = $_POST['user_id'];

                $messages = $this->messageService->getConversationMessages($conv_id, $user_id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Messages récupérés avec succès',
                    'data' => $messages
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la récupération des messages',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function markAsRead() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $messageId = $_POST['message_id'];
                $userId = $_POST['user_id'];

                $result = $this->messageService->markMessageAsRead($messageId, $userId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Message marqué comme lu',
                    'data' => $result
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors du marquage du message',
                'errors' => $e->getMessage()
            ]);
        }
    }
}

// PathSwitcher (MÊME LOGIQUE que Auth.controller.php)
$MessageController = new MessageController();
$action = $_POST['action'] ?? null;

switch($action) {
    case 'send':
        $MessageController->sendMessage();
        break;
    case 'getMessages':
        $MessageController->getMessages();
        break;
    case 'markAsRead':
        $MessageController->markAsRead();
        break;
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Action non reconnue par le serveur'
        ]);
} 
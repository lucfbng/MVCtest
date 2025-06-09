<?php

require_once "../services/MessageService/Message.service.php";
require_once "../services/AuthServices/Auth.service.php";
require_once "../models/User.model.php";

class DataValidatorService {


    private $userModel;
    private $messageService;
    private $authService;
    private $isConnected;



    public function __construct() {

        $this->userModel = new User();
        $this->messageService = new MessageService();
        $this->authService = new AuthService();
    }

    // Vérification si l'utilisateur est connecté
    public function isConnected() {
        $this->isConnected = $this->authService->isAuthenticated();
        if (!$this->isConnected) {
            throw new Exception("Utilisateur non connecté");
        }
    }

    // Vérifie si l'utilisateur est connecté + récupère les données de l'utilisateur
    public function ifConnectedGetUserData() {
        $user = $this->authService->getCurrentUser();
        return $user;
    }

    // Nettoyage de donnée MESSAGE
    public function cleaningMessageData($messageData) {
        if ($messageData['user2_id'] === null) {
            throw new Exception("Utilisateur non défini");
        } else {
            $user2 = $this->userModel->findById($messageData['user2_id']);
            if ($user2 === false) {
                throw new Exception("Utilisateur non trouvé");
            }
        }
        $user = $this->ifConnectedGetUserData();
        // Vérifie que l'utilisateur est le même que celui qui envoie le message
        $userGet = $this->userModel->findById($user['id_user']);
        $userDb = $this->userModel->findById($messageData['user_id']);
        // Vérifie que les utilisateurs existent
        if ($userGet === false || $userDb === false) {
            throw new Exception("Utilisateur non trouvé");
        }
        // Vérifie que les utilisateurs sont les mêmes
        if ($userGet['id_user'] !== $userDb['id_user']) {
            throw new Exception("Utilisateur non autorisé");
        }
        
        $validType = ['text', 'file'];
        if (!in_array($messageData['type'], $validType)) {
            throw new Exception("Type de message invalide");
        }
        if (empty(trim($messageData['content'])) || empty($messageData['conv_id']) || empty($messageData['user_id'])) {
            throw new Exception("Données manquantes");
        }
        if (!is_numeric($messageData['conv_id']) || !is_numeric($messageData['user_id'])) {
            throw new Exception("Données invalides");
        }
        if ($messageData['user_id'] !== $user['id_user']) {
            throw new Exception("Utilisateur non autorisé");
        }
        if (!is_bool($messageData['message_private'])) {
            throw new Exception("Données invalides");
        }
        $messageData = [
            'type' => $messageData['type'],
            'content' => htmlspecialchars(trim($messageData['content'])),
            'conv_id' => (int)$messageData['conv_id'],
            'user_id' => (int)$messageData['user_id'],
            'message_private' => (bool)($messageData['message_private'] ?? false),
            'message_date' => $messageData['message_date'] ?? date('Y-m-d H:i:s')
        ];
        return $messageData;
    }
}
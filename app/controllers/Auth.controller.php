<?php
require_once "../services/Auth.service.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

class AuthController {

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function registerForm() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formData = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'confirmPassword' => $_POST['confirmPassword'],
                    'userStatus' => 'Offline',
                    'userRole' => 'Associé'
                ];

                $result = $this->authService->register($formData);
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Inscription réussie',
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function loginForm() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $formData = [
                    'email' => $_POST['email'],
                    'password' => $_POST['password']
                ];
    
                $this->authService->authenticate($formData['email'], $formData['password']);

                echo json_encode([
                    'success' => true,
                    'message' => 'Connexion réussie',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'errors' => $e->getMessage()
            ]);
        }
    }

    public function logoutForm() {
        try {
            // Démarrer la session si elle n'est pas déjà active
            $result = $this->authService->logout();
            if ($result) {
                    echo json_encode([
                    'success' => true,
                    'message' => 'Déconnexion réussie',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la déconnexion',
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'errors' => $e->getMessage()
            ]);
        }
    }
}


// PathSwitcher
$AuthController = new AuthController();
$action = $_POST['action'] ?? null;

switch($action) {
    case 'register':
        $AuthController->registerForm();
        break;
    case 'login':
        $AuthController->loginForm();
        break;
    case 'logout':
        $AuthController->logoutForm();
        break;
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Action non reconnue par le serveur'
        ]);
}
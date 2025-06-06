<?php
require_once "../services/Auth.service.php";
require_once "../services/Sign.service.php";
require_once "../services/Session.service.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

class AuthController {
    public function registerForm() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $formData = [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'confirmPassword' => $_POST['confirmPassword']
                ];
                $signService = new SignService();
                $cleanData = $signService->cleaningData($formData, $type = 'register');

                if ($cleanData) {
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
    
                $signService = new SignService();
                $cleanData = $signService->cleaningData($formData, $type = 'login');
    
                if ($cleanData) {
                    $sessionService = new SessionService();
                    $sessionService->startUserSession($formData);
                }
                if ($sessionService) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Connexion réussie',
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

    public function logoutForm() {
        try {
            // Démarrer la session si elle n'est pas déjà active
            $sessionService = new SessionService();
            $sessionState = $sessionService->destroySession();
            if ($sessionState) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Déconnexion réussie',
                ]);
            } else if (!$sessionState) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Aucune session active',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Le serveur a rencontré un problème',
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
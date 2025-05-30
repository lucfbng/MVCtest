<?php
require_once "../services/Auth.service.php";
require_once "../services/Sign.service.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

class AuthController {
    public function registerData() {
        // Le submit du formulaire doit avoir name="register"
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'confirmPassword' => $_POST['confirmPassword']
            ];
            $signService = new SignService();
            $cleanData = $signService->cleanRegisterData($formData);
            echo $cleanData;
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'errors' => 'Problème avec les données reçues dans le controlleur'
            ]);
        }
    }

    public function loginData() {
        // Le submit du formulaire doit avoir name="login"
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'email' => $_POST['email'],
                'password' => $_POST['password']
            ];
            $signService = new SignService();
            $cleanData = $signService->cleanLoginData($formData);
            echo $cleanData;
            
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue',
                'errors' => 'Problème avec les données reçues dans le controlleur'
            ]);
        }
    }
}





// PathSwitcher
$AuthController = new AuthController();
$action = $_POST['action'] ?? null;

switch($action) {
    case 'register':
        $AuthController->registerData();
        break;
    case 'login':
        $AuthController->loginData();
        break;
    case 'logout':
        $AuthController->logoutData();
        break;
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Action non reconnue par le serveur'
        ]);
}
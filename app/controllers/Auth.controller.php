<?php
require_once "../services/Auth.service.php";
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

class AuthController {
    public function registerData() {
        // Le submit du formulaire doit avoir name="register"
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $formData = [
                'firstname' => $_POST['firstname'],
                'lastname' => $_POST['lastname'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'confirmPassword' => $_POST['confirmPassword']
            ];

            $errors = $this->cleanRegisterUser($formData);
        } else {
            echo json_encode(['errors' => 'Methode non autorisée']);
        }
    }
}
(new AuthController())->registerData();
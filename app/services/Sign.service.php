<?php
require_once "../models/User.model.php";

class SignService {

    public function cleanRegisterData($userData){
    
        $userData = filter_input_array(INPUT_POST, [
            'firstname' => FILTER_SANITIZE_STRING,
            'lastname' => FILTER_SANITIZE_STRING,
            'email' => FILTER_VALIDATE_EMAIL,
            'password' => FILTER_UNSAFE_RAW,
            'confirmPassword' => FILTER_UNSAFE_RAW
        ]);

        if ($userData['email'] === false) {
            return json_encode([
            "success" => "false",
            "message" => "Format d'email incorrect",
            "errors" => "Erreur lors de la validation de l'email"]);
        } else {
            $this->validateFields($userData);
        }
        if (empty($errors)) {
            // Mise en forme des données pour le modèle
            $userData = [
                'firstname' => trim($userData['firstname']),
                'lastname' => trim($userData['lastname']),
                'email' => strtolower($userData['email']),
                'passwordhash' => password_hash($userData['password'], PASSWORD_DEFAULT),
                'creationDate' => date('Y-m-d H:i:s'),
                'userStatus' => '0',
                'userRole' => 'Associe'
            ];
            // Envoie des données au modèle
            $userModel = new User();
            $result = $userModel->createUser($userData);
            $response = $result;
        } else {
            // $response['errors'] = $errors;
            echo json_encode([
                'success' => 'false',
                'message' => 'Erreur dans le service "Sign"',
                'errors' => $errors]);
        }
        echo json_encode($response);
    }
    

    public function validateFields($data) {
        $errors = [];
        if (empty($data['firstname']) || strlen($data['firstname']) < 3) {
            $errors['firstname'] = [
                'success' => false,
                'message' => 'Le prénom doit contenir au moin 3 caractères',
                'errors' => 'Erreur dans la validation des données'
            ]; 
        }
        if (empty($data['lastname']) || strlen($data['lastname']) < 3) {
            $errors['lastname'] = [
                'success' => false,
                'message' => 'Le Nom doit contenir au moin 3 caractères',
                'errors' => 'Erreur dans la validation des données'
            ];
        }
        if (strlen($data['password']) < 8) {
            $errors['passwordLength'] = [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moin 8 caractères',
                'errors' => 'Erreur dans la validation des données'
            ];
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $data['password'])) {
            $errors['passwordLength'] = [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moin 1 symbole',
                'errors' => 'Erreur dans la validation des données'
            ];
        }
        if (!preg_match('/[A-Z]/', $data['password'])) {
            $errors['passwordLength'] = [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moin une majuscule',
                'errors' => 'Erreur dans la validation des données'
            ];
        }
        if ($data['password'] !== $data['confirmPassword']) {
            $errors['passwordLength'] = [
                'success' => false,
                'message' => 'Les mot de passe ne correspondent pas',
                'errors' => 'Erreur dans la validation des données'
            ];
        }
        return json_encode($errors);
    }
}

<?php
require_once "../models/User.model.php";
// TODO renommer le ficher AuthValidator.service.php
class SignService {

    // REGISTER
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
            "success" => false,
            "message" => "Format d'email incorrect",
            "errors" => "Erreur lors de la validation de l'email"]);
        } else {
            $checkData = json_decode($this->validateRegisterData($userData), true);
        }
        if ($checkData['success'] === true) {
            // Le true a la fin de $checkEmail est un param optionnel pour utiliser le resultat comme un tableau
            // sans le true : if($checkEmail->success)
            // avec le true : if($checkEmail['success'])
            $userModel = new User();
            $checkEmail = json_decode($userModel->emailExists($userData['email']), true);

            if ($checkEmail['success']) {
                return json_encode([
                    'success' => false,
                    'message' => 'Cet email est déjà utilisé'
                ]);
            } else {
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
            }
        } else {
            return json_encode([
                'success' => false,
                'message' => $checkData['errors'],
            ]);
        }
        // $result contien déjà du json encodé donc un simple return $result suffit
        return $result;
    }

    public function validateRegisterData($userData) {
        $errors = [];

        if (strlen($userData['firstname']) < 3) {
            $errors[] = 'Le prénom doit contenir minimum 3 caractères';
        }
        if (strlen($userData['lastname']) < 3) {
            $errors[] = 'Le nom doit contenir minimum 3 caractères';
        }
        if (!preg_match('/^[A-Za-z0-9.-_]+@[A-Za-z0-9.-_]+[.]{1}[A-Za-z]{2,}$/i', $userData['email'])) {
            $errors[] = 'Format d\'email invalide';
        }
        if ($userData['password'] != $userData['confirmPassword']) {
            $errors[] = 'Les mots de passe ne correspondent pas';
        }
        if (strlen($userData['password']) < 8) {
            $errors[] = 'Mot de passe trop court';
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $userData['password'])) {
            $errors[] = 'Le mot de passe doit contenir un symbole';
        }
        if (!preg_match('/[A-Z]/', $userData['password'])) {
            $errors[] = 'Le mot de passe doit contenir une lettre majuscule';
        }

        if (!empty($errors)) {
            return json_encode([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $errors
            ]);
        }

        return json_encode([
            'success' => true,
            'message' => 'Validation réussie',
            'errors' => []
        ]);
    }

    // LOGIN
    public function cleanLoginData($userData) {
        // Vérifie le format de l'email et retourne false s'il est incorrect
        $userData['email'] = filter_var($userData['email'], FILTER_VALIDATE_EMAIL);

        if ($userData['email'] === false) {
            return json_encode([
                "success" => false,
                "message" => "Format d'email incorrect",
                "errors" => "Erreur lors de la validation de l'email"
            ]);
        } else {
            $userData = [
                'email' => strtolower($userData['email']),
                'password' => ($userData['password']),
            ];
            
            $userModel = new User();
            $result = $userModel->checkLoginData($userData);
            $result = json_decode($result, true);
        } 
        if ($result['success']) {
            return json_encode($result);
        } else {
            return json_encode([
                'success' => false,
                'message' => 'Email ou mot de passe incorrecte'
            ]);
        }    
    }
}


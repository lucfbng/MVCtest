<?php
require_once "../models/User.model.php";

class DataValidatorService {

    public function cleaningData($userData, $type){
        // REGISTER
        if($type === 'register'){
            $userData = filter_input_array(INPUT_POST, [
                'firstname' => FILTER_SANITIZE_STRING,
                'lastname' => FILTER_SANITIZE_STRING,
                'email' => FILTER_VALIDATE_EMAIL,
                'password' => FILTER_UNSAFE_RAW,
                'confirmPassword' => FILTER_UNSAFE_RAW
            ]);
    
            if ($userData['email'] === false) {
                throw new Exception("Format d'email incorrect");
            } else {
                $checkData = $this->validateData($userData, $type = 'register');
            }
            if ($checkData) {
                // Le true a la fin de $checkEmail est un param optionnel pour utiliser le resultat comme un tableau
                // sans le true : if($checkEmail->success)
                // avec le true : if($checkEmail['success'])
                $userModel = new User();
                $checkEmail = $userModel->emailExists($userData['email']);
    
                if ($checkEmail) {
                    throw new Exception("Cet email est déjà utilisé");
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
                    $result = $userModel->userRegister($userData);
                }
            } else {
                throw new Exception($checkData['errors']);
            }
            // $result contien déjà du json encodé donc un simple return $result suffit
            return $result;

            
            // LOGIN
        } else if($type === 'login') {
            $userData['email'] = filter_var($userData['email'], FILTER_VALIDATE_EMAIL);

            if ($userData['email'] === false) {
                throw new Exception("Format d'email incorrect");
            } else {
                $userData = [
                    'email' => strtolower($userData['email']),
                    'password' => ($userData['password']),
                ];

                $checkData = $this->validateData($userData, $type = 'login');

                if ($checkData) {
                    $userModel = new User();
                    $result = $userModel->userLogin($userData);
                    if ($result) {
                        return $result;
                    } else {
                        throw new Exception("Email ou mot de passe incorrecte");
                    } 
                } else {
                    throw new Exception($checkData['errors']);
                }
            } 
   
        }
    }

    public function validateData($userData, $type) {
        $errors = [];
        // Vérification communes
        if (!preg_match('/^[A-Za-z0-9.-_]+@[A-Za-z0-9.-_]+[.]{1}[A-Za-z]{2,}$/i', $userData['email'])) {
            $errors[] = 'Format d\'email invalide';
        }

        if($type === 'register'){
            if (strlen($userData['firstname']) < 3) {
                $errors[] = 'Le prénom doit contenir minimum 3 caractères';
            }
            if (strlen($userData['lastname']) < 3) {
                $errors[] = 'Le nom doit contenir minimum 3 caractères';
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
        } else if ($type === 'login') {
            if (empty($userData['password'])) {
                $errors[] = 'Le mot de passe est requis';
            }
        }
        if (!empty($errors)) {
            throw new Exception("Erreurs de validation : " . implode(', ', $errors));
        }
        return true;
        
    }
}


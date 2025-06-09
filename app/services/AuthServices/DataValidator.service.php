<?php
require_once "../services/AuthServices/Auth.service.php";

class DataValidatorService {

    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    // Nettoyage de donnée INSCRIPTION / LOGIN
    public function cleaningData($userData, $type){
        try {
            // REGISTER
            if($type === 'register'){
                $userData['firstname'] = htmlspecialchars(trim($userData['firstname']));
                $userData['lastname'] = htmlspecialchars(trim($userData['lastname']));
                $userData['email'] = strtolower(filter_var($userData['email'], FILTER_VALIDATE_EMAIL));
                $userData['password'] = $userData['password'];
                $userData['confirmPassword'] = $userData['confirmPassword'];
        
                if ($userData['email'] === false ) {
                    throw new Exception("Format d'email incorrect");
                } else {
                    $checkData = $this->validateData($userData, $type = 'register');
                    if ($checkData) {
                        // Mise en forme des données pour le modèle
                        $userData = [
                            'firstname' => htmlspecialchars(trim($userData['firstname'])),
                            'lastname' => htmlspecialchars(trim($userData['lastname'])),
                            'email' => strtolower($userData['email']),
                            'password' => $userData['password']
                        ];
                        return $userData;
                    }
                }
            // LOGIN
            } else if ($type === 'login') {
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
                        return $userData;
                    }
                } 
            }
        } catch (Exception $e) {
            throw new Exception("Erreur lors du nettoyage des données : " . $e->getMessage());
        }
    }


    public function validateData($userData, $type) {
        $errors = [];
        // Vérification communes
        if (!preg_match('/^[A-Za-z0-9.-_]+@[A-Za-z0-9.-_]+[.]{1}[A-Za-z]{2,}$/i', $userData['email'])) {
            $errors[] = 'Format d\'email invalide';
        }

        if($type === 'register'){
            if (strlen(trim($userData['firstname'])) < 3 || preg_match('/[0-9]/', $userData['firstname']) || preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $userData['firstname'])) {
                $errors[] = 'Le prénom doit contenir minimum 3 caractères et ne doit pas contenir de chiffres ou de symboles';
            }
            if (strlen(trim($userData['lastname'])) < 3 || preg_match('/[0-9]/', $userData['lastname']) || preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $userData['lastname'])) {
                $errors[] = 'Le nom doit contenir minimum 3 caractères et ne doit pas contenir de chiffres ou de symboles';
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


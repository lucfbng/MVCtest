<?php
require_once "../models/User.model.php";

class SessionService {

    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function startUserSession($userData) {
        try {
            session_start();

            if(!empty($_SESSION)){
                $_SESSION = array();
            }

            $user = $this->userModel->findByEmail($userData['email']);
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['userFullName'] = $user['user_lastname'].' '.$user['user_firstname'];
            $_SESSION['email'] = $user['user_email'];
            $_SESSION['logged_in'] = true;
            session_regenerate_id(true);
            return true;

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la démarrage de la session : " . $e->getMessage());
        }
    }

    public function isAuthenticated() {
        if (!isset($_SESSION['email']) || !isset($_SESSION['logged_in'])) {
            throw new Exception("Utilisateur non authentifié");
        }
        return true;
    }
    
    public function destroySession() {
        try {
            
            if (!isset($_SESSION['email'])) {
                throw new Exception("Aucune session active");
            }

            $_SESSION = array();
            // Supprimer le cookie de session
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
                );
            }

            // Détruire la session
            session_destroy();
            return true;

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la déconnexion : " . $e->getMessage());
        }
    }





}
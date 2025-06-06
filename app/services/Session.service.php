<?php
require_once "../services/User.service.php";


class SessionService {

    public static function startUserSession($userData) {
        try {
            session_start();

            if(!empty($_SESSION)){
                $_SESSION = array();
            }

            $userService = new UserService();
            $user = $userService->getUserByEmail($userData['email']);

            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['userFullName'] = $user['user_lastname'].' '.$user['user_firstname'];
            $_SESSION['email'] = $user['user_email'];
            $_SESSION['logged_in'] = true;
            return true;

        } catch (Exception $e) {
            throw new Exception("Erreur lors de la démarrage de la session : " . $e->getMessage());
        }
    }
    
    public function destroySession() {
        try {
            session_start();

            if (!isset($_SESSION['email'])) {
                return false;
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
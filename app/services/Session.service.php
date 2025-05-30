<?php

class SessionService {

    public static function startUserSession($userData) {
        session_start();
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['userFullName'] = $userData['lastname'].' '.$userData['firstname'];
        $_SESSION['email'] = $userData['email'];
        $_SESSION['logged_in'] = true;
    }
    
    public static function destroySession() {
        // Nettoyer toutes les variables de session
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
        session_unset();
        session_destroy();
    }





}
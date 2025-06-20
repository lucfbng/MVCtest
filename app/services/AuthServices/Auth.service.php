<?php
require_once "../models/User.model.php";
require_once "../services/AuthServices/Session.service.php";

class AuthService {
    private $userModel;
    private $sessionService;

    
    // Constantes pour les statuts utilisateur
    const STATUS_ONLINE = "En ligne";
    const STATUS_OFFLINE = "Hors-ligne";
    
    // Constantes pour les rôles
    const ROLE_ASSOCIE = "Associé";
    const ROLE_ADMIN = "Admin";
    
    public function __construct() {
        $this->userModel = new User();
        $this->sessionService = new SessionService();
    }
    
    // Fonction d'authentification
    public function authenticate($userData) {
        try {
            $checkEmail = $this->checkEmailExists($userData['email']);
            if (!$checkEmail) {
                throw new Exception("Email ou mot de passe incorrect");
            }
            $result = $this->userModel->userLogin($userData);
            
            if ($result) {
                // Démarrer la session utilisateur
                $this->sessionService->startUserSession($userData);
                
                // Mettre à jour le statut en ligne
                $this->updateUserStatus($userData['email'], self::STATUS_ONLINE);
                
                return true;
            }
            
        } catch (Exception $e) {
            throw new Exception("Erreur d'authentification : " . $e->getMessage());
        }
    }
    
    // Fonction d'inscription
    public function register($cleanedData) {
        $userData = [
            'firstname' => $cleanedData['firstname'],
            'lastname' => $cleanedData['lastname'],
            'email' => $cleanedData['email'],
            'passwordhash' => password_hash($cleanedData['password'], PASSWORD_DEFAULT),
            'creationDate' => date('Y-m-d H:i:s'),
            'userStatus' => self::STATUS_OFFLINE, 
            'userRole' => self::ROLE_ASSOCIE      
        ];
        $checkEmail = $this->checkEmailExists($cleanedData['email']);
        if ($checkEmail) {
            throw new Exception("Cet email est déjà utilisé");
        }
        try {
            $result = $this->userModel->userRegister($userData);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'inscription : " . $e->getMessage());
        }
            
    }
    
    // Fonction de déconnexion
    public function logout() {
        try {
            session_start();
            
            // Récupérer l'email avant de détruire la session
            $userEmail = $_SESSION['email'] ?? null;
            
            if (!$userEmail) {
                throw new Exception("Aucune session active");
            }
            
            // Mettre à jour le statut hors-ligne AVANT de détruire la session
            $this->updateUserStatus($userEmail, self::STATUS_OFFLINE);
            
            // Détruire la session
            $sessionDestroyed = $this->sessionService->destroySession();
            
            if ($sessionDestroyed) {
                return true;
            } else {
                throw new Exception("Erreur lors de la déconnexion");
            }
            
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la déconnexion : " . $e->getMessage());
        }
    }

    public function checkEmailExists($email) {
        try {
            $result = $this->userModel->emailExists($email);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la vérification de l'email : " . $e->getMessage());
        }
    }
    
    // Fonction de vérification d'authentification
    public function isAuthenticated() {
        try {
            session_start();

            if (!$this->sessionService->isAuthenticated()) {
                return false;
            }

            $user = $this->userModel->findByEmail($_SESSION['email']);
            
            if (!$user || !$user['user_status'] === self::STATUS_ONLINE) {
               return false;
            }

            return true;
            
        } catch (Exception $e) {
            throw new Exception("Erreur d'authentification : " . $e->getMessage());
        }
    }
    
    // Fonction de récupération des informations de l'utilisateur authentifié
    public function getCurrentUser() {
        try {
            session_start();
            
            if (!$this->isAuthenticated()) {
                return null;
            }
            
            return $this->userModel->findByEmail($_SESSION['email']);
            
        } catch (Exception $e) {
            throw new Exception("Erreur récupération utilisateur : " . $e->getMessage());
        }
    }
    
    // Fonction de mise à jour du statut d'un utilisateur
    private function updateUserStatus($email, $status) {
        try {
            return $this->userModel->updateUserStatus($email, $status);
        } catch (Exception $e) {
            throw new Exception("Erreur mise à jour statut : " . $e->getMessage());
        }
    }
    
    // Fonction de vérification si un utilisateur a un rôle spécifique
    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['user_role'] === $role;
    }
    
    // Fonction de vérification si l'utilisateur est un administrateur
    public function isAdmin() {
        return $this->hasRole(self::ROLE_ADMIN);
    }
    
    // Fonction de mise à jour du timestamp de dernière activité
    public function updateLastActivity() {
        try {
            session_start();
            
            if (isset($_SESSION['email'])) {
                $_SESSION['last_activity'] = time();
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            throw new Exception("Erreur mise à jour activité : " . $e->getMessage());
        }
    }
    
    // Fonction de vérification si la session a expiré (timeout de 30 minutes par défaut)
    public function isSessionExpired($timeout = 1800) {
        try {
            session_start();
            
            if (!isset($_SESSION['last_activity'])) {
                return true;
            }
            
            return (time() - $_SESSION['last_activity']) > $timeout;
            
        } catch (Exception $e) {
            throw new Exception("Erreur vérification expiration : " . $e->getMessage());
        }
    }
    
    // Fonction de nettoyage des sessions expirées (à appeler périodiquement)
    public function cleanExpiredSessions() {
        try {
            if ($this->isSessionExpired()) {
                $this->logout();
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            throw new Exception("Erreur nettoyage sessions : " . $e->getMessage());
        }
    }
} 
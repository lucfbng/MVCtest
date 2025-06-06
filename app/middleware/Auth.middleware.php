<?php
require_once "../services/Auth.service.php";

class AuthMiddleware {
    private $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function checkAuth() {
        if (!$this->authService->isAuthenticated()) {
            throw new Exception("Accès non autorisé");
        }
        return true;
    }
    
    // Protection par rôle
    public function requireAdmin() {
        $this->checkAuth();
        
        if (!$this->authService->isAdmin()) {
            throw new Exception("Accès administrateur requis");
        }
        return true;
    }
    
    // Verification du timeout de la session
    /* public function checkSessionTimeout() {
        if ($this->authService->isSessionExpired()) {
            $this->authService->logout(); // Auto-logout
            throw new Exception("Session expirée");
        }
        
        return true;
    } */
}
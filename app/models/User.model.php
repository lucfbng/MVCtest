<?php

require_once "../config/database.php";

class User {
    //DB
    private $db;
    // Paramètres
    private $id;
    private $firstname;
    private $lastname;
    private $email;
    private $passwordhash;
    private $creationDate;
    private $userStatus;
    private $userRole;

    // Injection de déprendance
    public function __construct() {
        $this->db = Database::connectToDb();
    }

    // SETTER
    public function setUserData($userData) {
        if (isset($userData['firstname'])) $this->firstname = $userData['firstname'];
        if (isset($userData['lastname'])) $this->lastname = $userData['lastname'];
        if (isset($userData['email'])) $this->email = $userData['email'];
        if (isset($userData['passwordhash'])) $this->passwordhash = $userData['passwordhash'];
        if (isset($userData['creationDate'])) $this->creationDate = $userData['creationDate'];
        if (isset($userData['userStatus'])) $this->userStatus = $userData['userStatus'];
        if (isset($userData['userRole'])) $this->userRole = $userData['userRole'];
    }

    // GETTERS
    public function getId() { return $this->id; }
    public function getFirstname() { return $this->firstname; }
    public function getLastname() { return $this->lastname; }
    public function getEmail() { return $this->email; }
    public function getPasswordHash() { return $this->passwordhash; }
    public function getCreationDate() { return $this->creationDate; }
    public function getUserStatus() { return $this->userStatus; }
    public function getUserRole() { return $this->userRole; }

    // Méthodes
    // CRUD
    public function createUser($userData) {
        $this->setUserData($userData);
        // Requête préparée qui créer un utilisateur en base de donnée
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_table (user_firstname, user_lastname, user_email, user_password, user_creation_date, user_status, user_role)
                VALUES (:firstname, :lastname, :email, :password, :creationDate, :userStatus, :userRole)
            ");
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $this->passwordhash);
            $stmt->bindParam(':creationDate', $this->creationDate);
            $stmt->bindParam(':userStatus', $this->userStatus);
            $stmt->bindParam(':userRole', $this->userRole);
            if ($stmt->execute()) {
                //Récupérer l'ID du dernier utilisateur inséré
                $this->id = $this->db->lastInsertId();
                return json_encode([
                    'success' => true, 
                    "message" => "Utilisateur crée avec succès"
                ]);
            } else {
                return json_encode([
                    'success' => false,
                    'message' => 'Erreur dans la requête du modele' ,
                    "errors" => "Erreur dans le model User lors de l'execution de la requête"
                ]);
            }
            
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur dans la fonction createUser dans le modèle',
                'errors' => $e->getMessage()
            ]);
        } // Échec de l'insertion
    }
    
    public function updateUser($userData) {
        $this->setUserData($userData);
        // Requête pour modifier un utilisateur
        try {
            $stmt = $this->db->prepare("
                UPDATE user_table 
                SET firstname = :fistname, lastname = :lastname, email = :email,
                userStatus = :userStatus, userRole = :userRole
                WHERE id_user = :id 
            ");
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':userStatus', $this->userStatus);
            $stmt->bindParam(':userRole', $this->userRole);
            $stmt->execute();
            return json_encode(['success' => true, 'message' => 'Utilisateur actualisé !']);

        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur dans la fonction updateUser du modèle',
                'errors' => $e->getMessage(),]);
        }
    }

    public function deleteUser($id) {
        // Requête pour supprimer un utilisateur
        try {
            $stmt = $this->db->prepare("
                DELETE FROM users WHERE id = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return json_encode(['success' => true, 'message' => 'Utilisateur supprimé !']);
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur dans la fonction deleteUser du modèle',
                'errors' => $e->getMessage(),]);
        }
    }

    // Autres

    public function getAllUser() {
        // Requête pour récupérer tous les utilisateurs
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_table
            ");
            $stmt->execute();
            $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return json_encode(['success' => true, 'message' => 'Utilisateurs récupérés !', $allUsers]);

        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur dans la fonction getAllUser dans le modèle',
                'errors' => $e->getMessage(),]);
        }
    }

    public function findById($id) {
        // Requête pour trouver un utilisateur avec l'email
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_table
                WHERE id_user = :id
            ");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $foundedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            return json_encode(['success' => true, 'message' => 'Utilisateur récupéré !', $foundedUser]);
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur dans la fonction findById dans le modèle',
                'errors' => $e->getMessage(),]);
        }
    }
}
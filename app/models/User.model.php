<?php

require_once "../config/Database.config.php";

class User {
    //DB
    private $db;
    // Paramètres
    private $id;
    private $firstname;
    private $lastname;
    private $email;
    private $password;
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
        if (isset($userData['firstname'])) $this->firstname = $userData['firstname'] ?? null;
        if (isset($userData['lastname'])) $this->lastname = $userData['lastname'] ?? null;
        if (isset($userData['email'])) $this->email = $userData['email'];
        if (isset($userData['password'])) $this->password = $userData['password'];
        if (isset($userData['passwordhash'])) $this->passwordhash = $userData['passwordhash'];
        if (isset($userData['creationDate'])) $this->creationDate = $userData['creationDate'] ?? null;
        if (isset($userData['userStatus'])) $this->userStatus = $userData['userStatus'] ?? null;
        if (isset($userData['userRole'])) $this->userRole = $userData['userRole'] ?? null;
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
    public function userRegister($userData) {
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
                    'message' => 'Erreur inatendue du serveur' ,
                    "errors" => "Erreur dans le model User lors de l'execution de la requête"
                ]);
            }
            
        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur inatendue de la base de donnée',
                'errors' => $e->getMessage()
            ]);
        } // Échec de l'insertion
    }

    public function userLogin($userData) {
        $this->setUserData($userData);
        try {
            $stmt = $this->db->prepare(
                "SELECT user_email, user_password
                FROM user_table
                WHERE user_email = :email"
            );
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$user) {
                throw new Exception("Email ou mot de passe incorrecte");

            }
            if(password_verify($this->password, $user['user_password'])) {
                $this->password = null;
                return true;

            } else {
                throw new Exception("Email ou mot de passe incorrect");

            };
        } catch (PDOException $e) {
            throw new Exception("Erreur lors de la vérification de l'email : " . $e->getMessage());
        }
    }
    
    public function updateUser($userData) {
        $this->setUserData($userData);
        // Requête pour modifier un utilisateur
        try {
            $stmt = $this->db->prepare(
                "UPDATE user_table 
                SET firstname = :fistname, lastname = :lastname, email = :email,
                userStatus = :userStatus, userRole = :userRole
                WHERE id_user = :id"
            );
            $stmt->bindParam(':firstname', $this->firstname);
            $stmt->bindParam(':lastname', $this->lastname);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':userStatus', $this->userStatus);
            $stmt->bindParam(':userRole', $this->userRole);
            $stmt->execute();
            return true;

        } catch (PDOException $e) {
            throw new Exception("Erreur dans findById : " . $e->getMessage());
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
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur dans deleteUser : " . $e->getMessage());
        }
    }

    // Autres
    // Vérifie si l'email existe déja dans la db
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM user_table
                WHERE user_email = :email
            ");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            // return true si l'email existe déjà
            if($count > 0) {
                return true;
            }
            return false;

        } catch (PDOException $e) {
            return json_encode([
                'success' => false,
                'message' => 'Erreur lors de la vérification de l\'email',
                'errors' => $e->getMessage()
            ]);
        }
    }
    public function updateUserStatus($email, $status) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE user_table
                SET user_status = :status
                WHERE user_email = :email"
            );
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':status', $status);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur dans updateUserStatus : " . $e->getMessage());
        }
    }
    public function userStatusHandler($email, $status) {
        $status === "connect" ? $updateStatus = "En ligne" : "Hors-ligne";
        try {
            $stmt = $this->db->prepare(
                "UPDATE user_table
                SET user_status = :status 
                WHERE user_email = :email"
            );
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':status', $updateStatus);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            throw new Exception("Erreur dans userStatusHandler : " . $e->getMessage());
        }
    }
    public function getAllUser() {
        // Requête pour récupérer tous les utilisateurs
        try {
            $stmt = $this->db->prepare(
                "SELECT user_id, user_firstname, user_lastname, user_email, user_creation_date, user_status, user_role 
                FROM user_table"
            );
            $stmt->execute();
            $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $allUsers;

        } catch (PDOException $e) {
            throw new Exception("Erreur dans getAllUser : " . $e->getMessage());
        }
    }

    public static function findByEmail($email) {
        // Requête pour trouver un utilisateur avec l'email
        try {
            $stmt = Database::connectToDb()->prepare(
                "SELECT id_user, user_firstname, user_lastname, user_email, user_creation_date, user_status, user_role
                FROM user_table
                WHERE user_email = :email LIMIT 1"
            );
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $foundedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            return $foundedUser;

        } catch (PDOException $e) {
            throw new Exception("Erreur dans findByEmail : " . $e->getMessage());
        }
    }
}
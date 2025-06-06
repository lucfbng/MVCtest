<?php
require_once "../models/User.model.php";

class UserService {
        public function getUserByEmail($email) {
            $user = User::findByEmail($email);
            return $user;
        }
}
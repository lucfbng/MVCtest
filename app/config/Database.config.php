<?php
class Database {
    private static $instance = null; 

    public static function connectToDb() {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=echo-db;charset=utf8mb4',
                    'root',
                    'root',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                error_log('Database error: ' . $e->getMessage());
                throw new Exception('Database connection failed');
            }
        }
        return self::$instance;
    }
}
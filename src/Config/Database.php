<?php

namespace CoMit\ApiBd\Config;

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database {
    private static $host;
    private static $db_name;
    private static $username;
    private static $password;
    private static $conn = null;

    public static function getConnection() {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2)); 
        $dotenv->load();
        
        self::$host = $_ENV['DB_HOST'];
        self::$db_name = $_ENV['DB_NAME'];
        self::$username = $_ENV['DB_USER'];
        self::$password = $_ENV['DB_PASSWORD'];

        if (self::$conn === null) {
            try {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $exception) {
                echo json_encode(["error" => "Erro de conexão: " . $exception->getMessage()]);
                exit;
            }
        }

        return self::$conn;
    }
}

?>
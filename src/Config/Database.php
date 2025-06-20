<?php

namespace CoMit\ApiBd\Config;

use PDO;
use PDOException;

class Database {
    private static $host = "mysql";
    private static $db_name = "api";
    private static $username = "user";
    private static $password = "senha";
    private static $conn = null;

    public static function getConnection() {
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
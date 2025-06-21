<?php

require __DIR__ . '/../vendor/autoload.php';

use CoMit\ApiBd\Config\Database;
use CoMit\ApiBd\Controllers\UserController;
use CoMit\ApiBd\Jwt\Jwt;
use CoMit\ApiBd\Middleware\AuthMiddleware;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$conn = Database::getConnection();
$userController = new UserController($conn);

$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$resources = explode('/', trim($uri, '/'));


if ($resources[0] === "login") {
    if ($method === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);

        function getByUsername(string $username): array | false
        {
            $conn = Database::getConnection();
            $sql = 'SELECT * FROM users WHERE nome = :nome';
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':nome', $username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $user = getByUsername($data["nome"]);

        if (!$user || !password_verify($data['senha'], $user['senha'])) {
            http_response_code(401);
            echo json_encode(["message" => "Autenticação inválida"]);
            exit;
        }

        $payload = [
            "id" => $user['id'],
            "nome" => $user["nome"]
        ];

        $jwt = new Jwt($_ENV["SECRET_KEY"]);
        $token = $jwt->encode($payload);

        echo json_encode(["token" => $token]);
        exit;
    }
}

if ($resources[0] === "users") {
    AuthMiddleware::authenticate();

    switch ($method) {
        case "GET":
            $id = $_GET['id'] ?? null;
            if (!isset($id)) {
                $userController->getAll();
            } else {
                $userController->find($id);
            }
            break;

        case "POST":
            $userController->post();
            break;

        case "PUT":
            $userController->put();
            break;

        case "PATCH":
            $userController->patch();
            break;

        case "DELETE":
            $userController->delete();
            break;

        default:
            echo json_encode(["error" => "Método de requisição inválido"]);
    }
}

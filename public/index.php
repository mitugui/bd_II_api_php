<?php

require __DIR__ . '/vendor/autoload.php';

use CoMit\ApiBd\Config\Database;
use CoMit\ApiBd\Controllers\UserController;

header("Content-Type: application/json");

$conn = Database::getConnection();
$userController = new UserController($conn);

$method = $_SERVER["REQUEST_METHOD"];
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$resources = explode('/', trim($uri, '/'));

if ($resources[0] === "users") {
    switch ($method){
        case "GET":
            $id = $_GET['id'] ?? NULL;

            if (!isset($id)){
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
?>
<?php

require_once 'config/Database.php';

header("Content-Type: application/json");

$conn = Database::getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        $id = $_GET['id'] ?? 'Não informado';

        if ($id == 'Não informado') {
            $query = "SELECT * FROM users";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        } else {
            $query = "SELECT * FROM users where id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }

        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "INSERT INTO users (nome, email) VALUES (:nome, :email)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":nome", $data["nome"]);
        $stmt->bindParam(":email", $data["email"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro inserido com sucesso!"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "UPDATE users SET nome = :nome, email = :email WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":nome", $data["nome"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->bindParam(":id", $data["id"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro atualizado com sucesso!"]);
        }
        break;

    case 'PATCH':
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID do usuário é obrigatório"]);
            break;
        }
        
        $fields = [];
        $params = [':id' => $data['id']];
        
        if (isset($data['nome'])) {
            $fields[] = "nome = :nome";
            $params[':nome'] = $data['nome'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Nenhum campo válido para atualização"]);
            break;
        }
        
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro atualizado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar registro"]);
        }

        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id", $data["id"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro excluído com sucesso!"]);
        }
        break;

    default:
        echo json_encode(["error" => "Método de requisição inválido"]);
        break;
}
?>
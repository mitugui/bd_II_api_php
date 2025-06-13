<?php

class UserController
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $query = "SELECT * FROM users WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    public function find($id)
    {
        $query = "SELECT * FROM users where id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
    }

    public function post()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "INSERT INTO users (nome, email) VALUES (:nome, :email)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nome", $data["nome"]);
        $stmt->bindParam(":email", $data["email"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro inserido com sucesso!"]);
        } 
    }
    
    public function put()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "UPDATE users SET nome = :nome, email = :email WHERE id = :id AND deleted_at IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nome", $data["nome"]);
        $stmt->bindParam(":email", $data["email"]);
        $stmt->bindParam(":id", $data["id"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro atualizado com sucesso!"]);
        }
    }

    public function patch()
    {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["error" => "ID do usuário é obrigatório"]);
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
        }
                
        $query = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($query);
                
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
                
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro atualizado com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao atualizar registro"]);
        }      
    }

    public function delete()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "UPDATE users SET deleted_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $data["id"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro excluído com sucesso!"]);
        }        
    }
}


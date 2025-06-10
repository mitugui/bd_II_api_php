<?php
header("Content-Type: application/json");

class Database {
    private $host = "mysql";
    private $db_name = "api";
    private $username = "user";
    private $password = "senha";
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, 
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo json_encode(["error" => "Erro de conexão: " . $exception->getMessage()]);
            exit;
        }

        return $this->conn;
    }
}

$conn = (new Database())->getConnection();

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET': // Consulta os registros
        $query = "SELECT * FROM users";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;

    case 'POST': // Insere um novo registro
        $data = json_decode(file_get_contents("php://input"), true);
        $query = "INSERT INTO users (nome, email) VALUES (:nome, :email)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":nome", $data["nome"]);
        $stmt->bindParam(":email", $data["email"]);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Registro inserido com sucesso!"]);
        }
        break;

    case 'PUT': // Atualiza um registro existente
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

    case 'DELETE': // Exclui um registro
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
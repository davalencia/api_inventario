<?php
require_once 'config/Database.php';

class Login
{
    private $conn;
    private $table_name = "login";
    private $token_table = "session_tokens";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function login($data)
    {
        try {
            $username = $data['username'];
            $password = $data['password'];

            $stmt = $this->conn->prepare("SELECT id, username, password, role FROM " . $this->table_name . " WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                $pws_hash = getenv('PWS_HASH');
                $hashed_password = $user['password'];

                if (password_verify($password . $pws_hash, $hashed_password)) {

                    $token = bin2hex(random_bytes(32));
                    $stmt_token = $this->conn->prepare("INSERT INTO " . $this->token_table . " (user_id, token) VALUES (?, ?)");
                    $stmt_token->execute([$user['id'], $token]);

                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    http_response_code(200);
                    return array("message" => "OK", "token" => $token, "role" => $user['role']);
                } else {
                    http_response_code(401);
                    return array("message" => "Usuario o contraseña incorrectos.");
                }
            } else {
                http_response_code(404);
                return array("message" => "Usuario o contraseña incorrectos.");
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return array("message" => "Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    public function logout($data)
    {
        try {
            $query = "UPDATE " . $this->token_table . " SET estado = :estado WHERE token = :token";
            $stmt = $this->conn->prepare($query);

            $estado = 'I';
            $stmt->bindParam(":token", $data['token']);
            $stmt->bindParam(":estado", $estado);

            if ($stmt->execute()) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                session_unset();
                session_destroy();
                return ["message" => "OK"];
            } else {
                http_response_code(500);
                return ["message" => "ERROR"];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return array("message" => "Error al conectar a la base de datos: " . $e->getMessage());
        }
    }

    public function getToken($id)
    {
        $query = "SELECT * FROM " . $this->token_table . " WHERE token = ? AND estado = 'A' ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validateToken($token)
    {
        try {
            // Buscar el token en la base de datos
            $stmt = $this->conn->prepare("SELECT user_id FROM " . $this->token_table . " WHERE token = ? LIMIT 1");
            $stmt->execute([$token]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                // Token válido, devuelve el user_id asociado al token
                return $row['user_id'];
            } else {
                // Token no encontrado o no válido
                return null;
            }
        } catch (PDOException $e) {
            // Error al conectar a la base de datos
            http_response_code(500);
            echo json_encode(array("message" => "Error al conectar a la base de datos: " . $e->getMessage()));
            return null;
        }
    }
}

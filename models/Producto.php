<?php
require_once 'config/Database.php';

class Producto
{
    private $conn;
    private $table_name = "productos";

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function find($id)
    {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $response = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($response) {
                return $response;
            } else {
                $response = ["success" => true, "message" => 'Producto no encontrado'];
            }
            return $response;
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function all()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function allOption($data)
    {
        try {
            $search = $data['search'] ?? '';

            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

            if (!empty($search)) {
                $query .= " AND (id LIKE :search
                            OR cod LIKE :search 
                            OR nombre LIKE :search 
                            OR descripcion LIKE :search 
                            OR precio LIKE :search 
                            OR stock LIKE :search 
                            OR ubicacion LIKE :search)";
            }

            $stmt = $this->conn->prepare($query);

            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }
            $stmt->execute();

            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $response;
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function create($data)
    {
        try {
            $query = "INSERT INTO " . $this->table_name . " (cod, nombre, descripcion, precio, stock, ubicacion) VALUES (:cod, :nombre, :descripcion, :precio, :stock, :ubicacion)";
            $stmt = $this->conn->prepare($query);

            $ran = rand(1000, 10000);
            date_default_timezone_set('America/Bogota');
            $stmt->bindParam(":cod", $ran);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":descripcion", $data['descripcion']);
            $stmt->bindParam(":precio", $data['precio']);
            $stmt->bindParam(":stock", $data['stock']);
            $stmt->bindParam(":ubicacion", $data['ubicacion']);

            if ($stmt->execute()) {
                $id = $this->conn->lastInsertId();
                return ["success" => true, "message" => "OK", "id" => $id];
            } else {
                return ["error" => true, "message" => "Error al crear el Producto"];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function update($id, $data)
    {
        try {
            $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock, ubicacion = :ubicacion, estado = :estado WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":nombre", $data['nombre']);
            $stmt->bindParam(":descripcion", $data['descripcion']);
            $stmt->bindParam(":precio", $data['precio']);
            $stmt->bindParam(":stock", $data['stock']);
            $stmt->bindParam(":ubicacion", $data['ubicacion']);
            $stmt->bindParam(":estado", $data['estado']);

            if ($stmt->execute()) {
                return ["success" => true, "message" => "Producto actualizado"];
            } else {
                return ["error" => true, "message" => "Error al actualizar el Producto"];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function delete($id)
    {
        try {
            $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE id = ?";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(1, $id);
            $checkStmt->execute();

            if ($checkStmt->rowCount() === 0) {
                return ["error" => true, "message" => 'Producto no encontrado'];
            }

            $deleteQuery = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindParam(1, $id);

            if ($deleteStmt->execute()) {
                return ["success" => true, "message" => "Producto eliminado correctamente"];
            } else {
                return ["error" => true, "message" => "Error al eliminar el producto"];
            }
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }

    public function getCount()
    {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            http_response_code(500);
            return ["error" => true, "message" => "Error en la base de datos: " . $e->getMessage()];
        }
    }
}

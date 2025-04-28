<?php
require_once 'config/Database.php';

class Cliente {
    private $conn;
    private $table_name = "clientes";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function find($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function all() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allOption($data) {
        try {
            $draw = $data['draw'];
            $start = $data['start'];
            $length = $data['length'];
            $search = $data['search']['value'];

            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";

            if (!empty($search)) {
                $query .= " AND (id LIKE :search OR nombre LIKE :search OR apellido LIKE :search OR cedula LIKE :search OR telefono LIKE :search)";
            }

            $stmt = $this->conn->prepare($query);

            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }

            $stmt->execute();
            $totalRecords = $stmt->rowCount();

            $query .= " LIMIT :start, :length";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':start', $start, PDO::PARAM_INT);
            $stmt->bindParam(':length', $length, PDO::PARAM_INT);

            if (!empty($search)) {
                $searchParam = "%$search%";
                $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            }

            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                "draw" => intval($draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $totalRecords,
                "data" => $data
            ];
            return $response;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nombre, apellido, correo, telefono, cedula, direccion) VALUES (:nombre, :apellido, :correo, :telefono, :cedula, :direccion)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":apellido", $data['apellido']);
        $stmt->bindParam(":correo", $data['correo']);
        $stmt->bindParam(":telefono", $data['telefono']);
        $stmt->bindParam(":cedula", $data['cedula']);
        $stmt->bindParam(":direccion", $data['direccion']);

        if ($stmt->execute()) {
            $id = $this->conn->lastInsertId();
            return [
                "message" => "OK",
                "id" => $id
            ];
        } else {
            return ["message" => "ERROR"];
        }
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nombre = :nombre, apellido = :apellido, correo = :correo, telefono = :telefono, cedula = :cedula, direccion = :direccion, estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $data['nombre']);
        $stmt->bindParam(":apellido", $data['apellido']);
        $stmt->bindParam(":correo", $data['correo']);
        $stmt->bindParam(":telefono", $data['telefono']);
        $stmt->bindParam(":cedula", $data['cedula']);
        $stmt->bindParam(":direccion", $data['direccion']);
        $stmt->bindParam(":estado", $data['estado']);

        if ($stmt->execute()) {
            return ["message" => "Cliente actualizado"];
        } else {
            return ["message" => "Error al actualizar el cliente"];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            return ["message" => "Cliente eliminado"];
        } else {
            return ["message" => "Error al eliminar el cliente"];
        }
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as total_count FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getForYear() {
        $query = "SELECT YEAR(fecha) AS ano, MONTH(fecha) AS mes, COUNT(*) AS total_clientes 
                  FROM " . $this->table_name . " 
                  WHERE fecha IS NOT NULL AND fecha <> '' 
                  GROUP BY YEAR(fecha), MONTH(fecha)";
    
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    
            $datosClientes = [];
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ano = $row['ano'];
                $mes = intval($row['mes']) - 1;
                $total = intval($row['total_clientes']);
    
                if (!isset($datosClientes[$ano])) {
                    $datosClientes[$ano] = array_fill(0, 12, 0);
                }
    
                $datosClientes[$ano][$mes] += $total;
            }
    
            return $datosClientes;
    
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}
?>

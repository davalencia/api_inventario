<?php
require_once 'config/Database.php';

class Factura {
    private $conn;
    private $table_name = "facturas";

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
            
            if ($data['id_user'] != '') {
                $query = "SELECT f.*, c.nombre AS nombre 
                      FROM " . $this->table_name . " f
                      LEFT JOIN clientes c ON f.id_cli = c.id
                      WHERE 1=1 AND f.id_cli =".$data['id_user'];
            }else {
                $query = "SELECT f.*, c.nombre AS nombre 
                      FROM " . $this->table_name . " f
                      LEFT JOIN clientes c ON f.id_cli = c.id
                      WHERE 1=1";
            }
    
            if (!empty($search)) {
                $query .= " AND (f.id LIKE :search
                                OR f.articulo LIKE :search 
                                OR f.precio LIKE :search 
                                OR f.fecha LIKE :search 
                                OR f.garantia LIKE :search 
                                OR c.nombre LIKE :search)";
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
        $query = "INSERT INTO " . $this->table_name . " (cod, articulo, precio, garantia, descripcion, fecha, id_cli, estado) VALUES (:cod, :articulo, :precio, :garantia, :descripcion, :fecha, :id_cli, :estado)";
        $stmt = $this->conn->prepare($query);

        $ran=rand(1000, 10000);
        date_default_timezone_set('America/Bogota');
        $fec = date('d-m-Y');
        $stmt->bindParam(":cod", $ran);
        $stmt->bindParam(":articulo", $data['articulo']);
        $stmt->bindParam(":precio", $data['precio']);
        $stmt->bindParam(":garantia", $data['garantia']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":fecha", $fec);
        $stmt->bindParam(":id_cli", $data['id_cli']);
        $stmt->bindParam(":estado", $data['estado']);

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
        $query = "UPDATE " . $this->table_name . " SET cod = :cod, articulo = :articulo, precio = :precio, garantia = :garantia, descripcion = :descripcion, fecha = :fecha, id_cli = :id_cli, estado = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":cod", $data['cod']);
        $stmt->bindParam(":articulo", $data['articulo']);
        $stmt->bindParam(":precio", $data['precio']);
        $stmt->bindParam(":garantia", $data['garantia']);
        $stmt->bindParam(":descripcion", $data['descripcion']);
        $stmt->bindParam(":fecha", $data['fecha']);
        $stmt->bindParam(":id_cli", $data['id_cli']);
        $stmt->bindParam(":estado", $data['estado']);

        if ($stmt->execute()) {
            return ["message" => "Factura actualizada"];
        } else {
            return ["message" => "Error al actualizar la factura"];
        }
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        if ($stmt->execute()) {
            return ["message" => "Factura eliminada"];
        } else {
            return ["message" => "Error al eliminar la factura"];
        }
    }

    public function getTotalPrice() {
        $query = "SELECT SUM(precio) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalFormatted = number_format($result['total'], 2);
        return ["total" => $totalFormatted];
    }

    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getForYear() {
        $query = "SELECT YEAR(fecha) AS ano, MONTH(fecha) AS mes, COUNT(*) AS total_facturas 
                  FROM " . $this->table_name . " 
                  WHERE fecha IS NOT NULL AND fecha <> '' 
                  GROUP BY YEAR(fecha), MONTH(fecha)";
    
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
    
            $datosFacturas = [];
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $ano = $row['ano'];
                $mes = intval($row['mes']) - 1;
                $total = intval($row['total_facturas']);
    
                if (!isset($datosFacturas[$ano])) {
                    $datosFacturas[$ano] = array_fill(0, 12, 0);
                }
    
                $datosFacturas[$ano][$mes] += $total;
            }
    
            return $datosFacturas;
    
        } catch (PDOException $e) {
            die("Error en la consulta: " . $e->getMessage());
        }
    }
    
}
?>

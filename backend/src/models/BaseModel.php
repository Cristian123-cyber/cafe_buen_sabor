<?php
namespace App\Models;

use PDO;
use Config\Database;

// Clase base abstracta para modelos con métodos CRUD genéricos
abstract class BaseModel {
    protected $conn;
    protected $table_name;
    protected $primary_key = 'id';

    // El constructor maneja la conexión automáticamente
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // Obtener todos los registros con paginación y orden opcional
    public function getAll($page = 1, $limit = 10, $orderBy = null) {
        $offset = ($page - 1) * $limit;
        $orderClause = $orderBy ? "ORDER BY $orderBy" : "";
        $query = "SELECT * FROM {$this->table_name} $orderClause LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un registro por ID
    public function getById($id) {
        $query = "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo registro
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ":$field";
        }, $fields);
        $query = "INSERT INTO {$this->table_name} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->conn->prepare($query);
        foreach($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Actualizar un registro por ID
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = array_map(function($field) {
            return "$field = :$field";
        }, $fields);
        $query = "UPDATE {$this->table_name} SET " . implode(', ', $setClause) . " WHERE {$this->primary_key} = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        foreach($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    // Eliminar un registro por ID
    public function delete($id) {
        $query = "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Contar el total de registros
    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table_name}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    // Método para obtener la conexión (por si se necesita)
    protected function getConnection() {
        return $this->conn;
    }
}
?> 
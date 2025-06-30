<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Employees extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'employees';
    }

    public function getAll($page = 1, $limit = 10, $orderBy = null)
    {
    $offset = ($page - 1) * $limit;
    $order = $orderBy ? "ORDER BY $orderBy" : "";
    $query = "SELECT * FROM employees $order LIMIT :limit OFFSET :offset";
    $stmt = $this->conn->prepare($query);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM employees WHERE id_employe = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO employees 
            (employe_name, employe_email, password, employees_rol_id_rol, employees_statuses_id_status) 
            VALUES (:employe_name, :employe_email, :password, :employees_rol_id_rol, :employees_statuses_id_status)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employe_name', $data['employe_name']);
        $stmt->bindParam(':employe_email', $data['employe_email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':employees_rol_id_rol', $data['employees_rol_id_rol']);
        $stmt->bindParam(':employees_statuses_id_status', $data['employees_statuses_id_status']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    //filtrar empleados por rol y estado
    public function filterBy($rol = null, $status = null)
    {
    $query = "SELECT * FROM employees WHERE 1=1";
    $params = [];

    if ($rol !== null) {
        $query .= " AND employees_rol_id_rol = ?";
        $params[] = $rol;
    }
    if ($status !== null) {
        $query .= " AND employees_statuses_id_status = ?";
        $params[] = $status;
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function update($id, $data)
    {
        $query = "UPDATE employees SET 
            employe_name = :employe_name, 
            employe_email = :employe_email, 
            password = :password, 
            employees_rol_id_rol = :employees_rol_id_rol, 
            employees_statuses_id_status = :employees_statuses_id_status
            WHERE id_employe = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employe_name', $data['employe_name']);
        $stmt->bindParam(':employe_email', $data['employe_email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':employees_rol_id_rol', $data['employees_rol_id_rol']);
        $stmt->bindParam(':employees_statuses_id_status', $data['employees_statuses_id_status']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM employees WHERE id_employe = ?");
        return $stmt->execute([$id]);
    }

    // Búsqueda por nombre o email
    public function buscarUsuarios($termino)
    {
        $query = "SELECT * FROM employees 
                  WHERE employe_name LIKE :termino 
                  OR employe_email LIKE :termino
                  ORDER BY employe_name";
        $stmt = $this->conn->prepare($query);
        $likeTerm = "%$termino%";
        $stmt->bindParam(':termino', $likeTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
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

    // Obtener todos los empleados

public function getAll($page = 1, $limit = 10, $orderBy = null)
{
    $offset = ($page - 1) * $limit;
    // Opcional: valida que $orderBy sea un campo permitido
    $allowedOrderFields = ['id_employe', 'employe_name', 'employe_email', 'created_date'];
    $order = '';
    if ($orderBy && in_array($orderBy, $allowedOrderFields)) {
        $order = "ORDER BY $orderBy";
    }
    // Interpola solo enteros controlados
    $query = "
        SELECT e.*, r.rol_name, s.status_name
        FROM employees e
        JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol
        JOIN employees_statuses s ON e.employees_statuses_id_status = s.id_status
        $order
        LIMIT $limit OFFSET $offset
    ";
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $empleados = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($empleados as &$empleado) {
            unset($empleado['password']);
        }
        return $empleados;
    }
    catch (\PDOException $e) {
    die("Error en getAll: " . $e->getMessage());
}
}

    public function getById($id)
    {
        $stmt = $this->conn->prepare("
            SELECT e.*, r.rol_name, s.status_name
            FROM employees e
            JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol
            JOIN employees_statuses s ON e.employees_statuses_id_status = s.id_status
            WHERE e.id_employe = ?
        ");
        $stmt->execute([$id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($empleado) {
            unset($empleado['password']);
        }
        return $empleado;
    }

    public function create($data)
{
    // Validar rol 4: debe venir table_id_device
    if ($data['employees_rol_id_rol'] == 4) {
        if (!isset($data['table_id_device']) || $data['table_id_device'] === null) {
            // No se puede registrar sin id de mesa
            return false;
        }
    } else {
        // Si no es rol 4, forzar table_id_device a null
        $data['table_id_device'] = null;
    }

    try {
        $query = "INSERT INTO employees 
            (employe_name, employe_email, password, employees_rol_id_rol, employees_statuses_id_status, employee_cc, table_id_device, created_date) 
            VALUES (:employe_name, :employe_email, :password, :employees_rol_id_rol, :employees_statuses_id_status, :employee_cc, :table_id_device, CURDATE())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employe_name', $data['employe_name']);
        $stmt->bindParam(':employe_email', $data['employe_email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':employees_rol_id_rol', $data['employees_rol_id_rol']);
        $stmt->bindParam(':employees_statuses_id_status', $data['employees_statuses_id_status']);
        $stmt->bindParam(':employee_cc', $data['employee_cc']);
        $stmt->bindParam(':table_id_device', $data['table_id_device']);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    } catch (\PDOException $e) {
        error_log('ERROR SQL CREATE EMPLOYEE: ' . $e->getMessage());
        if (isset($_GET['debug_sql'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Error SQL: ' . $e->getMessage(),
                'query' => $query,
                'data' => $data
            ]);
            exit;
        }
        return false;
    }
}
    // Filtrar empleados por rol y estado con JOIN y sin password
    public function filterBy($rol = null, $status = null)
    {
        $query = "
            SELECT e.*, r.rol_name, s.status_name
            FROM employees e
            JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol
            JOIN employees_statuses s ON e.employees_statuses_id_status = s.id_status
            WHERE 1=1
        ";
        $params = [];

        if ($rol !== null) {
            $query .= " AND e.employees_rol_id_rol = ?";
            $params[] = $rol;
        }
        if ($status !== null) {
            $query .= " AND e.employees_statuses_id_status = ?";
            $params[] = $status;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($empleados as &$empleado) {
            unset($empleado['password']);
        }
        return $empleados;
    }
    

public function existsEmail($email, $excludeId = null)
{
    $query = "SELECT COUNT(*) FROM employees WHERE LOWER(TRIM(employe_email)) = LOWER(TRIM(:email))";
    if ($excludeId !== null) {
        $query .= " AND id_employe != :id";
    }
    $stmt = $this->conn->prepare($query);
    $cleanEmail = strtolower(trim($email));
    $stmt->bindParam(':email', $cleanEmail);
    if ($excludeId !== null) {
        $stmt->bindParam(':id', $excludeId);
    }
    $stmt->execute();
    $count = $stmt->fetchColumn();
    error_log('EXISTS_EMAIL: Email consultado: ' . $cleanEmail . ' | Resultado: ' . $count);
    if (isset($_GET['debug_email'])) {
        echo json_encode([
            'query' => $query,
            'email_consultado' => $cleanEmail,
            'resultado' => $count
        ]);
        exit;
    }
    return $count > 0;
}
    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $fields_sql = implode(', ', $fields);
        $query = "UPDATE employees SET $fields_sql WHERE id_employe = :id";
        $stmt = $this->conn->prepare($query);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
    $query = "UPDATE employees SET employees_statuses_id_status = 3 WHERE id_employe = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
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
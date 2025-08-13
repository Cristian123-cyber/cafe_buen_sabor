<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Employees extends BaseModel
{
    public function getTotalCount($filters = [])
{
    // La consulta es similar a getAll, pero solo cuenta
    $query = "SELECT COUNT(e.id_employe) FROM employees e";
    
    // --- Lógica para filtros (WHERE) ---
    $whereConditions = [];
    $bindings = [];

    // Filtro por término de búsqueda (nombre o email)
    if (!empty($filters['term'])) {
        $whereConditions[] = "(e.employe_name LIKE ? OR e.employe_email LIKE ?)";
        $searchTerm = '%' . $filters['term'] . '%';
        $bindings[] = $searchTerm;
        $bindings[] = $searchTerm;
    }

    // Filtro por rol
    if (!empty($filters['role'])) {
        // Hacemos un JOIN solo si es necesario para el filtro
        $query .= " JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol";
        $whereConditions[] = "e.employees_rol_id_rol = ?";
        $bindings[] = $filters['role'];
    }

    // Filtro por estado
    if (!empty($filters['state'])) {
        // Hacemos un JOIN solo si es necesario para el filtro
        $query .= " JOIN employees_statuses s ON e.employees_statuses_id_status = s.id_status";
        $whereConditions[] = "e.employees_statuses_id_status = ?";
        $bindings[] = $filters['state'];
    }

    if (!empty($whereConditions)) {
        // Asegúrate de que los JOINS no se dupliquen si ambos filtros están activos
        // (Una mejora sería construir los JOINS dinámicamente también)
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }
    
    try {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($bindings);
        return (int) $stmt->fetchColumn(); // fetchColumn() devuelve el valor de la primera columna
    } catch (\PDOException $e) {
        error_log("Error en getTotalCount: " . $e->getMessage());
        throw new \Exception("Error al contar los registros.");
    }
}


    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'employees';
    }



    /**
     * Obtiene una lista paginada y filtrada de empleados.
     */
    public function getAll($page = 1, $limit = 10, $orderBy = 'created_date', $filters = [])
    {
        $offset = ($page - 1) * $limit;

        // --- Construcción de la consulta base ---
        $query = "
        SELECT e.*, r.rol_name, s.status_name
        FROM employees e
        JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol
        JOIN employees_statuses s ON e.employees_statuses_id_status = s.id_status
    ";

        // --- Lógica para filtros (WHERE) ---
        $whereConditions = [];
        $bindings = [];

        // Filtro por término de búsqueda (nombre o email)
        if (!empty($filters['term'])) {
            $whereConditions[] = "(e.employe_name LIKE ? OR e.employe_email LIKE ?)";
            $searchTerm = '%' . $filters['term'] . '%';
            $bindings[] = $searchTerm;
            $bindings[] = $searchTerm;
        }

        // Filtro por rol
        if (!empty($filters['role'])) {
            $whereConditions[] = "e.employees_rol_id_rol = ?";
            $bindings[] = $filters['role'];
        }

        // Filtro por estado
        if (!empty($filters['state'])) {
            $whereConditions[] = "e.employees_statuses_id_status = ?";
            $bindings[] = $filters['state'];
        }

        // Si hay condiciones, las unimos con AND y las añadimos a la query
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }

        // --- Lógica para Ordenamiento (ORDER BY) ---
        $allowedOrderFields = ['id_employe', 'employe_name', 'employe_email', 'created_date'];
        if ($orderBy && in_array($orderBy, $allowedOrderFields)) {
            $query .= " ORDER BY $orderBy DESC"; // Es seguro si validas contra una lista blanca
        }

        // --- Lógica para Paginación (LIMIT/OFFSET) ---
        $query .= " LIMIT ? OFFSET ?";
        $bindings[] = (int) $limit;
        $bindings[] = (int) $offset;

        try {
            $stmt = $this->conn->prepare($query);
            // Usamos bindValue para especificar tipos, especialmente para LIMIT/OFFSET
            foreach ($bindings as $key => $value) {
                // Los parámetros de LIMIT/OFFSET deben ser enteros
                if ($key >= count($bindings) - 2) {
                    $stmt->bindValue($key + 1, (int)$value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key + 1, $value, \PDO::PARAM_STR);
                }
            }
            $stmt->execute();
            $empleados = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($empleados as &$empleado) {
                unset($empleado['password']); // Buena práctica de seguridad
            }

            return $empleados;
        } catch (\PDOException $e) {
            // En un entorno de producción, loguea el error en lugar de hacer 'die'
            error_log("Error en getAll: " . $e->getMessage());
            throw new \Exception("Error al consultar la base de datos.");
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

    // Verificar si la cédula ya existe (excepto para un id dado)
    public function existsCedula($cedula, $excludeId = null)
    {
        if ($cedula === null) return false;
        $query = "SELECT COUNT(*) FROM employees WHERE employee_cc = :cedula";
        if ($excludeId !== null) {
            $query .= " AND id_employe != :id";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':cedula', $cedula);
        if ($excludeId !== null) {
            $stmt->bindParam(':id', $excludeId);
        }
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count > 0;
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

    /**
     * Obtiene el top de meseros por mesas atendidas
     */
    public function getTopWaitersByTablesServed($period = 'monthly')
    {
        $dateCondition = '';
        $params = [];
        
        switch ($period) {
            case 'weekly':
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'monthly':
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'all_time':
                $dateCondition = '';
                break;
            default:
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        }
        
        $query = "SELECT 
                    e.id_employe,
                    e.employe_name as name,
                    0 as tables_served
                  FROM employees e
                  INNER JOIN employees_rol er ON e.employees_rol_id_rol = er.id_rol
                  WHERE e.employees_statuses_id_status = 1
                  AND er.rol_name = 'Mesero'
                  ORDER BY e.employe_name ASC
                  LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar ranking
        $rankedResults = [];
        foreach ($results as $index => $waiter) {
            $waiter['rank'] = $index + 1;
            $rankedResults[] = $waiter;
        }
        
        return $rankedResults;
    }
}

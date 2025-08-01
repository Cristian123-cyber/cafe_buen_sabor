<?php

namespace App\Models;

use PDO;


class Table extends BaseModel
{
    
    public function __construct()
    {
        parent::__construct(); // Llama al constructor de BaseModel
        $this->table_name = 'tables';
        $this->primary_key = 'id_table';
    }
    // Obtener todas las mesas
    public function all($filters)
    {

        $query =  "SELECT t.* FROM {$this->table_name} t
        ";

        // --- Lógica para filtros (WHERE) ---
        $whereConditions = [];
        $bindings = [];

        // Filtro por término de búsqueda (nombre o email)
        if (!empty($filters['term'])) {
            $whereConditions[] = "(t.table_number LIKE ?)";
            $searchTerm = '%' . $filters['term'] . '%';
            $bindings[] = $searchTerm;
        }

        // Filtro por estado
        if (!empty($filters['state'])) {
            $whereConditions[] = "t.table_status = ?";
            $bindings[] = $filters['state'];
        }

        // Si hay condiciones, las unimos con AND y las añadimos a la query
        $whereConditions[] = "t.table_status != 'DELETED'";

        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }


        // --- Lógica para ordenamiento ---
        $query .= " ORDER BY t.table_number ASC";


        try {
            $stmt = $this->conn->prepare($query);
            // Usamos bindValue para especificar tipos, especialmente para LIMIT/OFFSET
            foreach ($bindings as $key => $value) {
                // Los parámetros de LIMIT/OFFSET deben ser enteros
               
                    $stmt->bindValue($key + 1, $value, \PDO::PARAM_STR);
                
            }
            $stmt->execute();
            $tables = $stmt->fetchAll(\PDO::FETCH_ASSOC);



            return $tables ?: [];
        } catch (\PDOException $e) {

            throw new \Exception("Error al consultar la base de datos.");
        }

       // return $this->getAll(1, 1000);
    }

    // Buscar por ID
    public function find($id)
    {
        return $this->getById($id);
    }

    // Crear nueva mesa
    public function createTable($data)
    {
          // El método create de BaseModel ya devuelve el lastInsertId
        $id = $this->create($data);
        if ($id) {
            return $this->getById($id);
        }
        return false;
    }

    // Actualizar mesa
    public function updateTable($id, $data)
    {
        if ($this->update($id, $data)) {
            return $this->getById($id);
        }
        return false;
    }


   

    // Eliminar mesa
    public function deleteTable($id)
    {
        return $this->delete($id);
    }

    // Buscar por qr_token
    public function findByToken($qr_token)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE qr_token = :qr_token LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':qr_token', $qr_token);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // --- MÉTODO CORREGIDO ---

    /**
     * Valida si un token de QR específico corresponde a una mesa específica.
     *
     * @param string $qrToken El token QR a validar.
     * @param int $idTable El ID de la mesa con la que se debe comparar el token.
     * @return array|false Devuelve los datos de la mesa si la validación es exitosa, de lo contrario devuelve false.
     */
    public function validateQRTokenTable(string $qrToken, int $idTable)
    {
        $sql = "SELECT id_table, table_number, token_expiration 
                FROM {$this->table_name} 
                WHERE {$this->primary_key} = :id_table AND qr_token = :qr_token";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_table', $idTable, PDO::PARAM_INT);
            $stmt->bindParam(':qr_token', $qrToken, PDO::PARAM_STR);
            $stmt->execute();

            $table = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$table) {
                return false;
            }

            // Verificación de expiración
            if (isset($table['token_expiration']) && $table['token_expiration'] !== null) {
                $expirationTime = strtotime($table['token_expiration']);
                if (time() > $expirationTime) {
                    return false; 
                }
            }
            
            return $table;

        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Obtiene estadísticas de las mesas (ocupadas vs total)
     */
    public function getTableStats()
    {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN table_status = 'OCCUPIED' THEN 1 ELSE 0 END) as occupied
                  FROM {$this->table_name}
                  WHERE table_status != 'INACTIVE'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return [
            'total' => (int)$result['total'],
            'occupied' => (int)$result['occupied']
        ];
    }
}
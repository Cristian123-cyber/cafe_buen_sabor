<?php

namespace App\Models;

use PDO;

/**
 * Modelo para la tabla table_sessions
 */
class TableSession extends BaseModel
{

    public function __construct()
    {
        parent::__construct(); // Llama al constructor de BaseModel
        $this->table_name = 'table_sessions';
        $this->primary_key = 'id_session';
    }

    public function all()
    {
        return $this->getAll(1, 1000);
    }

    public function find($id)
    {
        return $this->getById($id);
    }

    public function updateSession($id, $data)
    {
        if ($this->update($id, $data)) {
            return $this->getById($id);
        }
        return false;
    }

    public function deleteSession($id)
    {
        return $this->delete($id);
    }

    public function findByTable($table_id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE tables_id_table = :table_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':table_id', $table_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * Busca una sesión de mesa activa para un ID de mesa específico.
     *
     * @param int $tableId El ID de la mesa.
     * @return array|false Devuelve los datos de la sesión si se encuentra una activa, de lo contrario false.
     */
    public function findActiveByTableId(int $tableId)
    {
        $sql = "SELECT id_session, tables_id_table AS id_table, session_status 
                FROM {$this->table_name} 
                WHERE tables_id_table = :id_table AND session_status = 'ACTIVE' 
                LIMIT 1";

        try {
            //Conexion heredada de BaseModell
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_table', $tableId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Crea una nueva sesión para una mesa.
     * Este método es específico y más seguro que usar el create() genérico,
     * ya que fuerza el estado 'ACTIVE' y los timestamps desde el servidor.
     *
     * @param int $tableId El ID de la mesa para la cual se crea la sesión.
     * @return int|false Devuelve el ID de la nueva sesión creada, o false si falla.
     */
    public function createSession(int $tableId)
    {
        $sql = "INSERT INTO {$this->table_name} (tables_id_table, session_status, stared_at, ended_at) 
                VALUES (:id_table, 'ACTIVE', NOW(), NULL)";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id_table', $tableId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Usamos la conexión para obtener el último ID insertado,
                // tal como lo hace el método create() de en el BaseModel.
                return (int)$this->conn->lastInsertId();
            }

            return false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    

    public function validateSession(int $id)
    {
        $sql = "SELECT id_session, tables_id_table AS table_id FROM table_sessions WHERE id_session = :id AND session_status = 'ACTIVE'";
       try {
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Verificar si se encontró una sesión activa
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && !empty($result)) {
            return $result; // La sesión es válida y está activa
        } else {
            return false; // No existe o está inactiva
        }

    } catch (\PDOException $e) {
        // Log opcional: error_log($e->getMessage());
        return false;
    }
    }
}

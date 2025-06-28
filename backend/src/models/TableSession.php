<?php

namespace App\Models;

/**
 * Modelo para la tabla table_sessions
 */
class TableSession extends BaseModel
{
    protected $table_name = 'table_sessions';
    protected $primary_key = 'id_session';

    public function all()
    {
        return $this->getAll(1, 1000);
    }

    public function find($id)
    {
        return $this->getById($id);
    }

    public function createSession($data)
    {
        $id = $this->create($data);
        if ($id) {
            return $this->getById($id);
        }
        return false;
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
}
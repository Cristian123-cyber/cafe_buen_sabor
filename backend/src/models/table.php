<?php

namespace App\Models;

class Table extends BaseModel
{
    protected $table_name = 'tables';
    protected $primary_key = 'id_table';

    // Obtener todas las mesas
    public function all()
    {
        return $this->getAll(1, 1000);
    }

    // Buscar por ID
    public function find($id)
    {
        return $this->getById($id);
    }

    // Crear nueva mesa
    public function createTable($data)
    {
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
}
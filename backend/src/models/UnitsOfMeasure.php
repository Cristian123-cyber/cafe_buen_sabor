<?php

namespace App\Models;

use PDO;
use Exception;

class UnitsOfMeasure extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'units_of_measure';
        $this->primary_key = 'id_unit';
    }

    /**
     * Obtiene todas las unidades de medida
     */
    public function getAll()
    {
        $query = "SELECT * FROM units_of_measure ORDER BY unit_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class UnitOfMeasure extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'units_of_measure';
        $this->primary_key = 'id_unit';
    }

    /**
     * Obtiene todas las unidades con información de ingredientes
     */
    public function getAllWithIngredientCount() {
        $query = "SELECT uom.*, COUNT(i.id_ingredient) as cantidad_ingredientes
                 FROM units_of_measure uom
                 LEFT JOIN ingredients i ON uom.id_unit = i.units_of_measure_id_unit
                 GROUP BY uom.id_unit
                 ORDER BY uom.unit_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene ingredientes que usan una unidad específica
     */
    public function getIngredientsByUnit($unit_id) {
        $query = "SELECT i.*, ist.name_status as estado_nombre
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 WHERE i.units_of_measure_id_unit = :unit_id
                 ORDER BY i.ingredient_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':unit_id', $unit_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
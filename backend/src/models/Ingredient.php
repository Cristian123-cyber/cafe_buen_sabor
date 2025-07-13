<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Ingredient extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ingredients';
        $this->primary_key = 'id_ingredient';
    }

    /**
     * Obtiene ingredientes con información completa (estado y unidad de medida)
     */
    public function getAllWithDetails($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        ist.desc_status as estado_descripcion,
                        uom.unit_name as unidad_nombre,
                        uom.unit_abbreviation as unidad_abreviacion
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 ORDER BY i.ingredient_name
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un ingrediente por ID con información completa
     */
    public function getByIdWithDetails($id) {
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        ist.desc_status as estado_descripcion,
                        uom.unit_name as unidad_nombre,
                        uom.unit_abbreviation as unidad_abreviacion
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 WHERE i.id_ingredient = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca ingredientes por nombre
     */
    public function searchByName($termino) {
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        uom.unit_name as unidad_nombre
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 WHERE i.ingredient_name LIKE :termino
                 ORDER BY i.ingredient_name";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%$termino%";
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene ingredientes por estado
     */
    public function getByStatus($status_id) {
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        uom.unit_name as unidad_nombre
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 WHERE i.ingredient_statuses_id_status = :status_id
                 ORDER BY i.ingredient_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status_id', $status_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene ingredientes con stock bajo
     */
    public function getLowStock() {
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        uom.unit_name as unidad_nombre
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 WHERE i.ingredient_stock <= i.low_stock_level 
                 AND i.ingredient_stock > i.critical_stock_level
                 ORDER BY i.ingredient_stock ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene ingredientes con stock crítico
     */
    public function getCriticalStock() {
        $query = "SELECT i.*, 
                        ist.name_status as estado_nombre,
                        uom.unit_name as unidad_nombre
                 FROM ingredients i
                 LEFT JOIN ingredient_statuses ist ON i.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                 WHERE i.ingredient_stock <= i.critical_stock_level
                 ORDER BY i.ingredient_stock ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el stock de un ingrediente
     */
    public function updateStock($id, $cantidad, $tipo_movimiento) {
        $this->conn->beginTransaction();
        
        try {
            // Obtener stock actual
            $query = "SELECT ingredient_stock FROM ingredients WHERE id_ingredient = :id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $ingredient = $stmt->fetch();
            
            if (!$ingredient) {
                throw new Exception("Ingrediente no encontrado");
            }

            $stock_anterior = $ingredient['ingredient_stock'] ?? 0;
            $stock_nuevo = $tipo_movimiento === 'entrada' ? 
                          $stock_anterior + $cantidad : 
                          $stock_anterior - $cantidad;

            if ($stock_nuevo < 0) {
                throw new Exception("Stock insuficiente");
            }

            // Actualizar stock
            $query = "UPDATE ingredients 
                     SET ingredient_stock = :stock_nuevo
                     WHERE id_ingredient = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':stock_nuevo', $stock_nuevo);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Registrar movimiento de stock
            $this->registrarMovimientoStock($id, $cantidad, $tipo_movimiento);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    /**
     * Registra un movimiento de stock de ingrediente
     */
    private function registrarMovimientoStock($ingredient_id, $cantidad, $tipo_movimiento) {
        $movement_type = $tipo_movimiento === 'entrada' ? 'IN' : 'OUT';
        
        $query = "INSERT INTO stock_movements 
                 (movement_type, quantity, movement_date, movement_notes, product_type, id_product) 
                 VALUES (:movement_type, :quantity, NOW(), :notes, 'INGREDIENTE', :ingredient_id)";
        
        $stmt = $this->conn->prepare($query);
        $notes = "Movimiento de stock de ingrediente: " . ($tipo_movimiento === 'entrada' ? 'Entrada' : 'Salida') . " de $cantidad unidades";
        $stmt->bindParam(':movement_type', $movement_type);
        $stmt->bindParam(':quantity', $cantidad);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':ingredient_id', $ingredient_id);
        $stmt->execute();
    }

    /**
     * Obtiene el historial de movimientos de stock de un ingrediente
     */
    public function getHistorialMovimientosStock($ingredient_id) {
        $query = "SELECT sm.*, i.ingredient_name
                 FROM stock_movements sm
                 LEFT JOIN ingredients i ON sm.id_product = i.id_ingredient
                 WHERE sm.id_product = :ingredient_id AND sm.product_type = 'INGREDIENTE'
                 ORDER BY sm.movement_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingredient_id', $ingredient_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos que usan un ingrediente específico
     */
    public function getProductosQueUsanIngrediente($ingredient_id) {
        $query = "SELECT p.*, phi.quantity as cantidad_usada,
                        pt.type_name as tipo_producto
                 FROM products p
                 INNER JOIN products_has_ingredients phi ON p.id_product = phi.products_id_product
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 WHERE phi.ingredients_id_ingredient = :ingredient_id
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ingredient_id', $ingredient_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza automáticamente el estado del ingrediente basado en el stock
     */
    public function actualizarEstadoAutomatico($id) {
        $query = "UPDATE ingredients 
                 SET ingredient_statuses_id_status = 
                     CASE 
                         WHEN ingredient_stock <= critical_stock_level THEN 4 -- Agotado
                         WHEN ingredient_stock <= low_stock_level THEN 2 -- Bajo
                         ELSE 1 -- Óptimo
                     END
                 WHERE id_ingredient = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Obtiene estadísticas de ingredientes
     */
    public function getEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total_ingredientes,
                    SUM(CASE WHEN ingredient_stock <= critical_stock_level THEN 1 ELSE 0 END) as stock_critico,
                    SUM(CASE WHEN ingredient_stock <= low_stock_level AND ingredient_stock > critical_stock_level THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(CASE WHEN ingredient_stock > low_stock_level THEN 1 ELSE 0 END) as stock_optimo
                 FROM ingredients";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 
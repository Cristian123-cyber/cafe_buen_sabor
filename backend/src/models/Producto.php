<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Producto extends BaseModel
{
    public function __construct()
    {
        parent::__construct(); // Llama al constructor de BaseModel
        $this->table_name = 'products';
        $this->primary_key = 'id_product';
    }

    /**
     * Obtiene productos con información de tipo de producto y estado
     */
    public function getProductosConCategoria($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, pt.type_name as categoria_nombre, 
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 ORDER BY p.created_date DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos por tipo de producto
     */
    public function getProductosPorCategoria($tipo_producto_id) {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_types_id_type = :tipo_producto_id 
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo_producto_id', $tipo_producto_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos con sus ingredientes
     */
    public function getProductosConIngredientes($producto_id = null) {
        if ($producto_id) {
            $query = "SELECT p.*, pt.type_name as categoria_nombre,
                            ist.name_status as estado_stock,
                            i.ingredient_name, i.ingredient_stock,
                            phi.quantity as cantidad_ingrediente,
                            uom.unit_name, uom.unit_abbreviation
                     FROM products p
                     LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                     LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                     LEFT JOIN products_has_ingredients phi ON p.id_product = phi.products_id_product
                     LEFT JOIN ingredients i ON phi.ingredients_id_ingredient = i.id_ingredient
                     LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit
                     WHERE p.id_product = :producto_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':producto_id', $producto_id);
        } else {
            $query = "SELECT p.*, pt.type_name as categoria_nombre,
                            ist.name_status as estado_stock,
                            GROUP_CONCAT(i.ingredient_name SEPARATOR ', ') as ingredientes
                     FROM products p
                     LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                     LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                     LEFT JOIN products_has_ingredients phi ON p.id_product = phi.products_id_product
                     LEFT JOIN ingredients i ON phi.ingredients_id_ingredient = i.id_ingredient
                     GROUP BY p.id_product
                     ORDER BY p.product_name";
            
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el stock de un producto (para productos no preparados)
     */
    public function actualizarStock($id, $cantidad, $tipo_movimiento) {
        $this->conn->beginTransaction();
        
        try {
            // Obtener stock actual
            $query = "SELECT product_stock FROM products WHERE id_product = :id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $producto = $stmt->fetch();
            
            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }

            $stock_anterior = $producto['product_stock'] ?? 0;
            $stock_nuevo = $tipo_movimiento === 'entrada' ? 
                          $stock_anterior + $cantidad : 
                          $stock_anterior - $cantidad;

            if ($stock_nuevo < 0) {
                throw new Exception("Stock insuficiente");
            }

            // Actualizar stock
            $query = "UPDATE products 
                     SET product_stock = :stock_nuevo,
                         last_updated_date = CURRENT_DATE
                     WHERE id_product = :id";
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
     * Registra un movimiento de stock
     */
    private function registrarMovimientoStock($producto_id, $cantidad, $tipo_movimiento) {
        $movement_type = $tipo_movimiento === 'entrada' ? 'IN' : 'OUT';
        
        $query = "INSERT INTO stock_movements 
                 (movement_type, quantity, movement_date, movement_notes, id_product) 
                 VALUES (:movement_type, :quantity, NOW(), :notes, :producto_id)";
        
        $stmt = $this->conn->prepare($query);
        $notes = "Movimiento de stock: " . ($tipo_movimiento === 'entrada' ? 'Entrada' : 'Salida') . " de $cantidad unidades";
        $stmt->bindParam(':movement_type', $movement_type);
        $stmt->bindParam(':quantity', $cantidad);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();
    }

    /**
     * Busca productos por nombre, descripción o código
     */
    public function buscarProductos($termino) {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_name LIKE :termino 
                 OR p.product_desc LIKE :termino
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%$termino%";
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos por estado de stock
     */
    public function getProductosPorEstadoStock($estado_id) {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.ingredient_statuses_id_status = :estado_id
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estado_id', $estado_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos con stock bajo
     */
    public function getProductosStockBajo() {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_stock <= p.low_stock_level 
                 AND p.product_stock > p.critical_stock_level
                 ORDER BY p.product_stock ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos con stock crítico
     */
    public function getProductosStockCritico() {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_stock <= p.critical_stock_level
                 ORDER BY p.product_stock ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el historial de movimientos de stock de un producto
     */
    public function getHistorialMovimientosStock($producto_id) {
        $query = "SELECT sm.*, p.product_name
                 FROM stock_movements sm
                 LEFT JOIN products p ON sm.id_product = p.id_product
                 WHERE sm.id_product = :producto_id
                 ORDER BY sm.movement_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 
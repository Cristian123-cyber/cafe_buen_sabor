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
        $this->table_name = 'productos';
    }

    public function getProductosConCategoria($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                 FROM productos p
                 LEFT JOIN categorias_productos c ON p.categoria_id = c.id
                 ORDER BY p.fecha_creacion DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    public function getProductosPorCategoria($categoria_id) {
        $query = "SELECT * FROM productos 
                 WHERE categoria_id = :categoria_id 
                 AND disponible_venta = TRUE
                 ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':categoria_id', $categoria_id);
        $stmt->execute();

        return $stmt;
    }

    public function actualizarStock($id, $cantidad, $tipo_movimiento) {
        $this->conn->beginTransaction();
        
        try {
            // Obtener stock actual
            $query = "SELECT stock_actual FROM productos WHERE id = :id FOR UPDATE";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $producto = $stmt->fetch();
            
            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }

            $stock_anterior = $producto['stock_actual'];
            $stock_nuevo = $tipo_movimiento === 'entrada' ? 
                          $stock_anterior + $cantidad : 
                          $stock_anterior - $cantidad;

            if ($stock_nuevo < 0) {
                throw new Exception("Stock insuficiente");
            }

            // Actualizar stock
            $query = "UPDATE productos 
                     SET stock_actual = :stock_nuevo 
                     WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':stock_nuevo', $stock_nuevo);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function buscarProductos($termino) {
        $query = "SELECT p.*, c.nombre as categoria_nombre 
                 FROM productos p
                 LEFT JOIN categorias_productos c ON p.categoria_id = c.id
                 WHERE p.nombre LIKE :termino 
                 OR p.descripcion LIKE :termino
                 OR p.codigo LIKE :termino
                 ORDER BY p.nombre";
        
        $stmt = $this->conn->prepare($query);
        $termino = "%$termino%";
        $stmt->bindParam(':termino', $termino);
        $stmt->execute();

        return $stmt;
    }
}
?> 
<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class ProductType extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'product_types';
        $this->primary_key = 'id_type';
    }

    /**
     * Obtiene todos los tipos con información de productos
     */
    public function getAllWithProductCount() {
        $query = "SELECT pt.*, COUNT(p.id_product) as cantidad_productos
                 FROM product_types pt
                 LEFT JOIN products p ON pt.id_type = p.product_types_id_type
                 GROUP BY pt.id_type
                 ORDER BY pt.type_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos de un tipo específico
     */
    public function getProductsByType($type_id) {
        $query = "SELECT p.*, pc.category_name as categoria,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN products_category pc ON p.product_category = pc.id_category
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_types_id_type = :type_id
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type_id', $type_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
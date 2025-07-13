<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class ProductCategory extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'products_category';
        $this->primary_key = 'id_category';
    }

    /**
     * Obtiene todas las categorías con información de productos
     */
    public function getAllWithProductCount() {
        $query = "SELECT pc.*, COUNT(p.id_product) as cantidad_productos
                 FROM products_category pc
                 LEFT JOIN products p ON pc.id_category = p.product_category
                 GROUP BY pc.id_category
                 ORDER BY pc.category_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos de una categoría específica
     */
    public function getProductsByCategory($category_id) {
        $query = "SELECT p.*, pt.type_name as tipo_producto,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_category = :category_id
                 ORDER BY p.product_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
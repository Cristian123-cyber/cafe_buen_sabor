<?php

namespace App\Models;

use PDO;
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
     * Obtiene todos los tipos de productos
     */
    public function getAll($page = 1, $limit = 10, $orderBy = null): array
    {
        $query = "SELECT * FROM product_types ORDER BY type_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
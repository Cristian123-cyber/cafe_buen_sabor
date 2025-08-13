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

    public function createProduct(array $data)
    {
        $sql = "INSERT INTO products (
                    product_name, 
                    product_price, 
                    product_cost, 
                    product_desc, 
                    product_types_id_type, 
                    product_category,
                    product_stock,
                    low_stock_level,
                    critical_stock_level,
                    ingredient_statuses_id_status,
                    created_date
                ) VALUES (
                    :product_name, 
                    :product_price, 
                    :product_cost, 
                    :product_desc, 
                    :product_types_id_type,
                    :product_category,
                    :product_stock,
                    :low_stock_level,
                    :critical_stock_level,
                    :ingredient_statuses_id_status,
                    NOW()
                )";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':product_name' => $data['product_name'],
                ':product_price' => $data['product_price'],
                ':product_cost' => $data['product_cost'],
                ':product_desc' => $data['product_desc'],
                ':product_types_id_type' => $data['product_types_id_type'],
                // Importante: El nombre de la columna en la BD es 'product_category'
                ':product_category' => $data['product_category'],
                ':product_stock' => $data['product_stock'] ?? null,
                ':low_stock_level' => $data['low_stock_level'] ?? null,
                ':critical_stock_level' => $data['critical_stock_level'] ?? null,
                ':ingredient_statuses_id_status' => $data['ingredient_statuses_id_status'] ?? null,
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('Error en ProductoModel::create: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un producto PREPARADO y asocia sus ingredientes en una transacción.
     * @param array $productData Datos del producto.
     * @param array $ingredientIds Array de IDs de ingredientes a asociar.
     * @return int|false El ID del nuevo producto o false si la transacción falla.
     */
    public function createWithIngredients(array $productData, array $ingredientIds)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar el producto en la tabla `products`
            $productId = $this->createProduct($productData);

            if (!$productId) {
                throw new Exception("Falló la inserción del producto principal.");
            }

            // 2. Insertar las asociaciones en la tabla intermedia `products_has_ingredients`
            // Nota: Se omite 'quantity' como solicitaste, la BD le asignará su valor por defecto (NULL).
            $sqlAssoc = "INSERT INTO products_has_ingredients (products_id_product, ingredients_id_ingredient) VALUES (:product_id, :ingredient_id)";
            $stmtAssoc = $this->conn->prepare($sqlAssoc);

            foreach ($ingredientIds as $ingredientId) {
                $stmtAssoc->execute([
                    ':product_id' => $productId,
                    ':ingredient_id' => $ingredientId
                ]);
            }

            $this->conn->commit();
            return $productId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error en ProductoModel::createWithIngredients: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un producto PREPARADO y sus ingredientes en una transacción.
     * Reemplaza completamente los ingredientes anteriores.
     * @param int $productId El ID del producto.
     * @param array $productData Datos del producto a actualizar.
     * @param array $ingredientIds El NUEVO array de IDs de ingredientes.
     * @return bool True si la operación fue exitosa.
     */
    public function updateWithIngredients(int $productId, array $productData, array $ingredientIds)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Actualizar los datos del producto en la tabla `products`
            if (!empty($productData)) {
                $this->update($productId, $productData);
            }

            // 2. Borrar TODAS las asociaciones de ingredientes existentes para este producto
            $sqlDelete = "DELETE FROM products_has_ingredients WHERE products_id_product = :product_id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([':product_id' => $productId]);

            // 3. Insertar las nuevas asociaciones (si las hay)
            if (!empty($ingredientIds)) {
                $sqlAssoc = "INSERT INTO products_has_ingredients (products_id_product, ingredients_id_ingredient) VALUES (:product_id, :ingredient_id)";
                $stmtAssoc = $this->conn->prepare($sqlAssoc);

                foreach ($ingredientIds as $ingredientId) {
                    $stmtAssoc->execute([
                        ':product_id' => $productId,
                        ':ingredient_id' => $ingredientId
                    ]);
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log('Error en ProductoModel::updateWithIngredients: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Método auxiliar para obtener el tipo de un producto.
     * Útil en el método update del controlador.
     * @param int $productId
     * @return int|false El ID del tipo de producto o false si no se encuentra.
     */
    public function getProductTypeId(int $productId)
    {
        $sql = "SELECT product_types_id_type FROM products WHERE id_product = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $productId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['product_types_id_type'] : false;
        } catch (PDOException $e) {
            error_log('Error en ProductoModel::getProductTypeId: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la URL de la imagen de un producto.
     * @param int $productId
     * @param string $imageUrl
     * @return bool
     */
    public function updateImageUrl(int $productId, string $imageUrl)
    {
        $sql = "UPDATE products SET product_image_url = :image_url, last_updated_date = NOW() WHERE id_product = :id_product";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':image_url' => $imageUrl,
                ':id_product' => $productId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('Error en ProductoModel::updateImageUrl: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene productos con información de tipo de producto y estado
     */
    public function getProductosConCategoria($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $query = "SELECT p.*, pt.type_name as categoria_nombre, 
                        ist.name_status as estado_stock,
                        pc.category_name
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN products_category pc ON p.product_category = pc.id_category
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
    public function getProductosPorCategoria($tipo_producto_id)
    {
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
     * Obtiene productos con sus ingredientes (ingredientes como array y category_name)
     */
    public function getProductosConIngredientes($producto_id = null)
    {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock,
                        pc.category_name as category_name,
                        i.id_ingredient, i.ingredient_name, i.ingredient_stock,
                        phi.quantity as cantidad_ingrediente,
                        uom.unit_name, uom.unit_abbreviation, uom.id_unit as units_of_measure_id_unit
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN products_category pc ON p.product_category = pc.id_category
                 LEFT JOIN products_has_ingredients phi ON p.id_product = phi.products_id_product
                 LEFT JOIN ingredients i ON phi.ingredients_id_ingredient = i.id_ingredient
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit";
        if ($producto_id) {
            $query .= " WHERE p.id_product = :producto_id";
        }
        $query .= " ORDER BY p.id_product";

        $stmt = $this->conn->prepare($query);
        if ($producto_id) {
            $stmt->bindParam(':producto_id', $producto_id);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productos = [];
        foreach ($rows as $row) {
            $id = $row['id_product'];
            $tipo = $row['product_types_id_type'];

            if (!isset($productos[$id])) {
                $base = [
                    'id_product' => $row['id_product'],
                    'product_name' => $row['product_name'],
                    'product_price' => $row['product_price'],
                    'product_cost' => $row['product_cost'],
                    'product_desc' => $row['product_desc'],
                    'product_image_url' => $row['product_image_url'],
                    'created_date' => $row['created_date'],
                    'last_updated_date' => $row['last_updated_date'],
                    'product_stock' => $row['product_stock'],
                    'low_stock_level' => $row['low_stock_level'],
                    'critical_stock_level' => $row['critical_stock_level'],
                    'categoria_nombre' => $row['categoria_nombre'],
                    'category_name' => $row['category_name'],
                    'estado_stock' => $row['estado_stock'],
                    'product_types_id_type' => $row['product_types_id_type'],
                    'product_category' => $row['product_category']
                ];

                // Solo agregar ingredients para productos preparados (tipo 1)
                if ($tipo == 1) {
                    $base['ingredients'] = [];
                }

                $productos[$id] = $base;
            }

            // Solo agregar ingredientes para productos preparados
            if ($tipo == 1 && $row['id_ingredient']) {
                $productos[$id]['ingredients'][] = [
                    'id' => $row['id_ingredient'],
                    'cantidad' => $row['cantidad_ingrediente'],
                    'ingredient_name' => $row['ingredient_name'],
                    'units_of_measure_id_unit' => $row['units_of_measure_id_unit'],
                    'unit_name' => $row['unit_name'],
                    'unit_abbreviation' => $row['unit_abbreviation']
                ];
            }
        }

        $result = array_values($productos);

        // Si se solicita un producto específico, devolver solo ese producto
        if ($producto_id) {
            return $result[0] ?? null;
        }

        return $result;
    }

    /**
     * Actualiza el stock de un producto (para productos no preparados)
     */
    public function actualizarStock($id, $cantidad, $tipo_movimiento)
    {
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
    private function registrarMovimientoStock($producto_id, $cantidad, $tipo_movimiento)
    {
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
    public function buscarProductos($termino)
    {
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
    public function getProductosPorEstadoStock($estado_id)
    {
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
    public function getProductosStockBajo()
    {
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
    public function getProductosStockCritico()
    {
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
    public function getHistorialMovimientosStock($producto_id)
    {
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

    /**
     * Obtiene productos disponibles (con stock > 0)
     */
    public function getProductosDisponibles()
    {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 WHERE p.product_stock > 0 OR p.product_stock IS NULL
                 ORDER BY p.product_name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene productos populares (más vendidos)
     */
    public function getProductosPopulares()
    {
        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock,
                        COUNT(ohp.orders_id_order) as veces_vendido
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN orders_has_products ohp ON p.id_product = ohp.products_id_product
                 GROUP BY p.id_product
                 ORDER BY veces_vendido DESC
                 LIMIT 10";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el historial de stock (alias para compatibilidad)
     */
    public function getHistorialStock($producto_id)
    {
        return $this->getHistorialMovimientosStock($producto_id);
    }

    /**
     * Obtiene todos los productos agrupados por tipo y estructura compatible con la documentación
     */
    public function getProductosAgrupados($filters = [])
    {


        // --- Lógica para filtros (WHERE) ---
        $whereConditions = [];
        $bindings = [];

        // Filtro por término de búsqueda (nombre o email)
        if (!empty($filters['term'])) {
            $whereConditions[] = "(p.product_name LIKE ? OR p.id_product LIKE ?)";
            $searchTerm = '%' . $filters['term'] . '%';
            $bindings[] = $searchTerm;
            $bindings[] = $searchTerm;
        }

        // Filtro por rol
        if (!empty($filters['category'])) {
            $whereConditions[] = "p.product_category = ?";
            $bindings[] = $filters['category'];
        }


        $query = "SELECT p.*, pt.type_name as categoria_nombre,
                        ist.name_status as estado_stock,
                        pc.category_name as category_name,
                        i.id_ingredient, i.ingredient_name, i.ingredient_stock,
                        phi.quantity as cantidad_ingrediente,
                        uom.unit_name, uom.unit_abbreviation, uom.id_unit as units_of_measure_id_unit
                 FROM products p
                 LEFT JOIN product_types pt ON p.product_types_id_type = pt.id_type
                 LEFT JOIN ingredient_statuses ist ON p.ingredient_statuses_id_status = ist.id_status
                 LEFT JOIN products_category pc ON p.product_category = pc.id_category
                 LEFT JOIN products_has_ingredients phi ON p.id_product = phi.products_id_product
                 LEFT JOIN ingredients i ON phi.ingredients_id_ingredient = i.id_ingredient
                 LEFT JOIN units_of_measure uom ON i.units_of_measure_id_unit = uom.id_unit";



        // Si hay condiciones, las unimos con AND y las añadimos a la query
        if (!empty($whereConditions)) {
            $query .= " WHERE " . implode(" AND ", $whereConditions);
        }



        $query .= " ORDER BY id_product DESC";



        $stmt = $this->conn->prepare($query);
        // Usamos bindValue para especificar tipos, especialmente para LIMIT/OFFSET
        foreach ($bindings as $key => $value) {
            // Los parámetros de LIMIT/OFFSET deben ser enteros

            $stmt->bindValue($key + 1, $value, \PDO::PARAM_STR);
        }
        $stmt->execute();


        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $preparados = [];
        $no_preparados = [];
        $productos_tmp = [];

        foreach ($rows as $row) {
            $id = $row['id_product'];
            $tipo = $row['product_types_id_type'];
            if (!isset($productos_tmp[$id])) {
                $base = [
                    'id_product' => $row['id_product'],
                    'product_name' => $row['product_name'],
                    'product_price' => $row['product_price'],
                    'product_cost' => $row['product_cost'],
                    'product_desc' => $row['product_desc'],
                    'product_image_url' => $row['product_image_url'],
                    'created_date' => $row['created_date'],
                    'last_updated_date' => $row['last_updated_date'],
                    'product_stock' => $row['product_stock'],
                    'low_stock_level' => $row['low_stock_level'],
                    'critical_stock_level' => $row['critical_stock_level'],
                    'categoria_nombre' => $row['categoria_nombre'],
                    'category_name' => $row['category_name'],
                    'estado_stock' => $row['estado_stock'],
                    'product_types_id_type' => $row['product_types_id_type'],
                    'product_category' => $row['product_category']
                ];
                if ($tipo == 1) {
                    $base['ingredients'] = [];
                }
                $productos_tmp[$id] = $base;
            }
            if ($tipo == 1 && $row['id_ingredient']) {
                $productos_tmp[$id]['ingredients'][] = [
                    'id' => $row['id_ingredient'],
                    'cantidad' => $row['cantidad_ingrediente'],
                    'ingredient_name' => $row['ingredient_name'],
                    'units_of_measure_id_unit' => $row['units_of_measure_id_unit'],
                    'unit_name' => $row['unit_name'],
                    'unit_abbreviation' => $row['unit_abbreviation']
                ];
            }
        }
        foreach ($productos_tmp as $prod) {
            if ($prod['product_types_id_type'] == 1) {
                $preparados[] = $prod;
            } else if ($prod['product_types_id_type'] == 2) {
                unset($prod['ingredients']);
                $no_preparados[] = $prod;
            }
        }
        return [
            [
                'productos_preparados' => $preparados,
                'productos_no_preparados' => $no_preparados
            ]
        ];
    }

    /**
     * Obtiene los productos más vendidos
     */
    public function getTopProducts($limit = 5, $period = 'monthly')
    {
        $dateCondition = '';
        $params = [];

        switch ($period) {
            case 'weekly':
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'monthly':
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
            case 'all_time':
                $dateCondition = '';
                break;
            default:
                $dateCondition = 'AND o.created_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        }

        $query = "SELECT 
                    p.id_product,
                    p.product_name as name,
                    SUM(ohp.quantity) as quantity
                  FROM products p
                  INNER JOIN orders_has_products ohp ON p.id_product = ohp.products_id_product
                  INNER JOIN orders o ON ohp.orders_id_order = o.id_order
                  WHERE o.order_statuses_id_status IN (1, 2, 3, 4) -- Incluir todos los estados de pedidos
                  $dateCondition
                  GROUP BY p.id_product, p.product_name
                  HAVING quantity > 0
                  ORDER BY quantity DESC
                  LIMIT ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}

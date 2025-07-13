<?php
namespace App\Models;

use PDO;
use Exception;

class Order extends BaseModel
{
    protected $table_name = 'orders';
    protected $primary_key = 'id_order';

    public function getAll($page = 1, $limit = 10, $orderBy = null)
    {
    $offset = ($page - 1) * $limit;
    $query = "SELECT * FROM orders";
    if ($orderBy) {
        $query .= " ORDER BY $orderBy";
    }
    $query .= " LIMIT $limit OFFSET $offset";
    $stmt = $this->conn->query($query);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id_order = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO orders (created_date, total_amount, waiter_id, order_statuses_id_status, table_sessions_id_session)
             VALUES (NOW(), ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['total_amount'],
            $data['waiter_id'],
            $data['order_statuses_id_status'],
            $data['table_sessions_id_session']
        ]);
        return $this->getById($this->conn->lastInsertId());
    }

    public function addProductToOrder($orderId, $productId, $quantity, $price)
    {
        $sql = "INSERT INTO orders_has_products (orders_id_order, products_id_product, quantity, price, created_at)
                VALUES (:order_id, :product_id, :quantity, :price, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $productId,
            ':quantity' => $quantity,
            ':price' => $price
        ]);
    }
    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET total_amount = ?, waiter_id = ?, order_statuses_id_status = ?, table_sessions_id_session = ? WHERE id_order = ?"
        );
        $stmt->execute([
            $data['total_amount'],
            $data['waiter_id'],
            $data['order_statuses_id_status'],
            $data['table_sessions_id_session'],
            $id
        ]);
        return $this->getById($id);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM orders WHERE id_order = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE orders SET order_statuses_id_status = ? WHERE id_order = ?");
        $stmt->execute([$status, $id]);
        return $this->getById($id);
    }

    // Obtener pedidos por estado
    public function getByStatus($status)
    {
        $stmt = $this->conn->prepare(
            "SELECT o.*, ts.tables_id_table
             FROM orders o
             JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
             WHERE o.order_statuses_id_status = ?"
        );
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener pedidos por sesión de mesa
    public function getBySession($sessionId)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM orders WHERE table_sessions_id_session = ?"
        );
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Asociar mesero a todos los pedidos de una sesión
    public function bindWaiter($sessionId, $waiterId)
    {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET waiter_id = ? WHERE table_sessions_id_session = ?"
        );
        $stmt->execute([$waiterId, $sessionId]);
        return ['updated' => $stmt->rowCount()];
    }
    
    // Unificar pedidos (ejemplo básico, debes adaptar según tu modelo de datos)
    public function unifyOrders($ordersToUnify)
    {
        // 1. Validar que todos los pedidos pertenezcan a la misma sesión (hazlo en el controlador si prefieres)
        // 2. Crear registro en orders_unified
        $stmt = $this->conn->prepare("INSERT INTO orders_unified (created_at) VALUES (NOW())");
        $stmt->execute();
        $unifiedId = $this->conn->lastInsertId();
    
        // 3. Asociar pedidos a la orden unificada
        $stmtAssoc = $this->conn->prepare(
            "INSERT INTO orders_has_unified_has_orders (orders_unified_id, orders_id_order) VALUES (?, ?)"
        );
        foreach ($ordersToUnify as $orderId) {
            $stmtAssoc->execute([$unifiedId, $orderId]);
        }
        return $unifiedId;
    }
    
    // Obtener pedidos de una orden unificada
    public function getByUnified($unifiedId)
    {
        $stmt = $this->conn->prepare(
            "SELECT o.* FROM orders o
             JOIN orders_has_unified_has_orders uho ON o.id_order = uho.orders_id_order
             WHERE uho.orders_unified_id = ?"
        );
        $stmt->execute([$unifiedId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los pedidos con detalles completos
     */
    public function getAllWithDetails()
    {
        $query = "SELECT o.*, os.status_name as order_status_name,
                         ts.tables_id_table, t.table_number,
                         e.employe_name as waiter_name
                  FROM orders o
                  LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN employees e ON o.waiter_id = e.id_employe
                  ORDER BY o.created_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un pedido por ID con detalles completos incluyendo productos
     */
    public function getByIdWithDetails($id)
    {
        // Obtener datos básicos del pedido
        $query = "SELECT o.*, os.status_name as order_status_name,
                         ts.tables_id_table, t.table_number,
                         e.employe_name as waiter_name
                  FROM orders o
                  LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN employees e ON o.waiter_id = e.id_employe
                  WHERE o.id_order = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return null;
        }

        // Obtener productos del pedido
        $productsQuery = "SELECT ohp.*, p.product_name, p.product_desc
                         FROM orders_has_products ohp
                         LEFT JOIN products p ON ohp.products_id_product = p.id_product
                         WHERE ohp.orders_id_order = ?";
        
        $stmt = $this->conn->prepare($productsQuery);
        $stmt->execute([$id]);
        $order['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $order;
    }

    /**
     * Obtener pedidos por estado con detalles
     */
    public function getByStatusWithDetails($status)
    {
        $query = "SELECT o.*, os.status_name as order_status_name,
                         ts.tables_id_table, t.table_number,
                         e.employe_name as waiter_name
                  FROM orders o
                  LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN employees e ON o.waiter_id = e.id_employe
                  WHERE os.status_name = ?
                  ORDER BY o.created_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar productos a cada pedido
        foreach ($orders as &$order) {
            $productsQuery = "SELECT ohp.*, p.product_name, p.product_desc
                             FROM orders_has_products ohp
                             LEFT JOIN products p ON ohp.products_id_product = p.id_product
                             WHERE ohp.orders_id_order = ?";
            
            $stmt = $this->conn->prepare($productsQuery);
            $stmt->execute([$order['id_order']]);
            $order['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $orders;
    }

    /**
     * Obtener pedidos por sesión con detalles
     */
    public function getBySessionWithDetails($sessionId)
    {
        $query = "SELECT o.*, os.status_name as order_status_name,
                         ts.tables_id_table, t.table_number,
                         e.employe_name as waiter_name
                  FROM orders o
                  LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN employees e ON o.waiter_id = e.id_employe
                  WHERE o.table_sessions_id_session = ?
                  ORDER BY o.created_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sessionId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar productos a cada pedido
        foreach ($orders as &$order) {
            $productsQuery = "SELECT ohp.*, p.product_name, p.product_desc
                             FROM orders_has_products ohp
                             LEFT JOIN products p ON ohp.products_id_product = p.id_product
                             WHERE ohp.orders_id_order = ?";
            
            $stmt = $this->conn->prepare($productsQuery);
            $stmt->execute([$order['id_order']]);
            $order['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $orders;
    }

    /**
     * Obtener pedidos de una orden unificada con detalles
     */
    public function getByUnifiedWithDetails($unifiedId)
    {
        $query = "SELECT o.*, os.status_name as order_status_name,
                         ts.tables_id_table, t.table_number,
                         e.employe_name as waiter_name
                  FROM orders o
                  JOIN orders_has_unified_has_orders uho ON o.id_order = uho.orders_id_order
                  LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN employees e ON o.waiter_id = e.id_employe
                  WHERE uho.orders_unified_id = ?
                  ORDER BY o.created_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$unifiedId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Agregar productos a cada pedido
        foreach ($orders as &$order) {
            $productsQuery = "SELECT ohp.*, p.product_name, p.product_desc
                             FROM orders_has_products ohp
                             LEFT JOIN products p ON ohp.products_id_product = p.id_product
                             WHERE ohp.orders_id_order = ?";
            
            $stmt = $this->conn->prepare($productsQuery);
            $stmt->execute([$order['id_order']]);
            $order['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $orders;
    }
}
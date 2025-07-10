<?php

namespace App\Models;

use Exception;

class Sale extends BaseModel
{
    protected $table_name = 'sales';
    protected $primary_key = 'id_sale';
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'sales'; // Corregido: La tabla es 'employees'
        $this->primary_key = 'id_sale'; // Corregido: La PK es 'id_employe'
    }

    public function all()
    {
        return $this->getAll(1, 1000);
    }

    public function find($id)
    {
        return $this->getById($id);
    }

    public function createSale($data)
    {
        try {
            $this->conn->beginTransaction();
            
            // Validar que las órdenes existan
            $orderIds = $data['orders'];
            $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
            $orderQuery = "SELECT id_order, total_amount FROM orders WHERE id_order IN ($placeholders)";
            $stmt = $this->conn->prepare($orderQuery);
            $stmt->execute($orderIds);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (count($orders) !== count($orderIds)) {
                throw new Exception('Una o más órdenes no existen');
            }
            
            // Calcular total automáticamente
            $totalAmount = array_sum(array_column($orders, 'total_amount'));
            
            // Crear la venta
            $saleData = [
                'cashier_id' => $data['cashier_id'],
                'payment_method' => $data['payment_method'],
                'total_amount' => $totalAmount,
                'sale_status' => 'COMPLETED',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $saleId = $this->create($saleData);
            
            // Asociar órdenes con la venta en sales_has_orders
            $associationQuery = "INSERT INTO sales_has_orders (sale_id, order_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($associationQuery);
            
            foreach ($orderIds as $orderId) {
                $stmt->execute([$saleId, $orderId]);
            }
            
            $this->conn->commit();
            
            // Retornar la venta creada con sus datos
            return $this->findWithFullDetails($saleId);
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function updateSale($id, $data)
    {
        // Por motivos de seguridad, solo se permite cancelar
        if (isset($data['sale_status']) && $data['sale_status'] === 'CANCELED') {
            return $this->cancelSale($id);
        }
        throw new Exception('No se permite editar ventas por motivos de seguridad');
    }

    public function deleteSale($id)
    {
        throw new Exception('No se permite eliminar ventas por motivos de seguridad');
    }

    /**
     * Obtener todas las ventas con datos básicos de pedidos asociados (sin productos)
     * Para el endpoint GET /api/sales/
     */
    public function getAllWithOrders()
    {
        $query = "SELECT 
                    s.id_sale,
                    s.cashier_id,
                    s.payment_method,
                    s.total_amount,
                    s.sale_status,
                    s.created_at,
                    e.name as cashier_name,
                    e.email as cashier_email
                  FROM {$this->table_name} s
                  LEFT JOIN employees e ON s.cashier_id = e.id_employee
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->query($query);
        $sales = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Para cada venta, obtener sus órdenes básicas
        foreach ($sales as &$sale) {
            $ordersQuery = "SELECT 
                              o.id_order,
                              o.total_amount
                            FROM sales_has_orders sho
                            JOIN orders o ON sho.order_id = o.id_order
                            WHERE sho.sale_id = ?";
            
            $stmt = $this->conn->prepare($ordersQuery);
            $stmt->execute([$sale['id_sale']]);
            $sale['orders'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        return $sales;
    }

    /**
     * Obtener venta específica con todos los detalles incluyendo productos
     * Para el endpoint GET /api/sales/{id}
     */
    public function findWithFullDetails($id)
    {
        // Obtener datos básicos de la venta
        $saleQuery = "SELECT 
                        s.*,
                        e.name as cashier_name,
                        e.email as cashier_email
                      FROM {$this->table_name} s
                      LEFT JOIN employees e ON s.cashier_id = e.id_employee
                      WHERE s.{$this->primary_key} = ?";
        
        $stmt = $this->conn->prepare($saleQuery);
        $stmt->execute([$id]);
        $sale = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$sale) {
            return null;
        }
        
        // Obtener pedidos asociados con sus productos
        $ordersQuery = "SELECT 
                          o.id_order,
                          o.created_date,
                          o.total_amount,
                          o.order_statuses_id_status,
                          o.table_sessions_id_session
                        FROM sales_has_orders sho
                        JOIN orders o ON sho.order_id = o.id_order
                        WHERE sho.sale_id = ?
                        ORDER BY o.id_order";
        
        $stmt = $this->conn->prepare($ordersQuery);
        $stmt->execute([$id]);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Para cada orden, obtener sus productos
        foreach ($orders as &$order) {
            $productsQuery = "SELECT 
                                ohp.products_id_product,
                                ohp.quantity,
                                ohp.price as product_price,
                                p.name as product_name,
                                p.price as original_price
                              FROM orders_has_products ohp
                              JOIN products p ON ohp.products_id_product = p.id_product
                              WHERE ohp.orders_id_order = ?";
            
            $stmt = $this->conn->prepare($productsQuery);
            $stmt->execute([$order['id_order']]);
            $order['products'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        $sale['orders'] = $orders;
        
        return $sale;
    }

    /**
     * Cancelar una venta
     * Para el endpoint PUT /api/sales/{id}
     */
    public function cancelSale($id)
    {
        // Verificar que la venta existe
        $sale = $this->getById($id);
        
        if (!$sale) {
            return null;
        }
        
        if ($sale['sale_status'] === 'CANCELED') {
            throw new Exception('La venta ya está cancelada');
        }
        
        // Cancelar la venta
        $updateQuery = "UPDATE {$this->table_name} SET sale_status = 'CANCELED' WHERE {$this->primary_key} = ?";
        $stmt = $this->conn->prepare($updateQuery);
        $stmt->execute([$id]);
        
        return $this->getById($id);
    }

    /**
     * Obtener ventas por estado
     * Para el endpoint GET /api/sales/status/{status}
     */
    public function getByStatus($status)
    {
        $query = "SELECT 
                    s.id_sale,
                    s.cashier_id,
                    s.payment_method,
                    s.total_amount,
                    s.sale_status,
                    s.created_at,
                    e.name as cashier_name,
                    e.email as cashier_email
                  FROM {$this->table_name} s
                  LEFT JOIN employees e ON s.cashier_id = e.id_employee
                  WHERE s.sale_status = ?
                  ORDER BY s.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$status]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //visualización gráfica del recaudo mensual
    public function getMonthlyRevenue()
    {
    $query = "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') AS month,
                SUM(total_amount) AS total
              FROM {$this->table_name}
              WHERE sale_status = 'COMPLETED'
              GROUP BY month
              ORDER BY month ASC";
    $stmt = $this->conn->query($query);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Ingresos por fecha
    public function getRevenueByDate($from = null, $to = null)
    {
    $query = "SELECT DATE(created_at) as date, SUM(total_amount) as total
              FROM {$this->table_name}
              WHERE sale_status = 'COMPLETED'";
    $params = [];
    if ($from) {
        $query .= " AND created_at >= ?";
        $params[] = $from;
    }
    if ($to) {
        $query .= " AND created_at <= ?";
        $params[] = $to;
    }
    $query .= " GROUP BY date ORDER BY date ASC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Ingresos por empleado
    public function getRevenueByEmployee()
    {
        $query = "SELECT e.id_employe, e.employe_name as employee_name, SUM(s.total_amount) as total
                  FROM {$this->table_name} s
                  LEFT JOIN employees e ON s.cashier_id = e.id_employe
                  WHERE s.sale_status = 'COMPLETED'
                  GROUP BY e.id_employe, e.employe_name
                  ORDER BY total DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    //ingreso por mesa
    public function revenueByTable()
    {
        $query = "SELECT t.id_table, t.table_number, SUM(s.total_amount) as total
                  FROM {$this->table_name} s
                  JOIN sales_has_orders sho ON s.id_sale = sho.sales_id_sale
                  JOIN orders o ON sho.orders_id_order = o.id_order
                  JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  JOIN tables t ON ts.tables_id_table = t.id_table
                  WHERE s.sale_status = 'COMPLETED'
                  GROUP BY t.id_table, t.table_number
                  ORDER BY total DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //Cantidad de mesas atendidas por cada mesero
    public function getTablesServedByWaiter()
    {
        $query = "SELECT 
                    e.id_employe,
                    e.employe_name,
                    er.rol_name,
                    COUNT(DISTINCT t.id_table) as mesas_atendidas,
                    COUNT(DISTINCT o.id_order) as total_ordenes,
                    COALESCE(SUM(o.total_amount), 0) as total_ventas_generadas
                  FROM employees e
                  INNER JOIN employees_rol er ON e.employees_rol_id_rol = er.id_rol
                  LEFT JOIN orders o ON e.id_employe = o.waiter_id
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  WHERE e.employees_statuses_id_status = 1
                  GROUP BY e.id_employe, e.employe_name, er.rol_name
                  ORDER BY mesas_atendidas DESC";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    //para informe por empleado 
    public function getEmployeeSalesSummary($employeeId)
    {
        $query = "SELECT 
                    e.id_employe,
                    e.employe_name,
                    er.rol_name,
                    COUNT(DISTINCT t.id_table) as mesas_atendidas,
                    COUNT(DISTINCT s.id_sale) as total_ventas,
                    COALESCE(SUM(s.total_amount), 0) as total_vendido,
                    COUNT(DISTINCT o.id_order) as total_ordenes
                  FROM employees e
                  INNER JOIN employees_rol er ON e.employees_rol_id_rol = er.id_rol
                  LEFT JOIN orders o ON e.id_employe = o.waiter_id
                  LEFT JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
                  LEFT JOIN tables t ON ts.tables_id_table = t.id_table
                  LEFT JOIN sales_has_orders sho ON o.id_order = sho.orders_id_order
                  LEFT JOIN sales s ON sho.sales_id_sale = s.id_sale AND s.sale_status = 'COMPLETED'
                  WHERE e.id_employe = ?
                  GROUP BY e.id_employe, e.employe_name, er.rol_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employeeId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

}
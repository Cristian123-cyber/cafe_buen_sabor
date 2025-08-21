<?php

namespace App\Models;

use PDO;
use Exception;

class Reports extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 1. REPORTE: BALANCE GENERAL DE VENTAS
     * Obtiene los datos para el reporte de balance de ventas en un rango de fechas.
     * @param string $startDate 'Y-m-d'
     * @param string $endDate 'Y-m-d'
     * @return array
     */
    public function getSalesBalance(string $startDate, string $endDate): array
    {
        // Añadimos la hora para cubrir el día completo
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // --- Consulta para el resumen (summary) ---
        $summaryQuery = "
            SELECT
                SUM(total_amount) as total_revenue,
                COUNT(id_sale) as total_sales_count,
                AVG(total_amount) as average_sale_value,
                SUM(CASE WHEN payment_method = 'CONTADO' THEN total_amount ELSE 0 END) as contado_revenue,
                SUM(CASE WHEN payment_method = 'TRANSFERENCIA' THEN total_amount ELSE 0 END) as transferencia_revenue
            FROM sales
            WHERE created_at BETWEEN ? AND ?
              AND sale_status = 'COMPLETED'
        ";

        $stmtSummary = $this->conn->prepare($summaryQuery);
        $stmtSummary->execute([$startDateTime, $endDateTime]);
        $summaryData = $stmtSummary->fetch(PDO::FETCH_ASSOC);

        // --- Consulta para la lista de ventas (sales) ---
        $salesQuery = "
            SELECT
                s.id_sale as sale_id,
                s.created_at as sale_date,
                s.total_amount,
                s.payment_method,
                e.employe_name as cashier_name
            FROM sales s
            JOIN employees e ON s.cashier_id = e.id_employe
            WHERE s.created_at BETWEEN ? AND ?
              AND s.sale_status = 'COMPLETED'
            ORDER BY s.created_at DESC
        ";
        $stmtSales = $this->conn->prepare($salesQuery);
        $stmtSales->execute([$startDateTime, $endDateTime]);
        $salesList = $stmtSales->fetchAll(PDO::FETCH_ASSOC);

        // Formatear la respuesta como el mock esperado
        return [
            'summary' => [
                'total_revenue' => (float)($summaryData['total_revenue'] ?? 0),
                'total_sales_count' => (int)($summaryData['total_sales_count'] ?? 0),
                'average_sale_value' => (float)($summaryData['average_sale_value'] ?? 0),
                'payment_methods' => [
                    'CONTADO' => (float)($summaryData['contado_revenue'] ?? 0),
                    'TRANSFERENCIA' => (float)($summaryData['transferencia_revenue'] ?? 0)
                ]
            ],
            'sales' => $salesList
        ];
    }

    /**
     * 2. REPORTE: LISTADO DE FACTURAS
     * Obtiene una lista de facturas con sus pedidos asociados.
     * @param string $startDate 'Y-m-d'
     * @param string $endDate 'Y-m-d'
     * @return array
     */
    public function getInvoicesList(string $startDate, string $endDate): array
    {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        $query = "
            SELECT
                s.id_sale as invoice_id,
                s.created_at as invoice_date,
                s.total_amount,
                s.payment_method,
                e_cashier.employe_name as cashier_name,
                o.id_order as order_id,
                tbl.table_number,
                e_waiter.employe_name as waiter_name
            FROM sales s
            JOIN employees e_cashier ON s.cashier_id = e_cashier.id_employe
            JOIN sales_has_orders sho ON s.id_sale = sho.sales_id_sale
            JOIN orders o ON sho.orders_id_order = o.id_order
            JOIN table_sessions ts ON o.table_sessions_id_session = ts.id_session
            JOIN `tables` tbl ON ts.tables_id_table = tbl.id_table
            LEFT JOIN employees e_waiter ON o.waiter_id = e_waiter.id_employe
            WHERE s.created_at BETWEEN ? AND ?
              AND s.sale_status = 'COMPLETED'
            ORDER BY s.created_at DESC, o.id_order ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$startDateTime, $endDateTime]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar pedidos por factura
        $invoices = [];
        foreach ($results as $row) {
            $invoiceId = $row['invoice_id'];
            if (!isset($invoices[$invoiceId])) {
                $invoices[$invoiceId] = [
                    'invoice_id' => (int)$invoiceId,
                    'invoice_date' => $row['invoice_date'],
                    'total_amount' => (float)$row['total_amount'],
                    'payment_method' => $row['payment_method'],
                    'cashier_name' => $row['cashier_name'],
                    'included_orders' => []
                ];
            }
            $invoices[$invoiceId]['included_orders'][] = [
                'order_id' => (int)$row['order_id'],
                'table_number' => (int)$row['table_number'],
                'waiter_name' => $row['waiter_name'] ?? 'N/A'
            ];
        }

        return array_values($invoices); // Re-indexar el array
    }

    /**
     * 3. REPORTE: DESEMPEÑO DE EMPLEADOS (VERSIÓN CORREGIDA)
     * Obtiene métricas comparativas para todos los Meseros y Cajeros activos,
     * incluso si no tuvieron actividad en el rango de fechas.
     * @param string $startDate 'Y-m-d'
     * @param string $endDate 'Y-m-d'
     * @return array
     */
    public function getEmployeesPerformance(string $startDate, string $endDate): array
    {
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        /**
         * Esta consulta unificada es la clave:
         * 1. FROM employees: Empezamos con todos los empleados como base.
         * 2. WHERE e.employees_rol_id_rol IN (1, 3): Filtramos solo para Meseros y Cajeros.
         * 3. LEFT JOIN: Usamos LEFT JOIN para incluir a los empleados aunque no tengan órdenes o ventas
         *    en el rango de fechas. La condición de fecha DEBE ir en el ON, no en el WHERE.
         * 4. COALESCE(..., 0): Convierte cualquier resultado NULL (de empleados inactivos) a 0.
         * 5. GROUP BY: Agrupa por empleado para calcular sus métricas totales.
         */
        $query = "
            SELECT
                e.id_employe as employee_id,
                e.employe_name,
                r.rol_name as role,

                -- Métricas de Mesero (serán 0 para Cajeros)
                COUNT(DISTINCT o.id_order) as orders_confirmed,
                COALESCE(SUM(o.total_amount), 0) as total_value_managed,
                COALESCE(AVG(o.total_amount), 0) as average_order_value,

                -- Métricas de Cajero (serán 0 para Meseros)
                COUNT(DISTINCT s.id_sale) as sales_processed,
                COALESCE(SUM(s.total_amount), 0) as total_revenue_processed,
                COALESCE(AVG(s.total_amount), 0) as average_sale_value

            FROM employees e
            JOIN employees_rol r ON e.employees_rol_id_rol = r.id_rol
            LEFT JOIN orders o ON e.id_employe = o.waiter_id AND o.created_date BETWEEN ? AND ?
            LEFT JOIN sales s ON e.id_employe = s.cashier_id AND s.created_at BETWEEN ? AND ? AND s.sale_status = 'COMPLETED'
            
            WHERE e.employees_rol_id_rol IN (1, 3) -- 1: Mesero, 3: Cajero
              AND e.employees_statuses_id_status = 1 -- Solo empleados 'Activo'
            
            GROUP BY e.id_employe, e.employe_name, r.rol_name
            ORDER BY r.rol_name, e.employe_name;
        ";

        $stmt = $this->conn->prepare($query);
        // Pasamos los parámetros de fecha para ambos LEFT JOINs
        $stmt->execute([$startDateTime, $endDateTime, $startDateTime, $endDateTime]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $performanceData = [];

        foreach ($results as $row) {
            $metrics = [];
            // Llenamos las métricas según el rol, el resto queda en 0 gracias a la consulta
            if ($row['role'] === 'Mesero') {
                $metrics = [
                    'orders_confirmed' => (int)$row['orders_confirmed'],
                    'total_value_managed' => (float)$row['total_value_managed'],
                    'average_order_value' => (float)$row['average_order_value']
                ];
            } else if ($row['role'] === 'Cajero') {
                $metrics = [
                    'sales_processed' => (int)$row['sales_processed'],
                    'total_revenue_processed' => (float)$row['total_revenue_processed'],
                    'average_sale_value' => (float)$row['average_sale_value']
                ];
            }

            $performanceData[] = [
                'employee_id' => (int)$row['employee_id'],
                'employee_name' => $row['employe_name'],
                'role' => $row['role'],
                'metrics' => $metrics
            ];
        }

        return $performanceData;
    }
    /**
     * 4. REPORTE: ESTADO DEL INVENTARIO
     * Obtiene el estado actual de productos e ingredientes.
     * @return array
     */
    public function getInventoryStatus(): array
    {
        // --- Productos con stock ---
        $productsQuery = "
            SELECT
                p.id_product as product_id,
                p.product_name,
                pc.category_name as category,
                p.product_stock as stock,
                p.low_stock_level,
                p.critical_stock_level,
                CASE
                    WHEN p.product_stock <= p.critical_stock_level THEN 'CRITICO'
                    WHEN p.product_stock <= p.low_stock_level THEN 'BAJO'
                    ELSE 'OPTIMO'
                END as status
            FROM products p
            LEFT JOIN products_category pc ON p.product_category = pc.id_category
            WHERE p.product_stock IS NOT NULL
              AND p.product_types_id_type = 2 -- 'Producto no preparado'
        ";
        $stmtProducts = $this->conn->prepare($productsQuery);
        $stmtProducts->execute();
        $products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
        foreach ($products as &$product) {
            $product['stock_unit'] = 'Unidad'; // Añadido para consistencia con el mock
        }
        unset($product); // Romper la referencia



        return [
            'products' => $products
        ];
    }
}

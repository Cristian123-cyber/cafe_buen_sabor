<?php
// src/router.php
// Router principal con agrupación de rutas por recursos usando clases

use Bramus\Router\Router;

use App\Middleware\AccessControlMiddleware;
use PHPUnit\Framework\Attributes\Before;

$router = new Router();

// ========================================
// NAMESPACE (para no repetir el prefijo de los controladores)
// ========================================
$router->setNamespace('\App\Controllers');

// ========================================
// RUTAS PÚBLICAS
// ========================================

// La autenticación debe ser pública para poder obtener el token
$router->post('/api/auth/login', 'AuthController@login');
$router->post('/api/auth/refresh', 'AuthController@refresh');
$router->post('/api/table-sessions/validate-qr', 'TableSessionController1@validateQrAndStartSession');
// Aquí podrían ir otras rutas públicas como 'forgot-password'
// $router->post('/api/auth/forgot-password', 'AuthController@forgotPassword');


// ========================================
// GRUPO DE RUTAS PROTEGIDAS
// ========================================

//AccessControlMiddleware::handle([5, 2], false);

// --- Rutas de Productos ---

// ... añadir aquí el resto de rutas de productos si es necesario

// --- Rutas de Perfil (ejemplo de otra ruta protegida) ---
// $router->get('/api/auth/profile', 'AuthController@profile');


// ========================================
// MANEJO DE ERRORES GLOBALES
// ========================================

// Ruta para manejar 404 (no encontrada)
$router->set404(function () {
    http_response_code(404);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'success' => false,
        'message' => 'Ruta no encontrada',
        'data' => [],
        'error_code' => 'RES001'
    ]);
});

// ========================================
// CLASES PARA AGRUPAR RUTAS
// ========================================

/**
 * Clase base para agrupar rutas
 */
abstract class RouteGroup
{

    /**
     * Registra todas las rutas de la aplicación
     */
    public static function registerAll($router)
    {
        ProductRoutes::register($router);
        ProductCategoryRoutes::register($router);
        IngredientRoutes::register($router);
        NotificationRoutes::register($router);
        EmployeeRoutes::register($router);
        RoleRoutes::register($router);
        AuthRoutes::registerProtectedRoutes($router); // Rutas de auth que sí necesitan token
        EmployeeStatusRoutes::register($router);
        TableRoutes::register($router);
        TableSessionRoutes::register($router);
        SaleRoutes::register($router);
        SaleOrderRoutes::register($router);
        OrdersRoutes::register($router);
        UnitsOfMeasureRoutes::register($router);
        AnalyticsRoutes::register($router);
    }
}
/**
 * Rutas relacionadas con empleados
 */
class EmployeeRoutes
{
    public static function register($router)
    {
        $router->get('/api/employees', 'EmployeesController@index');
        $router->get('/api/employees/(\d+)', 'EmployeesController@show');
        $router->post('/api/employees', 'EmployeesController@store');
        $router->post('/api/employees/(\d+)/change-password', 'EmployeesController@changePassword');
        $router->put('/api/employees/(\d+)', 'EmployeesController@update');
        $router->delete('/api/employees/(\d+)', 'EmployeesController@delete');


        $router->get('/api/employees/filter', 'EmployeesController@filter');
        $router->get('/api/employees/tables-served', 'SalesController@tablesServedByWaiter'); //cantidad de mesas atendidas por mesero 
        $router->get('/api/employees/(\d+)/sales-summary', 'SalesController@employeeSalesSummary');

        // Rutas para roles de empleados
        $router->get('/api/employees/roles', 'RolesController@index');
    }
}
/**
 * Rutas relacionadas con roles de los trabajadores
 */
class RoleRoutes
{
    public static function register($router)
    {
        // CRUD básico para roles
        $router->get('/api/roles', 'RolesController@index');
        $router->get('/api/roles/(\d+)', 'RolesController@show');
        $router->post('/api/roles', 'RolesController@store');
        $router->put('/api/roles/(\d+)', 'RolesController@update');
        $router->delete('/api/roles/(\d+)', 'RolesController@delete');
    }
}
/**
 * Rutas relacionadas con el estatus de los trabajadores
 */
class EmployeeStatusRoutes
{

    public static function register($router)
    {
        // CRUD básico para estados de empleados
        $router->get('/api/estados-empleados', 'EmployeesStatusesController@index');
        $router->get('/api/estados-empleados/(\d+)', 'EmployeesStatusesController@show');
        $router->post('/api/estados-empleados', 'EmployeesStatusesController@store');
        $router->put('/api/estados-empleados/(\d+)', 'EmployeesStatusesController@update');
        $router->delete('/api/estados-empleados/(\d+)', 'EmployeesStatusesController@delete');

        // Rutas para estados de empleados (solo lectura según documentación)
        $router->get('/api/employees/statuses', 'EmployeesStatusesController@index');
    }
}
/**
 * Rutas relacionadas con productos
 */
class ProductRoutes
{

    public static function register($router)
    {
        // CRUD básico
        $router->get('/api/productos', 'ProductoController@index');
        $router->get('/api/productos/(\d+)', 'ProductoController@show');
        $router->post('/api/productos', 'ProductoController@store');
        $router->put('/api/productos/(\d+)', 'ProductoController@update');
        $router->delete('/api/productos/(\d+)', 'ProductoController@delete');

        // Rutas específicas de productos
        $router->get('/api/productos/buscar', 'ProductoController@search');
        $router->get('/api/productos/categoria/(\d+)', 'ProductoController@getByCategory');
        $router->get('/api/productos/ingredientes', 'ProductoController@getWithIngredients');

        // Rutas de gestión de imágenes
        $router->post('/api/productos/(\d+)/image', 'ProductoController@uploadImage');
        $router->delete('/api/productos/(\d+)/image', 'ProductoController@deleteImage');

        // Rutas de gestión de stock
        $router->put('/api/productos/(\d+)/stock', 'ProductoController@updateStock');
        $router->get('/api/productos/(\d+)/stock/historial', 'ProductoController@getStockHistory');
        $router->get('/api/productos/stock/estado/(\d+)', 'ProductoController@getByStockStatus');
        $router->get('/api/productos/stock/bajo', 'ProductoController@getLowStock');
        $router->get('/api/productos/stock/critico', 'ProductoController@getCriticalStock');

        // Rutas adicionales (mantener compatibilidad)
        $router->get('/api/productos/disponibles', 'ProductoController@getAvailable');
        $router->get('/api/productos/populares', 'ProductoController@getPopular');

        // Rutas para tipos de productos
        $router->get('/api/products/types', 'ProductTypesController@index');
    }
}

/**
 * Rutas relacionadas con categorías de productos
 */
class ProductCategoryRoutes
{
    public static function register($router)
    {
        // CRUD básico
        $router->get('/api/categorias-productos', 'ProductCategoryController@index');
        $router->get('/api/categorias-productos/(\d+)', 'ProductCategoryController@show');
        $router->post('/api/categorias-productos', 'ProductCategoryController@store');
        $router->put('/api/categorias-productos/(\d+)', 'ProductCategoryController@update');
        $router->delete('/api/categorias-productos/(\d+)', 'ProductCategoryController@delete');

        // Rutas específicas
        $router->get('/api/categorias-productos/(\d+)/productos', 'ProductCategoryController@getProducts');
    }
}

/**
 * Rutas relacionadas con ingredientes
 */
class IngredientRoutes
{
    public static function register($router)
    {
        // CRUD básico
        $router->get('/api/ingredientes', 'IngredientController@index');
        $router->get('/api/ingredientes/(\d+)', 'IngredientController@show');
        $router->post('/api/ingredientes', 'IngredientController@store');
        $router->put('/api/ingredientes/(\d+)', 'IngredientController@update');
        $router->delete('/api/ingredientes/(\d+)', 'IngredientController@delete');

        // Rutas específicas de ingredientes
        $router->get('/api/ingredientes/buscar', 'IngredientController@search');
        $router->get('/api/ingredientes/estado/(\d+)', 'IngredientController@getByStatus');
        $router->get('/api/ingredientes/stock/bajo', 'IngredientController@getLowStock');
        $router->get('/api/ingredientes/stock/critico', 'IngredientController@getCriticalStock');

        // Rutas de gestión de stock
        $router->put('/api/ingredientes/(\d+)/stock', 'IngredientController@updateStock');
        $router->get('/api/ingredientes/(\d+)/stock/historial', 'IngredientController@getStockHistory');
        $router->get('/api/ingredientes/(\d+)/productos', 'IngredientController@getProductsUsingIngredient');

        // Rutas de estadísticas
        $router->get('/api/ingredientes/estadisticas', 'IngredientController@getStatistics');
    }
}

/**
 * Rutas relacionadas con unidades de medida
 */
class UnitsOfMeasureRoutes
{
    public static function register($router)
    {
        // Solo obtener todas las unidades de medida
        $router->get('/api/units-of-measure', 'UnitsOfMeasureController@index');
    }
}

/**
 * Rutas relacionadas con analytics y dashboard
 */
class AnalyticsRoutes
{
    public static function register($router)
    {
        // Dashboard principal del administrador
        $router->get('/api/analytics/dashboard-summary', 'AnalyticsController@dashboardSummary');

        // Gráficos de analytics
        $router->get('/api/analytics/yearly-revenue', 'AnalyticsController@yearlyRevenue');
        $router->get('/api/analytics/top-waiters', 'AnalyticsController@topWaiters');
        $router->get('/api/analytics/top-products', 'AnalyticsController@topProducts');

        $router->get('/api/analytics/sales-balance', 'ReportsController@salesBalance');
        $router->get('/api/analytics/invoices-list', 'ReportsController@invoicesList');
        $router->get('/api/analytics/employees-performance', 'ReportsController@employeesPerformance');
        $router->get('/api/analytics/inventory-status', 'ReportsController@inventoryStatus');
    }
}

/**
 * Rutas relacionadas con notificaciones
 */
class NotificationRoutes
{
    public static function register($router)
    {
        // CRUD básico
        $router->get('/api/notificaciones', 'NotificationController@index');
        $router->get('/api/notificaciones/(\d+)', 'NotificationController@show');
        $router->post('/api/notificaciones', 'NotificationController@store');
        $router->put('/api/notificaciones/(\d+)', 'NotificationController@update');
        $router->delete('/api/notificaciones/(\d+)', 'NotificationController@delete');

        // Rutas específicas de notificaciones
        $router->get('/api/notificaciones/empleado/(\d+)', 'NotificationController@getByEmployee');
        $router->get('/api/notificaciones/tipo/([A-Z_]+)', 'NotificationController@getByType');
        $router->get('/api/notificaciones/no-leidas', 'NotificationController@getUnread');
        $router->get('/api/notificaciones/empleado/(\d+)/no-leidas', 'NotificationController@getUnreadByEmployee');

        // Rutas de gestión de lectura
        $router->put('/api/notificaciones/(\d+)/leer', 'NotificationController@markAsRead');
        $router->put('/api/notificaciones/leer-multiples', 'NotificationController@markMultipleAsRead');
        $router->put('/api/notificaciones/empleado/(\d+)/leer-todas', 'NotificationController@markAllAsReadByEmployee');

        // Rutas de creación específica
        $router->post('/api/notificaciones/pedido-listo', 'NotificationController@createOrderReadyNotification');
        $router->post('/api/notificaciones/pedido-confirmado', 'NotificationController@createOrderConfirmedNotification');
        $router->post('/api/notificaciones/pedido-cancelado', 'NotificationController@createOrderCancelledNotification');

        // Rutas de estadísticas
        $router->get('/api/notificaciones/estadisticas', 'NotificationController@getStatistics');
        $router->get('/api/notificaciones/empleado/(\d+)/estadisticas', 'NotificationController@getStatisticsByEmployee');
        $router->get('/api/notificaciones/por-fechas', 'NotificationController@getByDateRange');
    }
}

/**
 * Rutas relacionadas con mesas
 */
class TableRoutes
{
    public static function register($router)
    {
        // Listar todas las mesas
        $router->get('/api/tables', 'TablesController@index');
        // Obtener una mesa por ID
        $router->get('/api/tables/(\d+)', 'TablesController@show');
        // Crear una nueva mesa
        $router->post('/api/tables', 'TablesController@store');
        // Actualizar una mesa
        $router->put('/api/tables/(\d+)', 'TablesController@update');
        // Eliminar una mesa
        $router->delete('/api/tables/(\d+)', 'TablesController@delete');
        // Cambiar estado de la mesa (ejemplo: ocupar/liberar)
        $router->put('/api/tables/(\d+)/estado', 'TablesController@updateStatus');
        // Buscar por token QR
        $router->get('/api/tables/token/([a-zA-Z0-9_]+)', 'TablesController@findByToken');
        // Obtener token por id de mesa
        $router->get('/api/tables/(\d+)/qr', 'TablesController@getQrToken');
        // Desactivar una mesa
        $router->put('/api/tables/(\d+)/deactivate', 'TablesController@deactivate');
        // Activar una mesa
        $router->put('/api/tables/(\d+)/activate', 'TablesController@activate');
        // validar token
        $router->post('/api/tables/validate-token', 'TableSessionController@validateQrAndStartSession');
    }
}

class TableSessionRoutes
{
    public static function register($router)
    {
        //$router->get('/api/table-sessions', 'TableSessionController@index');
        //$router->get('/api/table-sessions/(\d+)', 'TableSessionController@show');
        $router->post('/api/table-sessions', 'TableSessionController@store');
        $router->post('/api/table-sessions/validate-session', 'TableSessionController1@validateClientSession');
        $router->put('/api/table-sessions/(\d+)', 'TableSessionController@update');
        $router->delete('/api/table-sessions/(\d+)', 'TableSessionController@delete');
        $router->get('/api/table-sessions/table/(\d+)', 'TableSessionController@byTable');

        $router->get('/api/table-sessions/', 'TableSessionController@allWithTable');
        $router->get('/api/table-sessions/status/([A-Z]+)', 'TableSessionController@byStatusWithTable');
        $router->put('/api/table-sessions/(\d+)/close', 'TableSessionController@close');
    }
}
/**
 * Rutas relacionadas con autenticación
 */
class AuthRoutes
{

    /**
     * Rutas públicas que no requieren token
     */
    public static function registerPublicRoutes($router)
    {
        // Autenticación básica
        $router->post('/api/auth/login', 'AuthController@login');
    }

    /**
     * Rutas que sí requieren un token válido
     */
    public static function registerProtectedRoutes($router)
    {
        $router->post('/api/auth/logout', 'AuthController@logout');

        // Perfil
        $router->get('/api/auth/me', 'AuthController@me');
    }
}
//rutas de ventas
class SaleRoutes
{
    public static function register($router)
    {
        $router->get('/api/sales', 'SalesController@index');
        $router->get('/api/sales/(\d+)', 'SalesController@show');
        $router->post('/api/sales', 'SalesController@store');
        $router->put('/api/sales/(\d+)', 'SalesController@update');
        $router->delete('/api/sales/(\d+)', 'SalesController@delete');
        $router->get('/api/sales/monthly-revenue', 'SalesController@monthlyRevenue'); //OBTENER RECAUDO MENSUAL
        $router->get('/api/sales/revenue-by-date', 'SalesController@revenueByDate'); //Ingresos por fecha
        $router->get('/api/sales/revenue-by-employee', 'SalesController@revenueByEmployee'); //ingreso por empleado
        $router->get('/api/sales/revenue-by-table', 'SalesController@revenueByTable'); //ingresos por mesa
    }
}

class SaleOrderRoutes
{
    public static function register($router)
    {
        $router->get('/api/sales-orders', 'SaleOrderController@index');
        $router->get('/api/sales-orders/sale/(\d+)', 'SaleOrderController@bySale');
        $router->get('/api/sales-orders/order/(\d+)', 'SaleOrderController@byOrder');
        $router->post('/api/sales-orders', 'SaleOrderController@add');
        $router->delete('/api/sales-orders', 'SaleOrderController@remove');
    }
}
// Rutas relacionadas con pedidos
class OrdersRoutes
{
    public static function register($router)
    {
        // CRUD básico
        $router->get('/api/orders', 'OrderController@index'); //mostrar todos
        $router->get('/api/orders/(\d+)', 'OrderController@show'); //filtrar por id
        $router->post('/api/orders', 'OrderController@store'); //agregar

        // Rutas de estado de pedidos
        $router->put('/api/orders/(\d+)/confirm', 'OrderController@confirm');
        $router->put('/api/orders/(\d+)/cancel', 'OrderController@cancel');
        $router->put('/api/orders/(\d+)/ready', 'OrderController@ready');
        $router->put('/api/orders/(\d+)/complete', 'OrderController@complete');

        // Rutas específicas
        $router->get('/api/orders/status/([A-Z]+)', 'OrderController@getByStatus');
        $router->get('/api/orders/session/(\d+)', 'OrderController@getBySession');
        $router->put('/api/orders/bind-waiter', 'OrderController@bindWaiter');
        $router->post('/api/orders/unify', 'OrderController@unify');
        $router->get('/api/orders/unified/(\d+)', 'OrderController@getUnified');
    }
}

$router->before('POST', '/api/table-sessions/validate-session', function () {

    AccessControlMiddleware::handle([], true);
});
RouteGroup::registerAll($router);

$router->before('GET', '/api/auth/me', function () {

    AccessControlMiddleware::handle([], false);
});



return $router;

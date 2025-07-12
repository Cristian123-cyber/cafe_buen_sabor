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
        EmployeeRoutes::register($router);
        RoleRoutes::register($router);
        AuthRoutes::registerProtectedRoutes($router); // Rutas de auth que sí necesitan token
        EmployeeStatusRoutes::register($router);
        TableRoutes::register($router);
        TableSessionRoutes::register($router);
        SaleRoutes::register($router);
        SaleOrderRoutes::register($router);
        OrdersRoutes::register($router);
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
        $router->put('/api/employees/(\d+)', 'EmployeesController@update');
        $router->delete('/api/employees/(\d+)', 'EmployeesController@delete');

        $router->get('/api/employees/filter', 'EmployeesController@filter');
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

        // Rutas de gestión de stock
        $router->put('/api/productos/(\d+)/stock', 'ProductoController@updateStock');
        $router->get('/api/productos/(\d+)/stock/historial', 'ProductoController@getStockHistory');
        $router->get('/api/productos/stock/estado/(\d+)', 'ProductoController@getByStockStatus');
        $router->get('/api/productos/stock/bajo', 'ProductoController@getLowStock');
        $router->get('/api/productos/stock/critico', 'ProductoController@getCriticalStock');

        // Rutas adicionales (mantener compatibilidad)
        $router->get('/api/productos/disponibles', 'ProductoController@getAvailable');
        $router->get('/api/productos/populares', 'ProductoController@getPopular');
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
    }
}

class TableSessionRoutes
{
    public static function register($router)
    {
        $router->get('/api/table-sessions', 'TableSessionController@index');
        $router->get('/api/table-sessions/(\d+)', 'TableSessionController@show');
        $router->post('/api/table-sessions', 'TableSessionController@store');
        $router->post('/api/table-sessions/validate-session', 'TableSessionController1@validateClientSession');
        $router->put('/api/table-sessions/(\d+)', 'TableSessionController@update');
        $router->delete('/api/table-sessions/(\d+)', 'TableSessionController@delete');
        $router->get('/api/table-sessions/table/(\d+)', 'TableSessionController@byTable');
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
        $router->get('/api/orders', 'OrdersController@index'); //mostrar todos
        $router->get('/api/orders/(\d+)', 'OrdersController@show'); //filtrar por id
        $router->post('/api/orders', 'OrdersController@store'); //agregar
        $router->put('/api/orders/(\d+)', 'OrdersController@update'); //actualizar
        $router->delete('/api/orders/(\d+)', 'OrdersController@delete'); //eliminar
        $router->patch('/api/orders/(\d+)/status', 'OrdersController@updateStatus'); //actualizar estado
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

<?php
// src/router.php
// Router principal con agrupación de rutas por recursos usando clases

use Bramus\Router\Router;
use App\Middleware\AuthMiddleware;

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
// Aquí podrían ir otras rutas públicas como 'forgot-password'
// $router->post('/api/auth/forgot-password', 'AuthController@forgotPassword');


// ========================================
// GRUPO DE RUTAS PROTEGIDAS
// ========================================

// Todas las rutas dentro de este grupo requerirán un token JWT válido
$router->before('POST|PUT|DELETE|GET', '/api/.*', function() {
    // Excepción para la ruta de login que ya está definida fuera del grupo
    if (strpos($_SERVER['REQUEST_URI'], '/api/auth/login') === 0) {
        return;
    }
    //AuthMiddleware::handle();
});

// --- Rutas de Productos ---

// ... añadir aquí el resto de rutas de productos si es necesario

// --- Rutas de Perfil (ejemplo de otra ruta protegida) ---
// $router->get('/api/auth/profile', 'AuthController@profile');


// ========================================
// MANEJO DE ERRORES GLOBALES
// ========================================

// Ruta para manejar 404 (no encontrada)
$router->set404(function() {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode([
        'success' => false, 
        'message' => 'Ruta no encontrada',
        'data' => []
    ]);
});

// ========================================
// CLASES PARA AGRUPAR RUTAS
// ========================================

/**
 * Clase base para agrupar rutas
 */
abstract class RouteGroup {
    
    /**
     * Registra todas las rutas de la aplicación
     */
    public static function registerAll($router) {
        ProductRoutes::register($router);
        EmployeeRoutes::register($router);
        RoleRoutes::register($router);
        AuthRoutes::registerProtectedRoutes($router); // Rutas de auth que sí necesitan token
        EmployeeStatusRoutes::register($router);
        TableRoutes::register($router);
        TableSessionRoutes::register($router);
        SaleRoutes::register($router);
        SaleOrderRoutes::register($router);
    }
}
/**
 * Rutas relacionadas con empleados
 */
class EmployeeRoutes {
    public static function register($router) {
        $router->get('/api/employees', 'EmployeesController@index');
        $router->get('/api/employees/(\d+)', 'EmployeesController@show');
        $router->post('/api/employees', 'EmployeesController@store');
        $router->put('/api/employees/(\d+)', 'EmployeesController@update');
        $router->delete('/api/employees/(\d+)', 'EmployeesController@delete');
    }
}
/**
 * Rutas relacionadas con roles de los trabajadores
 */
class RoleRoutes {
    public static function register($router) {
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
class EmployeeStatusRoutes {
    
    public static function register($router) {
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
class ProductRoutes {
    
    public static function register($router) {
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
class TableRoutes {
    public static function register($router) {
        // Listar todas las mesas
        $router->get('/api/mesas', 'TablesController@index');
        // Obtener una mesa por ID
        $router->get('/api/mesas/(\d+)', 'TablesController@show');
        // Crear una nueva mesa
        $router->post('/api/mesas', 'TablesController@store');
        // Actualizar una mesa
        $router->put('/api/mesas/(\d+)', 'TablesController@update');
        // Eliminar una mesa
        $router->delete('/api/mesas/(\d+)', 'TablesController@delete');
        // Cambiar estado de la mesa (ejemplo: ocupar/liberar)
        $router->put('/api/mesas/(\d+)/estado', 'TablesController@updateStatus');
        // Buscar por token QR
        $router->get('/api/mesas/token/([a-zA-Z0-9_]+)', 'TablesController@findByToken');
    }
}

class TableSessionRoutes {
    public static function register($router) {
        $router->get('/api/table-sessions', 'TableSessionController@index');
        $router->get('/api/table-sessions/(\d+)', 'TableSessionController@show');
        $router->post('/api/table-sessions', 'TableSessionController@store');
        $router->put('/api/table-sessions/(\d+)', 'TableSessionController@update');
        $router->delete('/api/table-sessions/(\d+)', 'TableSessionController@delete');
        $router->get('/api/table-sessions/table/(\d+)', 'TableSessionController@byTable');
    }
}
/**
 * Rutas relacionadas con autenticación
 */
class AuthRoutes {
    
    /**
     * Rutas públicas que no requieren token
     */
    public static function registerPublicRoutes($router) {
        // Autenticación básica
        $router->post('/api/auth/login', 'AuthController@login');
        
        // Recuperación de contraseña
        $router->post('/api/auth/forgot-password', 'AuthController@forgotPassword');
        $router->post('/api/auth/reset-password', 'AuthController@resetPassword');
        
        // Verificación (puede ser pública dependiendo de la lógica)
        $router->post('/api/auth/verify-email', 'AuthController@verifyEmail');
        $router->post('/api/auth/resend-verification', 'AuthController@resendVerification');
    }

    /**
     * Rutas que sí requieren un token válido
     */
    public static function registerProtectedRoutes($router) {
        $router->post('/api/auth/logout', 'AuthController@logout');
        $router->post('/api/auth/refresh', 'AuthController@refresh');
        
        // Perfil
        $router->get('/api/auth/profile', 'AuthController@profile');
        $router->put('/api/auth/profile', 'AuthController@updateProfile');
    }
} 
//rutas de ventas
class SaleRoutes {
    public static function register($router) {
        $router->get('/api/sales', 'SalesController@index');
        $router->get('/api/sales/(\d+)', 'SalesController@show');
        $router->post('/api/sales', 'SalesController@store');
        $router->put('/api/sales/(\d+)', 'SalesController@update');
        $router->delete('/api/sales/(\d+)', 'SalesController@delete');
    }
}

class SaleOrderRoutes {
    public static function register($router) {
        $router->get('/api/sales-orders', 'SaleOrderController@index');
        $router->get('/api/sales-orders/sale/(\d+)', 'SaleOrderController@bySale');
        $router->get('/api/sales-orders/order/(\d+)', 'SaleOrderController@byOrder');
        $router->post('/api/sales-orders', 'SaleOrderController@add');
        $router->delete('/api/sales-orders', 'SaleOrderController@remove');
    }
}
RouteGroup::registerAll($router);
return $router;

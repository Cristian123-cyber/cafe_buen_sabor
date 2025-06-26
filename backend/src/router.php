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
    AuthMiddleware::handle();
});

// --- Rutas de Productos ---
$router->get('/api/productos', 'ProductoController@index');
$router->get('/api/productos/(\d+)', 'ProductoController@show');
$router->post('/api/productos', 'ProductoController@store');
$router->put('/api/productos/(\d+)', 'ProductoController@update');
$router->delete('/api/productos/(\d+)', 'ProductoController@delete');
$router->get('/api/productos/buscar', 'ProductoController@search');
$router->get('/api/productos/categoria/(\d+)', 'ProductoController@getByCategory');
$router->put('/api/productos/(\d+)/stock', 'ProductoController@updateStock');
$router->get('/api/productos/(\d+)/stock/historial', 'ProductoController@getStockHistory');
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

return $router;

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
        AdminRoutes::register($router);
        CategoryRoutes::register($router);
        OrderRoutes::register($router);
        RoleRoutes::register($router);
        AuthRoutes::registerProtectedRoutes($router); // Rutas de auth que sí necesitan token
        EmployeesStatusRoutes::register($router);
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
 * Rutas relacionadas con administradores
 */
class AdminRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/administradores', 'AdminController@index');
        $router->get('/api/administradores/(\d+)', 'AdminController@show');
        $router->post('/api/administradores', 'AdminController@store');
        $router->put('/api/administradores/(\d+)', 'AdminController@update');
        $router->delete('/api/administradores/(\d+)', 'AdminController@delete');
        
        // Rutas específicas de administradores
        $router->get('/api/administradores/perfil', 'AdminController@profile');
        $router->put('/api/administradores/cambiar-password', 'AdminController@changePassword');
        $router->get('/api/administradores/estadisticas', 'AdminController@getStats');
        $router->post('/api/administradores/activar/(\d+)', 'AdminController@activate');
        $router->post('/api/administradores/desactivar/(\d+)', 'AdminController@deactivate');
    }
}

/**
 * Rutas relacionadas con categorías
 */
class CategoryRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/categorias', 'CategoriaController@index');
        $router->get('/api/categorias/(\d+)', 'CategoriaController@show');
        $router->post('/api/categorias', 'CategoriaController@store');
        $router->put('/api/categorias/(\d+)', 'CategoriaController@update');
        $router->delete('/api/categorias/(\d+)', 'CategoriaController@delete');
        
        // Rutas específicas de categorías
        $router->get('/api/categorias/activas', 'CategoriaController@getActive');
        $router->get('/api/categorias/(\d+)/productos', 'CategoriaController@getProducts');
        $router->post('/api/categorias/(\d+)/activar', 'CategoriaController@activate');
        $router->post('/api/categorias/(\d+)/desactivar', 'CategoriaController@deactivate');
    }
}

/**
 * Rutas relacionadas con pedidos
 */
class OrderRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/pedidos', 'PedidoController@index');
        $router->get('/api/pedidos/(\d+)', 'PedidoController@show');
        $router->post('/api/pedidos', 'PedidoController@store');
        $router->put('/api/pedidos/(\d+)', 'PedidoController@update');
        $router->delete('/api/pedidos/(\d+)', 'PedidoController@delete');
        
        // Rutas específicas de pedidos
        $router->put('/api/pedidos/(\d+)/estado', 'PedidoController@updateStatus');
        $router->get('/api/pedidos/estado/(\w+)', 'PedidoController@getByStatus');
        $router->get('/api/pedidos/cliente/(\d+)', 'PedidoController@getByCustomer');
        $router->get('/api/pedidos/hoy', 'PedidoController@getToday');
        $router->get('/api/pedidos/estadisticas', 'PedidoController@getStats');
        $router->post('/api/pedidos/(\d+)/cancelar', 'PedidoController@cancel');
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
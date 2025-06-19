<?php
// src/router.php
// Router principal con agrupación de rutas por recursos usando clases

use Bramus\Router\Router;

$router = new Router();

// ========================================
// AGRUPACIÓN DE RUTAS POR RECURSOS
// ========================================

// Registrar todas las rutas usando clases
RouteGroup::registerAll($router);

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
        AuthRoutes::register($router);
    }
}

/**
 * Rutas relacionadas con productos
 */
class ProductRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/productos', 'App\\Controllers\\ProductoController@index');
        $router->get('/api/productos/(\d+)', 'App\\Controllers\\ProductoController@show');
        $router->post('/api/productos', 'App\\Controllers\\ProductoController@store');
        $router->put('/api/productos/(\d+)', 'App\\Controllers\\ProductoController@update');
        $router->delete('/api/productos/(\d+)', 'App\\Controllers\\ProductoController@delete');
        
        // Rutas específicas de productos
        $router->get('/api/productos/buscar', 'App\\Controllers\\ProductoController@search');
        $router->get('/api/productos/categoria/(\d+)', 'App\\Controllers\\ProductoController@getByCategory');
        $router->put('/api/productos/(\d+)/stock', 'App\\Controllers\\ProductoController@updateStock');
        $router->get('/api/productos/disponibles', 'App\\Controllers\\ProductoController@getAvailable');
        $router->get('/api/productos/populares', 'App\\Controllers\\ProductoController@getPopular');
    }
}

/**
 * Rutas relacionadas con administradores
 */
class AdminRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/administradores', 'App\\Controllers\\AdminController@index');
        $router->get('/api/administradores/(\d+)', 'App\\Controllers\\AdminController@show');
        $router->post('/api/administradores', 'App\\Controllers\\AdminController@store');
        $router->put('/api/administradores/(\d+)', 'App\\Controllers\\AdminController@update');
        $router->delete('/api/administradores/(\d+)', 'App\\Controllers\\AdminController@delete');
        
        // Rutas específicas de administradores
        $router->get('/api/administradores/perfil', 'App\\Controllers\\AdminController@profile');
        $router->put('/api/administradores/cambiar-password', 'App\\Controllers\\AdminController@changePassword');
        $router->get('/api/administradores/estadisticas', 'App\\Controllers\\AdminController@getStats');
        $router->post('/api/administradores/activar/(\d+)', 'App\\Controllers\\AdminController@activate');
        $router->post('/api/administradores/desactivar/(\d+)', 'App\\Controllers\\AdminController@deactivate');
    }
}

/**
 * Rutas relacionadas con categorías
 */
class CategoryRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/categorias', 'App\\Controllers\\CategoriaController@index');
        $router->get('/api/categorias/(\d+)', 'App\\Controllers\\CategoriaController@show');
        $router->post('/api/categorias', 'App\\Controllers\\CategoriaController@store');
        $router->put('/api/categorias/(\d+)', 'App\\Controllers\\CategoriaController@update');
        $router->delete('/api/categorias/(\d+)', 'App\\Controllers\\CategoriaController@delete');
        
        // Rutas específicas de categorías
        $router->get('/api/categorias/activas', 'App\\Controllers\\CategoriaController@getActive');
        $router->get('/api/categorias/(\d+)/productos', 'App\\Controllers\\CategoriaController@getProducts');
        $router->post('/api/categorias/(\d+)/activar', 'App\\Controllers\\CategoriaController@activate');
        $router->post('/api/categorias/(\d+)/desactivar', 'App\\Controllers\\CategoriaController@deactivate');
    }
}

/**
 * Rutas relacionadas con pedidos
 */
class OrderRoutes {
    
    public static function register($router) {
        // CRUD básico
        $router->get('/api/pedidos', 'App\\Controllers\\PedidoController@index');
        $router->get('/api/pedidos/(\d+)', 'App\\Controllers\\PedidoController@show');
        $router->post('/api/pedidos', 'App\\Controllers\\PedidoController@store');
        $router->put('/api/pedidos/(\d+)', 'App\\Controllers\\PedidoController@update');
        $router->delete('/api/pedidos/(\d+)', 'App\\Controllers\\PedidoController@delete');
        
        // Rutas específicas de pedidos
        $router->put('/api/pedidos/(\d+)/estado', 'App\\Controllers\\PedidoController@updateStatus');
        $router->get('/api/pedidos/estado/(\w+)', 'App\\Controllers\\PedidoController@getByStatus');
        $router->get('/api/pedidos/cliente/(\d+)', 'App\\Controllers\\PedidoController@getByCustomer');
        $router->get('/api/pedidos/hoy', 'App\\Controllers\\PedidoController@getToday');
        $router->get('/api/pedidos/estadisticas', 'App\\Controllers\\PedidoController@getStats');
        $router->post('/api/pedidos/(\d+)/cancelar', 'App\\Controllers\\PedidoController@cancel');
    }
}

/**
 * Rutas relacionadas con autenticación
 */
class AuthRoutes {
    
    public static function register($router) {
        // Autenticación básica
        $router->post('/api/auth/login', 'App\\Controllers\\AuthController@login');
        $router->post('/api/auth/logout', 'App\\Controllers\\AuthController@logout');
        $router->post('/api/auth/refresh', 'App\\Controllers\\AuthController@refresh');
        
        // Recuperación de contraseña
        $router->post('/api/auth/forgot-password', 'App\\Controllers\\AuthController@forgotPassword');
        $router->post('/api/auth/reset-password', 'App\\Controllers\\AuthController@resetPassword');
        
        // Verificación
        $router->post('/api/auth/verify-email', 'App\\Controllers\\AuthController@verifyEmail');
        $router->post('/api/auth/resend-verification', 'App\\Controllers\\AuthController@resendVerification');
        
        // Perfil
        $router->get('/api/auth/profile', 'App\\Controllers\\AuthController@profile');
        $router->put('/api/auth/profile', 'App\\Controllers\\AuthController@updateProfile');
    }
} 
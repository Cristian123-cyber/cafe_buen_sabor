<?php
// src/router.php
// Aquí se definen todas las rutas de la API

use Bramus\Router\Router;

$router = new Router();

// Ruta para obtener todos los productos
$router->get('/api/productos', 'App\\Controllers\\ProductoController@index');
// Ruta para obtener un producto por ID
$router->get('/api/productos/(\d+)', 'App\\Controllers\\ProductoController@show');

// Ruta para manejar 404 (no encontrada)
$router->set404(function() {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
});

return $router; 
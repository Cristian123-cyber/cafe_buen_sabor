<?php
// index.php - Nuestro "Front Controller" y "Router" simple

// ----------------------------------------------------
// 1. CABECERAS (Headers)
// ----------------------------------------------------
// Es una buena práctica establecer las cabeceras aquí en PHP, 
// además de en el .htaccess. Esto le da más control a la aplicación.
// Le decimos al navegador que la respuesta será en formato JSON y codificada en UTF-8.
header("Content-Type: application/json; charset=UTF-8");
// La cabecera de CORS ya la maneja .htaccess, pero si quisiéramos ser redundantes:
// header("Access-Control-Allow-Origin: *"); 


// ----------------------------------------------------
// 2. OBTENER LA RUTA (URL)
// ----------------------------------------------------
// Gracias a la regla del .htaccess (RewriteRule ^api/(.*)$ index.php?url=$1),
// la parte de la URL después de /api/ viene en el parámetro 'url'.
// Por ejemplo: /api/productos -> $_GET['url'] será 'productos'
$url = isset($_GET['url']) ? $_GET['url'] : '';


// ----------------------------------------------------
// 3. ENRUTADOR (Router) SIMPLE
// ----------------------------------------------------
// Aquí decidimos qué hacer basándonos en la URL que recibimos.
// Usamos un 'switch' para manejar diferentes "endpoints" o rutas.
switch ($url) {
    case 'productos':
        getProductos();
        break;
    case 'test':
        getTestResponse();
        break;
    default:
        errorNotFound();
        break;
}

// ----------------------------------------------------
// 4. LÓGICA DE LOS ENDPOINTS (Funciones) - CORREGIDO
// ----------------------------------------------------

/**
 * Endpoint para /api/productos
 * Devuelve una lista de productos de ejemplo.
 */
function getProductos() {
    $productos = [
        ['id' => 1, 'nombre' => 'Café CARTAGO', 'precio' => 2.50, 'categoria' => 'Bebidas'],
        ['id' => 2, 'nombre' => 'Croissant de Almendras', 'precio' => 3.75, 'categoria' => 'Pastelería'],
        ['id' => 3, 'nombre' => 'Jugo de Naranja Natural', 'precio' => 4.00, 'categoria' => 'Bebidas'],
        ['id' => 4, 'nombre' => 'Tarta de Chocolate', 'precio' => 5.50, 'categoria' => 'Postres']
    ];

    // La respuesta ya era consistente, la mantenemos.
    $respuesta = [
        'success' => true,
        'message' => 'Productos obtenidos correctamente.', // Mensaje más específico
        'data'    => $productos
    ];

    http_response_code(200);
    echo json_encode($respuesta);
}

/**
 * Endpoint para /api/test
 * Devuelve un mensaje de éxito simple.
 */
function getTestResponse() {
    // AHORA ES CONSISTENTE: incluye 'success', 'message' y 'data' (como null).
    $respuesta = [
        'success' => true,
        'message' => 'La API de Café Buen Sabor está funcionando correctamente.',
        'data'    => null // Se añade 'data' como null para mantener la estructura.
    ];

    http_response_code(200);
    echo json_encode($respuesta);
}

/**
 * Función para manejar rutas no encontradas.
 */
function errorNotFound() {
    // AHORA ES CONSISTENTE: usa 'success: false' y también incluye 'data: null'.
    $respuesta = [
        'success' => false,
        'message' => 'Endpoint no encontrado.',
        'data'    => null
    ];
    
    http_response_code(404);
    echo json_encode($respuesta);
}


?>
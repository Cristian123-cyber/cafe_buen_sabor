<?php
// index.php - Nuestro "Front Controller" y "Router" simple

// ----------------------------------------------------
// 1. CABECERAS (Headers)
// ----------------------------------------------------
// Es una buena práctica establecer las cabeceras aquí en PHP, 
// además de en el .htaccess. Esto le da más control a la aplicación.
// Le decimos al navegador que la respuesta será en formato JSON y codificada en UTF-8.
header("Content-Type: application/json; charset=UTF-8");

// Cargar variables de entorno
require_once __DIR__ . '/../vendor/autoload.php';

// ROBUSTES MEJORADA PARA CARGAR VARIABLES DE ENTORNO
// Intentamos cargar el archivo .env de forma segura, sin lanzar excepciones si no existe:
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->safeLoad(); // safeLoad() no lanza excepción si el archivo no existe
} catch (Exception $e) {
    // Opcional: Registrar el error pero continuar la ejecución
    error_log('No se pudo cargar el archivo .env: ' . $e->getMessage());
}

/* $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load(); */

// Punto de entrada de la aplicación (Front Controller)
// Solo carga el autoload y el router
// require_once __DIR__ . '/../vendor/autoload.php'; // Movido arriba

// Importa el router definido en src/router.php
$router = require __DIR__ . '/../src/router.php';

// Ejecuta el router para procesar la petición
$router->run();
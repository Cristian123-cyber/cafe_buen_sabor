<?php
// index.php - Nuestro "Front Controller" y "Router" simple

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

// Importa el router definido en src/router.php
$router = require __DIR__ . '/../src/router.php';

// Ejecuta el router para procesar la petición
$router->run();
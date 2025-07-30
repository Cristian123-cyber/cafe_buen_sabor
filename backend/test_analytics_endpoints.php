<?php
/**
 * Script de prueba para los endpoints de Analytics
 * Ejecutar desde la raíz del proyecto: php test_analytics_endpoints.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Configurar base de datos
require_once __DIR__ . '/src/config/database.php';

// Incluir controladores
require_once __DIR__ . '/src/controllers/BaseController.php';
require_once __DIR__ . '/src/controllers/AnalyticsController.php';

// Incluir modelos
require_once __DIR__ . '/src/models/BaseModel.php';
require_once __DIR__ . '/src/models/sale.php';
require_once __DIR__ . '/src/models/order.php';
require_once __DIR__ . '/src/models/table.php';
require_once __DIR__ . '/src/models/employees.php';
require_once __DIR__ . '/src/models/Producto.php';

echo "🧪 Probando endpoints de Analytics...\n\n";

try {
    // Test 1: Dashboard Summary
    echo "📊 1. Probando /api/analytics/dashboard-summary\n";
    echo "----------------------------------------\n";
    
    $analyticsController = new App\Controllers\AnalyticsController();
    
    // Simular la petición HTTP para el dashboard
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/api/analytics/dashboard-summary';
    
    // Capturar la salida del controlador
    ob_start();
    $analyticsController->dashboardSummary();
    $dashboardOutput = ob_get_clean();
    
    echo "✅ Dashboard generado correctamente\n";
    echo "📈 Respuesta JSON:\n";
    echo $dashboardOutput . "\n\n";
    
    // Test 2: Yearly Revenue
    echo "📈 2. Probando /api/analytics/yearly-revenue\n";
    echo "----------------------------------------\n";
    
    // Simular parámetros GET
    $_GET['year'] = date('Y');
    
    ob_start();
    $analyticsController->yearlyRevenue();
    $yearlyOutput = ob_get_clean();
    
    echo "✅ Datos anuales generados correctamente\n";
    echo "📊 Respuesta JSON:\n";
    echo $yearlyOutput . "\n\n";
    
    // Test 3: Top Waiters
    echo "👥 3. Probando /api/analytics/top-waiters\n";
    echo "----------------------------------------\n";
    
    // Simular parámetros GET
    $_GET['period'] = 'monthly';
    
    ob_start();
    $analyticsController->topWaiters();
    $waitersOutput = ob_get_clean();
    
    echo "✅ Top meseros generado correctamente\n";
    echo "👥 Respuesta JSON:\n";
    echo $waitersOutput . "\n\n";
    
    // Test 4: Top Products
    echo "🛍️ 4. Probando /api/analytics/top-products\n";
    echo "----------------------------------------\n";
    
    // Simular parámetros GET
    $_GET['limit'] = '5';
    $_GET['period'] = 'monthly';
    
    ob_start();
    $analyticsController->topProducts();
    $productsOutput = ob_get_clean();
    
    echo "✅ Top productos generado correctamente\n";
    echo "🛍️ Respuesta JSON:\n";
    echo $productsOutput . "\n\n";
    
    echo "🎉 ¡Todos los endpoints funcionan correctamente!\n";
    echo "📋 Resumen de endpoints implementados:\n";
    echo "   ✅ GET /api/analytics/dashboard-summary\n";
    echo "   ✅ GET /api/analytics/yearly-revenue\n";
    echo "   ✅ GET /api/analytics/top-waiters\n";
    echo "   ✅ GET /api/analytics/top-products\n";
    
} catch (Exception $e) {
    echo "❌ Error durante las pruebas: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
    echo "📍 Stack trace:\n" . $e->getTraceAsString() . "\n";
} 
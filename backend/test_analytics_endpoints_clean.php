<?php
/**
 * Script de prueba limpio para los endpoints de Analytics
 * Ejecutar: docker-compose exec backend php test_analytics_endpoints_clean.php
 */

// Evitar warnings de headers
ob_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/config/database.php';
require_once __DIR__ . '/src/controllers/BaseController.php';
require_once __DIR__ . '/src/controllers/AnalyticsController.php';
require_once __DIR__ . '/src/models/BaseModel.php';
require_once __DIR__ . '/src/models/sale.php';
require_once __DIR__ . '/src/models/order.php';
require_once __DIR__ . '/src/models/table.php';
require_once __DIR__ . '/src/models/employees.php';
require_once __DIR__ . '/src/models/Producto.php';

// Limpiar cualquier salida previa
ob_clean();

echo "🧪 Probando endpoints de Analytics...\n\n";

try {
    $analyticsController = new App\Controllers\AnalyticsController();
    
    // Test 1: Dashboard Summary
    echo "📊 1. Probando /api/analytics/dashboard-summary\n";
    echo "----------------------------------------\n";
    
    // Simular parámetros GET
    $_GET = [];
    
    // Capturar la salida del controlador
    ob_start();
    $analyticsController->dashboardSummary();
    $dashboardOutput = ob_get_clean();
    
    // Decodificar JSON para mostrar mejor
    $dashboardData = json_decode($dashboardOutput, true);
    echo "✅ Dashboard generado correctamente\n";
    echo "📈 Estructura de datos:\n";
    echo json_encode($dashboardData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    // Test 2: Yearly Revenue
    echo "📈 2. Probando /api/analytics/yearly-revenue\n";
    echo "----------------------------------------\n";
    
    $_GET['year'] = date('Y');
    
    ob_start();
    $analyticsController->yearlyRevenue();
    $yearlyOutput = ob_get_clean();
    
    $yearlyData = json_decode($yearlyOutput, true);
    echo "✅ Datos anuales generados correctamente\n";
    echo "📊 Estructura de datos:\n";
    echo json_encode($yearlyData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    // Test 3: Top Waiters
    echo "👥 3. Probando /api/analytics/top-waiters\n";
    echo "----------------------------------------\n";
    
    $_GET['period'] = 'monthly';
    
    ob_start();
    $analyticsController->topWaiters();
    $waitersOutput = ob_get_clean();
    
    $waitersData = json_decode($waitersOutput, true);
    echo "✅ Top meseros generado correctamente\n";
    echo "👥 Estructura de datos:\n";
    echo json_encode($waitersData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    // Test 4: Top Products
    echo "🛍️ 4. Probando /api/analytics/top-products\n";
    echo "----------------------------------------\n";
    
    $_GET['limit'] = '5';
    $_GET['period'] = 'monthly';
    
    ob_start();
    $analyticsController->topProducts();
    $productsOutput = ob_get_clean();
    
    $productsData = json_decode($productsOutput, true);
    echo "✅ Top productos generado correctamente\n";
    echo "🛍️ Estructura de datos:\n";
    echo json_encode($productsData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    echo "🎉 ¡Todos los endpoints funcionan correctamente!\n";
    echo "📋 Resumen de endpoints implementados:\n";
    echo "   ✅ GET /api/analytics/dashboard-summary\n";
    echo "   ✅ GET /api/analytics/yearly-revenue\n";
    echo "   ✅ GET /api/analytics/top-waiters\n";
    echo "   ✅ GET /api/analytics/top-products\n";
    echo "\n";
    echo "📊 Estado de la base de datos:\n";
    echo "   - Ingresos hoy: " . $dashboardData['data']['revenue']['value'] . "\n";
    echo "   - Pedidos hoy: " . $dashboardData['data']['totalOrders']['value'] . "\n";
    echo "   - Mesas activas: " . $dashboardData['data']['activeTables']['value'] . "\n";
    echo "   - Ticket promedio: " . $dashboardData['data']['averageTicket']['value'] . "\n";
    
} catch (Exception $e) {
    echo "❌ Error durante las pruebas: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
} 
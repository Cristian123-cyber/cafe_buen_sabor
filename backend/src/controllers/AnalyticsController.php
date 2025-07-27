<?php

namespace App\Controllers;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Table;
use Exception;

class AnalyticsController extends BaseController
{
    private $saleModel;
    private $orderModel;
    private $tableModel;

    public function __construct()
    {
        parent::__construct();
        $this->saleModel = new Sale();
        $this->orderModel = new Order();
        $this->tableModel = new Table();
    }

    /**
     * Dashboard principal del administrador
     * GET /api/analytics/dashboard-summary
     */
    public function dashboardSummary()
    {
        return $this->executeWithErrorHandling(function () {
            // Obtener períodos de tiempo
            $today = date('Y-m-d 00:00:00');
            $yesterday = date('Y-m-d 00:00:00', strtotime('-1 day'));
            $now = date('Y-m-d H:i:s');
            $yesterdayNow = date('Y-m-d H:i:s', strtotime('-1 day'));

            // Calcular métricas
            $revenue = $this->calculateRevenue($today, $now, $yesterday, $yesterdayNow);
            $totalOrders = $this->calculateTotalOrders($today, $now, $yesterday, $yesterdayNow);
            $activeTables = $this->calculateActiveTables();
            $averageTicket = $this->calculateAverageTicket($today, $now, $yesterday, $yesterdayNow);

            $response = [
                'revenue' => $revenue,
                'totalOrders' => $totalOrders,
                'activeTables' => $activeTables,
                'averageTicket' => $averageTicket
            ];

            $this->handleResponse(true, 'Dashboard obtenido correctamente', $response);
        }, 'Error al obtener el dashboard');
    }

    /**
     * Calcula los ingresos con tendencia
     */
    private function calculateRevenue($today, $now, $yesterday, $yesterdayNow)
    {
        // Ingresos de hoy
        $revenueToday = $this->saleModel->getRevenueByDateRange($today, $now);
        
        // Ingresos de ayer (mismo período)
        $revenueYesterday = $this->saleModel->getRevenueByDateRange($yesterday, $yesterdayNow);
        
        // Calcular tendencia
        $trend = $this->calculateTrend($revenueToday, $revenueYesterday);
        
        return [
            'title' => 'Ingresos (Hoy)',
            'value' => '$' . number_format($revenueToday, 2),
            'trend' => [
                'value' => $trend,
                'text' => 'vs ayer'
            ]
        ];
    }

    /**
     * Calcula el total de pedidos con tendencia
     */
    private function calculateTotalOrders($today, $now, $yesterday, $yesterdayNow)
    {
        // Pedidos de hoy
        $ordersToday = $this->orderModel->getOrdersByDateRange($today, $now);
        
        // Pedidos de ayer (mismo período)
        $ordersYesterday = $this->orderModel->getOrdersByDateRange($yesterday, $yesterdayNow);
        
        // Calcular tendencia
        $trend = $this->calculateTrend($ordersToday, $ordersYesterday);
        
        return [
            'title' => 'Pedidos Totales',
            'value' => (string)$ordersToday,
            'trend' => [
                'value' => $trend,
                'text' => 'vs ayer'
            ]
        ];
    }

    /**
     * Calcula las mesas activas (snapshot)
     */
    private function calculateActiveTables()
    {
        $tableStats = $this->tableModel->getTableStats();
        
        $occupiedTables = $tableStats['occupied'] ?? 0;
        $totalTables = $tableStats['total'] ?? 0;
        $progress = $totalTables > 0 ? round(($occupiedTables / $totalTables) * 100) : 0;
        
        return [
            'title' => 'Mesas Activas',
            'value' => $occupiedTables . ' / ' . $totalTables,
            'progress' => $progress,
            'text' => $progress . '% ocupación',
            'trend' => null
        ];
    }

    /**
     * Calcula el ticket promedio con tendencia
     */
    private function calculateAverageTicket($today, $now, $yesterday, $yesterdayNow)
    {
        // Datos de hoy
        $revenueToday = $this->saleModel->getRevenueByDateRange($today, $now);
        $ordersToday = $this->orderModel->getOrdersByDateRange($today, $now);
        $averageToday = $ordersToday > 0 ? $revenueToday / $ordersToday : 0;
        
        // Datos de ayer
        $revenueYesterday = $this->saleModel->getRevenueByDateRange($yesterday, $yesterdayNow);
        $ordersYesterday = $this->orderModel->getOrdersByDateRange($yesterday, $yesterdayNow);
        $averageYesterday = $ordersYesterday > 0 ? $revenueYesterday / $ordersYesterday : 0;
        
        // Calcular tendencia
        $trend = $this->calculateTrend($averageToday, $averageYesterday);
        
        return [
            'title' => 'Ticket Promedio',
            'value' => '$' . number_format($averageToday, 2),
            'trend' => [
                'value' => $trend,
                'text' => 'vs ayer'
            ]
        ];
    }

    /**
     * Calcula la tendencia entre dos valores
     */
    private function calculateTrend($currentValue, $previousValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }
        
        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }
} 
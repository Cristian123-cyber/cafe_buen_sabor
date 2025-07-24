<?php

namespace App\Controllers;

use App\Models\Sale;
use Exception;

class SalesController
{
    public function index()
    {
        try {
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 10;
            $orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : null;
            $saleModel = new Sale();
            $sales = $saleModel->getAll($page, $limit, $orderBy);
            echo json_encode([
                'success' => true,
                'data' => $sales
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener las ventas',
                'error' => $e->getMessage()
            ]);
        }
    }



    public function show($id)
    {
        try {
            $saleModel = new Sale();
            $sale = $saleModel->find($id);
            if ($sale) {
                echo json_encode([
                    'success' => true,
                    'data' => $sale
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener la venta',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $saleModel = new Sale();
            $sale = $saleModel->createSale($data);
            echo json_encode([
                'success' => true,
                'data' => $sale
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear la venta',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $saleModel = new Sale();
            $sale = $saleModel->updateSale($id, $data);
            if ($sale) {
                echo json_encode([
                    'success' => true,
                    'data' => $sale
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la venta',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function delete($id)
    {
        try {
            $saleModel = new Sale();
            $deleted = $saleModel->deleteSale($id);
            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Venta eliminada'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Venta no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la venta',
                'error' => $e->getMessage()
            ]);
        }
    }

    //visualización gráfica del recaudo mensual 
    public function monthlyRevenue()
    {
        try {
            $saleModel = new Sale();
            $data = $saleModel->getMonthlyRevenue();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error al obtener el recaudo mensual', 'error' => $e->getMessage()]);
        }
    }

    //Ingresos por fecha
    public function revenueByDate()
    {
        try {
            $from = $_GET['from'] ?? null;
            $to = $_GET['to'] ?? null;
            $saleModel = new Sale();
            $data = $saleModel->getRevenueByDate($from, $to);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    //ingreso por empleado 
    public function revenueByEmployee()
    {
        try {
            $saleModel = new Sale();
            $data = $saleModel->getRevenueByEmployee();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    //ingreso por mesa
    public function revenueByTable()
    {
        try {
            $saleModel = new Sale();
            $data = $saleModel->revenueByTable();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    //Cantidad de mesas atendidas por cada mesero
    public function tablesServedByWaiter()
    {
        try {
            $saleModel = new Sale();
            $data = $saleModel->getTablesServedByWaiter();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function employeeSalesSummary($id)
    {
        try {
            $saleModel = new \App\Models\Sale();
            $data = $saleModel->getEmployeeSalesSummary($id);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado o sin ventas.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
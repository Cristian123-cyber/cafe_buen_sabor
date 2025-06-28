<?php

namespace App\Controllers;

use App\Models\Sale;
use Exception;

class SalesController
{
    public function index()
    {
        try {
            $saleModel = new Sale();
            $sales = $saleModel->all();
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
}
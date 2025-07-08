<?php

namespace App\Controllers;

use App\Models\Sale;
use App\Models\Employee;
use Exception;

class SalesController 
{
    /**
     * Obtener todas las ventas
     * GET /api/sales/
     */
    public function index()
    {
        try {
            $saleModel = new Sale();
            // Obtener ventas con datos básicos de pedidos asociados (sin productos)
            $sales = $saleModel->getAllWithOrders();
            
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

    /**
     * Obtener una venta específica con todos los detalles
     * GET /api/sales/{id}
     */
    public function show($id)
    {
        try {
            $saleModel = new Sale();
            // Obtener venta con pedidos y productos asociados
            $sale = $saleModel->findWithFullDetails($id);
            
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

    /**
     * Crear una nueva venta
     * POST /api/sales/
     */
    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos requeridos
            if (!$this->validateStoreData($data)) {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $this->getValidationErrors($data)
                ]);
                return;
            }

            // Validar que el cashier_id corresponda a un empleado con rol de cajero
            $employeeModel = new Employee();
            $cashier = $employeeModel->findCashierById($data['cashier_id']);
            
            if (!$cashier) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El empleado especificado no tiene rol de cajero'
                ]);
                return;
            }

            $saleModel = new Sale();
            $sale = $saleModel->createSale($data);
            
            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Venta creada exitosamente',
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

    /**
     * Cancelar una venta (solo permite cambiar a CANCELED)
     * PUT /api/sales/{id}
     */
    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validar que solo se pueda cancelar
            if (!isset($data['sale_status']) || $data['sale_status'] !== 'CANCELED') {
                http_response_code(422);
                echo json_encode([
                    'success' => false,
                    'message' => 'Solo se permite cancelar ventas. El estado debe ser CANCELED'
                ]);
                return;
            }

            $saleModel = new Sale();
            $sale = $saleModel->cancelSale($id);
            
            if ($sale) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Venta cancelada exitosamente',
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
                'message' => 'Error al cancelar la venta',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener ventas por estado
     * GET /api/sales/status/{status}
     */
    public function getByStatus($status)
    {
        try {
            // Validar estado
            if (!in_array($status, ['COMPLETED', 'CANCELED'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Estado no válido. Debe ser COMPLETED o CANCELED'
                ]);
                return;
            }

            $saleModel = new Sale();
            $sales = $saleModel->getByStatus($status);
            
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

    /**
     * Método eliminado según especificaciones
     * NO SE PODRA ELIMINAR LA DATA DE LAS VENTAS POR MOTIVOS DE SEGURIDAD
     */

    /**
     * Validar datos para crear venta
     */
    private function validateStoreData($data)
    {
        if (!isset($data['orders']) || !is_array($data['orders']) || empty($data['orders'])) {
            return false;
        }
        
        if (!isset($data['cashier_id']) || !is_numeric($data['cashier_id'])) {
            return false;
        }
        
        if (!isset($data['payment_method']) || !in_array($data['payment_method'], ['EFECTIVO', 'TRANSFERENCIA'])) {
            return false;
        }
        
        // Validar que todos los elementos del array orders sean números
        foreach ($data['orders'] as $orderId) {
            if (!is_numeric($orderId)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Obtener errores de validación
     */
    private function getValidationErrors($data)
    {
        $errors = [];
        
        if (!isset($data['orders']) || !is_array($data['orders']) || empty($data['orders'])) {
            $errors['orders'] = 'El campo orders es requerido y debe ser un array no vacío';
        }
        
        if (!isset($data['cashier_id']) || !is_numeric($data['cashier_id'])) {
            $errors['cashier_id'] = 'El campo cashier_id es requerido y debe ser un número';
        }
        
        if (!isset($data['payment_method']) || !in_array($data['payment_method'], ['EFECTIVO', 'TRANSFERENCIA'])) {
            $errors['payment_method'] = 'El campo payment_method es requerido y debe ser EFECTIVO o TRANSFERENCIA';
        }
        
        return $errors;
    }
}
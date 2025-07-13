<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\TableSession;
use App\Models\Employees;
use Exception;

class OrderController extends BaseController
{
    private $orderModel;
    private $tableSessionModel;
    private $employeeModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
        $this->tableSessionModel = new TableSession();
        $this->employeeModel = new Employees();
    }

    /**
     * Crear un pedido
     * POST /api/orders/
     */
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos según documentación
            $requiredFields = ['table_session_id', 'products'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Validar que la sesión de mesa esté activa
            $session = $this->tableSessionModel->find($data['table_session_id']);
            if (!$session || $session['session_status'] !== 'ACTIVE') {
                $this->handleResponse(false, 'La sesión de mesa no está activa o no existe', [], 400);
                return;
            }

            // Validar que products sea un array
            if (!is_array($data['products']) || empty($data['products'])) {
                $this->handleResponse(false, 'El campo products debe ser un array no vacío', [], 400);
                return;
            }

            // Validar estructura de cada producto
            foreach ($data['products'] as $product) {
                if (!isset($product['id_product']) || !isset($product['quantity'])) {
                    $this->handleResponse(false, 'Cada producto debe tener id_product y quantity', [], 400);
                    return;
                }
            }

            // Crear el pedido
            $orderData = [
                'table_sessions_id_session' => $data['table_session_id'],
                'order_statuses_id_status' => 1, // PENDING
                'waiter_id' => null, // Se asigna después
                'total_amount' => 0 // Se calcula después
            ];

            $order = $this->orderModel->create($orderData);
            
            if (!$order) {
                $this->handleResponse(false, 'Error al crear el pedido', [], 500);
                return;
            }

            // Agregar productos al pedido y calcular total
            $totalAmount = 0;
            foreach ($data['products'] as $product) {
                // Obtener precio actual del producto
                $productPrice = $this->getProductPrice($product['id_product']);
                if ($productPrice === false) {
                    $this->handleResponse(false, 'Producto no encontrado: ' . $product['id_product'], [], 400);
                    return;
                }

                // Agregar producto al pedido
                $this->orderModel->addProductToOrder(
                    $order['id_order'],
                    $product['id_product'],
                    $product['quantity'],
                    $productPrice
                );

                $totalAmount += $productPrice * $product['quantity'];
            }

            // Actualizar total del pedido
            $this->orderModel->update($order['id_order'], [
                'total_amount' => $totalAmount,
                'waiter_id' => null,
                'order_statuses_id_status' => 1,
                'table_sessions_id_session' => $data['table_session_id']
            ]);

            $this->handleResponse(true, 'Pedido creado correctamente', [
                'id_order' => $order['id_order'],
                'table_session_id' => $data['table_session_id'],
                'total_amount' => $totalAmount
            ], 201);

        }, 'Error al crear el pedido');
    }

    /**
     * Obtener todos los pedidos
     * GET /api/orders/
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $orders = $this->orderModel->getAllWithDetails();
            $this->handleResponse(true, 'Pedidos obtenidos correctamente', $orders);
        }, 'Error al obtener los pedidos');
    }

    /**
     * Obtener un pedido específico
     * GET /api/orders/{id}
     */
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $orderId = $this->validateId($id);
            if (!$orderId) {
                $this->handleInvalidIdError('ID de pedido');
                return;
            }

            $order = $this->orderModel->getByIdWithDetails($orderId);
            
            if (!$order) {
                $this->handleResourceNotFoundError('Pedido');
                return;
            }

            $this->handleResponse(true, 'Pedido obtenido correctamente', $order);
        }, 'Error al obtener el pedido');
    }

    /**
     * Obtener pedidos por estado
     * GET /api/orders/status/{status}
     */
    public function getByStatus($status)
    {
        return $this->executeWithErrorHandling(function() use ($status) {
            $validStatuses = ['PENDING', 'CONFIRM', 'READY', 'CANCELED', 'COMPLETED'];
            
            if (!in_array($status, $validStatuses)) {
                $this->handleResponse(false, 'Estado no válido', [], 400);
                return;
            }

            $orders = $this->orderModel->getByStatusWithDetails($status);
            $this->handleResponse(true, 'Pedidos por estado obtenidos correctamente', $orders);
        }, 'Error al obtener pedidos por estado');
    }

    /**
     * Obtener pedidos de una sesión de mesa
     * GET /api/orders/session/{table_session_id}
     */
    public function getBySession($table_session_id)
    {
        return $this->executeWithErrorHandling(function() use ($table_session_id) {
            $sessionId = $this->validateId($table_session_id);
            if (!$sessionId) {
                $this->handleInvalidIdError('ID de sesión de mesa');
                return;
            }

            $orders = $this->orderModel->getBySessionWithDetails($sessionId);
            $this->handleResponse(true, 'Pedidos de la sesión obtenidos correctamente', $orders);
        }, 'Error al obtener pedidos de la sesión');
    }

    /**
     * Confirmar pedido
     * PUT /api/orders/{id}/confirm
     */
    public function confirm($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $orderId = $this->validateId($id);
            if (!$orderId) {
                $this->handleInvalidIdError('ID de pedido');
                return;
            }

            $result = $this->orderModel->updateStatus($orderId, 2); // CONFIRMED
            
            if ($result) {
                $this->handleResponse(true, 'Pedido confirmado correctamente', $result);
            } else {
                $this->handleResponse(false, 'Error al confirmar el pedido', [], 500);
            }
        }, 'Error al confirmar el pedido');
    }

    /**
     * Cancelar pedido
     * PUT /api/orders/{id}/cancel
     */
    public function cancel($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $orderId = $this->validateId($id);
            if (!$orderId) {
                $this->handleInvalidIdError('ID de pedido');
                return;
            }

            $result = $this->orderModel->updateStatus($orderId, 3); // CANCELED
            
            if ($result) {
                $this->handleResponse(true, 'Pedido cancelado correctamente', $result);
            } else {
                $this->handleResponse(false, 'Error al cancelar el pedido', [], 500);
            }
        }, 'Error al cancelar el pedido');
    }

    /**
     * Marcar pedido como listo
     * PUT /api/orders/{id}/ready
     */
    public function ready($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $orderId = $this->validateId($id);
            if (!$orderId) {
                $this->handleInvalidIdError('ID de pedido');
                return;
            }

            $result = $this->orderModel->updateStatus($orderId, 4); // READY
            
            if ($result) {
                $this->handleResponse(true, 'Pedido marcado como listo correctamente', $result);
            } else {
                $this->handleResponse(false, 'Error al marcar el pedido como listo', [], 500);
            }
        }, 'Error al marcar el pedido como listo');
    }

    /**
     * Completar pedido
     * PUT /api/orders/{id}/complete
     */
    public function complete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $orderId = $this->validateId($id);
            if (!$orderId) {
                $this->handleInvalidIdError('ID de pedido');
                return;
            }

            $result = $this->orderModel->updateStatus($orderId, 5); // COMPLETED
            
            if ($result) {
                $this->handleResponse(true, 'Pedido completado correctamente', $result);
            } else {
                $this->handleResponse(false, 'Error al completar el pedido', [], 500);
            }
        }, 'Error al completar el pedido');
    }

    /**
     * Asociar mesero a pedidos de una sesión
     * PUT /api/orders/bind-waiter/
     */
    public function bindWaiter()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            $requiredFields = ['table_session_id', 'waiter_id'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Validar que el empleado sea mesero
            $waiter = $this->employeeModel->findById($data['waiter_id']);
            if (!$waiter || $waiter['employees_rol_id_rol'] != 2) { // Asumiendo que 2 es el rol de mesero
                $this->handleResponse(false, 'El empleado especificado no tiene rol de mesero', [], 400);
                return;
            }

            $result = $this->orderModel->bindWaiter($data['table_session_id'], $data['waiter_id']);
            
            $this->handleResponse(true, 'Mesero asociado correctamente', $result);
        }, 'Error al asociar mesero');
    }

    /**
     * Unificar pedidos
     * POST /api/orders/unify
     */
    public function unify()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            if (!isset($data['orders_to_unify']) || !is_array($data['orders_to_unify'])) {
                $this->handleResponse(false, 'El campo orders_to_unify es requerido y debe ser un array', [], 400);
                return;
            }

            $unifiedId = $this->orderModel->unifyOrders($data['orders_to_unify']);
            
            $this->handleResponse(true, 'Pedidos unificados correctamente', [
                'unified_order_id' => $unifiedId
            ], 201);
        }, 'Error al unificar pedidos');
    }

    /**
     * Obtener pedidos de una orden unificada
     * GET /api/orders/unified/{id}
     */
    public function getUnified($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $unifiedId = $this->validateId($id);
            if (!$unifiedId) {
                $this->handleInvalidIdError('ID de orden unificada');
                return;
            }

            $orders = $this->orderModel->getByUnifiedWithDetails($unifiedId);
            $this->handleResponse(true, 'Pedidos de la orden unificada obtenidos correctamente', $orders);
        }, 'Error al obtener pedidos de la orden unificada');
    }

    /**
     * Obtener precio actual de un producto
     */
    private function getProductPrice($productId)
    {
        $stmt = $this->orderModel->conn->prepare("SELECT product_price FROM products WHERE id_product = ?");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result ? $result['product_price'] : false;
    }
} 
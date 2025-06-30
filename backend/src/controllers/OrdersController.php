<?php
namespace App\Controllers;

use App\Models\Order;
use Exception;

class OrdersController extends BaseController
{
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
    }

    // Listar todos los pedidos
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $orders = $this->orderModel->getAll();
            $this->handleResponse(true, 'Pedidos obtenidos correctamente.', $orders);
        }, 'Error al obtener los pedidos');
    }

    // Obtener un pedido por ID
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $order = $this->orderModel->getById($id);
            if ($order) {
                $this->handleResponse(true, 'Pedido obtenido correctamente.', $order);
            } else {
                $this->handleResponse(false, 'Pedido no encontrado', [], 404);
            }
        }, 'Error al obtener el pedido');
    }

    // Crear un nuevo pedido
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            $requiredFields = ['total_amount', 'waiter_id', 'order_statuses_id_status', 'table_sessions_id_session'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);

            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            $order = $this->orderModel->create($data);
            $this->handleResponse(true, 'Pedido creado exitosamente.', $order, 201);
        }, 'Error al crear el pedido');
    }

    // Actualizar un pedido
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $data = $this->getRequestData();
            $order = $this->orderModel->update($id, $data);
            $this->handleResponse(true, 'Pedido actualizado exitosamente.', $order);
        }, 'Error al actualizar el pedido');
    }

    // Eliminar un pedido
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $deleted = $this->orderModel->delete($id);
            if ($deleted) {
                $this->handleResponse(true, 'Pedido eliminado correctamente.');
            } else {
                $this->handleResponse(false, 'No se pudo eliminar el pedido', [], 500);
            }
        }, 'Error al eliminar el pedido');
    }

    // Cambiar estado de pedido
    public function updateStatus($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $data = $this->getRequestData();
            if (!isset($data['order_statuses_id_status'])) {
                $this->handleResponse(false, 'El campo order_statuses_id_status es requerido', [], 400);
                return;
            }
            $order = $this->orderModel->updateStatus($id, $data['order_statuses_id_status']);
            $this->handleResponse(true, 'Estado del pedido actualizado.', $order);
        }, 'Error al actualizar el estado del pedido');
    }
}
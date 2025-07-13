<?php

namespace App\Controllers;

use App\Models\Notification;
use Exception;

class NotificationController extends BaseController
{
    private $notificationModel;

    public function __construct()
    {
        parent::__construct();
        $this->notificationModel = new Notification();
    }

    /**
     * Obtiene todas las notificaciones con información completa
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            
            // Sanitizar parámetros de paginación
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $notifications = $this->notificationModel->getAllWithDetails($page, $limit);
            $this->handleResponse(true, 'Notificaciones obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener las notificaciones');
    }

    /**
     * Obtiene una notificación por ID con información completa
     */
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $notificationId = $this->validateId($id);
            if (!$notificationId) {
                $this->handleInvalidIdError('ID de notificación');
                return;
            }
            
            $notification = $this->notificationModel->getByIdWithDetails($notificationId);
            
            if (!$notification) {
                $this->handleResourceNotFoundError('Notificación');
                return;
            }
            
            $this->handleResponse(true, 'Notificación obtenida correctamente.', $notification);
            
        }, 'Error al obtener la notificación');
    }

    /**
     * Crea una nueva notificación
     */
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos
            $requiredFields = ['notification_type', 'message'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Sanitizar y validar datos
            $notificationData = [
                'notification_type' => $this->sanitizeString($data['notification_type']),
                'message' => $this->sanitizeString($data['message']),
                'is_read' => 'UNREAD',
                'employee_id' => isset($data['employee_id']) ? $this->validateId($data['employee_id']) : null,
                'order_id' => isset($data['order_id']) ? $this->validateId($data['order_id']) : null
            ];

            // Validar tipo de notificación
            $validTypes = ['ORDER_READY', 'ORDER_CONFIRMED', 'ORDER_CANCELLED'];
            if (!in_array($notificationData['notification_type'], $validTypes)) {
                $this->handleResponse(false, 'Tipo de notificación inválido', [], 400);
                return;
            }

            $resultado = $this->notificationModel->create($notificationData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación creada correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear la notificación', [], 500);
            }
            
        }, 'Error al crear la notificación');
    }

    /**
     * Actualiza una notificación
     */
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $notificationId = $this->validateId($id);
            if (!$notificationId) {
                $this->handleInvalidIdError('ID de notificación');
                return;
            }
            
            $data = $this->getRequestData();
            
            if (empty($data)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            // Sanitizar y validar datos
            $updateData = [];
            
            if (isset($data['message'])) {
                $updateData['message'] = $this->sanitizeString($data['message']);
            }
            
            if (isset($data['is_read'])) {
                $isRead = $this->sanitizeString($data['is_read']);
                if (!in_array($isRead, ['READ', 'UNREAD'])) {
                    $this->handleResponse(false, 'Estado de lectura inválido', [], 400);
                    return;
                }
                $updateData['is_read'] = $isRead;
            }
            
            if (isset($data['employee_id'])) {
                $employeeId = $this->validateId($data['employee_id']);
                if (!$employeeId) {
                    $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                    return;
                }
                $updateData['employee_id'] = $employeeId;
            }
            
            if (isset($data['order_id'])) {
                $orderId = $this->validateId($data['order_id']);
                if (!$orderId) {
                    $this->handleResponse(false, 'ID de pedido inválido', [], 400);
                    return;
                }
                $updateData['order_id'] = $orderId;
            }

            $resultado = $this->notificationModel->update($notificationId, $updateData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación actualizada correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al actualizar la notificación', [], 500);
            }
            
        }, 'Error al actualizar la notificación');
    }

    /**
     * Elimina una notificación
     */
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $notificationId = $this->validateId($id);
            if (!$notificationId) {
                $this->handleInvalidIdError('ID de notificación');
                return;
            }
            
            $resultado = $this->notificationModel->delete($notificationId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación eliminada correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al eliminar la notificación', [], 500);
            }
            
        }, 'Error al eliminar la notificación');
    }

    /**
     * Obtiene notificaciones por empleado
     */
    public function getByEmployee($employee_id)
    {
        return $this->executeWithErrorHandling(function() use ($employee_id) {
            // Validar ID de empleado
            $employeeId = $this->validateId($employee_id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }
            
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $notifications = $this->notificationModel->getByEmployee($employeeId, $page, $limit);
            $this->handleResponse(true, 'Notificaciones del empleado obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener notificaciones del empleado');
    }

    /**
     * Obtiene notificaciones por tipo
     */
    public function getByType($type)
    {
        return $this->executeWithErrorHandling(function() use ($type) {
            $validTypes = ['ORDER_READY', 'ORDER_CONFIRMED', 'ORDER_CANCELLED'];
            if (!in_array($type, $validTypes)) {
                $this->handleResponse(false, 'Tipo de notificación inválido', [], 400);
                return;
            }
            
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $notifications = $this->notificationModel->getByType($type, $page, $limit);
            $this->handleResponse(true, 'Notificaciones por tipo obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener notificaciones por tipo');
    }

    /**
     * Obtiene notificaciones no leídas
     */
    public function getUnread()
    {
        return $this->executeWithErrorHandling(function() {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $notifications = $this->notificationModel->getUnread($page, $limit);
            $this->handleResponse(true, 'Notificaciones no leídas obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener notificaciones no leídas');
    }

    /**
     * Obtiene notificaciones no leídas por empleado
     */
    public function getUnreadByEmployee($employee_id)
    {
        return $this->executeWithErrorHandling(function() use ($employee_id) {
            // Validar ID de empleado
            $employeeId = $this->validateId($employee_id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }
            
            $notifications = $this->notificationModel->getUnreadByEmployee($employeeId);
            $this->handleResponse(true, 'Notificaciones no leídas del empleado obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener notificaciones no leídas del empleado');
    }

    /**
     * Marca una notificación como leída
     */
    public function markAsRead($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $notificationId = $this->validateId($id);
            if (!$notificationId) {
                $this->handleInvalidIdError('ID de notificación');
                return;
            }
            
            $resultado = $this->notificationModel->markAsRead($notificationId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación marcada como leída correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al marcar la notificación como leída', [], 500);
            }
            
        }, 'Error al marcar la notificación como leída');
    }

    /**
     * Marca múltiples notificaciones como leídas
     */
    public function markMultipleAsRead()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            if (!isset($data['notification_ids']) || !is_array($data['notification_ids'])) {
                $this->handleResponse(false, 'IDs de notificaciones requeridos', [], 400);
                return;
            }
            
            // Validar que todos los IDs sean válidos
            $validIds = [];
            foreach ($data['notification_ids'] as $id) {
                $validId = $this->validateId($id);
                if ($validId) {
                    $validIds[] = $validId;
                }
            }
            
            if (empty($validIds)) {
                $this->handleResponse(false, 'No hay IDs válidos', [], 400);
                return;
            }
            
            $resultado = $this->notificationModel->markMultipleAsRead($validIds);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificaciones marcadas como leídas correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al marcar las notificaciones como leídas', [], 500);
            }
            
        }, 'Error al marcar las notificaciones como leídas');
    }

    /**
     * Marca todas las notificaciones de un empleado como leídas
     */
    public function markAllAsReadByEmployee($employee_id)
    {
        return $this->executeWithErrorHandling(function() use ($employee_id) {
            // Validar ID de empleado
            $employeeId = $this->validateId($employee_id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }
            
            $resultado = $this->notificationModel->markAllAsReadByEmployee($employeeId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Todas las notificaciones del empleado marcadas como leídas correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al marcar las notificaciones como leídas', [], 500);
            }
            
        }, 'Error al marcar las notificaciones como leídas');
    }

    /**
     * Crea una notificación para pedido listo
     */
    public function createOrderReadyNotification()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            if (!isset($data['order_id'])) {
                $this->handleResponse(false, 'ID de pedido requerido', [], 400);
                return;
            }
            
            $orderId = $this->validateId($data['order_id']);
            if (!$orderId) {
                $this->handleResponse(false, 'ID de pedido inválido', [], 400);
                return;
            }
            
            $employeeId = isset($data['employee_id']) ? $this->validateId($data['employee_id']) : null;
            
            $resultado = $this->notificationModel->createOrderReadyNotification($orderId, $employeeId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación de pedido listo creada correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear la notificación', [], 500);
            }
            
        }, 'Error al crear la notificación de pedido listo');
    }

    /**
     * Crea una notificación para pedido confirmado
     */
    public function createOrderConfirmedNotification()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            if (!isset($data['order_id'])) {
                $this->handleResponse(false, 'ID de pedido requerido', [], 400);
                return;
            }
            
            $orderId = $this->validateId($data['order_id']);
            if (!$orderId) {
                $this->handleResponse(false, 'ID de pedido inválido', [], 400);
                return;
            }
            
            $employeeId = isset($data['employee_id']) ? $this->validateId($data['employee_id']) : null;
            
            $resultado = $this->notificationModel->createOrderConfirmedNotification($orderId, $employeeId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación de pedido confirmado creada correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear la notificación', [], 500);
            }
            
        }, 'Error al crear la notificación de pedido confirmado');
    }

    /**
     * Crea una notificación para pedido cancelado
     */
    public function createOrderCancelledNotification()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            if (!isset($data['order_id'])) {
                $this->handleResponse(false, 'ID de pedido requerido', [], 400);
                return;
            }
            
            $orderId = $this->validateId($data['order_id']);
            if (!$orderId) {
                $this->handleResponse(false, 'ID de pedido inválido', [], 400);
                return;
            }
            
            $employeeId = isset($data['employee_id']) ? $this->validateId($data['employee_id']) : null;
            
            $resultado = $this->notificationModel->createOrderCancelledNotification($orderId, $employeeId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Notificación de pedido cancelado creada correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear la notificación', [], 500);
            }
            
        }, 'Error al crear la notificación de pedido cancelado');
    }

    /**
     * Obtiene estadísticas de notificaciones
     */
    public function getStatistics()
    {
        return $this->executeWithErrorHandling(function() {
            $estadisticas = $this->notificationModel->getEstadisticas();
            $this->handleResponse(true, 'Estadísticas obtenidas correctamente.', $estadisticas);
            
        }, 'Error al obtener estadísticas');
    }

    /**
     * Obtiene estadísticas de notificaciones por empleado
     */
    public function getStatisticsByEmployee($employee_id)
    {
        return $this->executeWithErrorHandling(function() use ($employee_id) {
            // Validar ID de empleado
            $employeeId = $this->validateId($employee_id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }
            
            $estadisticas = $this->notificationModel->getEstadisticasPorEmpleado($employeeId);
            $this->handleResponse(true, 'Estadísticas del empleado obtenidas correctamente.', $estadisticas);
            
        }, 'Error al obtener estadísticas del empleado');
    }

    /**
     * Obtiene notificaciones por rango de fechas
     */
    public function getByDateRange()
    {
        return $this->executeWithErrorHandling(function() {
            $start_date = $_GET['start_date'] ?? '';
            $end_date = $_GET['end_date'] ?? '';
            
            if (empty($start_date) || empty($end_date)) {
                $this->handleResponse(false, 'Fechas de inicio y fin requeridas', [], 400);
                return;
            }
            
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $notifications = $this->notificationModel->getByDateRange($start_date, $end_date, $page, $limit);
            $this->handleResponse(true, 'Notificaciones por rango de fechas obtenidas correctamente.', $notifications);
            
        }, 'Error al obtener notificaciones por rango de fechas');
    }
} 
<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Notification extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'notifications';
        $this->primary_key = 'id_notification';
    }

    /**
     * Obtiene notificaciones con información completa (empleado y pedido)
     */
    public function getAllWithDetails($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        e.employe_email as empleado_email,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una notificación por ID con información completa
     */
    public function getByIdWithDetails($id) {
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        e.employe_email as empleado_email,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE n.id_notification = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene notificaciones por empleado
     */
    public function getByEmployee($employee_id, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE n.employee_id = :employee_id
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene notificaciones por tipo
     */
    public function getByType($type, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE n.notification_type = :type
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene notificaciones no leídas
     */
    public function getUnread($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE n.is_read = 'UNREAD'
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene notificaciones no leídas por empleado
     */
    public function getUnreadByEmployee($employee_id) {
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE n.employee_id = :employee_id AND n.is_read = 'UNREAD'
                 ORDER BY n.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Marca una notificación como leída
     */
    public function markAsRead($id) {
        $query = "UPDATE notifications 
                 SET is_read = 'READ' 
                 WHERE id_notification = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Marca múltiples notificaciones como leídas
     */
    public function markMultipleAsRead($ids) {
        if (empty($ids)) {
            return false;
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $query = "UPDATE notifications 
                 SET is_read = 'READ' 
                 WHERE id_notification IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($ids);
    }

    /**
     * Marca todas las notificaciones de un empleado como leídas
     */
    public function markAllAsReadByEmployee($employee_id) {
        $query = "UPDATE notifications 
                 SET is_read = 'READ' 
                 WHERE employee_id = :employee_id AND is_read = 'UNREAD'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        return $stmt->execute();
    }

    /**
     * Crea una notificación para pedido listo
     */
    public function createOrderReadyNotification($order_id, $employee_id = null) {
        $data = [
            'notification_type' => 'ORDER_READY',
            'message' => 'Pedido listo para recoger',
            'is_read' => 'UNREAD',
            'employee_id' => $employee_id,
            'order_id' => $order_id
        ];
        
        return $this->create($data);
    }

    /**
     * Crea una notificación para pedido confirmado
     */
    public function createOrderConfirmedNotification($order_id, $employee_id = null) {
        $data = [
            'notification_type' => 'ORDER_CONFIRMED',
            'message' => 'Pedido confirmado y en preparación',
            'is_read' => 'UNREAD',
            'employee_id' => $employee_id,
            'order_id' => $order_id
        ];
        
        return $this->create($data);
    }

    /**
     * Crea una notificación para pedido cancelado
     */
    public function createOrderCancelledNotification($order_id, $employee_id = null) {
        $data = [
            'notification_type' => 'ORDER_CANCELLED',
            'message' => 'Pedido cancelado',
            'is_read' => 'UNREAD',
            'employee_id' => $employee_id,
            'order_id' => $order_id
        ];
        
        return $this->create($data);
    }

    /**
     * Obtiene estadísticas de notificaciones
     */
    public function getEstadisticas() {
        $query = "SELECT 
                    COUNT(*) as total_notificaciones,
                    SUM(CASE WHEN is_read = 'UNREAD' THEN 1 ELSE 0 END) as no_leidas,
                    SUM(CASE WHEN is_read = 'READ' THEN 1 ELSE 0 END) as leidas,
                    SUM(CASE WHEN notification_type = 'ORDER_READY' THEN 1 ELSE 0 END) as pedidos_listos,
                    SUM(CASE WHEN notification_type = 'ORDER_CONFIRMED' THEN 1 ELSE 0 END) as pedidos_confirmados,
                    SUM(CASE WHEN notification_type = 'ORDER_CANCELLED' THEN 1 ELSE 0 END) as pedidos_cancelados
                 FROM notifications";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene estadísticas de notificaciones por empleado
     */
    public function getEstadisticasPorEmpleado($employee_id) {
        $query = "SELECT 
                    COUNT(*) as total_notificaciones,
                    SUM(CASE WHEN is_read = 'UNREAD' THEN 1 ELSE 0 END) as no_leidas,
                    SUM(CASE WHEN is_read = 'READ' THEN 1 ELSE 0 END) as leidas,
                    SUM(CASE WHEN notification_type = 'ORDER_READY' THEN 1 ELSE 0 END) as pedidos_listos,
                    SUM(CASE WHEN notification_type = 'ORDER_CONFIRMED' THEN 1 ELSE 0 END) as pedidos_confirmados,
                    SUM(CASE WHEN notification_type = 'ORDER_CANCELLED' THEN 1 ELSE 0 END) as pedidos_cancelados
                 FROM notifications
                 WHERE employee_id = :employee_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employee_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Elimina notificaciones antiguas (más de 30 días)
     */
    public function deleteOldNotifications($days = 30) {
        $query = "DELETE FROM notifications 
                 WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':days', $days);
        return $stmt->execute();
    }

    /**
     * Obtiene notificaciones por rango de fechas
     */
    public function getByDateRange($start_date, $end_date, $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT n.*, 
                        e.employe_name as empleado_nombre,
                        o.total_amount as pedido_total,
                        os.status_name as estado_pedido
                 FROM notifications n
                 LEFT JOIN employees e ON n.employee_id = e.id_employe
                 LEFT JOIN orders o ON n.order_id = o.id_order
                 LEFT JOIN order_statuses os ON o.order_statuses_id_status = os.id_status
                 WHERE DATE(n.created_at) BETWEEN :start_date AND :end_date
                 ORDER BY n.created_at DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 
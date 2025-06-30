<?php
namespace App\Models;

use PDO;
use Exception;

class Order extends BaseModel
{
    protected $table_name = 'orders';
    protected $primary_key = 'id_order';

    public function getAll($page = 1, $limit = 10, $orderBy = null)
    {
    $offset = ($page - 1) * $limit;
    $query = "SELECT * FROM orders";
    if ($orderBy) {
        $query .= " ORDER BY $orderBy";
    }
    $query .= " LIMIT $limit OFFSET $offset";
    $stmt = $this->conn->query($query);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id_order = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO orders (created_date, total_amount, waiter_id, order_statuses_id_status, table_sessions_id_session)
             VALUES (NOW(), ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['total_amount'],
            $data['waiter_id'],
            $data['order_statuses_id_status'],
            $data['table_sessions_id_session']
        ]);
        return $this->getById($this->conn->lastInsertId());
    }

    public function update($id, $data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE orders SET total_amount = ?, waiter_id = ?, order_statuses_id_status = ?, table_sessions_id_session = ? WHERE id_order = ?"
        );
        $stmt->execute([
            $data['total_amount'],
            $data['waiter_id'],
            $data['order_statuses_id_status'],
            $data['table_sessions_id_session'],
            $id
        ]);
        return $this->getById($id);
    }

    public function delete($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM orders WHERE id_order = ?");
        return $stmt->execute([$id]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->conn->prepare("UPDATE orders SET order_statuses_id_status = ? WHERE id_order = ?");
        $stmt->execute([$status, $id]);
        return $this->getById($id);
    }
}
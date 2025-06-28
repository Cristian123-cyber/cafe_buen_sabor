<?php
namespace App\Models;

class SaleOrder extends BaseModel
{
    protected $table_name = 'sales_has_orders';
    protected $primary_key = null; // No hay clave primaria simple

    public function all()
    {
        return $this->getAll(1, 1000);
    }

    public function findBySale($sale_id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE sales_id_sale = :sale_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findByOrder($order_id)
    {
        $query = "SELECT * FROM {$this->table_name} WHERE orders_id_order = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function add($sale_id, $order_id)
    {
        $query = "INSERT INTO {$this->table_name} (sales_id_sale, orders_id_order) VALUES (:sale_id, :order_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }

    public function remove($sale_id, $order_id)
    {
        $query = "DELETE FROM {$this->table_name} WHERE sales_id_sale = :sale_id AND orders_id_order = :order_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }
}
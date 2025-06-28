<?php

namespace App\Controllers;

use App\Models\SaleOrder;
use Exception;

class SaleOrderController
{
    public function index()
    {
        try {
            $model = new SaleOrder();
            $data = $model->all();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function bySale($sale_id)
    {
        try {
            $model = new SaleOrder();
            $data = $model->findBySale($sale_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function byOrder($order_id)
    {
        try {
            $model = new SaleOrder();
            $data = $model->findByOrder($order_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function add()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $model = new SaleOrder();
            $ok = $model->add($data['sales_id_sale'], $data['orders_id_order']);
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function remove()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $model = new SaleOrder();
            $ok = $model->remove($data['sales_id_sale'], $data['orders_id_order']);
            echo json_encode(['success' => $ok]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
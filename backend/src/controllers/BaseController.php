<?php

namespace App\Controllers;

class BaseController {
    protected $db;
    protected $model;

    public function __construct($db) {
        $this->db = $db;
    }

    protected function handleResponse($success, $message, $data = [], $httpCode = 200)
    {
        http_response_code($httpCode);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    protected function getRequestData() {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function validateRequiredFields($data, $requiredFields) {
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missingFields[] = $field;
            }
        }
        return $missingFields;
    }

    protected function handlePagination($page, $limit) {
        $page = max(1, intval($page));
        $limit = max(1, min(100, intval($limit))); // Limitar máximo 100 registros por página
        return [$page, $limit];
    }
}
?> 
<?php
namespace App\Controllers;

use App\Models\Table;
use Exception;

class TablesController extends BaseController
{
    private $tableModel;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel = new Table();
    }

    // Listar todas las mesas
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $tables = $this->tableModel->all();
            $this->handleResponse(true, 'Mesas obtenidas correctamente.', $tables);
            
        }, 'Error al obtener las mesas');
    }

    // Obtener una mesa por ID
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }
            
            $table = $this->tableModel->find($tableId);
            if ($table) {
                $this->handleResponse(true, 'Mesa obtenida correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
            
        }, 'Error al obtener la mesa');
    }

    // Crear una nueva mesa
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos
            $requiredFields = ['table_number'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Sanitizar y validar datos
            $tableData = [
                'table_number' => $this->validateId($data['table_number']),
                'table_status' => $this->sanitizeString($data['table_status'] ?? 'FREE')
            ];

            // Validar número de mesa
            if (!$tableData['table_number']) {
                $this->handleResponse(false, 'Número de mesa inválido', [], 400);
                return;
            }

            // Validar estado de mesa
            $validStatuses = ['FREE', 'OCCUPIED', 'INACTIVE'];
            if (!in_array($tableData['table_status'], $validStatuses)) {
                $this->handleResponse(false, 'Estado de mesa inválido. Debe ser: FREE, OCCUPIED o INACTIVE', [], 400);
                return;
            }

            $table = $this->tableModel->createTable($tableData);
            if ($table) {
                $this->handleResponse(true, 'Mesa creada correctamente.', $table, 201);
            } else {
                $this->handleResponse(false, 'Error al crear la mesa', [], 500);
            }
            
        }, 'Error al crear la mesa');
    }

    // Actualizar una mesa
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }
            
            $data = $this->getRequestData();
            
            if (empty($data)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            // Sanitizar y validar datos
            $updateData = [];
            
            if (isset($data['table_number'])) {
                $tableNumber = $this->validateId($data['table_number']);
                if (!$tableNumber) {
                    $this->handleResponse(false, 'Número de mesa inválido', [], 400);
                    return;
                }
                $updateData['table_number'] = $tableNumber;
            }
            
            if (isset($data['table_status'])) {
                $status = $this->sanitizeString($data['table_status']);
                $validStatuses = ['FREE', 'OCCUPIED', 'INACTIVE'];
                if (!in_array($status, $validStatuses)) {
                    $this->handleResponse(false, 'Estado de mesa inválido. Debe ser: FREE, OCCUPIED o INACTIVE', [], 400);
                    return;
                }
                $updateData['table_status'] = $status;
            }

            $table = $this->tableModel->updateTable($tableId, $updateData);
            if ($table) {
                $this->handleResponse(true, 'Mesa actualizada correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
            
        }, 'Error al actualizar la mesa');
    }

    // Eliminar una mesa
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }
            
            $deleted = $this->tableModel->deleteTable($tableId);
            if ($deleted) {
                $this->handleResponse(true, 'Mesa eliminada correctamente.', []);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
            
        }, 'Error al eliminar la mesa');
    }

    // Cambiar el estado de la mesa (ejemplo: ocupar/liberar)
    public function updateStatus($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }
            
            $data = $this->getRequestData();
            
            // Validar campo requerido
            if (!isset($data['table_status'])) {
                $this->handleResponse(false, 'Campo table_status es requerido', [], 400);
                return;
            }

            $status = $this->sanitizeString($data['table_status']);
            $validStatuses = ['FREE', 'OCCUPIED', 'INACTIVE'];
            
            if (!in_array($status, $validStatuses)) {
                $this->handleResponse(false, 'Estado de mesa inválido. Debe ser: FREE, OCCUPIED o INACTIVE', [], 400);
                return;
            }

            $table = $this->tableModel->updateTable($tableId, ['table_status' => $status]);
            if ($table) {
                $this->handleResponse(true, 'Estado de mesa actualizado correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
            
        }, 'Error al actualizar el estado de la mesa');
    }

    // Buscar mesa por token QR
    public function findByToken($qr_token)
    {
        return $this->executeWithErrorHandling(function() use ($qr_token) {
            // Sanitizar token
            $token = $this->sanitizeString($qr_token);
            
            if (empty($token)) {
                $this->handleResponse(false, 'Token QR requerido', [], 400);
                return;
            }
            
            $table = $this->tableModel->findByToken($token);
            if ($table) {
                $this->handleResponse(true, 'Mesa encontrada correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
            
        }, 'Error al buscar la mesa por token');
    }
}
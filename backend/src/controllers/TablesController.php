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

            


            // --- Parámetros de Filtrado ---
            // Leemos los nuevos filtros desde la URL (query string)
            $filters = [
                'term' => isset($_GET['term']) ? $this->sanitizeString($_GET['term']) : null,
                'state' => isset($_GET['state']) ? $this->sanitizeString($_GET['state']) : null
            ];
            $tables = $this->tableModel->all($filters);
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
    //traer token de la mesa por id
    public function getQrToken($id)
    {
    return $this->executeWithErrorHandling(function() use ($id) {
        $tableId = $this->validateId($id);
        if (!$tableId) {
            $this->handleResponse(false, 'ID de mesa inválido', [], 400);
            return;
        }

        $table = $this->tableModel->find($tableId);
        if ($table && isset($table['qr_token'])) {
            $this->handleResponse(true, 'Token QR obtenido correctamente.', [
                'qr_token' => $table['qr_token'],
                'token_expiration' => $table['token_expiration'] ?? null
            ]);
        } else {
            $this->handleResponse(false, 'Mesa no encontrada o sin token QR', [], 404);
        }
        }, 'Error al obtener el token QR de la mesa');
    }
    function generarTokenSeguro() {
        return bin2hex(random_bytes(32));
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
                'table_status' => $this->sanitizeString($data['table_status'] ?? 'FREE'),
                'qr_token' => $this->generarTokenSeguro(),
                'token_expiration' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
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
                $validStatuses = ['FREE', 'OCCUPIED', 'INACTIVE', 'DELETED'];
               
            if (!in_array($status, $validStatuses)) {
            $this->handleResponse(false, 'Estado de mesa inválido. Debe ser: FREE, OCCUPIED, INACTIVE o DELETED', [], 400);
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

            // Borrado lógico: cambiar estado a DELETED
            $table = $this->tableModel->updateTable($tableId, ['table_status' => 'DELETED']);
            if ($table) {
                $this->handleResponse(true, 'Mesa eliminada correctamente (borrado lógico).', $table);
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
    // Desactivar una mesa (cambiar estado a INACTIVE)
    public function deactivate($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }

            $table = $this->tableModel->updateTable($tableId, ['table_status' => 'INACTIVE']);
            if ($table) {
                $this->handleResponse(true, 'Mesa desactivada correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
        }, 'Error al desactivar la mesa');
    }

    // Activar una mesa (cambiar estado a FREE)
    public function activate($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            $tableId = $this->validateId($id);
            if (!$tableId) {
                $this->handleResponse(false, 'ID de mesa inválido', [], 400);
                return;
            }

            $table = $this->tableModel->updateTable($tableId, ['table_status' => 'FREE']);
            if ($table) {
                $this->handleResponse(true, 'Mesa activada correctamente.', $table);
            } else {
                $this->handleResponse(false, 'Mesa no encontrada', [], 404);
            }
        }, 'Error al activar la mesa');
    }
}
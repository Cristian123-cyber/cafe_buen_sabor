<?php

namespace App\Controllers;

use App\Models\Table;
use App\Models\TableSession;
use Firebase\JWT\JWT;
use Exception;

class TableSessionController extends BaseController
{
    private $tableModel;
    private $tableSessionModel;

    public function __construct()
    {
        parent::__construct();
        $this->tableModel = new Table();
        $this->tableSessionModel = new TableSession();
    }

    /**
     * Valida un token de QR y un ID de mesa, inicia o recupera una sesión de mesa
     * y devuelve un JWT de sesión para el cliente.
     */
    public function validateQrAndStartSession()
    {
        return $this->executeWithErrorHandling(function() {
            // 1. Recibir y validar campos requeridos del POST
            $input = $this->getRequestData();

            $missingFields = $this->validateRequiredFields($input, ['qr_token', 'id_table']);
            
            if (!empty($missingFields)) {
               $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
               return;
           }
            // 2. Sanitizar y validar los datos de entrada
            $qrToken = $this->sanitizeString($input['qr_token']);
            $idTable = filter_var($input['id_table'], FILTER_VALIDATE_INT);

            if ($idTable === false) {
                $this->handleValidationError('El ID de la mesa no es válido.');
                return;
            }

            // 3. Validar el QR y el ID de la mesa usando nuestro nuevo método en el modelo.
            $tableData = $this->tableModel->validateQRTokenTable($qrToken, $idTable);

            if (!$tableData || $tableData === false) {
                // Mensaje más específico para el usuario.
                $this->handleResponse(false, 'Código QR inválido.', [], 404, 'E_INVALID_QR');
                return;
            }

            // 4. Buscar una sesión activa para esta mesa o crear una nueva
            $session = $this->tableSessionModel->findActiveByTableId($tableData['id_table']);
            
            if (!$session || $session === false || empty($session)) {
                // Si no hay sesión activa, creamos una nueva.
                $sessionId = $this->tableSessionModel->createSession($tableData['id_table']);
                if (!$sessionId || $sessionId === false) {
                    // Si la creación falla, es un error del servidor.
                    $this->handleResponse(false, 'No se pudo iniciar la sesión de la mesa.', [], 500);
                    return;
                }
                $session = ['id_session' => $sessionId, 'id_table' => $tableData['id_table']];
            }

            // 5. Generar el JWT de Sesión de Mesa (Session-JWT)
            $secretKey = $_ENV['JWT_SECRET'];
            $issuedAt = time();
            $sessionDurationHours = $_ENV['JWT_CLIENT_SESSION_EXPIRATION_HOURS'] ?? 3;
            $expire = $issuedAt + ($sessionDurationHours * 60 * 60);

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expire,
                'data' => [
                    'type'      => 'client_session',
                    'sessionId' => (int)$session['id_session'],
                    'tableId'   => (int)$session['id_table']
                ]
            ];

            $sessionJwt = JWT::encode($payload, $secretKey, 'HS256');

            // 6. Respuesta exitosa con el Session-JWT y la info de la mesa
            $this->handleResponse(true, 'Sesión de mesa validada.', [
                'session_token' => $sessionJwt,
                'table_info' => [
                    'id'     => (int)$tableData['id_table'],
                    'number' => (int)$tableData['table_number']
                ]
            ], 200);

        }, 'Error al validar la sesión de mesa');
    }

    // ... otros métodos del controlador ...




    

    /**
     * Obtener una sesión de mesa por su ID
     * Endpoint: GET /api/table-sessions/{id}
     */
    public function show($id)
    {
        try {
            $model = new TableSession();
            $data = $model->find($id);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sesión no encontrada']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Crear una nueva sesión de mesa
     * Endpoint: POST /api/table-sessions
     * Body esperado: { stared_at, ended_at, session_status, tables_id_table }
     */
    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $model = new TableSession();
            $session = $model->createSession($data);
            echo json_encode(['success' => true, 'data' => $session]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar una sesión de mesa existente
     * Endpoint: PUT /api/table-sessions/{id}
     * Body esperado: { stared_at, ended_at, session_status, tables_id_table }
     */
    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $model = new TableSession();
            $session = $model->updateSession($id, $data);
            if ($session) {
                echo json_encode(['success' => true, 'data' => $session]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sesión no encontrada']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar una sesión de mesa por su ID
     * Endpoint: DELETE /api/table-sessions/{id}
     */
    public function delete($id)
    {
        try {
            $model = new TableSession();
            $deleted = $model->deleteSession($id);
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Sesión eliminada']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sesión no encontrada']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Listar todas las sesiones de una mesa específica
     * Endpoint: GET /api/table-sessions/table/{table_id}
     */
    public function byTable($table_id)
    {
        try {
            $model = new TableSession();
            $data = $model->findByTable($table_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
      
    // Obtener todas las sesiones de mesa (con datos de la mesa asociada)
    public function allWithTable()
    {
        try {
            $model = new TableSession();
            $data = $model->allWithTable();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    //    Cerrar una sesión de mesa
    public function close($id)
    {
        try {
            $model = new TableSession();
            $ok = $model->closeSession($id);
            if ($ok) {
                echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Sesión no encontrada']);
            }
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    //    Obtener sesiones por estado (con datos de la mesa asociada)
    public function byStatusWithTable($status)
    {
        try {
            $model = new TableSession();
            $data = $model->byStatusWithTable($status);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}


<?php

namespace App\Controllers;

use App\Models\TableSession;
use Exception;

/**
 * Controlador para gestionar las sesiones de las mesas (table_sessions)
 */
class TableSessionController
{
    /**
     * Listar todas las sesiones de mesas
     * Endpoint: GET /api/table-sessions
     */
    public function index()
    {
        try {
            $model = new TableSession();
            $data = $model->all();
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

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
}
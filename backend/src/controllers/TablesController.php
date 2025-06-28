<?php
namespace App\Controllers;

use App\Models\Table;
use Exception;

class TablesController
{
    // Listar todas las mesas
    public function index()
    {
        try {
            $tableModel = new Table();
            $tables = $tableModel->all();
            echo json_encode([
                'success' => true,
                'data' => $tables
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener las mesas',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Obtener una mesa por ID
    public function show($id)
    {
        try {
            $tableModel = new Table();
            $table = $tableModel->find($id);
            if ($table) {
                echo json_encode([
                    'success' => true,
                    'data' => $table
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener la mesa',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Crear una nueva mesa
    public function store()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tableModel = new Table();
            $table = $tableModel->createTable($data);
            echo json_encode([
                'success' => true,
                'data' => $table
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear la mesa',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Actualizar una mesa
    public function update($id)
    {
        try {
             $data = json_decode(file_get_contents('php://input'), true);
        $tableModel = new Table();
        $table = $tableModel->updateTable($id, $data);
        if ($table) {
                
                echo json_encode([
                    'success' => true,
                    'data' => $table
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la mesa',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Eliminar una mesa
    public function delete($id)
    {
        try {
            $tableModel = new Table();
            $deleted = $tableModel->deleteTable($id);
            if ($deleted) {
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Mesa eliminada'
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la mesa',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Cambiar el estado de la mesa (ejemplo: ocupar/liberar)
    public function updateStatus($id)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $tableModel = new Table();
            if (isset($data['table_status'])) {
                $table = $tableModel->updateTable($id, ['table_status' => $data['table_status']]);
                if ($table) {
                    echo json_encode([
                        'success' => true,
                        'data' => $table
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Falta el campo table_status'
                    ]);
                }
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el estado de la mesa',
                'error' => $e->getMessage()
            ]);
        }
    }

    // Buscar mesa por token QR
    public function findByToken($qr_token)
    {
        try {
            $tableModel = new Table();
            $table = $tableModel->findByToken($qr_token);
            if ($table) {
                echo json_encode([
                    'success' => true,
                    'data' => $table
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Mesa no encontrada'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Error al buscar la mesa por token',
                'error' => $e->getMessage()
            ]);
        }
    }
}
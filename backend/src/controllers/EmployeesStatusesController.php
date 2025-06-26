<?php

namespace App\controllers;

use App\Models\EmployeesStatuses;

class EmployeesStatusesController extends BaseController
{
    private $statusesModel;

    public function __construct()
    {
        $this->statusesModel = new EmployeesStatuses();
    }

    public function index()
    {
        $statuses = $this->statusesModel->getAll();
        $this->handleResponse(true, 'Estados obtenidos correctamente.', $statuses);
    }

    public function show($id)
    {
        $status = $this->statusesModel->getById($id);
        if ($status) {
            $this->jsonResponse([
                'message' => 'Estado obtenido correctamente.',
                'data' => $status
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'Estado no encontrado'
            ], 404);
        }
    }

    public function store()
    {
        $data = $this->getRequestData();

        if (empty($data['status_name'])) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'El campo status_name es requerido'
            ], 400);
        }

        $statusId = $this->statusesModel->create($data);

        if ($statusId) {
            $status = $this->statusesModel->getById($statusId);
            $this->jsonResponse([
                'message' => 'Estado creado exitosamente',
                'data' => $status
            ], 201);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo crear el estado'
            ], 500);
        }
    }

    public function update($id)
    {
        $data = $this->getRequestData();

        $status = $this->statusesModel->getById($id);
        if (!$status) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Estado no encontrado'
            ], 404);
        }

        $data['status_name'] = $data['status_name'] ?? $status['status_name'];
        $data['status_desc'] = $data['status_desc'] ?? $status['status_desc'];

        $updated = $this->statusesModel->update($id, $data);

        if ($updated) {
            $status = $this->statusesModel->getById($id);
            $this->jsonResponse([
                'message' => "Estado $id actualizado exitosamente",
                'data' => $status
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo actualizar el estado'
            ], 500);
        }
    }

    public function delete($id)
    {
        $status = $this->statusesModel->getById($id);
        if (!$status) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Estado no encontrado'
            ], 404);
        }

        $deleted = $this->statusesModel->delete($id);

        if ($deleted) {
            $this->jsonResponse([
                'message' => "Estado $id eliminado exitosamente"
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo eliminar el estado'
            ], 500);
        }
    }
}
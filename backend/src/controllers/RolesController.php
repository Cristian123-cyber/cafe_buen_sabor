<?php

namespace App\controllers;

use App\models\Roles;

class RolesController extends BaseController
{
    private $rolesModel;

    public function __construct()
    {
        $this->rolesModel = new Roles();
    }

    // Obtener todos los roles
    public function index()
    {
        $roles = $this->rolesModel->getAll();
        $this->handleResponse(true, 'Roles obtenidos correctamente.', $roles);
    }

    // Obtener un rol por ID
    public function show($id)
    {
        $rol = $this->rolesModel->getById($id);
        if ($rol) {
            $this->jsonResponse([
                'message' => 'Rol obtenido correctamente.',
                'data' => $rol
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'Rol no encontrado'
            ], 404);
        }
    }

    // Crear un nuevo rol
    public function store()
    {
        $data = $this->getRequestData();

        if (empty($data['rol_name'])) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'El campo rol_name es requerido'
            ], 400);
        }

        $rolId = $this->rolesModel->create($data);

        if ($rolId) {
            $rol = $this->rolesModel->getById($rolId);
            $this->jsonResponse([
                'message' => 'Rol creado exitosamente',
                'data' => $rol
            ], 201);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo crear el rol'
            ], 500);
        }
    }

    // Actualizar un rol existente
    public function update($id)
    {
        $data = $this->getRequestData();

        $rol = $this->rolesModel->getById($id);
        if (!$rol) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $data['rol_name'] = $data['rol_name'] ?? $rol['rol_name'];

        $updated = $this->rolesModel->update($id, $data);

        if ($updated) {
            $rol = $this->rolesModel->getById($id);
            $this->jsonResponse([
                'message' => "Rol $id actualizado exitosamente",
                'data' => $rol
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo actualizar el rol'
            ], 500);
        }
    }

    // Eliminar un rol
    public function delete($id)
    {
        $rol = $this->rolesModel->getById($id);
        if (!$rol) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $deleted = $this->rolesModel->delete($id);

        if ($deleted) {
            $this->jsonResponse([
                'message' => "Rol $id eliminado exitosamente"
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo eliminar el rol'
            ], 500);
        }
    }
}
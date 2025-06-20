<?php

namespace App\controllers;

use App\models\Employees;

class EmployeesController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Employees();
    }

    // Obtener todos los usuarios
    public function index()
    {
        $usuarios = $this->usuarioModel->getAll();
        $this->handleResponse(true, 'empleados obtenidos correctamente.', $usuarios);
    }

    // Obtener un usuario por ID
    public function show($id)
    {
        $usuario = $this->usuarioModel->getById($id);
        if ($usuario) {
            $this->jsonResponse([
                'message' => 'Usuario obtenido correctamente.',
                'data' => $usuario
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
    }

    // Crear un nuevo usuario
    public function store()
    {
        $data = $this->getRequestData();

        $requiredFields = [
            'employe_name', 'employe_email', 'password', 'employees_rol_id_rol', 'employees_statuses_id_status'
        ];
        $missingFields = $this->validateRequiredFields($data, $requiredFields);

        if (!empty($missingFields)) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Campos requeridos faltantes: ' . implode(', ', $missingFields)
            ], 400);
        }

        // Hashear la contraseña antes de guardar
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $usuarioId = $this->usuarioModel->create($data);

        if ($usuarioId) {
            $usuario = $this->usuarioModel->getById($usuarioId);
            $this->jsonResponse([
                'message' => 'Usuario creado exitosamente',
                'data' => $usuario
            ], 201);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo crear el usuario'
            ], 500);
        }
    }

    // Actualizar un usuario existente
    public function update($id)
    {
        $data = $this->getRequestData();

        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // Si se envía una nueva contraseña, hashearla
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
        } else {
            $data['password'] = $usuario['contrasena_hash'];
        }

        // Mantener los campos opcionales si no se envían
        $data['employe_name'] = $data['employe_name'] ?? $usuario['employe_name'];
        $data['employe_email'] = $data['employe_email'] ?? $usuario['employe_email'];
        $data['employees_rol_id_rol'] = $data['employees_rol_id_rol'] ?? $usuario['employees_rol_id_rol'];
        $data['employees_statuses_id_status'] = $data['employees_statuses_id_status'] ?? $usuario['employees_statuses_id_status'];

        $updated = $this->usuarioModel->update($id, $data);

        if ($updated) {
            $usuario = $this->usuarioModel->getById($id);
            $this->jsonResponse([
                'message' => "Usuario $id actualizado exitosamente",
                'data' => $usuario
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo actualizar el usuario'
            ], 500);
        }
    }

    // Eliminar(desactivar) un usuario
     public function delete($id)
    {
        $usuario = $this->usuarioModel->getById($id);
        if (!$usuario) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $updated = $this->usuarioModel->update($id, ['activo' => 0]);

        if ($updated) {
            $this->jsonResponse([
                'message' => "Usuario $id desactivado exitosamente"
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'No se pudo desactivar el usuario'
            ], 500);
        }
    }
}
<?php

namespace App\Controllers;

use App\Models\Employees;
use App\Controllers\AuthController;

class EmployeesController extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        parent::__construct();
        $this->usuarioModel = new Employees();
    }

    // Obtener todos los usuarios
    public function index()
    {
        return $this->executeWithErrorHandling(function () {
            // --- Parámetros de Paginación ---
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $perPage = isset($_GET['perPage']) ? max(1, intval($_GET['perPage'])) : 10;
            $orderBy = isset($_GET['orderBy']) ? $this->sanitizeString($_GET['orderBy']) : null;


            // --- Parámetros de Filtrado ---
            // Leemos los nuevos filtros desde la URL (query string)
            $filters = [
                'term' => isset($_GET['term']) ? $this->sanitizeString($_GET['term']) : null,
                'role' => isset($_GET['role']) ? $this->sanitizeString($_GET['role']) : null,
                'state' => isset($_GET['state']) ? $this->sanitizeString($_GET['state']) : null
            ];


            // Pasamos los filtros a ambos métodos del modelo
            $usuarios = $this->usuarioModel->getAll($page, $perPage, $orderBy, $filters);
            $total = $this->usuarioModel->getTotalCount($filters); // ¡Importante pasar los filtros aquí también!

            $lastPage = ($perPage > 0) ? (int) ceil($total / $perPage) : 1;

            $response = [
                'total' => $total,
                'last_page' => $lastPage,
                'employees' => $usuarios
            ];

            $this->handleResponse(true, 'Empleados obtenidos correctamente.', $response);
        }, 'Error al obtener los empleados');
    }

    // Filtrar usuarios por rol y estado
    public function filter()
    {
        return $this->executeWithErrorHandling(function () {
            $rol = isset($_GET['rol']) ? $this->validateId($_GET['rol']) : null;
            $status = isset($_GET['status']) ? $this->validateId($_GET['status']) : null;

            $usuarios = $this->usuarioModel->filterBy($rol, $status);
            $this->handleResponse(true, 'Empleados filtrados correctamente.', $usuarios);
        }, 'Error al filtrar empleados');
    }
    // Obtener un usuario por ID
    public function show($id)
    {
        return $this->executeWithErrorHandling(function () use ($id) {
            // Validar ID
            $employeeId = $this->validateId($id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }

            $usuario = $this->usuarioModel->getById($employeeId);
            if ($usuario) {
                $this->handleResponse(true, 'Empleado obtenido correctamente.', $usuario);
            } else {
                $this->handleResponse(false, 'Empleado no encontrado', [], 404);
            }
        }, 'Error al obtener el empleado');
    }

    // Crear un nuevo usuario
    public function store()
    {
        return $this->executeWithErrorHandling(function () {
            $data = $this->getRequestData();

            $requiredFields = [
                'employe_name',
                'employe_email',
                'password',
                'employees_rol_id_rol',
                'employees_statuses_id_status'
            ];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);

            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Primero sanitiza y valida los datos
            $employeeData = [
                'employe_name' => $this->sanitizeString($data['employe_name']),
                'employe_email' => $this->validateEmail($data['employe_email']),
                'password' => $this->sanitizeString($data['password']),
                'employees_rol_id_rol' => $this->validateId($data['employees_rol_id_rol']),
                'employees_statuses_id_status' => $this->validateId($data['employees_statuses_id_status'])
            ];

            // Validación especial para rol 4 (dispositivo)
            if ($employeeData['employees_rol_id_rol'] == 4) {
                if (!isset($data['table_id_device']) || $this->validateId($data['table_id_device']) === null) {
                    $this->handleResponse(false, 'Para empleados de tipo dispositivo (rol 4) es obligatorio enviar el id de mesa (table_id_device)', [], 400);
                    return;
                }
            }


            // Validar email único
            if ($this->usuarioModel->existsEmail($employeeData['employe_email'])) {
                $this->handleResponse(false, 'El email ya está registrado', [], 400);
                return;
            }

            if (isset($data['employee_cc'])) {
                $employeeData['employee_cc'] = ($data['employee_cc'] === null || $data['employee_cc'] === '') ? null : $this->sanitizeString($data['employee_cc']);
            } else {
                $employeeData['employee_cc'] = null;
            }
            // Validar cédula única si viene
            if ($employeeData['employee_cc'] !== null && $this->usuarioModel->existsCedula($employeeData['employee_cc'])) {
                $this->handleResponse(false, 'La cédula ya está registrada', [], 400);
                return;
            }

            if (isset($data['table_id_device'])) {
                $employeeData['table_id_device'] = $this->validateId($data['table_id_device']);
            }

            if (!$employeeData['employe_email']) {
                $this->handleResponse(false, 'Formato de email inválido', [], 400);
                return;
            }

            if (strlen($employeeData['password']) < 6) {
                $this->handleResponse(false, 'La contraseña debe tener al menos 6 caracteres', [], 400);
                return;
            }

            if (!$employeeData['employees_rol_id_rol'] || !$employeeData['employees_statuses_id_status']) {
                $this->handleResponse(false, 'Rol y estado de empleado son requeridos', [], 400);
                return;
            }

            $employeeData['password'] = password_hash($employeeData['password'], PASSWORD_DEFAULT);

            $usuarioId = $this->usuarioModel->create($employeeData);

            if ($usuarioId) {
                $usuario = $this->usuarioModel->getById($usuarioId);
                $this->handleResponse(true, 'Empleado creado exitosamente', $usuario, 201);
            } else {
                $this->handleResponse(false, 'No se pudo crear el empleado', [], 500);
            }
        }, 'Error al crear el empleado');
    }
    // Actualizar un usuario existente

    public function update($id)
    {
        return $this->executeWithErrorHandling(function () use ($id) {
            // Validar ID
            $employeeId = $this->validateId($id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }

            $data = $this->getRequestData();

            $usuario = $this->usuarioModel->getById($employeeId);
            if (!$usuario) {
                $this->handleResponse(false, 'Empleado no encontrado', [], 404);
                return;
            }

            // Sanitizar y validar datos de actualización
            $updateData = [];

            if (isset($data['employe_name'])) {
                $updateData['employe_name'] = $this->sanitizeString($data['employe_name']);
            }

            if (isset($data['employe_email'])) {
                $email = $this->validateEmail($data['employe_email']);
                if (!$email) {
                    $this->handleResponse(false, 'Formato de email inválido', [], 400);
                    return;
                }
                // Validar que no exista en otro usuario
                if ($this->usuarioModel->existsEmail($email, $employeeId)) {
                    $this->handleResponse(false, 'El email ya está registrado por otro usuario', [], 400);
                    return;
                }
                $updateData['employe_email'] = $email;
            }


            if (array_key_exists('employee_cc', $data)) {
                $cedula = $data['employee_cc'] !== '' ? $this->sanitizeString($data['employee_cc']) : null;
                // Validar cédula única si viene y es diferente al actual
                if ($cedula !== null && $cedula !== $usuario['employee_cc'] && $this->usuarioModel->existsCedula($cedula, $employeeId)) {
                    $this->handleResponse(false, 'La cédula ya está registrada por otro usuario', [], 400);
                    return;
                }
                $updateData['employee_cc'] = $cedula;
            }

            if (isset($data['table_id_device'])) {
                $updateData['table_id_device'] = $this->validateId($data['table_id_device']);
            }

            if (isset($data['password']) && !empty($data['password'])) {
                $password = $this->sanitizeString($data['password']);
                if (strlen($password) < 6) {
                    $this->handleResponse(false, 'La contraseña debe tener al menos 6 caracteres', [], 400);
                    return;
                }
                $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if (isset($data['employees_rol_id_rol'])) {
                $roleId = $this->validateId($data['employees_rol_id_rol']);
                if (!$roleId) {
                    $this->handleResponse(false, 'ID de rol inválido', [], 400);
                    return;
                }
                $updateData['employees_rol_id_rol'] = $roleId;
            }

            if (isset($data['employees_statuses_id_status'])) {
                $statusId = $this->validateId($data['employees_statuses_id_status']);
                if (!$statusId) {
                    $this->handleResponse(false, 'ID de estado inválido', [], 400);
                    return;
                }
                $updateData['employees_statuses_id_status'] = $statusId;
            }

            if (empty($updateData)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            $updated = $this->usuarioModel->update($employeeId, $updateData);

            if ($updated) {
                $usuario = $this->usuarioModel->getById($employeeId);
                $this->handleResponse(true, 'Empleado actualizado exitosamente', $usuario);
            } else {
                $this->handleResponse(false, 'No se pudo actualizar el empleado', [], 500);
            }
        }, 'Error al actualizar el empleado');
    }

    // Eliminar(desactivar) un usuario
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function () use ($id) {
            // Validar ID
            $employeeId = $this->validateId($id);
            if (!$employeeId) {
                $this->handleResponse(false, 'ID de empleado inválido', [], 400);
                return;
            }

            $usuario = $this->usuarioModel->getById($employeeId);

            // Veiricar si el usuario que intenta eliminar no es el mismo que solicita la eliminación
            $authController = new AuthController();

            $currentUser = $authController->getCurrentUser();

            if (!$currentUser || $currentUser === null) {
                $this->handleResponse(false, 'No autorizado', [], 401);
                return;
            }

            if ((int) $currentUser['userId'] === $employeeId) {
                $this->handleResponse(false, 'No puedes eliminar tu propio usuario', [], 403);
                return;
            }




            if (!$usuario) {
                $this->handleResponse(false, 'Empleado no encontrado', [], 404);
                return;
            }

            // Cambiar estado a inactivo (ID 3 según la base de datos)
            $updated = $this->usuarioModel->update($employeeId, ['employees_statuses_id_status' => 3]);

            if ($updated) {
                $this->handleResponse(true, 'Empleado desactivado exitosamente', []);
            } else {
                $this->handleResponse(false, 'No se pudo desactivar el empleado', [], 500);
            }
        }, 'Error al desactivar el empleado');
    }
}

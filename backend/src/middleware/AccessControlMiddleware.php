<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AccessControlMiddleware
{
    /**
     * Valida el token JWT y opcionalmente verifica roles o tipo de sesión.
     *
     * @param array $allowedRoles Un array de roles de empleado permitidos (ej. ['Administrador', 'Mesero']). Si está vacío, cualquier empleado es válido.
     * @param bool $allowClientSession Si es true, también permite tokens de sesión de cliente.
     */
    public static function handle(array $allowedRoles = [], bool $allowClientSession = false)
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            self::sendUnauthorized('Token no proporcionado.');
        }

        $jwt = $matches[1];
        $secretKey = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));
            $tokenData = $decoded->data;

            // Determinar el tipo de token
            $isClientSession = isset($tokenData->type) && $tokenData->type === 'client_session';
            $isEmployee = !$isClientSession && isset($tokenData->role);

            // 1. Verificar si se permite sesión de cliente
            if ($isClientSession) {
                if (!$allowClientSession) {
                    throw new Exception('Esta acción no está permitida para sesiones de cliente.');
                }
               
                return; // Acceso concedido para el cliente
            }

            // 2. Verificar si es un empleado
            if ($isEmployee) {
                // Si $allowedRoles está vacío, significa que cualquier empleado puede acceder.
                if (empty($allowedRoles)) {
                    return; // Acceso concedido para cualquier empleado
                }

                // Si se especifican roles, verificar que el rol del usuario esté en la lista
                if (!in_array($tokenData->role, $allowedRoles)) {
                    throw new Exception("Acceso denegado. Rol '{$tokenData->role}' no autorizado.");
                }
                
                
                return; // Acesso concedido para el rol especifico
            }

            // Si no es ni cliente ni empleado, el token es inválido
            throw new Exception('Formato de token desconocido.');

        } catch (Exception $e) {
            self::sendUnauthorized('Acceso no autorizado: ' . $e->getMessage());
        }
    }

    private static function sendUnauthorized(string $message) {
        http_response_code(401);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'message' => $message]);
        exit();
    }
}
<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class AuthMiddleware
{
    /**
     * Verifica que el request contenga un token JWT de empleado válido.
     */
    public static function handle()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            self::sendUnauthorized('Token no proporcionado.');
        }

        $jwt = $matches[1];
        $secretKey = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            // Opcional pero recomendado: Verificar que el token es de un empleado
            if (isset($decoded->data->type) && $decoded->data->type === 'client_session') {
                throw new Exception('Se requiere un token de empleado, no de sesión de cliente.');
            }
            
            // Podrías añadir más validaciones, como verificar el rol del usuario contra una lista de roles permitidos.
            // Por ejemplo: self::checkRole($decoded->data->role, ['Administrador', 'Mesero']);

            // Hacemos los datos del usuario disponibles globalmente para los controladores
            $GLOBALS['user_data'] = $decoded->data;

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
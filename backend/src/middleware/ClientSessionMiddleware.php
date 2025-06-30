<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class ClientSessionMiddleware
{
    /**
     * Verifica que el request contenga un token JWT de sesión de cliente válido.
     */
    public static function handle()
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            self::sendUnauthorized('Token de sesión no proporcionado.');
        }

        $jwt = $matches[1];
        $secretKey = $_ENV['JWT_SECRET'];

        try {
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            // ¡Validación Clave! Nos aseguramos de que es un token de cliente
            if (!isset($decoded->data->type) || $decoded->data->type !== 'client_session') {
                throw new Exception('Tipo de token inválido. Se requiere un token de sesión de cliente.');
            }
            
            // Opcional: Podrías verificar si la sesión sigue activa en la BD para máxima seguridad
            // $sessionModel = new \App\Models\TableSession();
            // if (!$sessionModel->isActive($decoded->data->sessionId)) {
            //    throw new Exception('La sesión de mesa ha sido cerrada.');
            // }

        } catch (Exception $e) {
            self::sendUnauthorized('Sesión no autorizada: ' . $e->getMessage());
        }
    }

    private static function sendUnauthorized(string $message) {
        http_response_code(401);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'message' => $message]);
        exit();
    }
}
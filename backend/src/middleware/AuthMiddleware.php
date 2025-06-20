<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class AuthMiddleware
{
    /**
     * Maneja la verificación del token JWT en la petición.
     */
    public static function handle()
    {
        // 1. Obtener la cabecera de autorización
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? null;

        if (!$authHeader) {
            self::unauthorized('Token no proporcionado.');
        }

        // 2. Extraer el token (formato "Bearer <token>")
        // Usamos sscanf que es más seguro que explode para este caso.
        list($jwt) = sscanf($authHeader, 'Bearer %s');

        if (!$jwt) {
            self::unauthorized('Formato de token inválido.');
        }

        try {
            // 3. Decodificar y verificar el token
            $secretKey = $_ENV['JWT_SECRET'];
            // El tercer argumento de decode ahora es un objeto Key
            $decoded = JWT::decode($jwt, new Key($secretKey, 'HS256'));

            // Si se quisiera, aquí se podrían hacer más validaciones,
            // como comprobar si el usuario sigue existiendo en la BD.

            // También se podría hacer que los datos del usuario estén
            // disponibles globalmente para los controladores, pero para
            // este caso, con validar el token es suficiente.
            return true;

        } catch (ExpiredException $e) {
            self::unauthorized('Token expirado.');
        } catch (Exception $e) {
            // Esto captura otros errores de JWT (firma inválida, malformado, etc.)
            self::unauthorized('Token inválido.');
        }
    }

    /**
     * Envía una respuesta de error 401 Unauthorized y termina la ejecución.
     * @param string $message Mensaje de error específico.
     */
    private static function unauthorized($message)
    {
        http_response_code(401);
        // Es una buena práctica no revelar detalles internos en los mensajes de error.
        echo json_encode([
            'success' => false,
            'message' => 'Acceso no autorizado.',
            'error' => $message
        ]);
        exit();
    }
} 
<?php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Exception;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        try {
            // 1. Recibir datos del POST (en formato JSON)
            $input = $this->getRequestData();
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';

            // 2. Validar campos
            $requiredFields = ['email', 'password'];
            $missingFields = $this->validateRequiredFields($input, $requiredFields);
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Email y contraseña son requeridos.', [], 400);
                return;
            }

            // 3. Buscar usuario en la base de datos
            $user = $this->userModel->findByEmail($email);

            // 4. Verificar si el usuario existe y la contraseña es correcta
            if (!$user || !password_verify($password, $user['password'])) {
                $this->handleResponse(false, 'Credenciales inválidas.', [], 401);
                return;
            }

            // 5. Si todo es correcto, crear el JWT
            $secretKey  = $_ENV['JWT_SECRET'];
            $issuedAt   = time();
            $expire     = $issuedAt + ($_ENV['JWT_EXPIRATION_HOURS'] * 60 * 60); // expira en N horas

            $payload = [
                'iat'  => $issuedAt,         // Issued at: momento en que se generó el token
                'exp'  => $expire,           // Expire: momento en que expira el token
                'data' => [                  // Datos personalizados
                    'userId' => $user['id_employe'], // Corregido: PK es id_employe
                    'email' => $user['employe_email'], // Corregido: email es employe_email
                    'role'   => $user['rol_name']  // Corregido: rol viene del JOIN
                ]
            ];

            // 6. Codificar el JWT
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // 7. Enviar la respuesta
            $this->handleResponse(true, 'Login exitoso.', ['token' => $jwt], 200);

        } catch (Exception $e) {
            $this->handleResponse(false, 'Ocurrió un error en el servidor.', [
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
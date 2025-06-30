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
        parent::__construct();
        $this->userModel = new User();
    }

    public function login()
    {
        return $this->executeWithErrorHandling(function() {
            // 1. Recibir y validar datos del POST
            $input = $this->getRequestData();
            
            // 2. Validar campos requeridos
            $requiredFields = ['email', 'password'];
            $missingFields = $this->validateRequiredFields($input, $requiredFields);
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // 3. Sanitizar y validar email
            $email = $this->validateEmail($input['email']);
            if (!$email) {
                $this->handleInvalidEmailError();
                return;
            }

            // 4. Validar contraseña
            $password = $this->sanitizeString($input['password']);
            if (strlen($password) < 1) {
                $this->handleValidationError('La contraseña es requerida');
                return;
            }

            // 5. Buscar usuario en la base de datos
            $user = $this->userModel->findByEmail($email);
            if (!$user) {
                $this->handleInvalidCredentialsError();
                return;
            }

            // Validar que el usuario tenga un ID válido
            if (!isset($user['id_employe']) || empty($user['id_employe'])) {
                $this->handleResponse(false, 'Error en los datos del usuario', [], 500, self::ERROR_CODES['CONFIGURATION_ERROR']);
                return;
            }

            // 6. Verificar contraseña
            if (!password_verify($password, $user['password'])) {
                $this->handleInvalidCredentialsError();
                return;
            }

            // 7. Generar JWT
            $secretKey = $_ENV['JWT_SECRET'] ?? '';
            if (empty($secretKey)) {
                $this->handleJwtConfigError();
                return;
            }

            $issuedAt = time();
            $expirationHours = isset($_ENV['JWT_EXPIRATION_HOURS']) ? (int)$_ENV['JWT_EXPIRATION_HOURS'] : 24;
            $expire = $issuedAt + ($expirationHours * 60 * 60);

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expire,
                'data' => [
                    'userId' => (int)$user['id_employe'],
                    'email' => $this->sanitizeString($user['employe_email']),
                    'role' => $this->sanitizeString($user['rol_name'])
                ]
            ];

            // 8. Codificar JWT
            $jwt = JWT::encode($payload, $secretKey, 'HS256');

            // 9. Respuesta exitosa
            $this->handleResponse(true, 'Login exitoso.', [
                'token' => $jwt,
                'user' => [
                    'id' => (int)$user['id_employe'],
                    'name' => $this->sanitizeString($user['employe_name']),
                    'email' => $this->sanitizeString($user['employe_email']),
                    'role' => $this->sanitizeString($user['rol_name'])
                ]
            ], 200);

        }, 'Error en el proceso de autenticación');
    }

} 
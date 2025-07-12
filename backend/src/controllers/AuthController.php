<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\RefreshToken;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthController extends BaseController
{
    private $userModel;
    private $refreshTokenModel;

    private const REFRESH_TOKEN_LIFETIME = 7 * 24 * 60 * 60; // 7 días

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->refreshTokenModel = new RefreshToken();
    }

    public function login()
    {
        return $this->executeWithErrorHandling(function () {
            $input = $this->getRequestData();

            // Validaciones básicas
            $missingFields = $this->validateRequiredFields($input, ['email', 'password']);
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            $email = $this->validateEmail($input['email']);
            if (!$email) {
                $this->handleInvalidEmailError();
                return;
            }

            $password = $this->sanitizeString($input['password']);
            if (strlen($password) < 1) {
                $this->handleValidationError('La contraseña es requerida');
                return;
            }

            // Buscar usuario
            $user = $this->userModel->findByEmail($email);
            

            if (!$user) {
                $this->handleInvalidCredentialsError();
                return;
            }

            // Verificar estado del usuario
            if ((int)$user['status_id'] !== 1) {
                $this->handleResponse(false, 'Empleado inactivo', [], 401);
                return;
            }

            // Verificar contraseña
            if (!password_verify($password, $user['password'])) {
                $this->handleInvalidCredentialsError();
                return;
            }

            // Generar tokens
            $accessToken = $this->generateAccessToken($user);
            $refreshToken = $this->getOrCreateRefreshToken($user['id_employe']);



            if (!$refreshToken) {
                $this->handleResponse(false, 'ERROR INTERNO', [], 500);
            }

            // Configurar cookie
            $this->setRefreshTokenCookie($refreshToken);

            // Respuesta
            $this->handleResponse(true, 'Login exitoso', [
                'access_token' => $accessToken,
                'user' => [
                    'id' => (int)$user['id_employe'],
                    'name' => $this->sanitizeString($user['employe_name']),
                    'email' => $this->sanitizeString($user['employe_email']),
                    'role_name' => $this->sanitizeString($user['rol_name']),
                    'role_id' => (int) $this->sanitizeString($user['role_id']),
                    'table_id' => (int) $this->sanitizeString(($user['table_id_device']))
                ]
            ]);
        });
    }

    private function getOrCreateRefreshToken(int $employeeId)
    {


        $refreshToken = $this->generateRefreshToken();


        $result = $this->saveRefreshToken($employeeId, $refreshToken);


        if (!$result) {
            return false;
        }

        return $refreshToken;
    }

    public function refresh()
    {
        return $this->executeWithErrorHandling(function () {
            $refreshToken = $_COOKIE['refresh_token'] ?? null;

            if (!$refreshToken) {
                $this->handleResponse(false, 'Refresh token requerido', [], 401);
                return;
            }

            // Validar token
            $tokenData = $this->refreshTokenModel->validateToken($refreshToken);
            if (!$tokenData || $tokenData === null) {
                $this->clearRefreshTokenCookie();
                $this->handleResponse(false, 'Refresh token inválido', [], 401);
                return;
            }




            
            // Obtener usuario
            $user = $this->userModel->findById($tokenData['employe_id']);
    
            if (!$user || (int)$user['status_id'] !== 1) {
                $this->refreshTokenModel->revokeToken($refreshToken);
                $this->clearRefreshTokenCookie();
                $this->handleResponse(false, 'Usuario inválido', [], 401);
                return;
            }


            // Generar nuevo access token
            $newAccessToken = $this->generateAccessToken($user);

            $this->handleResponse(true, 'Token refrescado', [
                'access_token' => $newAccessToken,
                'user' => [
                    'id' => (int)$user['id_employe'],
                    'name' => $this->sanitizeString($user['employe_name']),
                    'email' => $this->sanitizeString($user['employe_email']),
                    'role_name' => $this->sanitizeString($user['rol_name']),
                    'role_id' => (int) $this->sanitizeString($user['role_id'])
                ]
            ]);
        });
    }
    public function logout()
    {
        return $this->executeWithErrorHandling(function () {
            $refreshToken = $_COOKIE['refresh_token'] ?? null;

            if ($refreshToken) {
                $this->refreshTokenModel->revokeToken($refreshToken);
            }

            $this->clearRefreshTokenCookie();
            $this->handleResponse(true, 'Sesión cerrada');
        });
    }

    public function me()
    {
        return $this->executeWithErrorHandling(function () {
            $currentUser = $this->getCurrentUser();

            if (!$currentUser || $currentUser === null) {
                $this->handleResponse(false, 'No autorizado', [], 401);
                return;
            }

            $user = $this->userModel->findById($currentUser['userId']);
            if (!$user || (int)$user['status_id'] !== 1) {
                $this->handleResponse(false, 'Usuario invalido', [], 401);
                return;
            }

            $this->handleResponse(true, 'Datos de usuario', [
                'id' => (int)$user['id_employe'],
                'name' => $this->sanitizeString($user['employe_name']),
                'email' => $this->sanitizeString($user['employe_email']),
                'role_name' => $this->sanitizeString($user['rol_name']),
                'role_id' => (int) $this->sanitizeString($user['role_id']),
                'table_id' => (int) $this->sanitizeString($user['table_id_device']),
                
            ]);
        });
    }

    private function generateAccessToken(array $user)
    {
        $secretKey = $_ENV['JWT_SECRET'];
        $issuedAt = time();
        $expire = $issuedAt + (int) ($_ENV['JWT_EXPIRATION_HOURS'] * 3600);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => [
                'type' => 'employee_session',
                'userId' => (int)$user['id_employe'],
                'email' => $this->sanitizeString($user['employe_email']),
                'role' => (int) $this->sanitizeString($user['role_id'])
            ]
        ];



        return JWT::encode($payload, $secretKey, 'HS256');
    }

    private function generateRefreshToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function saveRefreshToken(int $employeeId, string $refreshToken)
    {
        $hashedToken = hash('sha256', $refreshToken);
        $expiresAt = date('Y-m-d H:i:s', time() + self::REFRESH_TOKEN_LIFETIME);

        return $this->refreshTokenModel->createRefreshToken([
            'employe_id' => $employeeId,
            'token_hash' => $hashedToken,
            'expires_at' => $expiresAt
        ]);
    }

    private function setRefreshTokenCookie(string $refreshToken): void
    {
        setcookie('refresh_token', $refreshToken, [
            'expires' => time() + self::REFRESH_TOKEN_LIFETIME,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private function clearRefreshTokenCookie(): void
    {
        setcookie('refresh_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }

    private function getCurrentUser()
    {
        try {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

            if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return null;
            }

            $token = $matches[1];
            $secretKey = $_ENV['JWT_SECRET'];

            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array)$decoded->data;
        } catch (Exception $e) {
            return null;
        }
    }
}

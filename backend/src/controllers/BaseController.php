<?php

namespace App\Controllers;

use Exception;
use PDOException;

class BaseController {
    protected $db;
    protected $model;

    // Códigos de error para diferentes tipos de problemas
    const ERROR_CODES = [
        // Errores de validación
        'VALIDATION_ERROR' => 'VAL001',
        'MISSING_REQUIRED_FIELDS' => 'VAL002',
        'INVALID_EMAIL_FORMAT' => 'VAL003',
        'INVALID_ID_FORMAT' => 'VAL004',
        'INVALID_PRICE_FORMAT' => 'VAL005',
        'PASSWORD_TOO_SHORT' => 'VAL006',
        'INVALID_STATUS_VALUE' => 'VAL007',
        
        // Errores de autenticación
        'INVALID_CREDENTIALS' => 'AUTH001',
        'TOKEN_EXPIRED' => 'AUTH002',
        'TOKEN_INVALID' => 'AUTH003',
        'UNAUTHORIZED_ACCESS' => 'AUTH004',
        'JWT_CONFIG_ERROR' => 'AUTH005',
        
        // Errores de recursos
        'RESOURCE_NOT_FOUND' => 'RES001',
        'RESOURCE_ALREADY_EXISTS' => 'RES002',
        'RESOURCE_DELETION_FAILED' => 'RES003',
        'RESOURCE_UPDATE_FAILED' => 'RES004',
        'RESOURCE_CREATION_FAILED' => 'RES005',
        
        // Errores de base de datos
        'DATABASE_CONNECTION_ERROR' => 'DB001',
        'DATABASE_QUERY_ERROR' => 'DB002',
        'DATABASE_CONSTRAINT_VIOLATION' => 'DB003',
        
        // Errores de entrada
        'INVALID_JSON_FORMAT' => 'INP001',
        'MISSING_REQUEST_DATA' => 'INP002',
        'INVALID_REQUEST_METHOD' => 'INP003',
        
        // Errores del sistema
        'INTERNAL_SERVER_ERROR' => 'SYS001',
        'CONFIGURATION_ERROR' => 'SYS002',
        'SERVICE_UNAVAILABLE' => 'SYS003'
    ];

    public function __construct($db = null) {
        $this->db = $db;
    }

    /**
     * Maneja la respuesta JSON de forma segura
     */
    protected function handleResponse($success, $message, $data = [], $httpCode = 200, $errorCode = null)
    {
        try {
            // Sanitizar el mensaje
            $message = $this->sanitizeString($message);
            
            // Sanitizar los datos si es un array
            if (is_array($data)) {
                $data = $this->sanitizeArray($data);
            }
            
            http_response_code($httpCode);
            header('Content-Type: application/json; charset=UTF-8');
            
            $response = [
                'success' => (bool)$success,
                'message' => $message,
                'data' => $data
            ];
            
            // Agregar código de error si existe
            if (!$success && $errorCode) {
                $response['error_code'] = $errorCode;
            }
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            // Fallback en caso de error en la respuesta
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor',
                'data' => [],
                'error_code' => 'INTERNAL_ERROR'
            ]);
        }
        exit;
    }

    /**
     * Obtiene y sanitiza los datos de la petición
     */
    protected function getRequestData() {
        try {
            $input = file_get_contents('php://input');
            if (empty($input)) {
                return [];
            }
            
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->handleInvalidJsonError();
            }
            
            return $this->sanitizeArray($data ?? []);
            
        } catch (Exception $e) {
            $this->handleResponse(false, 'Error al procesar los datos de entrada', [], 400, self::ERROR_CODES['INVALID_JSON_FORMAT']);
        }
    }

    /**
     * Valida campos requeridos de forma segura
     */
    protected function validateRequiredFields($data, $requiredFields) {
        try {
            if (!is_array($data)) {
                return $requiredFields;
            }
            
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field]) || 
                    (is_string($data[$field]) && trim($data[$field]) === '') ||
                    $data[$field] === null) {
                    $missingFields[] = $field;
                }
            }
            return $missingFields;
            
        } catch (Exception $e) {
            return $requiredFields;
        }
    }

    /**
     * Maneja la paginación de forma segura
     */
    protected function handlePagination($page, $limit) {
        try {
            $page = max(1, intval($page ?? 1));
            $limit = max(1, min(100, intval($limit ?? 10))); // Limitar máximo 100 registros por página
            return [$page, $limit];
            
        } catch (Exception $e) {
            return [1, 10]; // Valores por defecto seguros
        }
    }

    /**
     * Sanitiza una cadena de texto
     */
    protected function sanitizeString($string) {
        // Si es un número, convertirlo a string y retornarlo
        if (is_numeric($string)) {
            return (string)$string;
        }
        
        // Si no es string, convertir a string
        if (!is_string($string)) {
            $string = (string)$string;
        }
        
        // Remover caracteres peligrosos
        $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
        $string = strip_tags($string);
        $string = trim($string);
        
        return $string;
    }

    /**
     * Sanitiza un array recursivamente
     */
    protected function sanitizeArray($array) {
        if (!is_array($array)) {
            // Si es un número, retornarlo como está
            if (is_numeric($array)) {
                return $array;
            }
            return $this->sanitizeString($array);
        }
        
        $sanitized = [];
        foreach ($array as $key => $value) {
            $sanitizedKey = $this->sanitizeString($key);
            
            if (is_array($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeArray($value);
            } elseif (is_numeric($value)) {
                // Mantener números como están
                $sanitized[$sanitizedKey] = $value;
            } else {
                $sanitized[$sanitizedKey] = $this->sanitizeString($value);
            }
        }
        
        return $sanitized;
    }

    /**
     * Valida y sanitiza un ID numérico
     */
    protected function validateId($id) {
        try {
            $id = intval($id);
            return $id > 0 ? $id : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Valida formato de email
     */
    protected function validateEmail($email) {
        try {
            $email = $this->sanitizeString($email);
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Valida formato de precio
     */
    protected function validatePrice($price) {
        try {
            $price = floatval($price);
            return $price >= 0 ? $price : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Maneja errores de base de datos
     */
    protected function handleDatabaseError($e) {
        if ($e instanceof PDOException) {
            // Log del error para debugging (en producción, usar un sistema de logging)
            error_log("Database Error: " . $e->getMessage());
            
            // Determinar el tipo específico de error de BD
            $errorCode = self::ERROR_CODES['DATABASE_QUERY_ERROR'];
            
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errorCode = self::ERROR_CODES['RESOURCE_ALREADY_EXISTS'];
                $this->handleResponse(false, 'El recurso ya existe en el sistema', [], 409, $errorCode);
            } elseif (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                $errorCode = self::ERROR_CODES['DATABASE_CONSTRAINT_VIOLATION'];
                $this->handleResponse(false, 'No se puede eliminar el recurso porque está siendo utilizado', [], 400, $errorCode);
            } else {
                $this->handleResponse(false, 'Error en la base de datos', [], 500, $errorCode);
            }
        } else {
            $this->handleResponse(false, 'Error interno del servidor', [], 500, self::ERROR_CODES['INTERNAL_SERVER_ERROR']);
        }
    }

    /**
     * Maneja errores generales
     */
    protected function handleGeneralError($e) {
        // Log del error para debugging
        error_log("General Error: " . $e->getMessage());
        
        // Respuesta genérica al cliente
        $this->handleResponse(false, 'Error interno del servidor', [], 500, self::ERROR_CODES['INTERNAL_SERVER_ERROR']);
    }

    /**
     * Maneja errores de validación
     */
    protected function handleValidationError($message, $details = []) {
        $this->handleResponse(false, $message, $details, 400, self::ERROR_CODES['VALIDATION_ERROR']);
    }

    /**
     * Maneja errores de campos requeridos faltantes
     */
    protected function handleMissingFieldsError($missingFields) {
        $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), 
            ['missing_fields' => $missingFields], 400, self::ERROR_CODES['MISSING_REQUIRED_FIELDS']);
    }

    /**
     * Maneja errores de formato de email inválido
     */
    protected function handleInvalidEmailError() {
        $this->handleResponse(false, 'Formato de email inválido', [], 400, self::ERROR_CODES['INVALID_EMAIL_FORMAT']);
    }

    /**
     * Maneja errores de ID inválido
     */
    protected function handleInvalidIdError($fieldName = 'ID') {
        $this->handleResponse(false, $fieldName . ' inválido', [], 400, self::ERROR_CODES['INVALID_ID_FORMAT']);
    }

    /**
     * Maneja errores de precio inválido
     */
    protected function handleInvalidPriceError() {
        $this->handleResponse(false, 'Formato de precio inválido', [], 400, self::ERROR_CODES['INVALID_PRICE_FORMAT']);
    }

    /**
     * Maneja errores de contraseña muy corta
     */
    protected function handlePasswordTooShortError($minLength = 6) {
        $this->handleResponse(false, "La contraseña debe tener al menos {$minLength} caracteres", [], 400, self::ERROR_CODES['PASSWORD_TOO_SHORT']);
    }

    /**
     * Maneja errores de recurso no encontrado
     */
    protected function handleResourceNotFoundError($resourceName = 'Recurso') {
        $this->handleResponse(false, $resourceName . ' no encontrado', [], 404, self::ERROR_CODES['RESOURCE_NOT_FOUND']);
    }

    /**
     * Maneja errores de credenciales inválidas
     */
    protected function handleInvalidCredentialsError() {
        $this->handleResponse(false, 'Credenciales inválidas', [], 401, self::ERROR_CODES['INVALID_CREDENTIALS']);
    }

    /**
     * Maneja errores de token expirado
     */
    protected function handleTokenExpiredError() {
        $this->handleResponse(false, 'Token expirado', [], 401, self::ERROR_CODES['TOKEN_EXPIRED']);
    }

    /**
     * Maneja errores de token inválido
     */
    protected function handleTokenInvalidError() {
        $this->handleResponse(false, 'Token inválido', [], 401, self::ERROR_CODES['TOKEN_INVALID']);
    }

    /**
     * Maneja errores de acceso no autorizado
     */
    protected function handleUnauthorizedError() {
        $this->handleResponse(false, 'No autorizado', [], 401, self::ERROR_CODES['UNAUTHORIZED_ACCESS']);
    }

    /**
     * Maneja errores de configuración JWT
     */
    protected function handleJwtConfigError() {
        $this->handleResponse(false, 'Error de configuración del servidor', [], 500, self::ERROR_CODES['JWT_CONFIG_ERROR']);
    }

    /**
     * Maneja errores de formato JSON inválido
     */
    protected function handleInvalidJsonError() {
        $this->handleResponse(false, 'Formato JSON inválido', [], 400, self::ERROR_CODES['INVALID_JSON_FORMAT']);
    }

    /**
     * Maneja errores de datos de entrada faltantes
     */
    protected function handleMissingRequestDataError() {
        $this->handleResponse(false, 'No se proporcionaron datos para procesar', [], 400, self::ERROR_CODES['MISSING_REQUEST_DATA']);
    }

    /**
     * Maneja errores de valor de estado inválido
     */
    protected function handleInvalidStatusError($validValues = []) {
        $message = 'Valor de estado inválido';
        if (!empty($validValues)) {
            $message .= '. Valores válidos: ' . implode(', ', $validValues);
        }
        $this->handleResponse(false, $message, ['valid_values' => $validValues], 400, self::ERROR_CODES['INVALID_STATUS_VALUE']);
    }

    /**
     * Valida que el usuario esté autenticado (para rutas protegidas)
     */
    protected function validateAuthentication() {
        try {
            // Aquí puedes agregar lógica adicional de validación si es necesario
            return true;
        } catch (Exception $e) {
            $this->handleResponse(false, 'No autorizado', [], 401);
        }
    }

    /**
     * Ejecuta una operación con manejo de errores
     */
    protected function executeWithErrorHandling($operation, $errorMessage = 'Error en la operación') {
        try {
            return $operation();
        } catch (PDOException $e) {
            $this->handleDatabaseError($e);
        } catch (Exception $e) {
            $this->handleGeneralError($e);
        }
    }
} 
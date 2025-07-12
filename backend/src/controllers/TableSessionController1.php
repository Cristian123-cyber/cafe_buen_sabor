<?php

namespace App\Controllers;

use App\Models\Table;
use App\Models\TableSession;
use Firebase\JWT\JWT;

class TableSessionController1 extends BaseController
{
    private $tableModel;
    private $tableSessionModel;

    public function __construct()

    {
        parent::__construct();
        $this->tableModel = new Table();
        $this->tableSessionModel = new TableSession();
    }

    /**
     * Valida un token de QR y un ID de mesa, inicia o recupera una sesión de mesa
     * y devuelve un JWT de sesión para el cliente.
     */
    public function validateQrAndStartSession()
    {
        return $this->executeWithErrorHandling(function() {
            
            $input = $this->getRequestData();

            $missingFields = $this->validateRequiredFields($input, ['qr_token', 'id_table']);
            
            if (!empty($missingFields)) {
               $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
               return;
           }
            // 2. Sanitizar y validar los datos de entrada
            $qrToken = $this->sanitizeString($input['qr_token']);
            $idTable = filter_var($input['id_table'], FILTER_VALIDATE_INT);

            if ($idTable === false) {
                $this->handleValidationError('El ID de la mesa no es válido.');
                return;
            }

            // 3. Validar el QR y el ID de la mesa usando nuestro nuevo método en el modelo.
            $tableData = $this->tableModel->validateQRTokenTable($qrToken, $idTable);

            if (!$tableData || $tableData === false) {
                // Mensaje más específico para el usuario.
                $this->handleResponse(false, 'Código QR inválido.', [], 400, 'E_INVALID_QR');
                return;
            }

            // 4. Buscar una sesión activa para esta mesa o crear una nueva
            $session = $this->tableSessionModel->findActiveByTableId($tableData['id_table']);
            
            if (!$session || $session === false || empty($session)) {
                // Si no hay sesión activa, creamos una nueva.
                $sessionId = $this->tableSessionModel->createSession($tableData['id_table']);
                if (!$sessionId || $sessionId === false) {
                    // Si la creación falla, es un error del servidor.
                    $this->handleResponse(false, 'No se pudo iniciar la sesión de la mesa.', [], 500);
                    return;
                }
                $session = ['id_session' => $sessionId, 'id_table' => $tableData['id_table']];
            }

            // 5. Generar el JWT de Sesión de Mesa (Session-JWT)
            $secretKey = $_ENV['JWT_SECRET'];
            $issuedAt = time();
            $sessionDurationHours = $_ENV['JWT_CLIENT_SESSION_EXPIRATION_HOURS'] ?? 3;
            $expire = $issuedAt + ($sessionDurationHours * 60 * 60);

            $payload = [
                'iat' => $issuedAt,
                'exp' => $expire,
                'data' => [
                    'type'      => 'client_session',
                    'sessionId' => (int)$session['id_session'],
                    'tableId'   => (int)$session['id_table']
                ]
            ];

            $sessionJwt = JWT::encode($payload, $secretKey, 'HS256');

            // 6. Respuesta exitosa con el Session-JWT y la info de la mesa
            $this->handleResponse(true, 'Sesión de mesa validada.', [
                'session_token' => $sessionJwt,
                'table_info' => [
                    'id'     => (int)$tableData['id_table'],
                    'number' => (int)$tableData['table_number']
                ],
                'session_info' => [
                    'id' => (int)$session['id_session']
                ]
            ], 200);

        }, 'Error al validar la sesión de mesa');
    }


    public function validateClientSession() {


        return $this->executeWithErrorHandling(function() {

            $input = $this->getRequestData();

            $missingFields = $this->validateRequiredFields($input, ['session_id']);



            
            if (!empty($missingFields)) {
               $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
               return;
           }

             $idSession = filter_var($input['session_id'], FILTER_VALIDATE_INT);

             if ($idSession === false) {
                $this->handleValidationError('El ID de Session no es valido.');
                return;
            }


            $result = $this->tableSessionModel->validateSession((int) $idSession);

            if (!$result || empty($result)){

                $this->handleResponse(false, 'Acesso no autorizado, session expirada', [], 401);
            }


            $tableData = $this->tableModel->find($result['table_id']);

            if (!$tableData || empty($tableData)){

                $this->handleResponse(false, 'Acesso no autorizado, mesa no encontrada', [], 401);

            }


            // 6. Respuesta exitosa con el Session-JWT y la info de la mesa
            $this->handleResponse(true, 'Sesión de mesa validada.', [
                'table_info' => [
                    'id'     => (int)$tableData['id_table'],
                    'number' => (int)$tableData['table_number']
                ],
                'session_info' => [
                    'id' => (int) $idSession
                ]
            ], 200);















        }, 'Error al validar sesion.');



        
    }

    // ... otros métodos del controlador ...
}
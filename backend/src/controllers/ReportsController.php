<?php

namespace App\Controllers;

use App\Models\Reports;
use Exception;
use DateTime;

class ReportsController extends BaseController
{
    private $reportsModel;

    public function __construct()
    {
        parent::__construct();
        $this->reportsModel = new Reports();
    }

    private function validateDateFormat(string $date)
    {
        // Validar formato básico con regex
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        // Crear DateTime desde formato específico para validación estricta
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);

        // Verificar que la fecha sea válida y coincida exactamente con el input
        // Esto evita fechas como 2023-02-30 que PHP podría "corregir"
        if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
            return false;
        }

        // Resetear hora a 00:00:00 para comparaciones consistentes
        $dateTime->setTime(0, 0, 0);

        return $dateTime;
    }

    /**
     * Valida y sanitiza las fechas de los parámetros GET.
     * @return array|false
     */
    private function getValidatedDates()
    {
        // Obtener parámetros sin sanitización deprecada
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Verificar que ambos parámetros estén presentes
        if (empty($startDate) || empty($endDate)) {
            $this->handleResponse(false, 'Los parámetros start_date y end_date son requeridos.', null, 400);
            return false;
        }

        // Sanitizar manualmente (eliminar espacios y caracteres no deseados)
        $startDate = trim($startDate);
        $endDate = trim($endDate);

        // Validar formato usando DateTime para mayor robustez
        $startDateTime = $this->validateDateFormat($startDate);
        $endDateTime = $this->validateDateFormat($endDate);

        if (!$startDateTime || !$endDateTime) {
            $this->handleResponse(false, 'El formato de fecha debe ser YYYY-MM-DD válido.', null, 400);
            return false;
        }

        // Validación lógica: la fecha de inicio no puede ser posterior a la fecha de fin
        if ($startDateTime > $endDateTime) {
            $this->handleResponse(false, 'La fecha de inicio no puede ser posterior a la fecha de fin.', null, 400);
            return false;
        }

        // Validación de rango razonable (opcional, ajusta según tus necesidades)
        $maxDate = new DateTime('+10 years');
        $minDate = new DateTime('-10 years');

        if ($startDateTime < $minDate || $endDateTime > $maxDate) {
            $this->handleResponse(false, 'Las fechas deben estar dentro de un rango válido.', null, 400);
            return false;
        }

        return [
            'start' => $startDateTime->format('Y-m-d'),
            'end' => $endDateTime->format('Y-m-d'),
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime
        ];
    }

    /**
     * Endpoint: GET /api/analytics/sales-balance
     */
    public function salesBalance()
    {
        return $this->executeWithErrorHandling(function () {
            $dates = $this->getValidatedDates();
            if (!$dates) return;

            $data = $this->reportsModel->getSalesBalance($dates['start'], $dates['end']);
            $this->handleResponse(true, 'Balance de ventas obtenido correctamente.', $data);
        }, 'Error al obtener el balance de ventas.');
    }

    /**
     * Endpoint: GET /api/analytics/invoices-list
     */
    public function invoicesList()
    {
        return $this->executeWithErrorHandling(function () {
            $dates = $this->getValidatedDates();
            if (!$dates) return;

            $data = $this->reportsModel->getInvoicesList($dates['start'], $dates['end']);
            $this->handleResponse(true, 'Listado de facturas obtenido correctamente.', $data);
        }, 'Error al obtener el listado de facturas.');
    }

    /**
     * Endpoint: GET /api/analytics/employees-performance
     */
    public function employeesPerformance()
    {
        return $this->executeWithErrorHandling(function () {
            $dates = $this->getValidatedDates();
            if (!$dates) return;

            $data = $this->reportsModel->getEmployeesPerformance($dates['start'], $dates['end']);
            $this->handleResponse(true, 'Desempeño de empleados obtenido correctamente.', $data);
        }, 'Error al obtener el desempeño de empleados.');
    }

    /**
     * Endpoint: GET /api/analytics/inventory-status
     */
    public function inventoryStatus()
    {
        return $this->executeWithErrorHandling(function () {
            $data = $this->reportsModel->getInventoryStatus();
            $this->handleResponse(true, 'Estado de inventario obtenido correctamente.', $data);
        }, 'Error al obtener el estado de inventario.');
    }
}

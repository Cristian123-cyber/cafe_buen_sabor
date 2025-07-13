<?php

namespace App\Controllers;

use App\Models\UnitsOfMeasure;
use Exception;

class UnitsOfMeasureController extends BaseController
{
    private $unitsModel;

    public function __construct()
    {
        parent::__construct();
        $this->unitsModel = new UnitsOfMeasure();
    }

    /**
     * Obtener todas las unidades de medida
     * GET /api/units-of-measure/
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $units = $this->unitsModel->getAll();
            $this->handleResponse(true, 'Unidades de medida obtenidas correctamente.', $units);
        }, 'Error al obtener las unidades de medida');
    }
} 
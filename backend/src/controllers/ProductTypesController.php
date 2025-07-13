<?php

namespace App\Controllers;

use App\Models\ProductType;
use Exception;

class ProductTypesController extends BaseController
{
    private $productTypeModel;

    public function __construct()
    {
        parent::__construct();
        $this->productTypeModel = new ProductType();
    }

    /**
     * Obtener todos los tipos de productos
     * GET /api/products/types/
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $types = $this->productTypeModel->getAll();
            $this->handleResponse(true, 'Tipos de productos obtenidos correctamente.', $types);
        }, 'Error al obtener los tipos de productos');
    }
} 
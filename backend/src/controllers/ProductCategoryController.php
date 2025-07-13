<?php

namespace App\Controllers;

use App\Models\ProductCategory;
use Exception;

class ProductCategoryController extends BaseController
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new ProductCategory();
    }

    /**
     * Obtiene todas las categorías con información de productos
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $categorias = $this->categoryModel->getAllWithProductCount();
            $this->handleResponse(true, 'Categorías obtenidas correctamente.', $categorias);
            
        }, 'Error al obtener las categorías');
    }

    /**
     * Obtiene una categoría por ID
     */
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $categoryId = $this->validateId($id);
            if (!$categoryId) {
                $this->handleInvalidIdError('ID de categoría');
                return;
            }
            
            $categoria = $this->categoryModel->getById($categoryId);
            
            if (!$categoria) {
                $this->handleResourceNotFoundError('Categoría');
                return;
            }
            
            $this->handleResponse(true, 'Categoría obtenida correctamente.', $categoria);
            
        }, 'Error al obtener la categoría');
    }

    /**
     * Crea una nueva categoría
     */
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos
            $requiredFields = ['category_name'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Sanitizar datos
            $categoryData = [
                'category_name' => $this->sanitizeString($data['category_name'])
            ];

            $resultado = $this->categoryModel->create($categoryData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Categoría creada correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear la categoría', [], 500);
            }
            
        }, 'Error al crear la categoría');
    }

    /**
     * Actualiza una categoría
     */
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $categoryId = $this->validateId($id);
            if (!$categoryId) {
                $this->handleInvalidIdError('ID de categoría');
                return;
            }
            
            $data = $this->getRequestData();
            
            if (empty($data)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            // Sanitizar datos
            $updateData = [];
            
            if (isset($data['category_name'])) {
                $updateData['category_name'] = $this->sanitizeString($data['category_name']);
            }

            $resultado = $this->categoryModel->update($categoryId, $updateData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Categoría actualizada correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al actualizar la categoría', [], 500);
            }
            
        }, 'Error al actualizar la categoría');
    }

    /**
     * Elimina una categoría
     */
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $categoryId = $this->validateId($id);
            if (!$categoryId) {
                $this->handleInvalidIdError('ID de categoría');
                return;
            }
            
            $resultado = $this->categoryModel->delete($categoryId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Categoría eliminada correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al eliminar la categoría', [], 500);
            }
            
        }, 'Error al eliminar la categoría');
    }

    /**
     * Obtiene productos de una categoría específica
     */
    public function getProducts($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $categoryId = $this->validateId($id);
            if (!$categoryId) {
                $this->handleInvalidIdError('ID de categoría');
                return;
            }
            
            $productos = $this->categoryModel->getProductsByCategory($categoryId);
            $this->handleResponse(true, 'Productos de la categoría obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos de la categoría');
    }
} 
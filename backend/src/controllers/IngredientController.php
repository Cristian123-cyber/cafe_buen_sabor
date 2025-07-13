<?php

namespace App\Controllers;

use App\Models\Ingredient;
use Exception;

class IngredientController extends BaseController
{
    private $ingredientModel;

    public function __construct()
    {
        parent::__construct();
        $this->ingredientModel = new Ingredient();
    }

    /**
     * Obtiene todos los ingredientes con información completa
     */
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            
            // Sanitizar parámetros de paginación
            list($page, $limit) = $this->handlePagination($page, $limit);
            
            $ingredientes = $this->ingredientModel->getAllWithDetails($page, $limit);
            $this->handleResponse(true, 'Ingredientes obtenidos correctamente.', $ingredientes);
            
        }, 'Error al obtener los ingredientes');
    }

    /**
     * Obtiene un ingrediente por ID con información completa
     */
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $ingredient = $this->ingredientModel->getByIdWithDetails($ingredientId);
            
            if (!$ingredient) {
                $this->handleResourceNotFoundError('Ingrediente');
                return;
            }
            
            $this->handleResponse(true, 'Ingrediente obtenido correctamente.', $ingredient);
            
        }, 'Error al obtener el ingrediente');
    }

    /**
     * Crea un nuevo ingrediente
     */
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos
            $requiredFields = ['ingredient_name', 'ingredient_stock', 'low_stock_level', 'critical_stock_level', 'ingredient_statuses_id_status', 'units_of_measure_id_unit'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Sanitizar y validar datos
            $ingredientData = [
                'ingredient_name' => $this->sanitizeString($data['ingredient_name']),
                'ingredient_stock' => $this->validatePrice($data['ingredient_stock']),
                'low_stock_level' => $this->validatePrice($data['low_stock_level']),
                'critical_stock_level' => $this->validatePrice($data['critical_stock_level']),
                'ingredient_statuses_id_status' => $this->validateId($data['ingredient_statuses_id_status']),
                'units_of_measure_id_unit' => $this->validateId($data['units_of_measure_id_unit'])
            ];

            // Validar que los valores sean válidos
            if ($ingredientData['ingredient_stock'] === null || $ingredientData['low_stock_level'] === null || $ingredientData['critical_stock_level'] === null) {
                $this->handleInvalidPriceError();
                return;
            }

            // Validar que los IDs sean válidos
            if (!$ingredientData['ingredient_statuses_id_status'] || !$ingredientData['units_of_measure_id_unit']) {
                $this->handleResponse(false, 'Estado o unidad de medida inválidos', [], 400);
                return;
            }

            $resultado = $this->ingredientModel->create($ingredientData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Ingrediente creado correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear el ingrediente', [], 500);
            }
            
        }, 'Error al crear el ingrediente');
    }

    /**
     * Actualiza un ingrediente
     */
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $data = $this->getRequestData();
            
            if (empty($data)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            // Sanitizar y validar datos
            $updateData = [];
            
            if (isset($data['ingredient_name'])) {
                $updateData['ingredient_name'] = $this->sanitizeString($data['ingredient_name']);
            }
            
            if (isset($data['ingredient_stock'])) {
                $stock = $this->validatePrice($data['ingredient_stock']);
                if ($stock === null) {
                    $this->handleInvalidPriceError();
                    return;
                }
                $updateData['ingredient_stock'] = $stock;
            }
            
            if (isset($data['low_stock_level'])) {
                $lowStock = $this->validatePrice($data['low_stock_level']);
                if ($lowStock === null) {
                    $this->handleInvalidPriceError();
                    return;
                }
                $updateData['low_stock_level'] = $lowStock;
            }
            
            if (isset($data['critical_stock_level'])) {
                $criticalStock = $this->validatePrice($data['critical_stock_level']);
                if ($criticalStock === null) {
                    $this->handleInvalidPriceError();
                    return;
                }
                $updateData['critical_stock_level'] = $criticalStock;
            }
            
            if (isset($data['ingredient_statuses_id_status'])) {
                $statusId = $this->validateId($data['ingredient_statuses_id_status']);
                if (!$statusId) {
                    $this->handleResponse(false, 'Estado inválido', [], 400);
                    return;
                }
                $updateData['ingredient_statuses_id_status'] = $statusId;
            }
            
            if (isset($data['units_of_measure_id_unit'])) {
                $unitId = $this->validateId($data['units_of_measure_id_unit']);
                if (!$unitId) {
                    $this->handleResponse(false, 'Unidad de medida inválida', [], 400);
                    return;
                }
                $updateData['units_of_measure_id_unit'] = $unitId;
            }

            $resultado = $this->ingredientModel->update($ingredientId, $updateData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Ingrediente actualizado correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al actualizar el ingrediente', [], 500);
            }
            
        }, 'Error al actualizar el ingrediente');
    }

    /**
     * Elimina un ingrediente
     */
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $resultado = $this->ingredientModel->delete($ingredientId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Ingrediente eliminado correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al eliminar el ingrediente', [], 500);
            }
            
        }, 'Error al eliminar el ingrediente');
    }

    /**
     * Busca ingredientes por nombre
     */
    public function search()
    {
        return $this->executeWithErrorHandling(function() {
            $termino = $_GET['q'] ?? '';
            $termino = $this->sanitizeString($termino);
            
            if (empty($termino)) {
                $this->handleValidationError('Término de búsqueda requerido');
                return;
            }
            
            $ingredientes = $this->ingredientModel->searchByName($termino);
            $this->handleResponse(true, 'Búsqueda realizada correctamente.', $ingredientes);
            
        }, 'Error al buscar ingredientes');
    }

    /**
     * Obtiene ingredientes por estado
     */
    public function getByStatus($status_id)
    {
        return $this->executeWithErrorHandling(function() use ($status_id) {
            // Validar ID de estado
            $statusId = $this->validateId($status_id);
            if (!$statusId) {
                $this->handleResponse(false, 'ID de estado inválido', [], 400);
                return;
            }
            
            $ingredientes = $this->ingredientModel->getByStatus($statusId);
            $this->handleResponse(true, 'Ingredientes por estado obtenidos correctamente.', $ingredientes);
            
        }, 'Error al obtener ingredientes por estado');
    }

    /**
     * Obtiene ingredientes con stock bajo
     */
    public function getLowStock()
    {
        return $this->executeWithErrorHandling(function() {
            $ingredientes = $this->ingredientModel->getLowStock();
            $this->handleResponse(true, 'Ingredientes con stock bajo obtenidos correctamente.', $ingredientes);
            
        }, 'Error al obtener ingredientes con stock bajo');
    }

    /**
     * Obtiene ingredientes con stock crítico
     */
    public function getCriticalStock()
    {
        return $this->executeWithErrorHandling(function() {
            $ingredientes = $this->ingredientModel->getCriticalStock();
            $this->handleResponse(true, 'Ingredientes con stock crítico obtenidos correctamente.', $ingredientes);
            
        }, 'Error al obtener ingredientes con stock crítico');
    }

    /**
     * Actualiza el stock de un ingrediente
     */
    public function updateStock($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $data = $this->getRequestData();
            
            // Validar datos requeridos
            $requiredFields = ['cantidad', 'tipo_movimiento'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleMissingFieldsError($missingFields);
                return;
            }

            // Validar tipo de movimiento
            $tipoMovimiento = $this->sanitizeString($data['tipo_movimiento']);
            if (!in_array($tipoMovimiento, ['entrada', 'salida'])) {
                $this->handleValidationError('Tipo de movimiento debe ser "entrada" o "salida"');
                return;
            }

            // Validar cantidad
            $cantidad = $this->validatePrice($data['cantidad']);
            if ($cantidad === null || $cantidad <= 0) {
                $this->handleInvalidPriceError();
                return;
            }

            $resultado = $this->ingredientModel->updateStock($ingredientId, $cantidad, $tipoMovimiento);
            
            if ($resultado) {
                $this->handleResponse(true, 'Stock actualizado correctamente', [
                    'ingredient_id' => $ingredientId,
                    'cantidad' => $cantidad,
                    'tipo_movimiento' => $tipoMovimiento
                ]);
            } else {
                $this->handleResponse(false, 'Error al actualizar el stock', [], 500);
            }
            
        }, 'Error al actualizar el stock');
    }

    /**
     * Obtiene el historial de movimientos de stock de un ingrediente
     */
    public function getStockHistory($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $historial = $this->ingredientModel->getHistorialMovimientosStock($ingredientId);
            $this->handleResponse(true, 'Historial de stock obtenido correctamente.', $historial);
            
        }, 'Error al obtener el historial de stock');
    }

    /**
     * Obtiene productos que usan un ingrediente específico
     */
    public function getProductsUsingIngredient($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $ingredientId = $this->validateId($id);
            if (!$ingredientId) {
                $this->handleInvalidIdError('ID de ingrediente');
                return;
            }
            
            $productos = $this->ingredientModel->getProductosQueUsanIngrediente($ingredientId);
            $this->handleResponse(true, 'Productos que usan el ingrediente obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos que usan el ingrediente');
    }

    /**
     * Obtiene estadísticas de ingredientes
     */
    public function getStatistics()
    {
        return $this->executeWithErrorHandling(function() {
            $estadisticas = $this->ingredientModel->getEstadisticas();
            $this->handleResponse(true, 'Estadísticas obtenidas correctamente.', $estadisticas);
            
        }, 'Error al obtener estadísticas');
    }
} 
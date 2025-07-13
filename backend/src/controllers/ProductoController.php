<?php

namespace App\Controllers;

use App\Models\Producto;
use Exception;
use PharIo\Manifest\Extension;
use PhpParser\Node\Stmt\TryCatch;

class ProductoController extends BaseController
{
    private $productoModel;

    public function __construct()
    {
        parent::__construct();
        $this->productoModel = new Producto();
    }

    // Método para obtener todos los productos
    public function index()
    {
        return $this->executeWithErrorHandling(function() {
            $productos = $this->productoModel->getProductosAgrupados();
            $this->handleResponse(true, 'Productos obtenidos correctamente.', $productos);
        }, 'Error al obtener los productos');
    }

    // Método para obtener un producto por ID
    public function show($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $productId = $this->validateId($id);
            if (!$productId) {
                $this->handleInvalidIdError('ID de producto');
                return;
            }
            
            $producto = $this->productoModel->getById($productId);
            
            if (!$producto) {
                $this->handleResourceNotFoundError('Producto');
                return;
            }
            
            // Obtener información adicional del producto con ingredientes
            $productoDetallado = $this->productoModel->getProductosConIngredientes($productId);
            
            $this->handleResponse(true, 'Producto obtenido correctamente.', $productoDetallado);
            
        }, 'Error al obtener el producto');
    }

    // Método para obtener productos por categoría/tipo
    public function getByCategory($categoria_id)
    {
        return $this->executeWithErrorHandling(function() use ($categoria_id) {
            // Validar ID de categoría
            $categoryId = $this->validateId($categoria_id);
            if (!$categoryId) {
                $this->handleInvalidIdError('ID de categoría');
                return;
            }
            
            $productos = $this->productoModel->getProductosPorCategoria($categoryId);
            $this->handleResponse(true, 'Productos por categoría obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos por categoría');
    }

    // Método para obtener productos con ingredientes
    public function getWithIngredients()
    {
        return $this->executeWithErrorHandling(function() {
            $producto_id = $_GET['producto_id'] ?? null;
            
            // Si se proporciona un ID, validarlo
            if ($producto_id !== null) {
                $productId = $this->validateId($producto_id);
                if (!$productId) {
                    $this->handleResponse(false, 'ID de producto inválido', [], 400);
                    return;
                }
                $producto_id = $productId;
            }
            
            $productos = $this->productoModel->getProductosConIngredientes($producto_id);
            $this->handleResponse(true, 'Productos con ingredientes obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos con ingredientes');
    }

    // Método para buscar productos
    public function search()
    {
        return $this->executeWithErrorHandling(function() {
            $termino = $_GET['q'] ?? '';
            $termino = $this->sanitizeString($termino);
            
            if (empty($termino)) {
                $this->handleValidationError('Término de búsqueda requerido');
                return;
            }
            
            $productos = $this->productoModel->buscarProductos($termino);
            $this->handleResponse(true, 'Búsqueda realizada correctamente.', $productos);
            
        }, 'Error al buscar productos');
    }

    // Método para obtener productos por estado de stock
    public function getByStockStatus($estado_id)
    {
        return $this->executeWithErrorHandling(function() use ($estado_id) {
            // Validar ID de estado
            $statusId = $this->validateId($estado_id);
            if (!$statusId) {
                $this->handleResponse(false, 'ID de estado inválido', [], 400);
                return;
            }
            
            $productos = $this->productoModel->getProductosPorEstadoStock($statusId);
            $this->handleResponse(true, 'Productos por estado de stock obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos por estado de stock');
    }

    // Método para obtener productos con stock bajo
    public function getLowStock()
    {
        return $this->executeWithErrorHandling(function() {
            $productos = $this->productoModel->getProductosStockBajo();
            $this->handleResponse(true, 'Productos con stock bajo obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos con stock bajo');
    }

    // Método para obtener productos con stock crítico
    public function getCriticalStock()
    {
        return $this->executeWithErrorHandling(function() {
            $productos = $this->productoModel->getProductosStockCritico();
            $this->handleResponse(true, 'Productos con stock crítico obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos con stock crítico');
    }

    // Método para actualizar stock de un producto
    public function updateStock($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $productId = $this->validateId($id);
            if (!$productId) {
                $this->handleInvalidIdError('ID de producto');
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

            $resultado = $this->productoModel->actualizarStock($productId, $cantidad, $tipoMovimiento);
            
            if ($resultado) {
                $this->handleResponse(true, 'Stock actualizado correctamente', [
                    'producto_id' => $productId,
                    'cantidad' => $cantidad,
                    'tipo_movimiento' => $tipoMovimiento
                ]);
            } else {
                $this->handleResponse(false, 'Error al actualizar el stock', [], 500, self::ERROR_CODES['RESOURCE_UPDATE_FAILED']);
            }
            
        }, 'Error al actualizar el stock');
    }

    // Método para obtener historial de stock
    public function getStockHistory($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $productId = $this->validateId($id);
            if (!$productId) {
                $this->handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }
            
            $historial = $this->productoModel->getHistorialStock($productId);
            $this->handleResponse(true, 'Historial de stock obtenido correctamente.', $historial);
            
        }, 'Error al obtener el historial de stock');
    }

    // Método para crear un nuevo producto
    public function store()
    {
        return $this->executeWithErrorHandling(function() {
            $data = $this->getRequestData();
            
            // Validar campos requeridos
            $requiredFields = ['product_name', 'product_price', 'product_cost', 'product_types_id_type'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Sanitizar y validar datos
            $productData = [
                'product_name' => $this->sanitizeString($data['product_name']),
                'product_price' => $this->validatePrice($data['product_price']),
                'product_cost' => $this->validatePrice($data['product_cost']),
                'product_desc' => $this->sanitizeString($data['product_desc'] ?? ''),
                'product_image_url' => $this->sanitizeString($data['product_image_url'] ?? ''),
                'product_types_id_type' => $this->validateId($data['product_types_id_type'])
            ];

            // Validar que los precios sean válidos
            if ($productData['product_price'] === null || $productData['product_cost'] === null) {
                $this->handleResponse(false, 'Precios inválidos', [], 400);
                return;
            }

            // Validar que el tipo de producto sea válido
            if (!$productData['product_types_id_type']) {
                $this->handleResponse(false, 'Tipo de producto inválido', [], 400);
                return;
            }

            $resultado = $this->productoModel->create($productData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Producto creado correctamente', ['id' => $resultado], 201);
            } else {
                $this->handleResponse(false, 'Error al crear el producto', [], 500);
            }
            
        }, 'Error al crear el producto');
    }

    // Método para actualizar un producto
    public function update($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $productId = $this->validateId($id);
            if (!$productId) {
                $this->handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }
            
            $data = $this->getRequestData();
            
            if (empty($data)) {
                $this->handleResponse(false, 'No se proporcionaron datos para actualizar', [], 400);
                return;
            }

            // Sanitizar y validar datos
            $updateData = [];
            
            if (isset($data['product_name'])) {
                $updateData['product_name'] = $this->sanitizeString($data['product_name']);
            }
            
            if (isset($data['product_price'])) {
                $price = $this->validatePrice($data['product_price']);
                if ($price === null) {
                    $this->handleResponse(false, 'Precio inválido', [], 400);
                    return;
                }
                $updateData['product_price'] = $price;
            }
            
            if (isset($data['product_cost'])) {
                $cost = $this->validatePrice($data['product_cost']);
                if ($cost === null) {
                    $this->handleResponse(false, 'Costo inválido', [], 400);
                    return;
                }
                $updateData['product_cost'] = $cost;
            }
            
            if (isset($data['product_desc'])) {
                $updateData['product_desc'] = $this->sanitizeString($data['product_desc']);
            }
            
            if (isset($data['product_image_url'])) {
                $updateData['product_image_url'] = $this->sanitizeString($data['product_image_url']);
            }

            $resultado = $this->productoModel->update($productId, $updateData);
            
            if ($resultado) {
                $this->handleResponse(true, 'Producto actualizado correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al actualizar el producto', [], 500);
            }
            
        }, 'Error al actualizar el producto');
    }

    // Método para eliminar un producto
    public function delete($id)
    {
        return $this->executeWithErrorHandling(function() use ($id) {
            // Validar ID
            $productId = $this->validateId($id);
            if (!$productId) {
                $this->handleResponse(false, 'ID de producto inválido', [], 400);
                return;
            }
            
            $resultado = $this->productoModel->delete($productId);
            
            if ($resultado) {
                $this->handleResponse(true, 'Producto eliminado correctamente', []);
            } else {
                $this->handleResponse(false, 'Error al eliminar el producto', [], 500);
            }
            
        }, 'Error al eliminar el producto');
    }

    // Métodos adicionales para compatibilidad
    public function getAvailable()
    {
        return $this->executeWithErrorHandling(function() {
            $productos = $this->productoModel->getProductosDisponibles();
            $this->handleResponse(true, 'Productos disponibles obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos disponibles');
    }

    public function getPopular()
    {
        return $this->executeWithErrorHandling(function() {
            $productos = $this->productoModel->getProductosPopulares();
            $this->handleResponse(true, 'Productos populares obtenidos correctamente.', $productos);
            
        }, 'Error al obtener productos populares');
    }
} 
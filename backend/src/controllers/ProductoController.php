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
        $this->productoModel = new Producto();
    }

    // Método para obtener todos los productos
    public function index()
    {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            
            $productos = $this->productoModel->getProductosConCategoria($page, $limit);
            $this->handleResponse(true, 'Productos obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Hay un problema al momento de obtener los productos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener un producto por ID
    public function show($id)
    {
        try {
            $producto = $this->productoModel->getById($id);
            
            if (!$producto) {
                $this->handleResponse(false, 'Producto no encontrado', [], 404);
                return;
            }
            
            // Obtener información adicional del producto con ingredientes
            $productoDetallado = $this->productoModel->getProductosConIngredientes($id);
            
            $this->handleResponse(true, 'Producto obtenido correctamente.', $productoDetallado);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener el producto', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener productos por categoría/tipo
    public function getByCategory($categoria_id)
    {
        try {
            $productos = $this->productoModel->getProductosPorCategoria($categoria_id);
            $this->handleResponse(true, 'Productos por categoría obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener productos por categoría', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener productos con ingredientes
    public function getWithIngredients()
    {
        try {
            $producto_id = $_GET['producto_id'] ?? null;
            $productos = $this->productoModel->getProductosConIngredientes($producto_id);
            $this->handleResponse(true, 'Productos con ingredientes obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener productos con ingredientes', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para buscar productos
    public function search()
    {
        try {
            $termino = $_GET['q'] ?? '';
            
            if (empty($termino)) {
                $this->handleResponse(false, 'Término de búsqueda requerido', [], 400);
                return;
            }
            
            $productos = $this->productoModel->buscarProductos($termino);
            $this->handleResponse(true, 'Búsqueda realizada correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al buscar productos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener productos por estado de stock
    public function getByStockStatus($estado_id)
    {
        try {
            $productos = $this->productoModel->getProductosPorEstadoStock($estado_id);
            $this->handleResponse(true, 'Productos por estado de stock obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener productos por estado de stock', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener productos con stock bajo
    public function getLowStock()
    {
        try {
            $productos = $this->productoModel->getProductosStockBajo();
            $this->handleResponse(true, 'Productos con stock bajo obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener productos con stock bajo', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener productos con stock crítico
    public function getCriticalStock()
    {
        try {
            $productos = $this->productoModel->getProductosStockCritico();
            $this->handleResponse(true, 'Productos con stock crítico obtenidos correctamente.', $productos);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener productos con stock crítico', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para actualizar stock de un producto
    public function updateStock($id)
    {
        try {
            $data = $this->getRequestData();
            
            // Validar datos requeridos
            $requiredFields = ['cantidad', 'tipo_movimiento'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Validar tipo de movimiento
            if (!in_array($data['tipo_movimiento'], ['entrada', 'salida'])) {
                $this->handleResponse(false, 'Tipo de movimiento debe ser "entrada" o "salida"', [], 400);
                return;
            }

            // Validar cantidad
            if (!is_numeric($data['cantidad']) || $data['cantidad'] <= 0) {
                $this->handleResponse(false, 'La cantidad debe ser un número positivo', [], 400);
                return;
            }

            $resultado = $this->productoModel->actualizarStock($id, $data['cantidad'], $data['tipo_movimiento']);
            
            if ($resultado) {
                $this->handleResponse(true, 'Stock actualizado correctamente', [
                    'producto_id' => $id,
                    'cantidad' => $data['cantidad'],
                    'tipo_movimiento' => $data['tipo_movimiento']
                ]);
            } else {
                $this->handleResponse(false, 'Error al actualizar el stock', [], 500);
            }
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al actualizar el stock', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    // Método para obtener historial de movimientos de stock
    public function getStockHistory($id)
    {
        try {
            $historial = $this->productoModel->getHistorialMovimientosStock($id);
            $this->handleResponse(true, 'Historial de movimientos obtenido correctamente.', $historial);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener historial de movimientos', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function store()
    {
        try {
            $data = $this->getRequestData();
            
            // Validar datos requeridos
            $requiredFields = ['product_name', 'product_price', 'product_types_id_type'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Agregar fecha de creación
            $data['created_date'] = date('Y-m-d');
            
            $producto_id = $this->productoModel->create($data);
            
            if ($producto_id) {
                $this->handleResponse(true, 'Producto creado exitosamente', [
                    'id_product' => $producto_id,
                    'producto' => $data
                ], 201);
            } else {
                $this->handleResponse(false, 'Error al crear el producto', [], 500);
            }
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al crear el producto', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function update($id)
    {
        try {
            $data = $this->getRequestData();
            
            // Agregar fecha de actualización
            $data['last_updated_date'] = date('Y-m-d');
            
            $resultado = $this->productoModel->update($id, $data);
            
            if ($resultado) {
                $this->handleResponse(true, "Producto $id actualizado exitosamente", [
                    'id_product' => $id,
                    'producto' => $data
                ]);
            } else {
                $this->handleResponse(false, 'Error al actualizar el producto', [], 500);
            }
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al actualizar el producto', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $resultado = $this->productoModel->delete($id);
            
            if ($resultado) {
                $this->handleResponse(true, "Producto $id eliminado exitosamente");
            } else {
                $this->handleResponse(false, 'Error al eliminar el producto', [], 500);
            }
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al eliminar el producto', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
?> 
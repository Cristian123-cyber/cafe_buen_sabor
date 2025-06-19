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
            $productos = $this->productoModel->getAll();
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
            
            $this->handleResponse(true, 'Producto obtenido correctamente.', $producto);
        } catch(Exception $e) {
            $this->handleResponse(false, 'Error al obtener el producto', [
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
            $requiredFields = ['nombre', 'precio'];
            $missingFields = $this->validateRequiredFields($data, $requiredFields);
            
            if (!empty($missingFields)) {
                $this->handleResponse(false, 'Campos requeridos faltantes: ' . implode(', ', $missingFields), [], 400);
                return;
            }

            // Aquí irá la lógica para crear un producto
            $this->handleResponse(true, 'Producto creado exitosamente', $data, 201);
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
            
            // Aquí irá la lógica para actualizar un producto
            $this->handleResponse(true, "Producto $id actualizado exitosamente", $data);
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
            // Aquí irá la lógica para eliminar un producto
            $this->handleResponse(true, "Producto $id eliminado exitosamente");
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
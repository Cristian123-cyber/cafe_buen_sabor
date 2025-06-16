<?php

namespace App\Controllers;

use App\Models\Producto;

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
        $productos = $this->productoModel->getAll();
        $this->jsonResponse([
            'message' => 'Productos obtenidos correctamente.',
            'data' => $productos
        ]);
    }

    // Método para obtener un producto por ID
    public function show($id)
    {
        $producto = $this->productoModel->getById($id);
        if ($producto) {
            $this->jsonResponse([
                'message' => 'Producto obtenido correctamente.',
                'data' => $producto
            ]);
        } else {
            $this->jsonResponse([
                'error' => true,
                'message' => 'Producto no encontrado'
            ], 404);
        }
    }

    public function store()
    {
        $data = $this->getRequestData();
        
        // Validar datos requeridos
        $requiredFields = ['nombre', 'precio'];
        $missingFields = $this->validateRequiredFields($data, $requiredFields);
        
        if (!empty($missingFields)) {
            return $this->jsonResponse([
                'error' => true,
                'message' => 'Campos requeridos faltantes: ' . implode(', ', $missingFields)
            ], 400);
        }

        // Aquí irá la lógica para crear un producto
        $this->jsonResponse([
            'message' => 'Producto creado exitosamente',
            'data' => $data
        ], 201);
    }

    public function update($id)
    {
        $data = $this->getRequestData();
        
        // Aquí irá la lógica para actualizar un producto
        $this->jsonResponse([
            'message' => "Producto $id actualizado exitosamente",
            'data' => $data
        ]);
    }

    public function delete($id)
    {
        // Aquí irá la lógica para eliminar un producto
        $this->jsonResponse([
            'message' => "Producto $id eliminado exitosamente"
        ]);
    }
}
?> 
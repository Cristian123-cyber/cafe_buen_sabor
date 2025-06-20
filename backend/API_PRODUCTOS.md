# API de Productos - Café Buen Sabor

Esta API permite gestionar productos del sistema de café, incluyendo control de stock, ingredientes, categorías y más.

## Base URL
```
http://localhost:8000/api/productos
```

## Endpoints Disponibles

### 1. Obtener Todos los Productos
**GET** `/api/productos`

Obtiene todos los productos con información de categoría y estado de stock.

**Parámetros de consulta:**
- `page` (opcional): Número de página (default: 1)
- `limit` (opcional): Productos por página (default: 10)

**Ejemplo:**
```bash
GET /api/productos?page=1&limit=5
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Productos obtenidos correctamente.",
    "data": [
        {
            "id_product": 1,
            "product_name": "Vaso de cafe",
            "product_price": "10000.00",
            "product_cost": "4000.00",
            "product_desc": "Cafe melo",
            "product_image_url": "http://localhost:8000/public/images/imagen_cafe.jpg",
            "created_date": "2025-06-19",
            "last_updated_date": null,
            "product_stock": null,
            "low_stock_level": null,
            "critical_stock_level": null,
            "ingredient_statuses_id_status": null,
            "product_types_id_type": 1,
            "categoria_nombre": "Producto preparado",
            "estado_stock": null
        }
    ]
}
```

### 2. Obtener Producto por ID
**GET** `/api/productos/{id}`

Obtiene un producto específico con información detallada incluyendo ingredientes.

**Ejemplo:**
```bash
GET /api/productos/1
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Producto obtenido correctamente.",
    "data": [
        {
            "id_product": 1,
            "product_name": "Vaso de cafe",
            "product_price": "10000.00",
            "product_cost": "4000.00",
            "product_desc": "Cafe melo",
            "product_image_url": "http://localhost:8000/public/images/imagen_cafe.jpg",
            "created_date": "2025-06-19",
            "last_updated_date": null,
            "product_stock": null,
            "low_stock_level": null,
            "critical_stock_level": null,
            "ingredient_statuses_id_status": null,
            "product_types_id_type": 1,
            "categoria_nombre": "Producto preparado",
            "estado_stock": null,
            "ingredient_name": "Cafe",
            "ingredient_stock": "100.00",
            "cantidad_ingrediente": "10.00",
            "unit_name": "Gramo",
            "unit_abbreviation": "g"
        }
    ]
}
```

### 3. Obtener Productos por Categoría
**GET** `/api/productos/categoria/{categoria_id}`

Obtiene productos filtrados por tipo de producto.

**Ejemplo:**
```bash
GET /api/productos/categoria/1
```

### 4. Obtener Productos con Ingredientes
**GET** `/api/productos/ingredientes`

Obtiene productos con información de ingredientes.

**Parámetros de consulta:**
- `producto_id` (opcional): ID específico del producto

**Ejemplo:**
```bash
GET /api/productos/ingredientes
GET /api/productos/ingredientes?producto_id=1
```

### 5. Buscar Productos
**GET** `/api/productos/buscar`

Busca productos por nombre o descripción.

**Parámetros de consulta:**
- `q` (requerido): Término de búsqueda

**Ejemplo:**
```bash
GET /api/productos/buscar?q=cafe
```

### 6. Obtener Productos por Estado de Stock
**GET** `/api/productos/stock/estado/{estado_id}`

Obtiene productos filtrados por estado de stock.

**Estados disponibles:**
- 1: Óptimo
- 2: Bajo
- 3: Crítico
- 4: Agotado

**Ejemplo:**
```bash
GET /api/productos/stock/estado/2
```

### 7. Obtener Productos con Stock Bajo
**GET** `/api/productos/stock/bajo`

Obtiene productos que tienen stock bajo (entre nivel bajo y crítico).

**Ejemplo:**
```bash
GET /api/productos/stock/bajo
```

### 8. Obtener Productos con Stock Crítico
**GET** `/api/productos/stock/critico`

Obtiene productos que tienen stock crítico o agotado.

**Ejemplo:**
```bash
GET /api/productos/stock/critico
```

### 9. Actualizar Stock de Producto
**PUT** `/api/productos/{id}/stock`

Actualiza el stock de un producto específico.

**Body:**
```json
{
    "cantidad": 10,
    "tipo_movimiento": "entrada"
}
```

**Tipos de movimiento:**
- `entrada`: Aumenta el stock
- `salida`: Disminuye el stock

**Ejemplo:**
```bash
PUT /api/productos/1/stock
Content-Type: application/json

{
    "cantidad": 5,
    "tipo_movimiento": "salida"
}
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Stock actualizado correctamente",
    "data": {
        "producto_id": "1",
        "cantidad": 5,
        "tipo_movimiento": "salida"
    }
}
```

### 10. Obtener Historial de Movimientos de Stock
**GET** `/api/productos/{id}/stock/historial`

Obtiene el historial de movimientos de stock de un producto.

**Ejemplo:**
```bash
GET /api/productos/1/stock/historial
```

**Respuesta:**
```json
{
    "success": true,
    "message": "Historial de movimientos obtenido correctamente.",
    "data": [
        {
            "id_movement": 1,
            "movement_type": "OUT",
            "quantity": "5.00",
            "movement_date": "2025-06-19 15:30:00",
            "movement_notes": "Movimiento de stock: Salida de 5 unidades",
            "stock_movementscol": null,
            "id_product": 1,
            "product_name": "Vaso de cafe"
        }
    ]
}
```

### 11. Crear Producto
**POST** `/api/productos`

Crea un nuevo producto.

**Body:**
```json
{
    "product_name": "Café Americano",
    "product_price": 15000.00,
    "product_cost": 6000.00,
    "product_desc": "Café negro tradicional",
    "product_image_url": "http://localhost:8000/public/images/cafe_americano.jpg",
    "product_stock": 50,
    "low_stock_level": 10,
    "critical_stock_level": 5,
    "product_types_id_type": 1
}
```

**Campos requeridos:**
- `product_name`: Nombre del producto
- `product_price`: Precio de venta
- `product_types_id_type`: ID del tipo de producto

**Ejemplo:**
```bash
POST /api/productos
Content-Type: application/json

{
    "product_name": "Café Americano",
    "product_price": 15000.00,
    "product_cost": 6000.00,
    "product_desc": "Café negro tradicional",
    "product_types_id_type": 1
}
```

### 12. Actualizar Producto
**PUT** `/api/productos/{id}`

Actualiza un producto existente.

**Body:**
```json
{
    "product_name": "Café Americano Grande",
    "product_price": 18000.00,
    "product_desc": "Café negro tradicional en tamaño grande"
}
```

**Ejemplo:**
```bash
PUT /api/productos/1
Content-Type: application/json

{
    "product_name": "Café Americano Grande",
    "product_price": 18000.00
}
```

### 13. Eliminar Producto
**DELETE** `/api/productos/{id}`

Elimina un producto.

**Ejemplo:**
```bash
DELETE /api/productos/1
```

## Códigos de Estado HTTP

- **200**: OK - Operación exitosa
- **201**: Created - Recurso creado exitosamente
- **400**: Bad Request - Datos de entrada inválidos
- **404**: Not Found - Recurso no encontrado
- **500**: Internal Server Error - Error del servidor

## Estructura de Respuesta

Todas las respuestas siguen esta estructura:

```json
{
    "success": true|false,
    "message": "Mensaje descriptivo",
    "data": {...},
    "error": {...} // Solo en caso de error
}
```

## Tipos de Productos

- **1**: Producto preparado (requiere ingredientes)
- **2**: Producto no preparado (viene por unidad)

## Estados de Stock

- **1**: Óptimo
- **2**: Bajo
- **3**: Crítico
- **4**: Agotado

## Ejemplos de Uso Completo

### 1. Crear un producto y actualizar su stock
```bash
# 1. Crear producto
POST /api/productos
{
    "product_name": "Café Latte",
    "product_price": 12000.00,
    "product_cost": 5000.00,
    "product_desc": "Café con leche",
    "product_types_id_type": 1
}

# 2. Actualizar stock
PUT /api/productos/2/stock
{
    "cantidad": 20,
    "tipo_movimiento": "entrada"
}
```

### 2. Buscar productos y verificar stock
```bash
# 1. Buscar productos
GET /api/productos/buscar?q=cafe

# 2. Ver productos con stock bajo
GET /api/productos/stock/bajo

# 3. Ver historial de movimientos
GET /api/productos/1/stock/historial
```

### 3. Obtener productos por categoría
```bash
# Productos preparados
GET /api/productos/categoria/1

# Productos no preparados
GET /api/productos/categoria/2
``` 
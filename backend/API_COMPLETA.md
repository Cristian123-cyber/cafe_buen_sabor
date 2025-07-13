# API Completa - Café Buen Sabor

Esta documentación describe todos los endpoints disponibles en la API del sistema de Café Buen Sabor.

## Base URL
```
http://localhost:8000/api
```

## Autenticación

La mayoría de endpoints requieren autenticación JWT. Incluye el token en el header:
```
Authorization: Bearer <token>
```

## Endpoints de Autenticación

### Login
- **POST** `/api/auth/login`
- **Body:**
```json
{
    "email": "usuario@ejemplo.com",
    "password": "contraseña"
}
```

### Refresh Token
- **POST** `/api/auth/refresh`
- **Cookies:** Requiere refresh_token en cookies

### Logout
- **POST** `/api/auth/logout`
- **Cookies:** Revoca refresh_token

### Obtener Perfil
- **GET** `/api/auth/me`
- **Headers:** Authorization Bearer token

---

## Endpoints de Productos

### Obtener Todos los Productos
- **GET** `/api/productos`
- **Query Params:** `page`, `limit`
- **Respuesta:** Lista de productos con categoría y estado de stock

### Obtener Producto por ID
- **GET** `/api/productos/{id}`
- **Respuesta:** Producto con ingredientes detallados

### Crear Producto
- **POST** `/api/productos`
- **Body:**
```json
{
    "product_name": "Nombre del producto",
    "product_price": 10000.00,
    "product_cost": 4000.00,
    "product_desc": "Descripción",
    "product_image_url": "URL de imagen",
    "product_types_id_type": 1,
    "product_category": 2
}
```

### Actualizar Producto
- **PUT** `/api/productos/{id}`
- **Body:** Campos a actualizar

### Eliminar Producto
- **DELETE** `/api/productos/{id}`

### Buscar Productos
- **GET** `/api/productos/buscar?q=termino`
- **Respuesta:** Productos que coinciden con el término

### Productos por Categoría
- **GET** `/api/productos/categoria/{categoria_id}`
- **Respuesta:** Productos de una categoría específica

### Productos con Ingredientes
- **GET** `/api/productos/ingredientes`
- **Query Params:** `producto_id` (opcional)
- **Respuesta:** Productos con información de ingredientes

### Gestión de Stock

#### Actualizar Stock
- **PUT** `/api/productos/{id}/stock`
- **Body:**
```json
{
    "cantidad": 10,
    "tipo_movimiento": "entrada"
}
```

#### Historial de Stock
- **GET** `/api/productos/{id}/stock/historial`
- **Respuesta:** Movimientos de stock del producto

#### Productos por Estado de Stock
- **GET** `/api/productos/stock/estado/{estado_id}`
- **Estados:** 1=Óptimo, 2=Bajo, 3=Crítico, 4=Agotado

#### Productos con Stock Bajo
- **GET** `/api/productos/stock/bajo`
- **Respuesta:** Productos entre nivel bajo y crítico

#### Productos con Stock Crítico
- **GET** `/api/productos/stock/critico`
- **Respuesta:** Productos con stock crítico o agotado

### Productos Especiales

#### Productos Disponibles
- **GET** `/api/productos/disponibles`
- **Respuesta:** Productos con stock > 0

#### Productos Populares
- **GET** `/api/productos/populares`
- **Respuesta:** Top 10 productos más vendidos

---

## Endpoints de Categorías de Productos

### Obtener Todas las Categorías
- **GET** `/api/categorias-productos`
- **Respuesta:** Categorías con conteo de productos

### Obtener Categoría por ID
- **GET** `/api/categorias-productos/{id}`

### Crear Categoría
- **POST** `/api/categorias-productos`
- **Body:**
```json
{
    "category_name": "Nombre de la categoría"
}
```

### Actualizar Categoría
- **PUT** `/api/categorias-productos/{id}`
- **Body:** Campos a actualizar

### Eliminar Categoría
- **DELETE** `/api/categorias-productos/{id}`

### Productos de una Categoría
- **GET** `/api/categorias-productos/{id}/productos`
- **Respuesta:** Todos los productos de la categoría

---

## Endpoints de Ingredientes

### Obtener Todos los Ingredientes
- **GET** `/api/ingredientes`
- **Query Params:** `page`, `limit`
- **Respuesta:** Ingredientes con estado y unidad de medida

### Obtener Ingrediente por ID
- **GET** `/api/ingredientes/{id}`
- **Respuesta:** Ingrediente con información completa

### Crear Ingrediente
- **POST** `/api/ingredientes`
- **Body:**
```json
{
    "ingredient_name": "Nombre del ingrediente",
    "ingredient_stock": 100.00,
    "low_stock_level": 30.00,
    "critical_stock_level": 10.00,
    "ingredient_statuses_id_status": 1,
    "units_of_measure_id_unit": 2
}
```

### Actualizar Ingrediente
- **PUT** `/api/ingredientes/{id}`
- **Body:** Campos a actualizar

### Eliminar Ingrediente
- **DELETE** `/api/ingredientes/{id}`

### Buscar Ingredientes
- **GET** `/api/ingredientes/buscar?q=termino`
- **Respuesta:** Ingredientes que coinciden con el término

### Ingredientes por Estado
- **GET** `/api/ingredientes/estado/{estado_id}`
- **Estados:** 1=Óptimo, 2=Bajo, 3=Crítico, 4=Agotado

### Gestión de Stock de Ingredientes

#### Actualizar Stock
- **PUT** `/api/ingredientes/{id}/stock`
- **Body:**
```json
{
    "cantidad": 50.00,
    "tipo_movimiento": "entrada"
}
```

#### Historial de Stock
- **GET** `/api/ingredientes/{id}/stock/historial`
- **Respuesta:** Movimientos de stock del ingrediente

#### Productos que Usan el Ingrediente
- **GET** `/api/ingredientes/{id}/productos`
- **Respuesta:** Productos que contienen el ingrediente

### Ingredientes por Estado de Stock

#### Stock Bajo
- **GET** `/api/ingredientes/stock/bajo`
- **Respuesta:** Ingredientes entre nivel bajo y crítico

#### Stock Crítico
- **GET** `/api/ingredientes/stock/critico`
- **Respuesta:** Ingredientes con stock crítico o agotado

### Estadísticas de Ingredientes
- **GET** `/api/ingredientes/estadisticas`
- **Respuesta:** Conteos por estado de stock

---

## Endpoints de Notificaciones

### Obtener Todas las Notificaciones
- **GET** `/api/notificaciones`
- **Query Params:** `page`, `limit`
- **Respuesta:** Notificaciones con información de empleado y pedido

### Obtener Notificación por ID
- **GET** `/api/notificaciones/{id}`
- **Respuesta:** Notificación con información completa

### Crear Notificación
- **POST** `/api/notificaciones`
- **Body:**
```json
{
    "notification_type": "ORDER_READY",
    "message": "Mensaje de la notificación",
    "employee_id": 1,
    "order_id": 1
}
```

### Actualizar Notificación
- **PUT** `/api/notificaciones/{id}`
- **Body:** Campos a actualizar

### Eliminar Notificación
- **DELETE** `/api/notificaciones/{id}`

### Notificaciones por Empleado
- **GET** `/api/notificaciones/empleado/{employee_id}`
- **Query Params:** `page`, `limit`
- **Respuesta:** Notificaciones de un empleado específico

### Notificaciones por Tipo
- **GET** `/api/notificaciones/tipo/{tipo}`
- **Tipos:** `ORDER_READY`, `ORDER_CONFIRMED`, `ORDER_CANCELLED`
- **Query Params:** `page`, `limit`

### Notificaciones No Leídas
- **GET** `/api/notificaciones/no-leidas`
- **Query Params:** `page`, `limit`
- **Respuesta:** Solo notificaciones no leídas

### Notificaciones No Leídas por Empleado
- **GET** `/api/notificaciones/empleado/{employee_id}/no-leidas`
- **Respuesta:** Notificaciones no leídas de un empleado

### Gestión de Lectura

#### Marcar como Leída
- **PUT** `/api/notificaciones/{id}/leer`
- **Respuesta:** Notificación marcada como leída

#### Marcar Múltiples como Leídas
- **PUT** `/api/notificaciones/leer-multiples`
- **Body:**
```json
{
    "notification_ids": [1, 2, 3, 4]
}
```

#### Marcar Todas como Leídas por Empleado
- **PUT** `/api/notificaciones/empleado/{employee_id}/leer-todas`
- **Respuesta:** Todas las notificaciones del empleado marcadas como leídas

### Creación Específica de Notificaciones

#### Notificación de Pedido Listo
- **POST** `/api/notificaciones/pedido-listo`
- **Body:**
```json
{
    "order_id": 1,
    "employee_id": 1
}
```

#### Notificación de Pedido Confirmado
- **POST** `/api/notificaciones/pedido-confirmado`
- **Body:**
```json
{
    "order_id": 1,
    "employee_id": 1
}
```

#### Notificación de Pedido Cancelado
- **POST** `/api/notificaciones/pedido-cancelado`
- **Body:**
```json
{
    "order_id": 1,
    "employee_id": 1
}
```

### Estadísticas de Notificaciones

#### Estadísticas Generales
- **GET** `/api/notificaciones/estadisticas`
- **Respuesta:** Conteos por tipo y estado de lectura

#### Estadísticas por Empleado
- **GET** `/api/notificaciones/empleado/{employee_id}/estadisticas`
- **Respuesta:** Estadísticas específicas del empleado

### Notificaciones por Rango de Fechas
- **GET** `/api/notificaciones/por-fechas`
- **Query Params:** `start_date`, `end_date`, `page`, `limit`
- **Respuesta:** Notificaciones en el rango de fechas

---

## Endpoints de Empleados

### Obtener Todos los Empleados
- **GET** `/api/employees`
- **Query Params:** `page`, `limit`

### Obtener Empleado por ID
- **GET** `/api/employees/{id}`

### Crear Empleado
- **POST** `/api/employees`
- **Body:**
```json
{
    "employe_name": "Nombre del empleado",
    "employe_email": "email@ejemplo.com",
    "password": "contraseña",
    "employees_statuses_id_status": 1,
    "employees_rol_id_rol": 1,
    "employee_cc": "12345678"
}
```

### Actualizar Empleado
- **PUT** `/api/employees/{id}`
- **Body:** Campos a actualizar

### Eliminar Empleado
- **DELETE** `/api/employees/{id}`

### Filtrar Empleados
- **GET** `/api/employees/filter`
- **Query Params:** `status`, `role`, `search`

### Mesas Atendidas por Mesero
- **GET** `/api/employees/tables-served`
- **Respuesta:** Conteo de mesas atendidas por mesero

### Resumen de Ventas por Empleado
- **GET** `/api/employees/{id}/sales-summary`
- **Respuesta:** Resumen de ventas del empleado

---

## Endpoints de Mesas

### Obtener Todas las Mesas
- **GET** `/api/tables`

### Obtener Mesa por ID
- **GET** `/api/tables/{id}`

### Crear Mesa
- **POST** `/api/tables`
- **Body:**
```json
{
    "table_number": 1,
    "table_status": "FREE"
}
```

### Actualizar Mesa
- **PUT** `/api/tables/{id}`
- **Body:** Campos a actualizar

### Eliminar Mesa
- **DELETE** `/api/tables/{id}`

### Actualizar Estado de Mesa
- **PUT** `/api/tables/{id}/estado`
- **Body:**
```json
{
    "table_status": "OCCUPIED"
}
```

### Buscar Mesa por Token QR
- **GET** `/api/tables/token/{token}`

### Obtener Token QR de Mesa
- **GET** `/api/tables/{id}/qr`

### Desactivar Mesa
- **PUT** `/api/tables/{id}/deactivate`

### Activar Mesa
- **PUT** `/api/tables/{id}/activate`

### Validar Token QR
- **POST** `/api/tables/validate-token`
- **Body:**
```json
{
    "qr_token": "token_qr_123"
}
```

---

## Endpoints de Pedidos

### Obtener Todos los Pedidos
- **GET** `/api/orders`
- **Query Params:** `page`, `limit`

### Obtener Pedido por ID
- **GET** `/api/orders/{id}`

### Crear Pedido
- **POST** `/api/orders`
- **Body:**
```json
{
    "total_amount": 25000.00,
    "waiter_id": 1,
    "order_statuses_id_status": 1,
    "table_sessions_id_session": 1,
    "products": [
        {
            "product_id": 1,
            "quantity": 2,
            "product_price": 10000.00
        }
    ]
}
```

### Actualizar Pedido
- **PUT** `/api/orders/{id}`
- **Body:** Campos a actualizar

### Eliminar Pedido
- **DELETE** `/api/orders/{id}`

### Pedidos por Estado
- **GET** `/api/orders/status/{status_id}`

### Pedidos por Mesa
- **GET** `/api/orders/table/{table_id}`

### Pedidos por Empleado
- **GET** `/api/orders/employee/{employee_id}`

---

## Endpoints de Ventas

### Obtener Todas las Ventas
- **GET** `/api/sales`
- **Query Params:** `page`, `limit`

### Obtener Venta por ID
- **GET** `/api/sales/{id}`

### Crear Venta
- **POST** `/api/sales`
- **Body:**
```json
{
    "total_amount": 50000.00,
    "payment_method": "CONTADO",
    "cashier_id": 1,
    "orders": [1, 2, 3]
}
```

### Actualizar Venta
- **PUT** `/api/sales/{id}`
- **Body:** Campos a actualizar

### Eliminar Venta
- **DELETE** `/api/sales/{id}`

### Ventas por Empleado
- **GET** `/api/sales/employee/{employee_id}`

### Ventas por Fecha
- **GET** `/api/sales/date/{date}`

### Ventas por Rango de Fechas
- **GET** `/api/sales/date-range`
- **Query Params:** `start_date`, `end_date`

---

## Códigos de Respuesta

### Éxito
- **200:** Operación exitosa
- **201:** Recurso creado exitosamente

### Errores del Cliente
- **400:** Bad Request - Datos inválidos
- **401:** Unauthorized - No autenticado
- **403:** Forbidden - No autorizado
- **404:** Not Found - Recurso no encontrado

### Errores del Servidor
- **500:** Internal Server Error - Error interno

## Formato de Respuesta

### Respuesta Exitosa
```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": [...]
}
```

### Respuesta de Error
```json
{
    "success": false,
    "message": "Descripción del error",
    "data": [],
    "error_code": "CODIGO_ERROR"
}
```

## Códigos de Error

### Validación (VAL001-VAL007)
- **VAL001:** Error de validación general
- **VAL002:** Campos requeridos faltantes
- **VAL003:** Formato de email inválido
- **VAL004:** Formato de ID inválido
- **VAL005:** Formato de precio inválido
- **VAL006:** Contraseña muy corta
- **VAL007:** Valor de estado inválido

### Autenticación (AUTH001-AUTH005)
- **AUTH001:** Credenciales inválidas
- **AUTH002:** Token expirado
- **AUTH003:** Token inválido
- **AUTH004:** Acceso no autorizado
- **AUTH005:** Error de configuración JWT

### Recursos (RES001-RES005)
- **RES001:** Recurso no encontrado
- **RES002:** Recurso ya existe
- **RES003:** Error al eliminar recurso
- **RES004:** Error al actualizar recurso
- **RES005:** Error al crear recurso

### Base de Datos (DB001-DB003)
- **DB001:** Error de conexión a BD
- **DB002:** Error de consulta BD
- **DB003:** Violación de restricción BD

### Entrada (INP001-INP003)
- **INP001:** Formato JSON inválido
- **INP002:** Datos de petición faltantes
- **INP003:** Método de petición inválido

### Sistema (SYS001-SYS003)
- **SYS001:** Error interno del servidor
- **SYS002:** Error de configuración
- **SYS003:** Servicio no disponible 
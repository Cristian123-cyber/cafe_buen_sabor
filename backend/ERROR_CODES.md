# Códigos de Error - API Café Buen Sabor

Este documento describe todos los códigos de error utilizados en la API para facilitar el debugging y el manejo de errores en el frontend.

## Estructura de Respuesta de Error

```json
{
    "success": false,
    "message": "Descripción del error",
    "data": [],
    "error_code": "CODIGO_ERROR"
}
```

## Códigos de Error por Categoría

### 🔍 Errores de Validación (VAL)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `VAL001` | Error general de validación | 400 |
| `VAL002` | Campos requeridos faltantes | 400 |
| `VAL003` | Formato de email inválido | 400 |
| `VAL004` | Formato de ID inválido | 400 |
| `VAL005` | Formato de precio inválido | 400 |
| `VAL006` | Contraseña muy corta | 400 |
| `VAL007` | Valor de estado inválido | 400 |

### 🔐 Errores de Autenticación (AUTH)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `AUTH001` | Credenciales inválidas | 401 |
| `AUTH002` | Token expirado | 401 |
| `AUTH003` | Token inválido | 401 |
| `AUTH004` | Acceso no autorizado | 401 |
| `AUTH005` | Error de configuración JWT | 500 |

### 📦 Errores de Recursos (RES)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `RES001` | Recurso no encontrado | 404 |
| `RES002` | Recurso ya existe | 409 |
| `RES003` | Error al eliminar recurso | 500 |
| `RES004` | Error al actualizar recurso | 500 |
| `RES005` | Error al crear recurso | 500 |

### 🗄️ Errores de Base de Datos (DB)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `DB001` | Error de conexión a BD | 500 |
| `DB002` | Error en consulta de BD | 500 |
| `DB003` | Violación de restricción de BD | 400 |

### 📥 Errores de Entrada (INP)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `INP001` | Formato JSON inválido | 400 |
| `INP002` | Datos de entrada faltantes | 400 |
| `INP003` | Método de petición inválido | 405 |

### ⚙️ Errores del Sistema (SYS)

| Código | Descripción | HTTP Status |
|--------|-------------|-------------|
| `SYS001` | Error interno del servidor | 500 |
| `SYS002` | Error de configuración | 500 |
| `SYS003` | Servicio no disponible | 503 |

## Ejemplos de Uso

### Error de Validación
```json
{
    "success": false,
    "message": "Campos requeridos faltantes: email, password",
    "data": {
        "missing_fields": ["email", "password"]
    },
    "error_code": "VAL002"
}
```

### Error de Recurso No Encontrado
```json
{
    "success": false,
    "message": "Producto no encontrado",
    "data": [],
    "error_code": "RES001"
}
```

### Error de Credenciales Inválidas
```json
{
    "success": false,
    "message": "Credenciales inválidas",
    "data": [],
    "error_code": "AUTH001"
}
```

### Error de Base de Datos
```json
{
    "success": false,
    "message": "El recurso ya existe en el sistema",
    "data": [],
    "error_code": "RES002"
}
```

## Manejo en el Frontend

### JavaScript/TypeScript
```javascript
async function handleApiResponse(response) {
    const data = await response.json();
    
    if (!data.success) {
        switch (data.error_code) {
            case 'AUTH001':
                // Redirigir al login
                redirectToLogin();
                break;
            case 'AUTH002':
                // Renovar token
                refreshToken();
                break;
            case 'VAL002':
                // Mostrar campos faltantes
                showValidationErrors(data.data.missing_fields);
                break;
            case 'RES001':
                // Mostrar mensaje de no encontrado
                showNotFoundMessage(data.message);
                break;
            default:
                // Error genérico
                showErrorMessage(data.message);
        }
    }
    
    return data;
}
```

### React Hook
```javascript
const useApiError = () => {
    const handleError = (error) => {
        const errorCode = error.error_code;
        
        switch (errorCode) {
            case 'AUTH001':
                return { type: 'auth', action: 'redirect_to_login' };
            case 'VAL002':
                return { type: 'validation', fields: error.data.missing_fields };
            case 'RES001':
                return { type: 'not_found', message: error.message };
            default:
                return { type: 'general', message: error.message };
        }
    };
    
    return { handleError };
};
```

## Logging y Monitoreo

Los errores se registran en el servidor con el siguiente formato:

```
[ERROR] [2025-01-20 10:30:15] Database Error: Duplicate entry 'user@email.com' for key 'email'
Error Code: RES002
User ID: 123
Request: POST /api/employees
```

## Mejores Prácticas

1. **Siempre verificar el código de error** antes de procesar la respuesta
2. **Usar los códigos para lógica condicional** en lugar de analizar el mensaje
3. **Implementar manejo específico** para cada tipo de error
4. **Registrar errores** para debugging y monitoreo
5. **Proporcionar feedback claro** al usuario basado en el código de error

## Actualización de Códigos

Para agregar nuevos códigos de error:

1. Agregar la constante en `BaseController::ERROR_CODES`
2. Crear método específico en `BaseController` si es necesario
3. Actualizar esta documentación
4. Actualizar el frontend para manejar el nuevo código 
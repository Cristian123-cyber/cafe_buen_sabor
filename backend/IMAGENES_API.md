# API de Gestión de Imágenes de Productos

Esta documentación describe los endpoints disponibles para gestionar las imágenes de los productos.

## Base URL
```
http://localhost:8000/api
```

## Endpoints de Imágenes

### Subir Imagen de Producto
- **POST** `/api/productos/{id}/image`
- **Content-Type:** `multipart/form-data`
- **Headers:** `Authorization: Bearer <token>` (si es requerido)

**Parámetros:**
- `image` (file): Archivo de imagen (JPG, PNG, GIF, WebP)
- Máximo 5MB

**Ejemplo de uso con cURL:**
```bash
curl -X POST \
  http://localhost:8000/api/productos/1/image \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -F 'image=@/path/to/image.jpg'
```

**Respuesta exitosa:**
```json
{
    "success": true,
    "message": "Imagen subida correctamente",
    "data": {
        "product_id": 1,
        "image_url": "/images/products/product_1_1703123456_a1b2c3d4e5f6g7h8.jpg"
    }
}
```

**Respuesta de error:**
```json
{
    "success": false,
    "message": "Error al subir imagen: El archivo es demasiado grande. Máximo 5MB",
    "data": []
}
```

### Eliminar Imagen de Producto
- **DELETE** `/api/productos/{id}/image`
- **Headers:** `Authorization: Bearer <token>` (si es requerido)

**Ejemplo de uso con cURL:**
```bash
curl -X DELETE \
  http://localhost:8000/api/productos/1/image \
  -H 'Authorization: Bearer YOUR_TOKEN'
```

**Respuesta exitosa:**
```json
{
    "success": true,
    "message": "Imagen eliminada correctamente",
    "data": {
        "product_id": 1,
        "image_deleted": true
    }
}
```

## Características de la Implementación

### Validaciones
- **Tipos de archivo permitidos:** JPG, JPEG, PNG, GIF, WebP
- **Tamaño máximo:** 5MB
- **Validación MIME:** Verifica el tipo real del archivo
- **Validación de extensión:** Verifica la extensión del archivo

### Nomenclatura de Archivos
Los archivos se guardan con el siguiente formato:
```
product_{product_id}_{timestamp}_{random_hash}.{extension}
```

Ejemplo: `product_1_1703123456_a1b2c3d4e5f6g7h8.jpg`

### Ubicación de Archivos
- **Directorio:** `backend/public/images/products/`
- **URL base:** `/images/products/`
- **Acceso directo:** `http://localhost:8000/images/products/filename.jpg`

### Seguridad
- Validación de tipos MIME
- Verificación de extensiones permitidas
- Límite de tamaño de archivo
- Nombres únicos para evitar conflictos
- Verificación de rutas para prevenir directory traversal

### Gestión de Errores
- Si falla la actualización del producto, se elimina la imagen subida
- Validación de existencia del producto antes de subir imagen
- Mensajes de error descriptivos

## Ejemplos de Uso

### Frontend (JavaScript)
```javascript
// Subir imagen
const formData = new FormData();
formData.append('image', fileInput.files[0]);

fetch('/api/productos/1/image', {
    method: 'POST',
    headers: {
        'Authorization': 'Bearer ' + token
    },
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Imagen subida:', data.data.image_url);
    }
});

// Eliminar imagen
fetch('/api/productos/1/image', {
    method: 'DELETE',
    headers: {
        'Authorization': 'Bearer ' + token
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Imagen eliminada');
    }
});
```

### PHP (cURL)
```php
// Subir imagen
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/productos/1/image');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'image' => new CURLFile('/path/to/image.jpg')
]);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
curl_close($ch);
```

## Notas Importantes

1. **Permisos:** Asegúrate de que el directorio `backend/public/images/products/` tenga permisos de escritura (755).

2. **Backup:** Las imágenes se almacenan localmente. Considera implementar un sistema de backup.

3. **Limpieza:** El sistema incluye una función para limpiar imágenes huérfanas.

4. **Escalabilidad:** Para producción, considera usar un servicio de almacenamiento en la nube (AWS S3, Google Cloud Storage, etc.).

5. **Optimización:** Considera implementar redimensionamiento automático de imágenes para diferentes tamaños (thumbnail, medium, large). 
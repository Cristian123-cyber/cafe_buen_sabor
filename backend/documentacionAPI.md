# explicacion de logica y flujo de los endpoints

# ASEGURARSE DE SERGUIR LAS CONVENCIONES TANTO EN NOMBRES Y ENDPOINTS DE RUTAS COMO EN METODOS HTTP, Y EN SEPARACION DE RUTAS
# YA QUE CON LAS CONVENCIONES DEFININAS EN ESTE DOCUMENTO DE TRABAJARA LA LOGICA DEL MIDDLEWARE DEL ROUTER PARA PROTEGER 
# LAS RUTAS DE FORMA AGRUPADA (FUNCIONALIDAD MIDDLEWARE Y AUTH IMPLEMENTADA POR CRISTIAN)

## EJEMPLO DE COMO QUEDARA EL ROUTER AL FINAL CUANDO ESTEN TODAS LAS RUTAS DEFINIDAS EN EL FINAL DEL DOCUMENTO
## SOLO REGISTRAR LAS RUTAS POR AHORA, DESPUES SE PROTEGERAN AGRUPADAMENTE CON EL MIDDLEWARE PARA EVITAR CONFLICTOS MIENTRAS SE DESARROLLA

## EMPLOYEES:

# - RUTA BASE ESPERADA: api/employees/

# - CREAR UN EMPLEADO:

    - METODO: POST
    - ENDPOINT: api/employees/
    - ESTRUCTURA DE DATOS BASE ENVIADA:
        {
            "employe_name": "Juan Pérez",
            "employe_email": "juanperez@example.com",
            "password": "contrasenasegura123",
            "employees_statuses_id_status": 1, --siempre sera 1 (activo)
            "employees_rol_id_rol": 2,
            "employee_cc": "1020304050"
        }

        -CASOS ESPECIALES:
            -Cuando un empleado vaya con el rol 4 (TABLE_DEVICE) quiere decir que se registraran los
             datos de login para un dispositivo de mesa, esto para proteger las vistas QR con un login.
             cuando sea un empleado rol 4. se envia lo siguiente adicional:

             {
                "employe_name": "Dispositivo mesa X",
                "employe_email": "email@example.com",
                "password": "contrasenasegura123",
                "employees_statuses_id_status": 1, --siempre sera 1 (activo)
                "employees_rol_id_rol": **4**,
                "employee_cc": NULL,
                "table_id_device": 5 --- ID DE LA MESA ASOCIADA (SOLO PARA DEVICES).
             }

             -Cuando no sea un empleado rol 4. dejar el table_id_device null en la BD

        -IMPORTANTE: El campo created_at en la BD se debe enviar en la consulta con la funcion CURDATE
         o la de su preferencia, no se envia en el payload.
        -IMPORTANTE: La logica de los refresh tokens y access_tokens sera manejada despues

# EDITAR UN EMPLEADO:

    -METODO: PUT
    -ENDPOINT: api/employees/{id}
    -ESTRUCTURA DE DATOS ENVIADA:
        {
            "employe_name": "Juan Actualizado",
            "employe_email": "juan.nuevo@example.com",
            "password": "nuevaClave123",
            "employees_statuses_id_status": 2,
            "employees_rol_id_rol": 1,
            "employee_cc": "1234567890",
            "table_id_device": 3
        }

        -CASOS ESPECIALES: NO HAY, sea un empleado rol 4 o no se hace el update de todo

# ELIMINAR UN EMPLEADO:

    -METODO: DELETE
    -ENDPOINT: api/employees/{id}
    -ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Se hace borrado logico con el campo employees_statuses_id_status (id **3**)

# OBTENER LISTA DE EMPLEADOS (INCLUIDOS DEVICES)

    -METODO: GET
    -DNPOINT: api/employees/
    -ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    IMPORTANTE: No enviar contraseñas.
    Para los roles y los estados enviar tanto el ID como el nombre del estado y rol (JOINS).

# OBTENER UN EMPLEADO ESPECIFICO

    -METODO: GET
    -ENDPOINT: api/employees/{id}
    -ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    IMPORTANTE: No enviar contraseñas.
    Para los roles y los estados enviar tanto el ID como el nombre del estado y rol (JOINS).

# ADICIONALES:

    -Mantener consistencia estructural en los datos tanto en envios como en respuestas
    -Es decir:
    si se envia esto, devolver esta misma estructura:
            {
                "employe_name": "Dispositivo mesa X",
                "employe_email": "email@example.com",
                "password": "contrasenasegura123",
                "employees_statuses_id_status": 1, --siempre sera 1 (activo)
                "employees_rol_id_rol": **4**,
                "employee_cc": NULL,
                "table_id_device": 5 --- ID DE LA MESA ASOCIADA (SOLO PARA DEVICES).
             }

## PRODUCTOS:

# - RUTA BASE ESPERADA: api/products/

# - CREAR UN PRODUCTO:

    -EL PROCESO DE CREACION DE UN PRODUCTO TIENE UNA LOGICA ESPECIAL


    - **PRIMERO SE ENVIA TODA LA DATA DEL PRODUCTO SIN LA IMAGEN EN JSON**
    - METODO: POST
    - ENDPOINT: api/products/

    - ESTRUCTURA DE DATOS BASE ENVIADA:


        - **PRODUCTOS PREPARADOS**:

            -Para los productos preparados se debe tener en cuenta que se componen de ingredientes
            asi que deben asociarse a ingredientes:

            JSON:
            {
                "product_name": "Torta de chocolate",
                "product_price": 15000.00,
                "product_cost": 9000.00,
                "product_desc": "Deliciosa torta de chocolate con crema",
                "product_stock": null, (no aplica para preparados)
                "low_stock_level": null, (no aplica para preparados)
                "critical_stock_level": null, (no aplica para preparados)
                "ingredient_statuses_id_status": null, (no aplica para preparados)
                "product_types_id_type": **1** (id de los preparados)
                "ingredients": [
                    {
                        "id": 1,
                        "cantidad": 100
                    },
                    {
                        "id": 2,
                        "cantidad": 100
                    },
                    {
                        "id": 3,
                        "cantidad": 100
                    },
                    ...Mas ingredientes
                ]
            }

            -IMPORTANTE: Los campos null es por que no aplican para un producto preparado ya que estos datos iran en los ingredientes

                -Se debe manejar la logica de agregado en la tabla intermedia entre productos e ingredientes (products_has_ingredients) recorriendo la lista de ingredientes y
                haciendo el respectivo insert asociando el id de producto, id de ingrediente y su cantidad

                -Esto solo para los productos que lleven el product_type = 1

         - **PRODUCTOS NO PREPARADOS**:

              -Para los productos NO preparados se debe tener en cuenta que el stock se maneja en la misma tabla

              JSON:
              {
                "product_name": "Torta de chocolate",
                "product_price": 15000.00,
                "product_cost": 9000.00,
                "product_desc": "Deliciosa torta de chocolate con crema",
                "product_stock": 30, --APLICA
                "low_stock_level": 10, --APLICA
                "critical_stock_level": 3, --APLICA
                "ingredient_statuses_id_status": 1, --APLICA (Mismo estado que los ingredientes)
                "product_types_id_type": **2** -- tipo no preparado
            }

            -IMPORTANTE: Las unidad de medida para los productos no preparados es Unidad, no se manejan unidades especiales.

            -Esto solo para los productos que lleven el product_type = 2

    -IMPORTANTE: Se debe de devolver en la respuesta exitosa el ID del producto agregado
    esto para manejar la subida de la imagen por separado para evitar confusiones y mantener
    el sistema limpio y mantenible.
    Con ese id se hara otra solicitud a otro endpoint descrito a continuacion.

# SUBIR IMAGEN DE UN PRODUCTO:

    - METODO: POST
    - ENDPOINT: api/products/{id_producto}/image
    - Content-Type: multipart/form-data -- Tipo de dato para subida de imagenes

    - ESTRUCTURA DE DATOS BASE ENVIADA:

        image: (archivo .jpg o .png)


    INSTRUCCIONES DE MANEJO BACKCEND (Documentarse de todas formas y adaptar a la arquitectura implementada):

```php

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $productId = $_GET['id'] ?? null;

                if (!isset($_FILES['image']) || $_FILES['image']['error'] !== 0) {
                    http_response_code(400);
                    echo json_encode(['error' => 'No se recibió una imagen válida.']);
                    exit;
                }

                // Guardar imagen...
                $imageName = uniqid() . '_' . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/uploads/$imageName");

                $imageUrl = "https://tusitio.com/uploads/$imageName";

                // Actualizar el producto con la URL
                $stmt = $pdo->prepare("UPDATE products SET product_image_url = ? WHERE id_product = ?");
                $stmt->execute([$imageUrl, $productId]);

                echo json_encode(['message' => 'Imagen subida correctamente', 'url' => $imageUrl]);

            }

```

        -IMPORTANTE: Guardar URL en el campo del image_url del producto enviado

# EDITAR UN PRODUCTO:

    - MÉTODO: PUT
    - ENDPOINT: api/products/{id_producto}


    -LOGICA GENERAL:
        Este endpoint permite actualizar un producto existente, tanto si es preparado como si no lo es.
        La lógica varía según el tipo (product_types_id_type):

        1 = Preparado

        2 = No preparado


        -ESTRUCTURA DE DATOS ESPERADA:
        JSON:
        PRODUCTOS PREPARADOS (tipo = 1):

```json
{
  "product_name": "Torta de fresas",
  "product_price": 16000.0,
  "product_cost": 10000.0,
  "product_desc": "Torta de fresas con crema",
  "product_stock": null,
  "low_stock_level": null,
  "critical_stock_level": null,
  "ingredient_statuses_id_status": null,
  "product_types_id_type": 1,
  "ingredients": [
    { "id": 1, "cantidad": 50 },
    { "id": 4, "cantidad": 80 }
  ]
}
```

            IMPORTANTE:
            Actualizar datos base del producto.
            Verificar si ingredients viene en el body.

            Si viene:

            Eliminar los registros anteriores (DELETE FROM products_has_ingredients WHERE id_producto = {id}) del producto en la tabla intermedia products_has_ingredients.
            Insertar los nuevos ingredientes con sus cantidades.

            No modificar product_stock, low_stock_level, ni ingredient_statuses_id_status. (PARA TIPO 1)

         PRODUCTOS NO PREPARADOS (tipo = 2):

```json
{
  "product_name": "Café americano",
  "product_price": 4000.0,
  "product_cost": 1500.0,
  "product_desc": "Café negro sin azúcar",
  "product_stock": 100,
  "low_stock_level": 20,
  "critical_stock_level": 5,
  "ingredient_statuses_id_status": 1,
  "product_types_id_type": 2
}
```

            IMPORTANTE:
            Actualizar todos los campos directamente.
            Ignorar el campo ingredients si viene.

            product_stock, low_stock_level, critical_stock_level, y ingredient_statuses_id_status deben ser actualizados normalmente.



    **IMPORTANTE:** La lógica se basa en el valor de `product_types_id_type`.

# ELIMINAR UN PRODUCTO

    -METODO: DELETE
    -ENDPOINT: api/products/{id}

    - ESTRUCTURA DE DATOS BASE ENVIADA: **NO APLICA**
    **IMPORTANTE:** La lógica se basa en el valor de `product_types_id_type`.




    Tipo 1 (Preparado):
    Se debe eliminar primero cualquier relación con ingredientes en la tabla intermedia products_has_ingredients.

    Tipo 2 (No preparado):
    Se puede eliminar directamente el registro del producto sin relaciones adicionales.


    IMPORTANTE: Implementar borrado logico con el campo product_types_id_type estableciendo el tipo 3 (Producto eliminado) se debe agregar este tipo si no se tiene en la BD

# OBTENER TODOS LOS PRODUCTOS:

    -METODO: GET
    -ENDPOINT: api/products/


    ESTRUCTURA DE DATOS ENVIDA: **NO APLICA**


    IMPORTANTE: Devolver esta estructura:

```json

    [
        {
            "productos_preparados": [
                {
                    "id_product": 1,
                    "product_name": "Torta de chocolate",
                    "product_price": 15000.00,
                    "product_cost": 9000.00,
                    "product_desc": "Deliciosa torta de chocolate con crema",
                    ...otros campos del producto preparado,
                    "ingredients": [
                        ...todos los ingredientes del producto preparado (JOINS para obtener nombre, unidad de medida y demas etc.)
                    ],

                }
                ...todos los productos preparados (junto con sus ingredientes)
            ],
            "productos_no_preparados": [
                {

                }
                ...todos los productos no preparados
            ]

        }
    ]


```

# OBTENER UN PRODUCTO ESPECIFICO:

    -METODO: GET
    -ENDPOINT: api/products/{id_producto}

    ESTRUCTURA DE DATOS ENVIDA: **NO APLICA**

    IMPORTANTE: Devolver esta estructura:
    **IMPORTANTE:** La lógica se basa en el valor del tipo de producto del producto solicitado.

    -Productos preparados (tipo = 1):

```json
    {
        "id_product": 1,
        "product_name": "Torta de chocolate",
        "product_price": 15000.00,
        "product_cost": 9000.00,
        "product_desc": "Deliciosa torta de chocolate con crema",
        ...otros campos del producto preparado,
        "ingredients": [
            {
                "id": 1,
                "cantidad": 100,
                "ingredient_name": "Azúcar",
                "units_of_measure_id_unit": 2
            },
            ...otros ingredientes
        ]
    }
```

    -Productos no preparados (tipo = 2):

```json
    {
        "id_product": 2,
        "product_name": "Café americano",
        "product_price": 4000.00,
        "product_cost": 1500.00,
        "product_desc": "Café
    }
```

# RESTOCK DE UN PRODUCTO:

    - METODO: PUT
    - ENDPOINT: api/products/{id_producto}/restock
    - ESTRUCTURA DE DATOS ENVIADA:

```json
        {
            "product_stock": 10-- Cantidad a reabastecer
        }
```

    -IMPORTANTE: Actualizar el stock del producto sumando la cantidad enviada al stock actual.
    Esto aplica solo para productos no preparados (tipo = 2).

    -VALIDAR SIEMPRE QUE EL TIPO DE PRODUCTO SEA 2 (NO PREPARADO) ANTES DE HACER EL RESTOCK.

## INGREDIENTS:

# - RUTA BASE ESPERADA: api/ingredients/

# - CREAR UN INGREDIENTE:

    - METODO: POST
    - ENDPOINT: api/ingredients/
    - ESTRUCTURA DE DATOS BASE ENVIADA:
        {
            "ingredient_name": "Azúcar",
            "ingredient_stock": 150.00,
            "low_stock_level": 50.00,
            "critical_stock_level": 20.00,
            "ingredient_statuses_id_status": 1,
            "units_of_measure_id_unit": 2
        }

    -IMPORTANTE: Las tablas tipos y unidades de medida deben estar creadas y pobladas con los datos necesarios. (NO ES NECESARIO IMPLEMENTAR ENDPOINTS EN LA API PARA CREAR O GESTIONAR ESAS TABLAS, SE ASUME QUE YA EXISTEN, MAS ADELANTE SE PUEDE IMPLEMENTAR UN ENDPOINT PARA GESTIONAR ESTAS TABLAS SI SE DESEA)

# EDITAR UN INGREDIENTE:

    - METODO: PUT
    - ENDPOINT: api/ingredients/{id}
    - ESTRUCTURA DE DATOS ENVIADA:
        {
            "ingredient_name": "Azúcar moreno",
            "ingredient_stock": 200.00,
            "low_stock_level": 60.00,
            "critical_stock_level": 30.00,
            "ingredient_statuses_id_status": 2,
            "units_of_measure_id_unit": 3
        }

    -IMPORTANTE: Actualizar todos los campos del ingrediente, incluyendo el estado y la unidad de medida.

# ELIMINAR UN INGREDIENTE:

    - METODO: DELETE
    - ENDPOINT: api/ingredients/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Se puede hacer borrado total o borrado lógico, dependiendo de la implementación. Si se hace borrado lógico, se debe crear el registro de estado de ingrediente elimado en la tabla ingredients_statuses.

# OBTENER LISTA DE INGREDIENTES:

    - METODO: GET
    - ENDPOINT: api/ingredients/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los ingredientes con sus respectivos estados y unidades de medida, tanto IDs como data especifica  (JOINS).

# OBTENER UN INGREDIENTE ESPECIFICO:

    - METODO: GET
    - ENDPOINT: api/ingredients/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver el ingrediente con sus respectivos estados y unidades de medida, tanto IDs como data especifica (JOINS).

# HACER RESTOCK DE UN INGREDIENTE:

    - METODO: PUT
    - ENDPOINT: api/ingredients/{id}/restock
    - ESTRUCTURA DE DATOS ENVIADA:

```json
        {
            "ingredient_stock": 100.00 -- Cantidad a reabastecer
        }
```

    -IMPORTANTE: Actualizar el stock del ingrediente sumando la cantidad enviada al stock actual.

## TABLES:

# - RUTA BASE ESPERADA: api/tables/

# - CREAR UNA MESA:

    - METODO: POST
    - ENDPOINT: api/tables/
    - ESTRUCTURA DE DATOS BASE ENVIADA:

```json
            {
                "table_number": 5, -- Numero de la mesa
                "table_status": "FREE" (ENUM: ["FREE", "OCCUPIED", "INACTIVE"]),
            }
```

        **IMPORTANTE**: Los campos qr_token Y token_expiration SE DEBEN GENERAR AUTOMATICAMENTE EN EL BACKEND AL MOMENTO DE CREAR LA MESA Y ASIGNARSE A LOS DATOS DE LA MESA E INSERTAR EN BD.
        (Documentarse de como generar un token unico y una fecha de expiracion para el token, se puede usar librerias de generacion de tokens o UUIDs)

        "POR EL MOMENTO SOLO ESO, DESPUES SE IMPLEMENTARA UN CRON JOB EN EL SERVIDOR QUE SE ENCARGE DE GENERAR LOS TOKENS Y ASIGNARLOS A LAS MESAS CADA X TIEMPO, con script de python o el lenguaje que se desee, pero por ahora la generacion del token es independiente de la regeneracion"

# EDITAR UNA MESA:

    - METODO: PUT
    - ENDPOINT: api/tables/{id}
    - ESTRUCTURA DE DATOS ENVIADA:

```json
            {
                "table_number": 5, -- Numero de la mesa
                "table_status": "OCCUPIED" (ENUM: ["FREE", "OCCUPIED", "INACTIVE"]),
            }
```

    **IMPORTANTE**: No se debe modificar el qr_token ni el token_expiration al editar una mesa.

# ELIMINAR UNA MESA:

    - METODO: DELETE
    - ENDPOINT: api/tables/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Se debe hacer borrado logico con el campo table_status estableciendo el estado "DELETED" (ENUM: ["FREE", "OCCUPIED", "INACTIVE", "DELETED"]).

# OBTENER LISTA DE MESAS:

    - METODO: GET
    - ENDPOINT: api/tables/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**
    **IMPORTANTE**: Devolver una lista de todas las mesas con sus respectivos estados y datos, incluyendo el qr_token y fecha expiration.

# OBTENER UNA MESA ESPECIFICA:

    - METODO: GET
    - ENDPOINT: api/tables/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**
    **IMPORTANTE**: Devolver la mesa con sus respectivos estados y datos, incluyendo el qr_token y fecha_expiration.

# DESACTIVAR UNA MESA:

    - METODO: PUT
    - ENDPOINT: api/tables/{id}/deactivate
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Cambiar el estado de la mesa a "INACTIVE" (ENUM: ["FREE", "OCCUPIED", "INACTIVE", "DELETED"]).
    Esto se usa para desactivar una mesa sin eliminarla, por ejemplo, si una mesa no se usa por un tiempo prolongado.

# ACTIVAR UNA MESA:

    - METODO: PUT
    - ENDPOINT: api/tables/{id}/activate
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Cambiar el estado de la mesa a "FREE" (ENUM: ["FREE", "OCCUPIED", "INACTIVE", "DELETED"]).
    Esto se usa para activar una mesa que estaba inactiva, permitiendo que vuelva a estar disponible para uso.

# VALIDAR QR DE UNA MESA:

    - METODO: POST
    - ENDPOINT: api/tables/validate-token
    - ESTRUCTURA DE DATOS ENVIADA:

```json
{
  "qr_token": "token_de_la_mesa",
  "table_id": 1
}
```

    **IMPORTANTE**: Validar el token QR de la mesa y verificar que no haya expirado.

    -Si el token es válido: **ATENCION (LOGICA ESPECIAL)**
        - Verificar internamente si existe una sesion activa para la mesa en la tabla table_sessions.

        -Consulta:

```sql
        SELECT * FROM table_sessions WHERE session_status = "ACTIVE" AND tables_id_table = {id_table}
```

        - Si no existe una sesion activa (No devuelve nada la consulta anterior), SE DEBE CREAR UNA NUEVA SESION EN LA TABLA table_sessions ASOCIANDO EL ID DE LA MESA.
        Y DEVOLVER LOS DATOS DE ESA SESSION (ID, ID DE LA MESA, ESTADO DE LA SESION, FECHA DE CREACION, ETC).

        - Si existe una sesion activa. reutilizar esa session y devolver sus datos.
        -Adicional: validar antes de enviar que el estado de la session no sea EXPIRED

        (ESTO SE HACE PARA QUE MULTIPLES USUARIOS PUEDAN ESCANEAR EL QR DE LA MISMA MESA Y NO SE CREE UNA NUEVA SESION CADA VEZ QUE SE ESCANEE EL QR, SINO QUE SE REUTILICE LA MISMA SESION ACTIVA PARA ESA MESA Y ASOCIAR ESA SESION A LOS PEDIDOS QUE SE REALICEN DESDE ESA MESA)



    -Si el token es inválido o ha expirado, devolver un mensaje de unauthorized con codigo de estado 401.


## TABLE_SESSIONS:
# - RUTA BASE ESPERADA: api/table-sessions/

# OBTENER TODAS LAS SESSIONES DE MESA 
    - METODO: GET
    - ENDPOINT: api/table-sessions/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Devolver una lista de todas las sesiones de mesa con sus respectivos estados y datos de la mesa asociada a la session (JOINS) NO IMPORTA EL ESTADO, DEVOLVER TODAS LAS SESSIONES EXISTENTES.


# OBTENER SESIONES DE MESA POR ESTADO
    - METODO: GET
    - ENDPOINT: api/table-sessions/status/{status}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Devolver una lista de todas las sesiones de mesa filtradas por el estado especificado en el endpoint (ACTIVE, EXPIRED, CLOSED). Incluir TODOS LOS DATOS DE SESSION, numero de mesa.

# CERRAR TABLE SESSION 
    - METODO: PUT
    - ENDPOINT: api/table-sessions/{id}/close
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    **IMPORTANTE**: Cambiar el estado de la session a CLOSED (ENUM: ["ACTIVE", "EXPIRED", "CLOSED"]).
    Esto se usa para cerrar una session de mesa desde el admin por alguna razon. 

    -IMPORTANTE: NO SE NECESITA NADA MAS EN TABBLE SESSION POR AHORA, NI METODOS PARA CREAR EDITAR NI NADA  

## - ORDERS:

# - RUTA BASE ESPERADA: api/orders/

# - CREAR UN PEDIDO:

    - METODO: POST
    - ENDPOINT: api/orders/
    - ESTRUCTURA DE DATOS BASE ENVIADA:

```json

        {
            "table_session_id": 7 -- ID DE LA SESION DE LA MESA DESDE LA QUE SE REALIZA EL PEDIDO,
            "products": [
                {
                    "id_product": 1, -- ID DEL PRODUCTO
                    "quantity": 2, -- CANTIDAD DEL PRODUCTO
                },
                {
                    "id_product": 2,
                    "quantity": 1
                }
            ],
        }
```

        -REGISTRAR PRODUCTOS EN LA TABLA INTERMEDIA orders_has_products:
        -IMPORTANTE: Se debe recorrer el array de products y hacer un insert en la tabla asociando cada producto a el order id generado despues de insertar los datos principales de la orden.

        SE REGISTRA LA CANTIDAD DE PRODUCTO Y EL PRECIO ACTUAL QUE TIENE EL PRODUCTO EN EL MOMENTO DE CREAR EL PEDIDO. PARA CADA PRODUCTO. ESTO PARA MANTENER LA CONTABILIDAD EN CASO DE QUE EL PRECIO DEL PRODUCTO CAMBIE DESPUES DE CREAR EL PEDIDO.

        -IMPORTANTE: Los campos created_at se deben enviar en la consulta con la funcion CURDATE
         o la de su preferencia, no se envia en el payload.

         El campo total_amount se calcula automaticamente en el backend sumando el precio de los productos por la cantidad y guardandolo en la base de datos.

         El campo waiter_id se deja null por el momento, ya que el que se encarga de hacer esa asociacion es el cajero al momento de cerrar el pedido y crear la venta.

         El campo order_status se deja en "PENDING" automaticamente (ENUM: ["PENDING", "CONFIRM", "READY", "CANCELED", "COMPLETED"]) ya que el pedido esta pendiente de ser procesado.

        -IMPORTANTE: Se debe validar que la session de la mesa este activa y no haya expirado antes de crear el pedido. esto consultando la tabla table_sessions y verificando el estado de la session.

# EDITAR UN PEDIDO: **POR MOTIVOS DE SEGURIDAD NO SE PODRA EDITAR LA DATA DE LOS PEDIDOS, EN CASO DE QUE LOS CLIENTES QUIERAN CAMBIAR LA DATA DE PEDIDO DESPUES DE HABERLO HECHO, SOLO SE CANCELARA EL PEDIDO Y SE LE PEDIRA QUE VUELVA A REALIZAR EL PEDIDO**

# CAMBIAR ESTADO UN PEDIDO A CONFIRMADO:

    - METODO: PUT
    - ENDPOINT: api/orders/{id}/confirm
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Cambiar estado del pedido a confirmado CONFIRMED ID **2**

# CAMBIAR ESTADO UN PEDIDO A CANCELADO:

    - METODO: PUT
    - ENDPOINT: api/orders/{id}/cancel
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Cambiar estado del pedido a confirmado CANCELED ID **3**

# CAMBIAR ESTADO UN PEDIDO A READY:

    - METODO: PUT
    - ENDPOINT: api/orders/{id}/ready
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Cambiar estado del pedido a confirmado READY ID **4**

# CAMBIAR ESTADO UN PEDIDO A COMPLETED:

    - METODO: PUT
    - ENDPOINT: api/orders/{id}/complete
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Cambiar estado del pedido a confirmado COMPLETED ID **5**

**ESTAS ACCIONES DE ESTADO SE DEBEN HACER POR ENDPOINTS SEPARADOS YA QUE CADA ACCION DEBE PROTEGERSE SEGUN EL ROL

# OBTENER TODOS LOS PEDIDOS:

    - METODO: GET
    - ENDPOINT: api/orders/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los pedidos con sus respectivos estados y datos de la mesa asociada a la session del pedido (JOINS). MENOS LOS PRODUCTOS ASOCIADOS AL PEDIDO, ESTO SE HARA EN UN ENDPOINT SEPARADO PARA OBTENER LOS PRODUCTOS DE UN PEDIDO ESPECIFICO.

# OBTENER UN PEDIDO ESPECIFICO:

    - METODO: GET
    - ENDPOINT: api/orders/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver el pedido con sus respectivos estados y datos de la mesa asociada a la session del pedido (JOINS). INCLUYENDO LOS PRODUCTOS ASOCIADOS AL PEDIDO. SE USARA ESTE ENDPOINT PARA VISTAS DETALLADAS DE PEDIDOS EN EL FRONTEND.

# OBTENER PEDIDOS POR ESTADO

    - METODO: GET
    - ENDPOINT: api/orders/status/{status}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los pedidos filtrados por el estado especificado en el endpoint (PENDING, CONFIRM, READY, CANCELED, COMPLETED). Incluir TODOS LOS DATOS DE PEDIDO INCLUIDOS PRODUCTOS Y ESTADOS. IDS Y VALORES.

# OBTENER PEDIDOS DE UNA SESION DE MESA:

    - METODO: GET
    - ENDPOINT: api/orders/session/{table_session_id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los pedidos asociados a la session de mesa especificada, incluyendo todos los datos de pedido, productos y estados. IDS Y VALORES.

# ASOCIAR MESERO A UNA LISTA DE PEDIDOS BASADO EN UNA SESSION DE MESA:

    - METODO: PUT
    - ENDPOINT: api/orders/bind-waiter/
    - ESTRUCTURA DE DATOS ENVIADA:

```json
        {
            "table_session_id": 7, -- ID DE LA SESION DE LA MESA
            "waiter_id": 3 -- ID DEL MESERO A ASOCIAR
        }
```

-IMPORTANTE: Se debe consultar internamente en el back todos los pedidos asociados a esa session enviada.
Y asociar a cada pedido el id del mesero que se envia.

- VALIDAR QUE EL ID ENVIADO SI CORRESPONDA A UN EMPLEADO CON EL ROL DE MESERO.

# UNIFICAR PEDIDOS EN UNO SOLO

    - METODO: POST
    - ENDPOINT: api/orders/unify
    - ESTRUCTURA DE DATOS ENVIADA:

```json

        {
            "orders_to_unify": [1, 2, 3], -- IDs DE LOS PEDIDOS A UNIFICAR
        }
```

    -IMPORTANTE: Se debe validar que todos los pedidos enviados pertenezcan a la misma session de mesa.
    -IMPORTANTE:
        INTERNAMENTE SE DEBE CREAR UN REGISTRO DE ORDEN UNIFICADA EN LA TABLA orders_unified Y CON ESE ID GENERADO SE DEBE INSERTAR EN LA TABLA orders_has_unified_has_orders ASOCIANDO LOS PEDIDOS UNIFICADOS CON EL ID DE LA ORDEN UNIFICADA CREADA.

        ESTO SOLO SE HACE PARA QUE LOS DATOS QUEDEN MEJOR ALMACENADOS Y SEA MAS FACIL CONSULTARLOS PARA MOSTRAR LOS PEDIDOS UNIFICADOS EN EL FRONTEND.

        ESTO NO TENDRA NADA QUE VER CON LA LOGICA DE VENTAS.

    - DEVOLVER EN LA RESPUESTA EL ID DE ORDEN UNIFICADA.

# OBTENER PEDIDOS DE UNA ORDEN UNIFICADA:

    - METODO: GET
    - ENDPOINT: api/orders/unified/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los pedidos asociados a la orden unificada especificada, incluyendo todos los datos de pedido, productos y estados. IDS Y VALORES.

    -IMPORTANTE: Se debe consultar internamente en el back todos los pedidos asociados a esa orden unificada enviada.

## SALES:

# - RUTA BASE ESPERADA: api/sales/

# - CREAR UNA VENTA:

    - METODO: POST
    - ENDPOINT: api/sales/
    - ESTRUCTURA DE DATOS BASE ENVIADA:

```json
        {
            "orders": [1,2,3], -- IDs PEDIDOS A ASOCIAR A LA VENTA
            "cashier_id": 3, -- ID DEL CAJERO QUE REGISTRA LA VENTA
            "payment_method": "EFECTIVO", -- METODO DE PAGO (ENUM: ["EFECTIVO", "TRANSFERENCIA"])

        }
```

    -IMPORTANTE: Se debe validar que el cashier_id enviado corresponda a un empleado con el rol de cajero.

    SE DEBE RECORRER EL ARRAY DE ORDERS Y HACER UN INSERT EN LA TABLA sales_has_orders ASOCIANDO CADA PEDIDO CON LA VENTA CREADA.

    EL CAMPO total_amount se calcula automaticamente en el backend sumando el total de los pedidos asociados a la venta y guardandolo en la base de datos.

    El CAMPO created_at se debe enviar en la consulta con la funcion CURDATE
     o la de su preferencia, no se envia en el payload.

# EDITAR UNA VENTA: **POR MOTIVOS DE SEGURIDAD NO SE PODRA EDITAR LA DATA DE LAS VENTAS DESPUES DE REALIZARSE**

# CANCELAR UNA VENTA:

    - METODO: PUT
    - ENDPOINT: api/sales/{id}
    - ESTRUCTURA DE DATOS ENVIADA:

```json
        {
            "sale_status": "CANCELED" -- Cambiar el estado de la venta a CANCELED (ENUM: ["COMPLETED", "CANCELED"])
        }
```

# OBTENER TODAS LAS VENTAS:

    - METODO: GET
    - ENDPOINT: api/sales/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todas las ventas con sus respectivos estados y datos de los pedidos asociados a la venta (JOINS). NO ENVIAR LA DATA DE PEDIDOS NI PRODUCTOS EN LOS PEDIDOS.
    ESO SE ENVIARA EN UN ENDPOINT SEPARADO PARA OBTENER LA DATA DE UNA VENTA ESPECIFICA.

# OBTENER UNA VENTA ESPECIFICA:

    - METODO: GET
    - ENDPOINT: api/sales/{id}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver la venta con sus respectivos estados y datos de los pedidos asociados a la venta (JOINS). INCLUYENDO LOS PRODUCTOS ASOCIADOS A LOS PEDIDOS DE LA VENTA. SE USARA ESTE ENDPOINT PARA VISTAS DETALLADAS DE VENTAS EN EL FRONTEND.

# OBTENER VENTAS POR ESTADO

    -METODO: GET
    -ENDPOINT: api/sales/status/{status}
    -ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todas las ventas filtradas por el estado especificado en el endpoint (COMPLETED, CANCELED). INCLUIR SOLO LOS DATOS DE VENTA, NO LOS PEDIDOS NI PRODUCTOS ASOCIADOS A LA VENTA.
    LA DATA ESPECIFICA SE OBTIENE CON EL ENDPOINT DE OBTENER UNA VENTA ESPECIFICA.

## NOTIFICATIONS:

# - RUTA BASE ESPERADA: api/notifications/

# - CREAR UNA NOTIFICACION:

    - METODO: POST
    - ENDPOINT: api/notifications/
    - ESTRUCTURA DE DATOS BASE ENVIADA:

```json
       {
            "notification_type": "ORDER", -- TIPO DE NOTIFICACION (ENUM: ["ORDER_READY", "ORDER_CONFIRMED", "ORDER_CANCELED"]),
            "message": "Nuevo pedido recibido", -- MENSAJE DE LA NOTIFICACION
            "order_id": 3, -- ID DE LA ORDEN ASOCIADA
            "employee_id": 2 -- ID QUE DISPARA LA NOTIFICACION
        }
       }
```

    -IMPORTANTE: Se debe validar que el tipo de notificacion enviado corresponda a un tipo valido y que los ids enviados correspondan a registros existentes en la base de datos.

    La tabla no tiene relaciones con otras tablas, se maneja de forma independiente.
    es una tabla de notificaciones que se pueden consultar y eliminar.

    Se limpiara la tabla de notificaciones cada cierto tiempo (cron job o script o eventos) para evitar que se llene de notificaciones antiguas.

# OBTENER NOTIFICACIONES POR ESTADO:

    - METODO: GET
    - ENDPOINT: api/notifications/status/{status}
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todas las notificaciones filtradas por el estado especificado en el endpoint (UNREAD, READ). Incluir todos los datos de la notificacion, incluyendo el tipo, mensaje, id de la orden asociada y data del empleado que disparo la notificacion.
    recordar que la tabla no tieene relaciones con otras tablas, se maneja de forma independiente.
    asi que no se pueden hacer joins, deben consultarse los datos de forma independiente.

## mas adelante se hara mas robusta esta funcionalidad para enviar notificaciones de todo tipo, pero por ahora se maneja de forma basica solo para ordenes entre meseros y cocineros.




# OBTENER TODAS LAS NOTIFICACIONES:

    - METODO: GET
    - ENDPOINT: api/notifications/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todas las notificaciones con sus respectivos estados y datos de la notificacion, incluyendo el tipo, mensaje, id de la orden asociada y data del empleado que disparo la notificacion.
    recordar que la tabla no tieene relaciones con otras tablas, se maneja de forma independiente.
    asi que no se pueden hacer joins, deben consultarse los datos de forma independiente.               

## UNITS OF MEASURE:

# - RUTA BASE ESPERADA: api/units-of-measure/

# - OBTENER TODAS LAS UNIDADES DE MEDIDA:

    - METODO: GET
    - ENDPOINT: api/units-of-measure/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todas las unidades de medida con sus respectivos estados y datos de la unidad de medida, incluyendo el id y el nombre de la unidad de medida y sus abreviaturas.

**SOLO SE NECESITA OBTENERLAS TODAS NO SE NECESITAN CREAR NI EDITAR NI NADA MAS**



## EMPLOYEES ROLES:
# - RUTA BASE ESPERADA: api/employees/roles/

# - OBTENER TODOS LOS ROLES DE EMPLEADOS:

    - METODO: GET
    - ENDPOINT: api/employees/roles/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los roles de empleados con sus respectivos datos del rol, incluyendo el id y el nombre del rol.

**SOLO SE NECESITA OBTENERLOS TODOS NO SE NECESITAN CREAR NI EDITAR NI NADA MAS**


## EMPLOYEES STATUSES:
# - RUTA BASE ESPERADA: api/employees/statuses/
# - OBTENER TODOS LOS ESTADOS DE EMPLEADOS:

    - METODO: GET
    - ENDPOINT: api/employees/statuses/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los estados de empleados con sus respectivos datos del estado, incluyendo el id y el nombre del estado.

**SOLO SE NECESITA OBTENERLOS TODOS NO SE NECESITAN CREAR NI EDITAR NI NADA MAS**

## PRODUCT TYPES:
# - RUTA BASE ESPERADA: api/products/types/


# - OBTENER TODOS LOS TIPOS DE PRODUCTOS:

    - METODO: GET
    - ENDPOINT: api/products/types/
    - ESTRUCTURA DE DATOS ENVIADA: **NO APLICA**

    -IMPORTANTE: Devolver una lista de todos los tipos de productos con sus respectivos datos del tipo, incluyendo el id y el nombre del tipo.

**SOLO SE NECESITA OBTENERLOS TODOS NO SE NECESITAN CREAR NI EDITAR NI NADA MAS**








### EJEMPLO ROUTER IMPLEMENTACION:
```php
<?php
// src/router.php

use Bramus\Router\Router;
use App\Middleware\AccessControlMiddleware;

$router = new Router();
$router->setNamespace('\App\Controllers');

// ========================================
// 1. RUTAS PÚBLICAS
// ========================================
// Estas rutas NO están protegidas y son los puntos de entrada.
// ----------------------------------------

// El login maneja tanto empleados como dispositivos.
$router->post('/api/auth/login', 'AuthController@login'); 

// La validación del QR para iniciar la sesión del cliente.
$router->post('/api/sessions/validate-qr', 'TableSessionController@validateQrAndStartSession');


// ========================================
// 2. REGISTRO DE RUTAS PROTEGIDAS
// ========================================


// --- Rutas de Productos ---
// Accesibles para Clientes y cualquier Empleado.
$router->before('GET', '/api/products.*', function() {
    AccessControlMiddleware::handle([], true); // Roles: [], allowClient: true
});
$router->get('/api/products', 'ProductController@index');
$router->get('/api/products/(\d+)', 'ProductController@show');

// El CRUD de productos solo para Administradores.
$router->before('POST|PUT|DELETE', '/api/products.*', function() {
    AccessControlMiddleware::handle(['Administrador']);
});
$router->post('/api/products', 'ProductController@store');
$router->put('/api/products/(\d+)', 'ProductController@update');
$router->delete('/api/products/(\d+)', 'ProductController@delete');


// --- Rutas de Pedidos ---
// Crear un pedido: solo clientes.
$router->before('POST', '/api/orders', function() {
    AccessControlMiddleware::handle([], true);
});
$router->post('/api/orders', 'OrderController@create');

// Ver pedidos: Clientes ven los suyos, Empleados ven según su rol.
$router->before('GET', '/api/orders.*', function() {
    AccessControlMiddleware::handle(['Mesero', 'Cocinero', 'Cajero', 'Administrador'], true);
});
$router->get('/api/orders', 'OrderController@index');
$router->get('/api/orders/(\d+)', 'OrderController@show');

// Actualizar estado de pedidos: solo empleados con roles específicos.
$router->before('PATCH', '/api/orders/(\d+)/confirm', function() {
    AccessControlMiddleware::handle(['Mesero', 'Administrador']);
});
$router->patch('/api/orders/(\d+)/confirm', 'OrderController@confirm');

$router->before('PATCH', '/api/orders/(\d+)/ready', function() {
    AccessControlMiddleware::handle(['Cocinero', 'Administrador']);
});
$router->patch('/api/orders/(\d+)/ready', 'OrderController@markAsReady');

$router->before('PATCH', '/api/orders/(\d+)/delivered', function() {
    AccessControlMiddleware::handle(['Mesero', 'Administrador']);
});
$router->patch('/api/orders/(\d+)/delivered', 'OrderController@markAsDelivered');


// --- Rutas de Perfil y Sesión de Empleado ---
// Accesible para cualquier empleado logueado.
$router->before('GET|POST', '/api/auth/(profile|refresh|logout)', function() {
    AccessControlMiddleware::handle(); // Roles: [], allowClient: false (por defecto)
});
$router->get('/api/auth/profile', 'AuthController@profile');
$router->post('/api/auth/refresh', 'AuthController@refresh');
$router->post('/api/auth/logout', 'AuthController@logout');


// --- Rutas de Gestión (Ej: Empleados, Mesas, Roles) ---
// solo para Administradores.
$router->before('GET|POST|PUT|DELETE', '/api/(employees|tables|roles).*', function() {
    AccessControlMiddleware::handle(['Administrador']);
});

$router->get('/api/employees', 'EmployeesController@index');
$router->post('/api/employees', 'EmployeesController@store');
// ... y así sucesivamente con el resto de rutas de gestión



// ========================================
// MANEJO DE ERRORES GLOBALES
// ========================================
$router->set404(function() {
    http_response_code(404);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Ruta no encontrada', 'error_code' => 'RES001']);
});


// Ejecutar el router
$router->run();
```
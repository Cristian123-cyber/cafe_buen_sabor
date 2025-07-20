

---

## **Especificación Técnica: Endpoint del Dashboard de Administrador**

### **1. Objetivo**

El propósito de este documento es definir los requerimientos técnicos para el endpoint de la API que proveerá los datos necesarios para el Dashboard Principal del rol "Administrador". Este endpoint debe agregar y procesar métricas clave del negocio, proporcionando no solo los valores actuales, sino también un contexto comparativo (ej. "vs. ayer") para un análisis de rendimiento efectivo.

### **2. Especificación del Endpoint**

*   **Método HTTP:** `GET`
*   **URL:** `/api/analytics/dashboard-summary`


### **3. Lógica de Negocio y Cálculo de Métricas**

El endpoint debe calcular las siguientes métricas. Para una comparación justa y precisa, todas las métricas comparativas ("vs. ayer") deben calcularse utilizando el mismo intervalo de tiempo transcurrido del día.

**Definición de Períodos de Tiempo:**
*   **`T_actual`**: Desde el inicio del día de hoy (e.j., `2023-10-27 00:00:00`) hasta el momento de la petición (`NOW()`).
*   **`T_comparacion`**: Desde el inicio del día de ayer (e.j., `2023-10-26 00:00:00`) hasta el mismo momento del día de ayer (`NOW() - 1 DAY`).

**Fórmula de Tendencia (Porcentaje de Cambio):**
`((Valor_Actual - Valor_Comparacion) / Valor_Comparacion) * 100`
*Nota: Manejar el caso de división por cero si `Valor_Comparacion` es 0. En ese caso, la tendencia puede ser `null` o `100` si `Valor_Actual` es positivo.*

---

#### **A. Métrica: Ingresos**

*   **`title`**: "Ingresos (Hoy)"
*   **`value` (Valor Actual)**:
    *   **Cálculo**: `SUM(total)` de la tabla `sales` (o equivalente) donde el `created_at` esté dentro de `T_actual`.
    *   **Formato**: String formateado como moneda (ej. "$1,250.75").
*   **`trend.value` (Tendencia Numérica)**:
    *   **Cálculo**: Aplicar la fórmula de tendencia usando los ingresos de `T_actual` y los ingresos de `T_comparacion`.
    *   **Formato**: Número flotante (ej. `15.1`).
*   **`trend.text`**: "vs ayer"

#### **B. Métrica: Pedidos Totales**

*   **`title`**: "Pedidos Totales"
*   **`value` (Valor Actual)**:
    *   **Cálculo**: `COUNT(*)` de la tabla `orders` donde `created_at` esté dentro de `T_actual`.
    *   **Formato**: String (ej. "84").
*   **`trend.value` (Tendencia Numérica)**:
    *   **Cálculo**: Aplicar la fórmula de tendencia usando el conteo de pedidos de `T_actual` y el de `T_comparacion`.
    *   **Formato**: Número flotante (ej. `-5.2`).
*   **`trend.text`**: "vs ayer"

#### **C. Métrica: Mesas Activas**

*   **Esta métrica es una instantánea (snapshot), no una agregación de tiempo.**
*   **`title`**: "Mesas Activas"
*   **`value` (Valor Actual)**:
    *   **Cálculo**: `COUNT(*)` de la tabla `tables` donde `status` = 'OCCUPIED' y el `COUNT(*)` total de mesas.
    *   **Formato**: String (ej. "12 / 20").
*   **`progress` (Barra de Progreso)**:
    *   **Cálculo**: `(mesas_ocupadas / total_mesas) * 100`.
    *   **Formato**: Número entero (ej. `60`).
*   **`text`**: Texto descriptivo del progreso.
    *   **Formato**: String (ej. "60% ocupación").
*   **`trend`**: Debe ser `null` para esta métrica, ya que no tiene un componente de tiempo comparativo.

#### **D. Métrica: Ticket Promedio**

*   **`title`**: "Ticket Promedio"
*   **`value` (Valor Actual)**:
    *   **Cálculo**: `Ingresos(T_actual) / Pedidos(T_actual)`. Manejar división por cero.
    *   **Formato**: String formateado como moneda (ej. "$14.89").
*   **`trend.value` (Tendencia Numérica)**:
    *   **Cálculo**: Primero calcular el Ticket Promedio de `T_actual` y el Ticket Promedio de `T_comparacion`. Luego, aplicar la fórmula de tendencia a estos dos valores.
    *   **Formato**: Número flotante (ej. `3.2`).
*   **`trend.text`**: "vs ayer"

---

### **4. Estructura de la Respuesta (Response)**

La respuesta para una petición exitosa (`200 OK`) debe seguir la siguiente estructura JSON. El frontend está diseñado para consumir este formato directamente.

```json
{
  "revenue": {
    "title": "Ingresos (Hoy)",
    "value": "$1,250.75",
    "trend": {
      "value": 15.1,
      "text": "vs ayer"
    }
  },
  "totalOrders": {
    "title": "Pedidos Totales",
    "value": "84",
    "trend": {
      "value": -5.2,
      "text": "vs ayer"
    }
  },
  "activeTables": {
    "title": "Mesas Activas",
    "value": "12 / 20",
    "progress": 60,
    "text": "60% ocupación",
    "trend": null // La tendencia es nula para esta métrica de tipo snapshot
  },
  "averageTicket": {
    "title": "Ticket Promedio",
    "value": "$14.89",
    "trend": {
      "value": 3.2,
      "text": "vs ayer"
    }
  }



}

    ```



### **Paso 1: Estructura de Datos y API (El Contrato con Backend)**

Antes de escribir una línea de Vue, definamos qué datos necesitamos.

#### **A. Recaudo Mensual (12 meses)**

*   **Endpoint Sugerido**: `GET /api/analytics/yearly-revenue?year=2023`
*   **Objetivo**: Obtener un desglose de los ingresos para cada uno de los 12 meses del año especificado.
*   **Estructura JSON de Respuesta**: Un objeto con dos arrays, `labels` y `data`, para ser consumido directamente por `Chart.js`.

    ```json
    {
      "labels": ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
      "data": [21050, 18300, 24100, 23500, 25600, 28900, 27500, 31200, 29800, 33400, 35100, 42500]
    }
    ```

#### **B. Top Meseros por Mesas Atendidas**

*   **Endpoint Sugerido**: `GET /api/analytics/top-waiters?period=monthly` (podra tener `weekly`, `all_time`)
*   **Objetivo**: Obtener una lista ordenada de los meseros según la cantidad de mesas únicas que han atendido en el período.
*   **Estructura JSON de Respuesta**: Un **array de objetos**, donde cada objeto representa a un mesero. Esta estructura es ideal para un `v-for`.

    ```json
    {
      "waiters": [
        { "rank": 1, "name": "Ana García", "tables_served": 142 },
        { "rank": 2, "name": "Carlos Ruiz", "tables_served": 128 },
        { "rank": 3, "name": "Sofía López", "tables_served": 115 },
        { "rank": 4, "name": "David Moreno", "tables_served": 98}
      ]
    }
    ```

#### **C. Top Productos Vendidos**

*   **Endpoint Sugerido**: `GET /api/analytics/top-products?limit=5&period=monthly`
*   **Objetivo**: Obtener los 5 productos más vendidos (por cantidad) en el período.
*   **Estructura JSON de Respuesta**: Objeto con `labels` y `data`, perfecto para un gráfico de barras.

    ```json
    {
      "labels": ["Latte Caramelo", "Cappuccino", "Pastel de Chocolate", "Espresso Doble", "Té Chai"],
      "data": [280, 254, 210, 198, 150]
    }
    ```

---

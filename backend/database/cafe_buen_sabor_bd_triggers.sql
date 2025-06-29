-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 29-06-2025 a las 21:37:09
-- Versión del servidor: 8.0.42
-- Versión de PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cafe_buen_sabor_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE `employees` (
  `id_employe` int NOT NULL,
  `employe_name` varchar(300) DEFAULT NULL,
  `employe_email` varchar(500) DEFAULT NULL,
  `password` varchar(1000) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `employees_statuses_id_status` int NOT NULL,
  `employees_rol_id_rol` int NOT NULL,
  `employee_cc` varchar(100) DEFAULT NULL,
  `table_id_device` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id_employe`, `employe_name`, `employe_email`, `password`, `created_date`, `employees_statuses_id_status`, `employees_rol_id_rol`, `employee_cc`, `table_id_device`) VALUES
(1, 'Cristian Chisavo', 'crischisavo@gmail.com', '12345', '2025-06-19 13:49:57', 1, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees_rol`
--

CREATE TABLE `employees_rol` (
  `id_rol` int NOT NULL,
  `rol_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `employees_rol`
--

INSERT INTO `employees_rol` (`id_rol`, `rol_name`) VALUES
(1, 'Mesero'),
(2, 'Cocinero'),
(3, 'Cajero'),
(4, 'TABLE_DISPLAY'),
(5, 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees_statuses`
--

CREATE TABLE `employees_statuses` (
  `id_status` int NOT NULL,
  `status_name` varchar(100) DEFAULT NULL,
  `status_desc` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `employees_statuses`
--

INSERT INTO `employees_statuses` (`id_status`, `status_name`, `status_desc`) VALUES
(1, 'Activo', 'empleado activo'),
(2, 'Inactivo', 'Empleado inactivo'),
(3, 'Eliminado', 'empleado eliminado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredients`
--

CREATE TABLE `ingredients` (
  `id_ingredient` int NOT NULL,
  `ingredient_name` varchar(300) DEFAULT NULL,
  `ingredient_stock` decimal(10,2) DEFAULT NULL,
  `low_stock_level` decimal(10,2) DEFAULT NULL,
  `critical_stock_level` decimal(10,2) DEFAULT NULL,
  `ingredient_statuses_id_status` int NOT NULL,
  `units_of_measure_id_unit` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `ingredients`
--

INSERT INTO `ingredients` (`id_ingredient`, `ingredient_name`, `ingredient_stock`, `low_stock_level`, `critical_stock_level`, `ingredient_statuses_id_status`, `units_of_measure_id_unit`) VALUES
(1, 'Cafe', 109.00, 30.00, 10.00, 1, 2);

--
-- Disparadores `ingredients`
--
DELIMITER $$
CREATE TRIGGER `trg_ingredient_stock_update` AFTER UPDATE ON `ingredients` FOR EACH ROW BEGIN
  -- 1. Declarar variables al inicio
  DECLARE mov_type ENUM('IN', 'OUT');
  DECLARE diff DECIMAL(10,2);

  -- 2. Verificar si cambió el stock
  IF NEW.ingredient_stock <> OLD.ingredient_stock THEN

    -- Calcular la diferencia
    SET diff = NEW.ingredient_stock - OLD.ingredient_stock;

    IF diff > 0 THEN
      SET mov_type = 'IN';
    ELSE
      SET mov_type = 'OUT';
      SET diff = ABS(diff); -- Convertir a positivo
    END IF;

    -- 3. Insertar en tabla movement
    INSERT INTO stock_movements (
      movement_type,
      quantity,
      movement_date,
      movement_notes,
      product_type,
      id_product
    )
    VALUES (
      mov_type,
      diff,
      NOW(),
      'Actualización directa del ingrediente - autotrigger',
      'INGREDIENTE',
      NEW.id_ingredient
    );

  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingredient_statuses`
--

CREATE TABLE `ingredient_statuses` (
  `id_status` int NOT NULL,
  `name_status` varchar(100) DEFAULT NULL,
  `desc_status` varchar(300) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `ingredient_statuses`
--

INSERT INTO `ingredient_statuses` (`id_status`, `name_status`, `desc_status`) VALUES
(1, 'Optimo', 'producto en estado optimo'),
(2, 'Bajo', 'producto en estado bajo'),
(3, 'Critico', 'producto en estado critico'),
(4, 'Agotado', 'producto sin existencias');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int NOT NULL,
  `notification_type` enum('ORDER_READY','ORDER_CONFIRMED','ORDER_CANCELLED') NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `is_read` enum('READ','UNREAD') DEFAULT 'UNREAD',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `employee_id` int DEFAULT NULL,
  `order_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id_order` int NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `waiter_id` int DEFAULT NULL,
  `order_statuses_id_status` int NOT NULL,
  `table_sessions_id_session` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id_order`, `created_date`, `total_amount`, `waiter_id`, `order_statuses_id_status`, `table_sessions_id_session`) VALUES
(1, '2025-06-19 14:27:41', 20000.00, 1, 1, 1),
(2, '2025-06-19 14:27:41', 10000.00, 1, 1, 1),
(3, '2025-06-19 14:28:13', 30000.00, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders_has_products`
--

CREATE TABLE `orders_has_products` (
  `orders_id_order` int NOT NULL,
  `products_id_product` int NOT NULL,
  `quantity` int DEFAULT NULL,
  `product_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `orders_has_products`
--

INSERT INTO `orders_has_products` (`orders_id_order`, `products_id_product`, `quantity`, `product_price`) VALUES
(1, 1, 2, 0.00),
(2, 1, 1, 0.00),
(3, 1, 3, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders_unified`
--

CREATE TABLE `orders_unified` (
  `id_order` int NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `orders_unified`
--

INSERT INTO `orders_unified` (`id_order`, `created_at`) VALUES
(1, '2025-06-19 14:30:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders_unified_has_orders`
--

CREATE TABLE `orders_unified_has_orders` (
  `orders_unified_id_order` int NOT NULL,
  `orders_id_order` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `orders_unified_has_orders`
--

INSERT INTO `orders_unified_has_orders` (`orders_unified_id_order`, `orders_id_order`) VALUES
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id_status` int NOT NULL,
  `status_name` varchar(45) DEFAULT NULL,
  `status_desc` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `order_statuses`
--

INSERT INTO `order_statuses` (`id_status`, `status_name`, `status_desc`) VALUES
(1, 'PENDING', 'Pedido pendiente de confirmación'),
(2, 'CONFIRMED', 'Pedido confirmado y en cola de preparación'),
(3, 'CANCELLED', 'Pedido cancelado'),
(4, 'READY', 'Pedido listo para recoger'),
(5, 'COMPLETED', 'Pedido entregado al cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id_product` int NOT NULL,
  `product_name` varchar(300) DEFAULT NULL,
  `product_price` decimal(10,2) DEFAULT NULL,
  `product_cost` decimal(10,2) DEFAULT NULL,
  `product_desc` varchar(300) DEFAULT NULL,
  `product_image_url` varchar(1000) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `last_updated_date` date DEFAULT NULL,
  `product_stock` int DEFAULT NULL,
  `low_stock_level` int DEFAULT NULL,
  `critical_stock_level` int DEFAULT NULL,
  `ingredient_statuses_id_status` int DEFAULT NULL,
  `product_types_id_type` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id_product`, `product_name`, `product_price`, `product_cost`, `product_desc`, `product_image_url`, `created_date`, `last_updated_date`, `product_stock`, `low_stock_level`, `critical_stock_level`, `ingredient_statuses_id_status`, `product_types_id_type`) VALUES
(1, 'Vaso de cafe', 10000.00, 4000.00, 'Cafe melo', 'http://localhost:8000/public/images/imagen_cafe.jpg', '2025-06-19', NULL, NULL, NULL, NULL, NULL, 1),
(2, 'Cocacola', 10000.00, 6000.00, 'cocacola', 'djafjsfcjsbdfhbfhbhsfbfhd', '2025-06-03', NULL, 40, 30, 10, 1, 2);

--
-- Disparadores `products`
--
DELIMITER $$
CREATE TRIGGER `tgr_after_update_stock_products` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
  -- 1. Declarar variables al inicio
  DECLARE mov_type ENUM('IN', 'OUT');
  DECLARE diff DECIMAL(10,2);

  -- 2. Verificar si cambió el stock
  IF NEW.product_stock <> OLD.product_stock THEN

    -- Calcular la diferencia
    SET diff = NEW.product_stock - OLD.product_stock;

    IF diff > 0 THEN
      SET mov_type = 'IN';
    ELSE
      SET mov_type = 'OUT';
      SET diff = ABS(diff); -- Convertir a positivo
    END IF;

    -- 3. Insertar en tabla movement
    INSERT INTO stock_movements (
      movement_type,
      quantity,
      movement_date,
      movement_notes,
      product_type,
      id_product
    )
    VALUES (
      mov_type,
      diff,
      NOW(),
      'Actualización directa del producto - autotrigger',
      'PRODUCTO',
      NEW.id_product
    );

  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products_has_ingredients`
--

CREATE TABLE `products_has_ingredients` (
  `products_id_product` int NOT NULL,
  `ingredients_id_ingredient` int NOT NULL,
  `quantity` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `products_has_ingredients`
--

INSERT INTO `products_has_ingredients` (`products_id_product`, `ingredients_id_ingredient`, `quantity`) VALUES
(1, 1, 10.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_types`
--

CREATE TABLE `product_types` (
  `id_type` int NOT NULL,
  `type_name` varchar(200) DEFAULT NULL,
  `type_desc` varchar(400) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `product_types`
--

INSERT INTO `product_types` (`id_type`, `type_name`, `type_desc`) VALUES
(1, 'Producto preparado', 'Producto que requiere preparacion y manejo de stock por ingredientes'),
(2, 'Producto no preparado', 'Producto que viene por unidad y no requiere stock de ingredientes'),
(3, 'Eliminado', 'producto eliminado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int NOT NULL,
  `employe_id` int NOT NULL,
  `token_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id_sale` int NOT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('CONTADO','TRANSFERENCIA') DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `cashier_id` int NOT NULL,
  `sale_status` enum('COMPLETED','CANCELED') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id_sale`, `total_amount`, `payment_method`, `created_at`, `cashier_id`, `sale_status`) VALUES
(1, 20000.00, 'CONTADO', '2025-06-19 14:31:21', 1, 'COMPLETED'),
(2, 40000.00, 'TRANSFERENCIA', '2025-06-19 14:31:21', 1, 'COMPLETED');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales_has_orders`
--

CREATE TABLE `sales_has_orders` (
  `sales_id_sale` int NOT NULL,
  `orders_id_order` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `sales_has_orders`
--

INSERT INTO `sales_has_orders` (`sales_id_sale`, `orders_id_order`) VALUES
(1, 1),
(2, 2),
(2, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id_movement` int NOT NULL,
  `movement_type` enum('IN','OUT') DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL,
  `movement_date` datetime DEFAULT NULL,
  `movement_notes` varchar(300) DEFAULT NULL,
  `product_type` enum('INGREDIENTE','PRODUCTO') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `id_product` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `stock_movements`
--

INSERT INTO `stock_movements` (`id_movement`, `movement_type`, `quantity`, `movement_date`, `movement_notes`, `product_type`, `id_product`) VALUES
(1, 'OUT', 1.00, '2025-06-29 21:28:39', 'Actualización directa del ingrediente - autotrigger', 'INGREDIENTE', 1),
(2, 'IN', 10.00, '2025-06-29 21:29:03', 'Actualización directa del ingrediente - autotrigger', 'INGREDIENTE', 1),
(3, 'IN', 4.00, '2025-06-29 21:35:38', 'Actualización directa del producto - autotrigger', 'PRODUCTO', 2),
(4, 'OUT', 54.00, '2025-06-29 21:35:55', 'Actualización directa del producto - autotrigger', 'PRODUCTO', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tables`
--

CREATE TABLE `tables` (
  `id_table` int NOT NULL,
  `table_number` int DEFAULT NULL,
  `qr_token` varchar(500) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  `table_status` enum('FREE','OCCUPIED','INACTIVE') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tables`
--

INSERT INTO `tables` (`id_table`, `table_number`, `qr_token`, `token_expiration`, `table_status`) VALUES
(1, 1, 'token_qr_231414143241432', '2025-06-20 13:25:00', 'OCCUPIED'),
(2, 2, 'qr_token_124143212515135', '2025-06-20 14:25:00', 'FREE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `table_sessions`
--

CREATE TABLE `table_sessions` (
  `id_session` int NOT NULL,
  `stared_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `session_status` enum('ACTIVE','CLOSED','EXPIRED') DEFAULT NULL,
  `tables_id_table` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `table_sessions`
--

INSERT INTO `table_sessions` (`id_session`, `stared_at`, `ended_at`, `session_status`, `tables_id_table`) VALUES
(1, '2025-06-19 14:26:42', NULL, 'ACTIVE', 1),
(2, '2025-06-18 14:42:00', '2025-06-12 14:42:00', 'CLOSED', 1),
(3, '2025-06-19 14:42:00', '2025-06-28 14:42:00', 'EXPIRED', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `units_of_measure`
--

CREATE TABLE `units_of_measure` (
  `id_unit` int NOT NULL,
  `unit_name` varchar(100) DEFAULT NULL,
  `unit_abbreviation` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `units_of_measure`
--

INSERT INTO `units_of_measure` (`id_unit`, `unit_name`, `unit_abbreviation`) VALUES
(1, 'Kilogramo', 'kg'),
(2, 'Gramo', 'g'),
(3, 'Miligramo', 'mg'),
(4, 'Libra', 'lb'),
(5, 'Onza', 'oz'),
(6, 'Litro', 'L'),
(7, 'Mililitro', 'mL'),
(8, 'Unidad', 'u');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id_employe`),
  ADD UNIQUE KEY `employee_cc` (`employee_cc`),
  ADD KEY `fk_employees_employees_statuses1_idx` (`employees_statuses_id_status`),
  ADD KEY `fk_employees_employees_rol1_idx` (`employees_rol_id_rol`),
  ADD KEY `table_id_device` (`table_id_device`);

--
-- Indices de la tabla `employees_rol`
--
ALTER TABLE `employees_rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `employees_statuses`
--
ALTER TABLE `employees_statuses`
  ADD PRIMARY KEY (`id_status`);

--
-- Indices de la tabla `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id_ingredient`),
  ADD KEY `fk_ingredients_ingredient_statuses_idx` (`ingredient_statuses_id_status`),
  ADD KEY `fk_ingredients_units_of_measure1_idx` (`units_of_measure_id_unit`);

--
-- Indices de la tabla `ingredient_statuses`
--
ALTER TABLE `ingredient_statuses`
  ADD PRIMARY KEY (`id_status`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`);

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `fk_orders_employees1_idx` (`waiter_id`),
  ADD KEY `fk_orders_order_statuses1_idx` (`order_statuses_id_status`),
  ADD KEY `fk_orders_table_sessions1_idx` (`table_sessions_id_session`);

--
-- Indices de la tabla `orders_has_products`
--
ALTER TABLE `orders_has_products`
  ADD PRIMARY KEY (`orders_id_order`,`products_id_product`),
  ADD KEY `fk_orders_has_products_products1_idx` (`products_id_product`),
  ADD KEY `fk_orders_has_products_orders1_idx` (`orders_id_order`);

--
-- Indices de la tabla `orders_unified`
--
ALTER TABLE `orders_unified`
  ADD PRIMARY KEY (`id_order`);

--
-- Indices de la tabla `orders_unified_has_orders`
--
ALTER TABLE `orders_unified_has_orders`
  ADD PRIMARY KEY (`orders_unified_id_order`,`orders_id_order`),
  ADD KEY `fk_orders_unified_has_orders_orders1_idx` (`orders_id_order`),
  ADD KEY `fk_orders_unified_has_orders_orders_unified1_idx` (`orders_unified_id_order`);

--
-- Indices de la tabla `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id_status`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `fk_no_prepare_products_ingredient_statuses1_idx` (`ingredient_statuses_id_status`),
  ADD KEY `fk_products_product_types1_idx` (`product_types_id_type`);

--
-- Indices de la tabla `products_has_ingredients`
--
ALTER TABLE `products_has_ingredients`
  ADD PRIMARY KEY (`products_id_product`,`ingredients_id_ingredient`),
  ADD KEY `fk_products_has_ingredients_ingredients1_idx` (`ingredients_id_ingredient`),
  ADD KEY `fk_products_has_ingredients_products1_idx` (`products_id_product`);

--
-- Indices de la tabla `product_types`
--
ALTER TABLE `product_types`
  ADD PRIMARY KEY (`id_type`);

--
-- Indices de la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_token_hash` (`token_hash`),
  ADD KEY `employe_id` (`employe_id`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id_sale`),
  ADD KEY `cashier_id` (`cashier_id`);

--
-- Indices de la tabla `sales_has_orders`
--
ALTER TABLE `sales_has_orders`
  ADD PRIMARY KEY (`sales_id_sale`,`orders_id_order`),
  ADD KEY `fk_sales_has_orders_orders1_idx` (`orders_id_order`),
  ADD KEY `fk_sales_has_orders_sales1_idx` (`sales_id_sale`);

--
-- Indices de la tabla `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id_movement`);

--
-- Indices de la tabla `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id_table`),
  ADD UNIQUE KEY `table_number_UNIQUE` (`table_number`);

--
-- Indices de la tabla `table_sessions`
--
ALTER TABLE `table_sessions`
  ADD PRIMARY KEY (`id_session`),
  ADD KEY `fk_table_sessions_tables1_idx` (`tables_id_table`);

--
-- Indices de la tabla `units_of_measure`
--
ALTER TABLE `units_of_measure`
  ADD PRIMARY KEY (`id_unit`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id_employe` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `employees_rol`
--
ALTER TABLE `employees_rol`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `employees_statuses`
--
ALTER TABLE `employees_statuses`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id_ingredient` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ingredient_statuses`
--
ALTER TABLE `ingredient_statuses`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `orders_unified`
--
ALTER TABLE `orders_unified`
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id_product` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id_type` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id_sale` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id_movement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tables`
--
ALTER TABLE `tables`
  MODIFY `id_table` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `table_sessions`
--
ALTER TABLE `table_sessions`
  MODIFY `id_session` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `units_of_measure`
--
ALTER TABLE `units_of_measure`
  MODIFY `id_unit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`table_id_device`) REFERENCES `tables` (`id_table`),
  ADD CONSTRAINT `fk_employees_employees_rol1` FOREIGN KEY (`employees_rol_id_rol`) REFERENCES `employees_rol` (`id_rol`),
  ADD CONSTRAINT `fk_employees_employees_statuses1` FOREIGN KEY (`employees_statuses_id_status`) REFERENCES `employees_statuses` (`id_status`);

--
-- Filtros para la tabla `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `fk_ingredients_ingredient_statuses` FOREIGN KEY (`ingredient_statuses_id_status`) REFERENCES `ingredient_statuses` (`id_status`),
  ADD CONSTRAINT `fk_ingredients_units_of_measure1` FOREIGN KEY (`units_of_measure_id_unit`) REFERENCES `units_of_measure` (`id_unit`);

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_employees1` FOREIGN KEY (`waiter_id`) REFERENCES `employees` (`id_employe`),
  ADD CONSTRAINT `fk_orders_order_statuses1` FOREIGN KEY (`order_statuses_id_status`) REFERENCES `order_statuses` (`id_status`),
  ADD CONSTRAINT `fk_orders_table_sessions1` FOREIGN KEY (`table_sessions_id_session`) REFERENCES `table_sessions` (`id_session`);

--
-- Filtros para la tabla `orders_has_products`
--
ALTER TABLE `orders_has_products`
  ADD CONSTRAINT `fk_orders_has_products_orders1` FOREIGN KEY (`orders_id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `fk_orders_has_products_products1` FOREIGN KEY (`products_id_product`) REFERENCES `products` (`id_product`);

--
-- Filtros para la tabla `orders_unified_has_orders`
--
ALTER TABLE `orders_unified_has_orders`
  ADD CONSTRAINT `fk_orders_unified_has_orders_orders1` FOREIGN KEY (`orders_id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `fk_orders_unified_has_orders_orders_unified1` FOREIGN KEY (`orders_unified_id_order`) REFERENCES `orders_unified` (`id_order`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_no_prepare_products_ingredient_statuses1` FOREIGN KEY (`ingredient_statuses_id_status`) REFERENCES `ingredient_statuses` (`id_status`),
  ADD CONSTRAINT `fk_products_product_types1` FOREIGN KEY (`product_types_id_type`) REFERENCES `product_types` (`id_type`);

--
-- Filtros para la tabla `products_has_ingredients`
--
ALTER TABLE `products_has_ingredients`
  ADD CONSTRAINT `fk_products_has_ingredients_ingredients1` FOREIGN KEY (`ingredients_id_ingredient`) REFERENCES `ingredients` (`id_ingredient`),
  ADD CONSTRAINT `fk_products_has_ingredients_products1` FOREIGN KEY (`products_id_product`) REFERENCES `products` (`id_product`);

--
-- Filtros para la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD CONSTRAINT `refresh_tokens_ibfk_1` FOREIGN KEY (`employe_id`) REFERENCES `employees` (`id_employe`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `employees` (`id_employe`);

--
-- Filtros para la tabla `sales_has_orders`
--
ALTER TABLE `sales_has_orders`
  ADD CONSTRAINT `fk_sales_has_orders_orders1` FOREIGN KEY (`orders_id_order`) REFERENCES `orders` (`id_order`),
  ADD CONSTRAINT `fk_sales_has_orders_sales1` FOREIGN KEY (`sales_id_sale`) REFERENCES `sales` (`id_sale`);

--
-- Filtros para la tabla `table_sessions`
--
ALTER TABLE `table_sessions`
  ADD CONSTRAINT `fk_table_sessions_tables1` FOREIGN KEY (`tables_id_table`) REFERENCES `tables` (`id_table`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

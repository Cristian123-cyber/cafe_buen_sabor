-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql
-- Tiempo de generación: 01-08-2025 a las 14:32:00
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
(1, 'Cristian Chisavo', 'crischisavo@gmail.com', '12345', '2025-06-19 13:49:57', 3, 3, NULL, NULL),
(2, 'Juan Pérez', 'juanperez@example.com', '$2y$10$2nkfM3nmXIDzzNCTbF.8jOCY9azlMHg9444dGf8apFyEIoCJ0nMlm', NULL, 3, 2, NULL, NULL),
(3, 'Dispositivo mesa X', 'email@example.com', '$2y$10$WYuxgOWH8KriJvak9RMC5.6IvfF8pT/bGkNvDxBqLWxlwLa/pYfWi', NULL, 1, 4, NULL, NULL),
(4, 'Dispositivo mesa X', 'email@example.com', '$2y$10$s./y5B8OqR1eFef9a5wg7unA4fqm/WmUSFnZKGf6w4UpSwwmaGljO', NULL, 1, 4, NULL, 1),
(7, 'Dispositivo mesa Y', 'device@gmail.com', '$2y$10$WYDE8Vysam91e0XsnzH9Wu8ZrHYobwqMCiNCtnJpA1lIKwzGouO0q', NULL, 3, 4, NULL, 1),
(8, 'Dispositivo mesa X', 'email@example.com', '$2y$10$YF46yVrd.q4oWu0/fCEScuWzL57.AaoyLPx.nyFArHEXF7b3d6raW', NULL, 1, 4, NULL, 2),
(9, 'Dispositivo mesa X', 'dev@ex.com', '$2y$10$B5DATQ0hDxQK5u27/PDBuOLLauelUxdrpVZ98ZtQVI8mle2/9nr9C', NULL, 3, 4, '', 2),
(10, 'Cristian Chisavo', 'admin@gmail.com', '$2y$10$h1/BPCbG/eP/VbgSZHhJEuQCp18C0M3QE7plxtjJ/lktGS1mUqawa', '2025-07-14 00:00:00', 1, 5, '1114151446', NULL),
(11, 'Juan Pérez', 'cocinero@gmail.com', '$2y$10$OhGlci/Kp3omtn3UrYIg4OCynKX183o6.Lmy5IFwFTHwJTI5xZBsS', '2025-07-16 00:00:00', 3, 2, '777777777', NULL),
(12, 'Juan Pérez', 'cajero@gmail.com', '$2y$10$2iYwpQTKRQ5IhkDElx3Vzu0yw8ItWez86MBCybkW3vFQa2.0tcQXy', '2025-07-16 00:00:00', 3, 3, '111111111', NULL),
(14, 'Juan Pérez', 'mesero@gmail.com', '$2y$10$7nhTTUyM37E4gFT.fRDQ3OcwO6iq7pI77c67xl4rlqGrpps2hv8ga', '2025-07-17 00:00:00', 3, 1, '9399339393', NULL),
(15, 'Daniel', 'dan@gmail.com', '$2y$10$cO8ztlJdCv25BInEiw252O1SzyhNJvTxZ1.nMdCMseG/UYSq9EZQO', '2025-07-23 00:00:00', 1, 3, '52136132512', NULL),
(16, 'fadeerrrr', 'daff@gmail.com', '$2y$10$0nJZ5bwRAt2s4ce0DK.RLeZsWduUDt5sRDv8xzKozjk0J30CNGtNa', '2025-07-23 00:00:00', 1, 1, '2314325243', NULL),
(23, 'Juan Pérez', 'meser8777o@gmail.com', '$2y$10$3m4qBJ8bHSmpVXPM3QS5WuY6AcM1aZBZ3pbhXxF3cRuqY1cu/g.jC', '2025-07-23 00:00:00', 1, 1, '9399339399893', NULL),
(24, 'eada', 'cris4444chisavo@gmail.com', '$2y$10$IKYvBOEUBdDYq2xtgGl0QeEMcwKyNRWbotoQRj7OkSB15ZhRlpnWO', '2025-07-23 00:00:00', 1, 3, '111415144698888', NULL),
(25, '677gjghhjbjhbj', 'crischisavo939393@gmail.com', '$2y$10$P1RVsZi6ECgKfY0J8RNCEuuNmnnM4JX3DxNhrACCgx..7hKx/0JJy', '2025-07-27 00:00:00', 3, 2, '111415144699', NULL),
(26, 'RANA', 'da444n@gmail.com', '$2y$10$/.hOBmEp4pWgSaRpo4ashO18J6Tczm1slAuKKG0vcsUFAVhsXsvve', '2025-07-27 00:00:00', 3, 1, '22222222222', NULL),
(27, 'Care monda', 'crischisavoi4u32i34@gmail.com', '$2y$10$rxGLEToDhCPtMtEqbIe7XeEneSgBX/dY1X/TuiTk9bZkxRmi7L5tO', '2025-07-27 00:00:00', 3, 3, '98763782912384', NULL),
(28, 'Super Admin', 'superadmin@gmail.com', '$2y$10$Mbi6PKhpyu5aVy/fyefijeEpSvwNwvfOwE6Grlzoxq8wQtDQfFMRi', '2025-07-27 00:00:00', 2, 5, '92873634839', NULL),
(29, 'Angie', 'angie@gmail.com', '$2y$10$LOSs2lDaTg019/a.olOCV.16hIAcjFqhYCN6eIfsWeESK4zCWO2PC', '2025-07-28 00:00:00', 1, 2, '41241241243', NULL),
(30, 'Mesa 7 login', 'mesa7@gmail.com', '$2y$10$h2QVABmLOUIeXWSZjUcTrer/7.1Y8q2wOmt.UPzcXAJPixIg9D7um', '2025-07-31 00:00:00', 1, 4, NULL, 3),
(31, 'mesa7', 'mesa777@gmail.com', '$2y$10$TAPgxLpJ.FVFckVuUAJ5zuyHPilHX557upOO8Tss2fFqQarzrYXy6', '2025-07-31 00:00:00', 1, 4, NULL, 6),
(32, 'Mesa 1 Login', 'device111@gmail.com', '$2y$10$mOuNI9.a2B/V5jZSrG4ibOGVXB0rE51Rhu.g8evHlWppMEjF7PYyG', '2025-07-31 00:00:00', 1, 4, NULL, 1);

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
(4, 'Dispositivo'),
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
(1, 'Cafe', 109.00, 30.00, 10.00, 1, 2),
(2, 'Leche', 100.00, 10.00, 4.00, 1, 6);

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
(3, '2025-06-19 14:28:13', 30000.00, 1, 1, 1),
(4, '2025-07-13 18:42:10', 0.00, NULL, 1, 6),
(9, '2025-07-13 19:08:05', 0.00, NULL, 1, 6),
(10, '2025-07-13 20:27:28', 36000.00, NULL, 1, 6),
(11, '2025-07-13 20:29:19', 36000.00, NULL, 1, 6),
(12, '2025-07-13 20:30:09', 36000.00, NULL, 1, 6),
(13, '2025-07-13 20:30:38', 36000.00, NULL, 1, 6),
(14, '2025-07-13 20:32:43', 18000.00, NULL, 1, 6),
(15, '2025-07-13 20:34:02', 18000.00, NULL, 1, 6),
(16, '2025-07-13 20:38:16', 94000.00, NULL, 1, 6),
(17, '2025-07-13 20:39:58', 18000.00, NULL, 1, 6),
(18, '2025-07-13 20:40:32', 18000.00, NULL, 1, 6),
(19, '2025-07-13 20:42:45', 18000.00, NULL, 1, 6),
(20, '2025-07-13 20:43:18', 18000.00, NULL, 1, 6),
(21, '2025-07-13 21:35:19', 18000.00, NULL, 1, 6),
(22, '2025-07-14 12:09:48', 50000.00, NULL, 1, 6),
(23, '2025-07-14 14:28:27', 126000.00, NULL, 1, 4),
(24, '2025-07-15 10:26:25', 40000.00, NULL, 1, 7),
(25, '2025-07-22 17:28:10', 40000.00, NULL, 1, 10),
(26, '2025-07-31 13:27:24', 70000.00, NULL, 1, 10);

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
(3, 1, 3, 0.00),
(10, 4, 2, 18000.00),
(11, 4, 2, 18000.00),
(12, 4, 2, 18000.00),
(13, 4, 2, 18000.00),
(14, 4, 1, 18000.00),
(15, 4, 1, 18000.00),
(16, 1, 4, 10000.00),
(16, 4, 2, 18000.00),
(16, 5, 1, 18000.00),
(17, 2, 1, 18000.00),
(18, 5, 1, 18000.00),
(19, 5, 1, 18000.00),
(20, 4, 1, 18000.00),
(21, 4, 1, 18000.00),
(22, 1, 5, 10000.00),
(23, 4, 7, 18000.00),
(24, 1, 4, 10000.00),
(25, 1, 4, 10000.00),
(26, 3, 7, 10000.00);

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
  `product_types_id_type` int NOT NULL,
  `product_category` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id_product`, `product_name`, `product_price`, `product_cost`, `product_desc`, `product_image_url`, `created_date`, `last_updated_date`, `product_stock`, `low_stock_level`, `critical_stock_level`, `ingredient_statuses_id_status`, `product_types_id_type`, `product_category`) VALUES
(1, 'Vaso de cafe', 10000.00, 4000.00, 'Cafe melo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-19', NULL, NULL, NULL, NULL, NULL, 1, 2),
(2, 'Cocacola', 18000.00, 6000.00, 'Cocacola fira con hielo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-03', NULL, 25, 30, 10, 4, 2, 1),
(3, 'Vaso de cafe', 10000.00, 4000.00, 'Cafe melo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-19', NULL, NULL, NULL, NULL, NULL, 1, 2),
(4, 'Cocacola', 18000.00, 6000.00, 'Cocacola fira con hielo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-03', NULL, 40, 30, 10, 1, 2, 1),
(5, 'Cocacola', 18000.00, 6000.00, 'Cocacola fira con hielo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-03', NULL, 40, 30, 10, 1, 2, 1),
(6, 'Cocacola', 18000.00, 6000.00, 'Cocacola fira con hielo', 'https://lh3.googleusercontent.com/aida-public/AB6AXuC8iC8aPqNKYPVMFV6SpisfVzvUwPQx-uyxkBB_t4iJPAfziled-q13wVCxPPJAhfXeWsrlExULezp4J4kQfimp_UYhGORyQIGFdxfgoMlCqrcslU6Z3TmhS2Ti4ZR_UBFA6fRfsPDcPmv_-fnxLBEuZbil5oX_1Ug1ukNFHqrTieNkyrExJIzdO0nsa8onPa2b2uQJqEw__GPXnrmwWWujoHnCPlGqLvOBc4zBjvWWH_hRNq__WEa-TtMO1aW3hLTwrKgnEUaxKpE', '2025-06-03', NULL, 40, 30, 10, 1, 2, 1);

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
-- Estructura de tabla para la tabla `products_category`
--

CREATE TABLE `products_category` (
  `id_category` int NOT NULL,
  `category_name` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `products_category`
--

INSERT INTO `products_category` (`id_category`, `category_name`) VALUES
(1, 'Gaseosas'),
(2, 'Cafes'),
(3, 'Bebidas'),
(4, 'Cervezas'),
(5, 'Cocteles');

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
(1, 1, 10.00),
(1, 2, 1.00);

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_revoked` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `refresh_tokens`
--

INSERT INTO `refresh_tokens` (`id`, `employe_id`, `token_hash`, `expires_at`, `created_at`, `is_revoked`) VALUES
(20, 2, '34139e7295f86d688a289f6b05c1912dcbf7506cb350dd638fe321409ad62fbc', '2025-07-13 19:06:54', '2025-07-07 00:06:54', 0),
(21, 2, '950a9f5293320141db4409e63ea1bfc491489d0fe3bd70da260ef3759dd887ba', '2025-07-13 19:10:32', '2025-07-07 00:10:32', 0),
(22, 2, 'cd3a3f3b5ed0900b1c41e77242c0f28deefc6b8ccb8350059c179efe687a5225', '2025-07-13 20:03:59', '2025-07-07 01:03:59', 0),
(23, 2, 'd04fa3a687e00bb93d439e03dcae0db61f3e431f88e71f950037dc95b0db89f1', '2025-07-13 20:10:18', '2025-07-07 01:10:18', 0),
(24, 2, '8ce91d25d9528a4c43a836936eea665c7ab3b2c40b21034a6138a47c02d580a0', '2025-07-13 20:22:35', '2025-07-07 01:22:35', 1),
(25, 2, 'b21e4a06adf61eddae9bd5c3db1630e1675831b47e9b5c13f8a5ef274e528ea3', '2025-07-13 20:38:07', '2025-07-07 01:38:07', 1),
(26, 2, 'e01e8ebcdb6362e7d29d4b5c1b66451f52f55e814022015348a806b3b3a18eae', '2025-07-13 20:43:18', '2025-07-07 01:43:18', 1),
(27, 2, '6f4205c310e285e50101a6af08170ac6217a2f2d3909811aa7cf02532d95d2b5', '2025-07-13 20:50:38', '2025-07-07 01:50:38', 1),
(28, 2, '7f41e09d6ab4b73a2f158603ac755a8fe62994ea2728793af5c553877165f519', '2025-07-13 21:13:18', '2025-07-07 02:13:18', 1),
(29, 2, '08390dd11b7fdbffdaeabecfed7a2a4f9403aa38c5fe938e25c8164e3a4d3d52', '2025-07-13 21:18:33', '2025-07-07 02:18:33', 1),
(30, 2, '56219b966599c7183ed860e2b56329944a6d7dfdc92719f5d7563f82dde5c10c', '2025-07-13 21:30:53', '2025-07-07 02:30:53', 0),
(31, 2, '63fe636b1874a5aebcc67b211e1893ef3f99a840d2ec143ae7825d1701086b89', '2025-07-13 21:34:26', '2025-07-07 02:34:26', 1),
(32, 2, 'ac549275ded582413fd95cd7f8a42aa93574c30fc583c25f4acc98537fe8fb4c', '2025-07-14 12:44:47', '2025-07-07 17:44:47', 1),
(33, 2, '3fac18e741bcf948c45fda8f1a0d80f9f702dd0a3b319b9abfad8df5fa592be0', '2025-07-14 12:45:12', '2025-07-07 17:45:12', 1),
(34, 2, '822e8e942b54c03311c589225b9f2e51917e188bc06265a3c5f0dd7112db9ac2', '2025-07-14 13:26:36', '2025-07-07 18:26:36', 1),
(35, 2, 'a42099abf363b5af901d4a2ad1f913c4254385bd9922299906e2ed5ab62d000a', '2025-07-14 15:42:40', '2025-07-07 20:42:40', 1),
(36, 2, '57a173a89cd6318e7eefae99ab6ff6cd2d89b2356c7209b0432b044572bdd0f1', '2025-07-14 17:21:19', '2025-07-07 22:21:19', 1),
(37, 7, 'afff2ff218d1df279e124886c2ef87eb918c170893a99de2972721784ac47f43', '2025-07-14 19:36:44', '2025-07-08 00:36:44', 0),
(38, 7, 'a88450ccb0ab7c5619d08fc96153a6a7ba4f922618877dbbbbe6b753cc855815', '2025-07-14 19:41:44', '2025-07-08 00:41:44', 0),
(39, 7, 'ca4507ed94679be2121005a72823f5835a91b8f7c21bf81b7406fd0e6e4a9268', '2025-07-14 19:44:17', '2025-07-08 00:44:17', 0),
(40, 7, '018e22da5fa0db3b237ddc87ad6c41b51be640a6a5ad1b16a52f4ef8475c3369', '2025-07-14 19:46:11', '2025-07-08 00:46:11', 0),
(41, 7, '173ebd3a8f84f5ba2b28e3dab6d6f0f81bf0a3823041b81039c331685527177a', '2025-07-14 19:49:53', '2025-07-08 00:49:53', 0),
(42, 9, '2b18bcf2f94c2cff25385e95dad2b8605e49cd65b044ea244cec3233d1f363c0', '2025-07-14 19:57:28', '2025-07-08 00:57:28', 0),
(43, 2, '590f52a0e0632a3164f341ef6e7466c73d37853dc56543679d50ad77175483b6', '2025-07-19 21:18:52', '2025-07-13 02:18:52', 1),
(44, 9, '02cab3e46a308ff1606cf6c416f5ffdeed62fd6817efa5ba445ea89afe5631c0', '2025-07-19 21:19:35', '2025-07-13 02:19:35', 1),
(45, 9, '672f441df4f5b6655c7077481ca94dba9c5533b0b1107c77f19d300ce987d8c3', '2025-07-21 12:05:57', '2025-07-14 17:05:57', 0),
(46, 7, 'f8bc337fabd0e2a3b11e6d5b42e0db3e9cbe1424f6d651825bef2e00d2572b16', '2025-07-21 14:26:02', '2025-07-14 19:26:02', 1),
(47, 10, '7704e64df6b0b905293b71a2ec488e8f7386d824652c7cae22d54d00013647cd', '2025-07-21 16:49:07', '2025-07-14 21:49:07', 1),
(48, 9, 'd86fd65ceaf84f460f719d259ad6d4f357df80091b05f26b4bc9eb5f86e9baf1', '2025-07-22 10:16:41', '2025-07-15 15:16:41', 0),
(49, 10, '5063a3d59d0d911722f0810b9dccd45067cd58bc2f97431eb98a144a8064acdd', '2025-07-23 13:37:18', '2025-07-16 18:37:18', 1),
(50, 10, '607955026ef0219229a5bcd6196454b126034a7c39c493ac30318222b22443de', '2025-07-23 15:24:37', '2025-07-16 20:24:37', 1),
(51, 11, '83b023d090cefb0cdd2ac3f44c1ba200f17d20fe915792ccdf3c56c5a8a2c295', '2025-07-23 16:17:47', '2025-07-16 21:17:47', 1),
(52, 11, '9c03e5efa0b6b6f04e7d26d77f3b1cbd45551ecebba50fcd94a04e1de2dbafb6', '2025-07-23 16:25:43', '2025-07-16 21:25:43', 1),
(53, 11, 'e7228a0d7e09d6c3e7e42d13e03194f662d79d5cfeac6ecc48f0ee23cf2339c6', '2025-07-23 16:26:13', '2025-07-16 21:26:13', 1),
(54, 12, '304d223e05b213d5c59b4876e2918c72f70dc8d595acb63ca03e062d152ba49c', '2025-07-23 16:27:19', '2025-07-16 21:27:19', 1),
(55, 10, '542d692e8fabbab1276dfbb3181d1494dff066514df3ec254d004b9d9522fc32', '2025-07-23 16:47:34', '2025-07-16 21:47:34', 1),
(56, 10, 'd2da019fb12f30707c4ea82423eca840f5ab9289e32731862773ead689c40c7e', '2025-07-24 12:26:59', '2025-07-17 17:26:59', 0),
(57, 10, '82d595e7a9637afa459bbb2e53c10742c2154ac8e8e196e936c22fccf5ea9a99', '2025-07-24 12:27:07', '2025-07-17 17:27:07', 1),
(58, 11, 'bc1b887de43df4f8e2f66ba9a070fbf89826b9db9bf2e015ddf9485aa1526bc9', '2025-07-24 12:35:02', '2025-07-17 17:35:02', 1),
(59, 12, '7094036fdf4353ca799a32b5e1a5e4bcfaf65a0321ff32d6cc480c291bab07b0', '2025-07-24 12:35:20', '2025-07-17 17:35:20', 1),
(60, 12, 'd618148337f1db686ef8dd2329d30864db6ff5c9dc89ab957514a5930a48247e', '2025-07-24 12:36:49', '2025-07-17 17:36:49', 1),
(61, 14, '175245f4d0e86ed589a84e92b3edc82fad4af5bb818f898e5cf86a9a347e390a', '2025-07-24 12:38:37', '2025-07-17 17:38:37', 1),
(62, 10, '88e480a93be8c51f2a6c2cee350df7bf46d68de0c66b35bba718d8b061a4dfcc', '2025-07-24 12:39:12', '2025-07-17 17:39:12', 1),
(63, 10, '48c8f6ad3799bb579248dcb1151960b5c29d536769eb32965b1d1edd1f4871d8', '2025-07-25 17:00:17', '2025-07-18 22:00:17', 1),
(64, 14, 'e3d3b45f65de0fd137ae9777c46ab6b04c05726baeda9a8ce3ddd09229eed25c', '2025-07-25 18:16:08', '2025-07-18 23:16:08', 1),
(65, 10, '5fd4a0a09c217b8d74f4ac8f9205af686bf9555214f89636792e7a495be0e601', '2025-07-25 18:16:40', '2025-07-18 23:16:40', 1),
(66, 10, '6c3f61580735dfe3b03ca7581b4445573b8c12d65c5a18a93362ed75ebda8e5e', '2025-07-25 18:17:03', '2025-07-18 23:17:03', 1),
(67, 10, '1e2468321e21b1e097655d7acd722c4369ccd75db5e743998f08761a6671d9ba', '2025-07-25 18:17:44', '2025-07-18 23:17:44', 1),
(68, 10, '45ca5902b23eca16748a6942bc5312901ad5a5cd3f0424269868aa1d9ee35961', '2025-07-25 18:18:45', '2025-07-18 23:18:45', 1),
(69, 10, '9a7d3560b87ccb587addec98ce7f12529f94215f4f9c2672467096984cfc5e57', '2025-07-25 18:19:52', '2025-07-18 23:19:52', 1),
(70, 14, '7b928f4aca00d4bf9b2a7331dba15f61c61f5b41f70e6737b239b33e147a8da5', '2025-07-25 18:20:58', '2025-07-18 23:20:58', 1),
(71, 10, '330d2bd209f9012471cc7b58a35eab1b13affc6020fa4a36541a328d0e30982c', '2025-07-25 18:26:41', '2025-07-18 23:26:41', 1),
(72, 10, '67fe886b01388a3041f2117515b7a5c3dc5aa3cc413b988892f5afe30293d2bc', '2025-07-25 18:29:10', '2025-07-18 23:29:10', 0),
(73, 10, '3131393ad7b2c286941ea7418c527e1b596c78ba45254d2b6c1f44ceba34c74a', '2025-07-26 14:54:09', '2025-07-19 19:54:09', 1),
(74, 7, '5a57cd7bbb1e075e8caf71d2e0e9aa313a88559c964d47b014af8d220f75dc16', '2025-07-26 22:27:25', '2025-07-20 03:27:25', 0),
(75, 10, '6999241ecbecb1f28a92350083ca567c8d6ab84a48ecd8185ae961d3389b00c1', '2025-07-26 22:31:58', '2025-07-20 03:31:58', 1),
(76, 10, 'eb656a744dedb44ef78ed93c41a33e47e6a4ea2d7f1a7ae37f3266057c67dcef', '2025-07-27 17:51:03', '2025-07-20 22:51:03', 1),
(77, 10, '1eae542d93b9b3e4bbd8e0b99d1a615fedb0a4cfcc1c92e35da6fe744984b3b6', '2025-07-27 19:06:00', '2025-07-21 00:06:00', 1),
(78, 10, '0d57311428896261cf936de446561647d97f3b50f2b47e7219c231049b377285', '2025-07-27 21:00:01', '2025-07-21 02:00:01', 1),
(79, 10, 'ca8061bfb8d3ee4a8f35d2fc81aa69267b10597ceda22f219f8682a2911aa86f', '2025-07-27 21:00:22', '2025-07-21 02:00:22', 1),
(80, 7, 'c28a5d0528da7ba8eae77f48c66d6bb08aa31b41008996213f3546ced38a367a', '2025-07-29 17:20:30', '2025-07-22 22:20:30', 0),
(81, 10, 'f392d55250b615458c5bc488d153ef79af8256ad7ba7b9210f03b0ab3c86be5c', '2025-07-29 17:30:12', '2025-07-22 22:30:12', 1),
(82, 10, '698719ffbe72050540e649b2a41646e0d97906a381d527089d609ac964e83309', '2025-07-30 10:27:45', '2025-07-23 15:27:45', 1),
(83, 10, '663fcf52896ce5b3ef9765745e0cdc8e90bbb9992108f2143b8903b52ccbc898', '2025-07-30 10:31:24', '2025-07-23 15:31:24', 0),
(84, 10, '5a3a7b74863c2b9f0cef16cdc2789fe4ee9c0ff45f2f3b11d368f62debea16e4', '2025-07-30 10:32:28', '2025-07-23 15:32:28', 0),
(85, 10, '56871e20c1d6ae84660c57c96bcf49326f5515770a2918955700a115f5abcbb4', '2025-07-30 10:50:23', '2025-07-23 15:50:23', 0),
(86, 28, 'd3d5d2ecd0342289cf9051a5fb004edddb9f949edfb851cd60796df32bf3cf51', '2025-08-03 15:34:35', '2025-07-27 20:34:35', 1),
(87, 10, '15b04881fd9415bad4e7cb849f7a436bcaedb2f82beabda6e2a397d2ac69d493', '2025-08-03 15:42:32', '2025-07-27 20:42:32', 1),
(88, 10, '937d1eaae539728173ac380c48844e0c511ee52ee40ae0cdc0d0d841b9372df9', '2025-08-03 15:43:22', '2025-07-27 20:43:22', 1),
(89, 10, 'c9ba26a10654c8e885b8faf6f97f0e0c2d74212b895b22402eea1e75c7b07e53', '2025-08-03 15:43:42', '2025-07-27 20:43:42', 0),
(90, 10, '55ac91e2d8274da263cd6697276447d7c129a2193c1de336d8073587aea6eae7', '2025-08-04 13:45:48', '2025-07-28 18:45:48', 0),
(91, 10, '8fe469954fa50d6fed5fa2d93fbb52c53c14eaf310fa2979de400ab28fd71381', '2025-08-06 14:07:34', '2025-07-30 19:07:34', 1),
(92, 7, '139bcae1e075c2eb207781ddf52fdc0300c54030fae3553f951a448e66c49c56', '2025-08-07 12:29:08', '2025-07-31 17:29:08', 0),
(93, 10, 'f12cd502f8bff7d671754846dc5994a04d0af06472a3f117d215d0151ed22d1b', '2025-08-07 13:33:08', '2025-07-31 18:33:08', 1),
(94, 7, 'b9d30d2c4e2fffab97c72fbb58fd07c67d78643f07264a4ee76745ef6f188075', '2025-08-07 14:00:24', '2025-07-31 19:00:24', 0),
(95, 10, '79cab90b55683116018833127c303c460b1ca51dd7655f4d43dfe9977841b846', '2025-08-07 14:01:14', '2025-07-31 19:01:14', 1),
(96, 7, 'd68a690fd9a72394af49c665a0574bae448f276d5509c9d5658a067256afbd99', '2025-08-07 14:05:09', '2025-07-31 19:05:09', 0),
(97, 7, '41133b0428b290e3987127e89794ee4aea06dba6ee83e0cd911af0f81c74dac9', '2025-08-07 14:06:02', '2025-07-31 19:06:02', 0),
(98, 10, '289c1119c3d97d04c3030bea9f191468e83ecf475910b17a17905a796ff4cd71', '2025-08-07 14:08:07', '2025-07-31 19:08:07', 1),
(99, 32, 'ae0458f762dac8cd635d9237b56c5c04fd62eac980c24f1f20bb90aba1c0d02a', '2025-08-07 15:35:39', '2025-07-31 20:35:39', 0),
(100, 32, '73f000ba4927b00e6238ea8648e4c5a78a20bd704352bbf3187315129bbc1fc4', '2025-08-07 15:53:42', '2025-07-31 20:53:42', 0),
(101, 10, '20eab1f86b7d7bd80b5105d126460099e7e72540d6c5538a9bb8d5a3ce7fe896', '2025-08-07 16:09:46', '2025-07-31 21:09:46', 1),
(102, 10, 'f39806b89bff960b1dffa5c27c31ba934ca4ba262180b9bf4e77eb03a0cbb9ff', '2025-08-07 16:30:33', '2025-07-31 21:30:33', 0),
(103, 32, '11805bbb7a0d13b6786d2a8b3aa34cea66d2a2084ba408c299a303ae7a9b75f4', '2025-08-07 16:52:08', '2025-07-31 21:52:08', 0);

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
(4, 'OUT', 54.00, '2025-06-29 21:35:55', 'Actualización directa del producto - autotrigger', 'PRODUCTO', 2),
(5, 'OUT', 15.00, '2025-07-31 21:14:49', 'Actualización directa del producto - autotrigger', 'PRODUCTO', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tables`
--

CREATE TABLE `tables` (
  `id_table` int NOT NULL,
  `table_number` int DEFAULT NULL,
  `qr_token` varchar(500) DEFAULT NULL,
  `token_expiration` datetime DEFAULT NULL,
  `table_status` enum('FREE','OCCUPIED','INACTIVE','DELETED') CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Volcado de datos para la tabla `tables`
--

INSERT INTO `tables` (`id_table`, `table_number`, `qr_token`, `token_expiration`, `table_status`) VALUES
(1, 1, 'a80e22af6634477f54dab3c07563fd7aa8962e326cbb778d93957d6a131417b5', NULL, 'OCCUPIED'),
(2, NULL, 'ed73ac6d1fd0f5398651f6f7e5148583e3e7994a74883db90b4ee6ec6961da49', '2025-07-19 22:40:02', 'DELETED'),
(3, 7, '45cafd043fb5cb19fb0cda85e43c00e8684f00dd4505895b41adb1fb7aab74f4', '2025-07-30 17:00:37', 'INACTIVE'),
(4, NULL, 'c9502c3d7d0ed2da193371b26b5456281a8bc9cbaf753973915de10314da5cdb', '2025-07-31 16:50:01', 'DELETED'),
(6, 4, '6d1ad1ca3f0610d9c71c60264f25e401f3a76acfec8921c5792452771c0860be', '2025-07-31 21:50:01', 'FREE'),
(8, NULL, 'ea5efdd5c951b7bb2e82936833dba34edb84ebe75be0b7c12af4292542afb65f', '2025-07-31 10:09:07', 'DELETED'),
(9, NULL, '02fbbeb088f67c909f4d490857b281333251aecb495e44fa4b3dfd5921c90c5f', '2025-07-31 12:30:01', 'DELETED'),
(10, 77, 'b0582cead4ea0aa9d404a8ccb466889fb83817e00079291c767f22348d7b6b19', '2025-07-31 21:50:01', 'FREE');

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
(1, '2025-06-19 14:26:42', NULL, 'CLOSED', 1),
(2, '2025-06-18 14:42:00', '2025-06-12 14:42:00', 'CLOSED', 1),
(3, '2025-06-19 14:42:00', '2025-06-28 14:42:00', 'EXPIRED', 1),
(4, '2025-07-08 12:15:03', NULL, 'CLOSED', 1),
(5, '2025-07-08 12:32:02', NULL, 'CLOSED', 2),
(6, '2025-07-09 12:43:53', NULL, 'CLOSED', 2),
(7, '2025-07-14 12:12:47', NULL, 'CLOSED', 2),
(8, '2025-07-19 22:28:52', NULL, 'CLOSED', 1),
(9, '2025-07-19 22:30:35', NULL, 'CLOSED', 2),
(10, '2025-07-22 17:26:03', NULL, 'CLOSED', 1),
(11, '2025-07-31 15:54:15', NULL, 'CLOSED', 1),
(12, '2025-07-31 16:28:49', NULL, 'CLOSED', 1),
(13, '2025-07-31 16:44:55', NULL, 'CLOSED', 1),
(14, '2025-07-31 16:45:36', NULL, 'CLOSED', 1),
(15, '2025-07-31 17:29:11', NULL, 'ACTIVE', 1);

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
  ADD KEY `fk_products_product_types1_idx` (`product_types_id_type`),
  ADD KEY `product_category` (`product_category`);

--
-- Indices de la tabla `products_category`
--
ALTER TABLE `products_category`
  ADD PRIMARY KEY (`id_category`);

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
  MODIFY `id_employe` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

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
  MODIFY `id_ingredient` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_order` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
  MODIFY `id_product` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `products_category`
--
ALTER TABLE `products_category`
  MODIFY `id_category` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `product_types`
--
ALTER TABLE `product_types`
  MODIFY `id_type` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id_sale` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id_movement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tables`
--
ALTER TABLE `tables`
  MODIFY `id_table` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `table_sessions`
--
ALTER TABLE `table_sessions`
  MODIFY `id_session` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  ADD CONSTRAINT `fk_products_product_types1` FOREIGN KEY (`product_types_id_type`) REFERENCES `product_types` (`id_type`),
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`product_category`) REFERENCES `products_category` (`id_category`);

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

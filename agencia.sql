-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 25-07-2025 a las 03:19:31
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agencia`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel`
--

DROP TABLE IF EXISTS `hotel`;
CREATE TABLE IF NOT EXISTS `hotel` (
  `id_hotel` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `habitaciones_disponibles` int NOT NULL,
  `tarifa_noche` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_hotel`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `hotel`
--

INSERT INTO `hotel` (`id_hotel`, `nombre`, `ubicacion`, `habitaciones_disponibles`, `tarifa_noche`, `created_at`) VALUES
(1, 'Hotel céntrico', 'Madrid', 15, 35000.00, '2025-07-09 21:31:42'),
(2, 'Hotel Eiffel', 'París', 9, 20000.00, '2025-07-09 22:11:33'),
(3, 'Hotel Coliseo', 'Roma', 6, 75000.00, '2025-07-09 22:12:25'),
(4, 'Hotel Big Ben', 'Londres', 3, 56000.00, '2025-07-09 22:12:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

DROP TABLE IF EXISTS `reserva`;
CREATE TABLE IF NOT EXISTS `reserva` (
  `id_reserva` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int DEFAULT NULL,
  `fecha_reserva` date NOT NULL,
  `id_vuelo` int DEFAULT NULL,
  `id_hotel` int DEFAULT NULL,
  PRIMARY KEY (`id_reserva`),
  KEY `fk_vuelo` (`id_vuelo`),
  KEY `fk_hotel` (`id_hotel`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id_reserva`, `id_cliente`, `fecha_reserva`, `id_vuelo`, `id_hotel`) VALUES
(1, 101, '2025-07-20', 1, 1),
(2, 102, '2025-07-21', 1, NULL),
(3, 103, '2025-07-22', NULL, 1),
(4, 104, '2025-07-23', 2, 2),
(5, 105, '2025-07-24', 2, 1),
(6, 106, '2025-07-25', 1, 2),
(7, 107, '2025-07-26', 3, 3),
(8, 108, '2025-07-27', NULL, 1),
(9, 109, '2025-07-28', 3, NULL),
(10, 110, '2025-07-29', 4, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelo`
--

DROP TABLE IF EXISTS `vuelo`;
CREATE TABLE IF NOT EXISTS `vuelo` (
  `id_vuelo` int NOT NULL AUTO_INCREMENT,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `fecha_salida` date NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `plazas_disponibles` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_vuelo`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `vuelo`
--

INSERT INTO `vuelo` (`id_vuelo`, `origen`, `destino`, `fecha_salida`, `precio`, `plazas_disponibles`, `created_at`) VALUES
(1, 'Santiago', 'Madrid', '2025-08-10', 550000.00, 4, '2025-07-09 21:28:45'),
(2, 'Santiago', 'París', '2025-09-01', 480000.00, 6, '2025-07-09 22:09:28'),
(3, 'Santiago', 'Roma', '2025-07-20', 600000.00, 2, '2025-07-09 22:10:05'),
(4, 'Santiago', 'Londres', '2025-10-15', 400000.00, 10, '2025-07-09 22:10:36');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

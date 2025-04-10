-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-04-2025 a las 06:51:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `informes_gastro`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coberturas`
--

CREATE TABLE `coberturas` (
  `id_cobertura` int(11) NOT NULL,
  `nombre_cobertura` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coberturas`
--

INSERT INTO `coberturas` (`id_cobertura`, `nombre_cobertura`) VALUES
(1, 'IOSFA'),
(5, 'LALALA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informes`
--

CREATE TABLE `informes` (
  `id_informe` int(11) NOT NULL,
  `nombre_paciente` varchar(100) NOT NULL,
  `dni_paciente` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `url_archivo` varchar(250) NOT NULL,
  `mail_paciente` varchar(100) NOT NULL,
  `tipo_informe` varchar(5) NOT NULL,
  `id_cobertura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `informes`
--

INSERT INTO `informes` (`id_informe`, `nombre_paciente`, `dni_paciente`, `fecha`, `url_archivo`, `mail_paciente`, `tipo_informe`, `id_cobertura`) VALUES
(1, 'asd', '', '2025-04-04', '', 'abril@gmail.com', '', NULL),
(2, 'asd', '', '2025-04-04', 'http://localhost/app-medica/public/uploads/asd/asd_20250404_Captur4.PNG,http://localhost/app-medica/public/uploads/asd/asd_20250404_Captura%205%28bis%29.png,http://localhost/app-medica/public/uploads/asd/asd_20250404_Captura%206%28bis%29.png', 'abril@gmail.com', '', 1),
(3, 'Nombre Actualizado', '1122334455', '2025-04-15', 'http://localhost/uploads/agus_123456/agus_123456_20250322_Parcial%202%20-%20Resuelto.pdf', 'nuevo.correo@example.com', 'Infor', 1),
(4, 'agus', '123456', '2025-03-22', 'http://localhost/uploads/agus_123456/agus_123456_20250322_Parcial%202%20-%20Resuelto.pdf', 'correo@gmail.com', 'prueb', 1),
(5, 'fede', '222222', '2025-03-22', 'http://localhost/uploads/fede_222222/fede_222222_20250322_Parcial%202%20-%20Resuelto.pdf', 'correo@gmail.com', 'prueb', 1),
(6, 'fede', '222222', '2025-03-22', 'uploads/fede_222222/20250407_155746', 'correo@gmail.com', 'prueb', 1),
(7, 'fede', '222222', '2025-03-22', 'uploads/fede_222222/20250407_160315/informe_2025_03_22_1744041795.pdf', 'correo@gmail.com', 'prueb', 1),
(8, 'agus', '1111111', '2025-03-22', 'uploads/agus_1111111/20250407_160339/informe_2025_03_22_1744041819.pdf', 'correo@gmail.com', 'prueb', 1),
(9, 'agus', '1111111', '2025-03-22', 'uploads/agus_1111111/20250407_160637/informe_2025_03_22_1744041997.pdf', 'correo@gmail.com', 'prueb', 1),
(10, 'agus', '1111111', '2025-03-22', 'uploads/agus_1111111/20250408_122404/informe_2025_03_22_1744125847.pdf', 'correo@gmail.com', 'prueb', 1),
(11, 'fernando', '1111111', '2025-03-22', 'uploads/fernando_1111111/20250408_122451/informe_2025_03_22_1744125891.pdf', 'agusfull22@hotmail.com', 'prueb', 1),
(12, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_122744/informe_2025_03_22_1744126064.pdf', 'federico.pared@gmail.com', 'prueb', 1),
(13, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_130935/informe_2025_03_22_1744128575.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(14, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_131532/informe_2025_03_22_1744128932.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(15, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_131837/informe_2025_03_22_1744129118.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(16, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_132230/informe_2025_03_22_1744129350.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(17, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_132547/informe_2025_03_22_1744129547.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(18, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_132932/informe_2025_03_22_1744129772.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(19, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_133225/informe_2025_03_22_1744129945.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(20, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_133748/informe_2025_03_22_1744130268.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(21, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_133805/informe_2025_03_22_1744130285.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(22, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_135435/informe_2025_03_22_1744131275.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(23, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_140025/informe_2025_03_22_1744131625.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(24, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_141155/informe_2025_03_22_1744132315.pdf', 'agusfull22@hotmail.com', 'prueb', 1),
(25, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_150645/informe_2025_03_22_1744135607.pdf', 'federico.pared@hotmail.com', 'prueb', 1),
(26, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_150717/informe_2025_03_22_1744135637.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1),
(27, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_150738/informe_2025_03_22_1744135658.pdf', 'federico.pared@gmail.com', 'prueb', 1),
(28, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250408_150801/informe_2025_03_22_1744135681.pdf', 'agusfull22@hotmail.com', 'prueb', 1),
(29, 'fede', '1111111', '2025-03-22', 'uploads/fede_1111111/20250409_012728/informe_2025_03_22_1744172851.pdf', 'agustin.moya.4219@gmail.com', 'prueb', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(200) NOT NULL,
  `pass` char(60) NOT NULL,
  `mail` varchar(200) DEFAULT NULL,
  `pidio_cambio` tinyint(1) NOT NULL,
  `pass_aux` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre_usuario`, `pass`, `mail`, `pidio_cambio`, `pass_aux`) VALUES
(1, 'admin', '$2y$10$fciWkE9uo6GnIie9qU66Qu9iF2ws7DlL.lEw55H3KUNHtfY7JNztK', 'agustin.moya.4219@gmail.com', 0, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `coberturas`
--
ALTER TABLE `coberturas`
  ADD PRIMARY KEY (`id_cobertura`);

--
-- Indices de la tabla `informes`
--
ALTER TABLE `informes`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `informe_cobertura_idx` (`id_cobertura`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `coberturas`
--
ALTER TABLE `coberturas`
  MODIFY `id_cobertura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `informes`
--
ALTER TABLE `informes`
  MODIFY `id_informe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `informes`
--
ALTER TABLE `informes`
  ADD CONSTRAINT `informe_cobertura` FOREIGN KEY (`id_cobertura`) REFERENCES `coberturas` (`id_cobertura`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

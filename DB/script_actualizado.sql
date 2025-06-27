CREATE DATABASE  IF NOT EXISTS `informes_gastro`;
USE `informes_gastro`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


DROP TABLE IF EXISTS `coberturas`;
CREATE TABLE `coberturas` (
  `id_cobertura` int(11) NOT NULL,
  `nombre_cobertura` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `informes`;
CREATE TABLE `informes` (
  `id_informe` int(11) NOT NULL,
  `nombre_paciente` varchar(100) NOT NULL,
  `dni_paciente` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `edad` int(11) DEFAULT NULL,
  `url_archivo` varchar(250) NOT NULL,
  `mail_paciente` varchar(100) NOT NULL,
  `tipo_informe` varchar(5) NOT NULL,
  `id_cobertura` int(11) DEFAULT NULL,
  `fecha_nacimiento_paciente` date DEFAULT NULL,
  `numero_afiliado` int(11) DEFAULT NULL,
  `medico_envia_estudio` varchar(100) DEFAULT NULL,
  `motivo_estudio` varchar(100) DEFAULT NULL,
  `estomago` varchar(100) DEFAULT NULL,
  `duodeno` varchar(100) DEFAULT NULL,
  `esofago` varchar(100) DEFAULT NULL,
  `conclusion` text DEFAULT NULL,
  `efectuo_terapeutica` bit(1) DEFAULT NULL,
  `tipo_terapeutica` varchar(15) DEFAULT NULL,
  `efectuo_biopsia` bit(1) DEFAULT NULL,
  `fracos_biopsia` int(11) DEFAULT NULL,
  `informe` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre_usuario` varchar(200) NOT NULL,
  `pass` char(60) NOT NULL,
  `mail` varchar(200) DEFAULT NULL,
  `pidio_cambio` tinyint(1) NOT NULL,
  `pass_aux` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `coberturas`
  ADD PRIMARY KEY (`id_cobertura`);

ALTER TABLE `informes`
  ADD PRIMARY KEY (`id_informe`),
  ADD KEY `informe_cobertura_idx` (`id_cobertura`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);


ALTER TABLE `coberturas`
  MODIFY `id_cobertura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `informes`
  MODIFY `id_informe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `informes`
  ADD CONSTRAINT `informe_cobertura` FOREIGN KEY (`id_cobertura`) REFERENCES `coberturas` (`id_cobertura`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;


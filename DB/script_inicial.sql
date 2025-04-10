CREATE DATABASE  IF NOT EXISTS `informes_gastro`;
USE `informes_gastro`;

DROP TABLE IF EXISTS `coberturas`;
CREATE TABLE `coberturas` (
  `id_cobertura` int NOT NULL,
  `nombre_cobertura` varchar(100) NOT NULL,
  PRIMARY KEY (`id_cobertura`)
);

DROP TABLE IF EXISTS `informes`;
CREATE TABLE `informes` (
  `id_informe` int NOT NULL,
  `nombre_paciente` varchar(100) NOT NULL,
  `dni_paciente` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `url_archivo` varchar(250) NOT NULL,
  `mail_paciente` varchar(100) NOT NULL,
  `tipo_informe` varchar(5) NOT NULL,
  `id_cobertura` int DEFAULT NULL,
  PRIMARY KEY (`id_informe`),
  KEY `informe_cobertura_idx` (`id_cobertura`),
  CONSTRAINT `informe_cobertura` FOREIGN KEY (`id_cobertura`) REFERENCES `coberturas` (`id_cobertura`)
);

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `nombre_usuario` varchar(200) NOT NULL,
  `pass` char(60) NOT NULL,
  `mail` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`)
);
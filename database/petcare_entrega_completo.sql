-- PetCare - TP6 Integrador - Base de datos con datos ficticios
-- Importar en una base de datos vacia llamada petcare.
-- Seleccionar primero la base de datos antes de ejecutar este script.
-- ADVERTENCIA: contiene DROP TABLE; no ejecutar sobre la base de produccion.

-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: petcare
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `dni` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `dni` (`dni`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `consultas`
--

DROP TABLE IF EXISTS `consultas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultas` (
  `id_consulta` int NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `diagnostico` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `id_mascota` int NOT NULL,
  `id_veterinario` int NOT NULL,
  PRIMARY KEY (`id_consulta`),
  KEY `id_mascota` (`id_mascota`),
  KEY `id_veterinario` (`id_veterinario`),
  CONSTRAINT `consultas_ibfk_1` FOREIGN KEY (`id_mascota`) REFERENCES `mascotas` (`id_mascota`),
  CONSTRAINT `consultas_ibfk_2` FOREIGN KEY (`id_veterinario`) REFERENCES `veterinarios` (`id_veterinario`),
  CONSTRAINT `consultas_chk_1` CHECK ((`precio` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mascotas` (
  `id_mascota` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `especie` varchar(30) NOT NULL,
  `raza` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `id_cliente` int NOT NULL,
  PRIMARY KEY (`id_mascota`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('administrador','usuario','veterinario') NOT NULL DEFAULT 'usuario',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `veterinarios`
--

DROP TABLE IF EXISTS `veterinarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `veterinarios` (
  `id_veterinario` int NOT NULL AUTO_INCREMENT,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `especialidad` varchar(50) NOT NULL,
  PRIMARY KEY (`id_veterinario`),
  UNIQUE KEY `matricula` (`matricula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- ============================================================
-- DATOS FICTICIOS PARA LA EVALUACION DEL TP6
-- No se incluyen datos ni contrasenas de usuarios reales.
-- Las claves se almacenan mediante hashes PHP password_hash().
-- ============================================================

INSERT INTO `clientes` (`id_cliente`, `dni`, `nombre`, `apellido`, `telefono`) VALUES
(1, '30111222', 'Ana', 'Perez', '1155550101'),
(2, '32222333', 'Martin', 'Lopez', '1155550102'),
(3, '34444555', 'Lucia', 'Fernandez', '1155550103');

INSERT INTO `veterinarios` (`id_veterinario`, `matricula`, `nombre`, `apellido`, `especialidad`) VALUES
(1, 'VET-DEMO-001', 'Laura', 'Garcia', 'Clinica general'),
(2, 'VET-DEMO-002', 'Diego', 'Romero', 'Dermatologia');

INSERT INTO `mascotas` (`id_mascota`, `nombre`, `especie`, `raza`, `fecha_nacimiento`, `id_cliente`) VALUES
(1, 'Luna', 'Perro', 'Mestiza', '2021-05-10', 1),
(2, 'Milo', 'Gato', 'Siames', '2022-09-15', 2),
(3, 'Toby', 'Perro', 'Labrador', '2020-03-20', 3);

INSERT INTO `consultas` (`id_consulta`, `fecha`, `diagnostico`, `precio`, `id_mascota`, `id_veterinario`) VALUES
(1, '2026-10-15 10:30:00', 'Control general y vacunacion', 18000.00, 1, 1),
(2, '2026-10-20 15:00:00', 'Revision dermatologica', 22000.00, 2, 2),
(3, '2026-11-05 09:15:00', 'Control de peso y alimentacion', 15000.00, 3, 1);

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `contrasena`, `rol`, `activo`) VALUES
(1, 'Administrador', 'Prueba', 'administrador.prueba@ejemplo.com', '$2y$12$cRqy1iSsk4m6EnQAnckUeOG0FBwaYECmgc58lSuVnHbwOFtrDQ0wu', 'administrador', 1),
(2, 'Veterinario', 'Prueba', 'veterinario.prueba@ejemplo.com', '$2y$12$P9WIs3N/ljUBFWlXTsxwvuQj8Oi3d6Lkjp4MHk1vjAmK1z5jBIzf6', 'veterinario', 1),
(3, 'Usuario', 'Prueba', 'usuario.prueba@ejemplo.com', '$2y$12$pZ4ZyrKXHH/BQEDEpe01ieTKk5GUvr/Ko1KoBpN4KaSvRa4wu3t/W', 'usuario', 1);

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-18 22:28:53

-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: hot_bd
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('hotel-la-mansion-cache-joge@gmail.com|127.0.0.1','i:1;',1780092125),('hotel-la-mansion-cache-joge@gmail.com|127.0.0.1:timer','i:1780092125;',1780092125),('hotel-la-mansion-cache-livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36','i:1;',1780092461),('hotel-la-mansion-cache-livewire-rate-limiter:056fc329aaaa757d31db450f525da23fde4d1b36:timer','i:1780092461;',1780092461);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitaciones`
--

DROP TABLE IF EXISTS `habitaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habitaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int unsigned NOT NULL,
  `precio_noche` decimal(10,2) NOT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `habitaciones_numero_unique` (`numero`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitaciones`
--

LOCK TABLES `habitaciones` WRITE;
/*!40000 ALTER TABLE `habitaciones` DISABLE KEYS */;
INSERT INTO `habitaciones` VALUES (6,'101','Doble',3,500.00,'Disponible','2 CAPAS UNA DE 2 PLAZAS Y UNA DE INDIVIDUAL',1,'2026-05-21 05:23:31','2026-05-21 05:24:24',NULL),(17,'601','Simple',1,100.00,'Disponible','Habitación simple con cama individual.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(18,'602','Simple',1,100.00,'Disponible','Habitación simple cómoda para una persona.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(19,'603','Doble',2,180.00,'Disponible','Habitación doble con dos camas.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(20,'604','Doble',2,180.00,'Disponible','Habitación doble con vista interior.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(21,'605','Matrimonial',2,220.00,'Disponible','Habitación matrimonial con cama doble.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(22,'606','Matrimonial',2,220.00,'Disponible','Habitación matrimonial amplia.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(23,'607','Familiar',4,350.00,'Disponible','Habitación familiar para cuatro personas.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(24,'608','Familiar',5,400.00,'Disponible','Habitación familiar amplia para grupos.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(25,'609','Suite',2,500.00,'Disponible','Suite con mayor comodidad y vista privilegiada.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL),(26,'610','Suite',3,550.00,'Disponible','Suite premium para estadías especiales.',1,'2026-05-21 22:18:22','2026-05-21 22:18:22',NULL);
/*!40000 ALTER TABLE `habitaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `huespedes`
--

DROP TABLE IF EXISTS `huespedes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `huespedes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `papelera_vaciada_at` timestamp NULL DEFAULT NULL,
  `papelera_vaciada_por` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_paterno` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido_materno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_documento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_documento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo_electronico` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nacionalidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `huespedes_user_id_foreign` (`user_id`),
  KEY `huespedes_papelera_vaciada_por_foreign` (`papelera_vaciada_por`),
  CONSTRAINT `huespedes_papelera_vaciada_por_foreign` FOREIGN KEY (`papelera_vaciada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `huespedes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `huespedes`
--

LOCK TABLES `huespedes` WRITE;
/*!40000 ALTER TABLE `huespedes` DISABLE KEYS */;
INSERT INTO `huespedes` VALUES (10,'2026-05-21 05:04:39','2026-05-22 04:03:25','2026-05-22 03:28:24','2026-05-22 04:03:25',10,NULL,'Marco','Antelo',NULL,'CI','34667900','444357899','jfabijove67@gmail.com','Argentina','2005-05-21',1),(11,'2026-05-21 07:21:20','2026-05-22 04:05:27',NULL,NULL,NULL,13,'Fabián','Antelo','Aliaga','CI','12893700','65545903','lpze.jorgefabian.jove.al@unifranz.edu.bo','Bolivia','2005-05-20',1),(12,'2026-05-28 15:03:35','2026-05-28 22:53:03',NULL,NULL,NULL,14,'María Fernanda','Rojas','Pérez','CI','8457391','71234598','mafer.rojas.95@gmail.com','Boliviana','1995-04-12',1),(13,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,15,'Carlos Andrés','Mendoza','Lima','CI','7364829','+59176543210','carlosml.88@gmail.com','Boliviana','1988-09-25',1),(14,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,16,'Valeria Camila','Flores','Gutiérrez','CI','9823145','70123456','vale.flores99@gmail.com','Boliviana','1999-01-18',1),(15,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,17,'Javier Alejandro','Quiroga','Salinas','CI','6951284','+59173456789','javiquiroga92@gmail.com','Boliviana','1992-07-03',1),(16,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,18,'Daniela Alejandra','Vargas','Molina','CI','8045127','72019834','dani.vargas97@gmail.com','Boliviana','1997-11-30',1),(17,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,19,'Luis Fernando','Arce','Villarroel','CI','5893412','+59178965412','luchito.arce85@gmail.com','Boliviana','1985-06-14',1),(18,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,20,'Gabriela Andrea','Paredes','Choque','CI','7652198','69874512','gaby.paredes94@gmail.com','Boliviana','1994-03-22',1),(19,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,21,'Sebastián Nicolás','Rivero','Suárez','CI','9346712','+59171567890','sebas.rivero91@gmail.com','Boliviana','1991-12-08',1),(20,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,22,'Paola Beatriz','Aguilar','Fernández','CI','8475619','77564321','paolita.aguilar96@gmail.com','Boliviana','1996-08-19',1),(21,'2026-05-28 15:03:35','2026-05-28 15:03:35',NULL,NULL,NULL,23,'Rodrigo Matías','Salazar','Medina','CI','6932581','+59170678912','rodrigo.salazar89@gmail.com','Boliviana','1989-10-11',1),(22,'2026-05-29 19:26:11','2026-05-29 19:26:11',NULL,NULL,NULL,24,'Jorge  Fabian','Jove','Aliaga','ci','12893701','65545901','pruebaregister28@gmail.com',NULL,'2005-03-16',1),(23,'2026-05-30 02:17:04','2026-05-30 02:17:04',NULL,NULL,NULL,25,'Jose Camilo','Tapia','Barrientos','CI','13623676','+5916544567','camilotapia1983@gmail.com','Bolivia','1983-07-14',1);
/*!40000 ALTER TABLE `huespedes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingrediente_plato`
--

DROP TABLE IF EXISTS `ingrediente_plato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingrediente_plato` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `plato_id` bigint unsigned NOT NULL,
  `ingrediente_id` bigint unsigned NOT NULL,
  `cantidad_requerida` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ingrediente_plato_plato_id_ingrediente_id_unique` (`plato_id`,`ingrediente_id`),
  KEY `ingrediente_plato_ingrediente_id_foreign` (`ingrediente_id`),
  CONSTRAINT `ingrediente_plato_ingrediente_id_foreign` FOREIGN KEY (`ingrediente_id`) REFERENCES `ingredientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ingrediente_plato_plato_id_foreign` FOREIGN KEY (`plato_id`) REFERENCES `platos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingrediente_plato`
--

LOCK TABLES `ingrediente_plato` WRITE;
/*!40000 ALTER TABLE `ingrediente_plato` DISABLE KEYS */;
INSERT INTO `ingrediente_plato` VALUES (6,5,6,9.99,'2026-05-21 05:52:18','2026-05-21 05:52:18'),(7,6,16,2.00,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(8,7,12,2.00,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(9,8,15,0.15,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(10,9,8,0.20,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(11,9,9,0.25,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(12,9,12,1.00,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(13,10,11,0.30,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(14,11,10,0.25,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(15,12,13,1.00,'2026-05-28 15:38:24','2026-05-28 15:38:24'),(16,12,14,0.20,'2026-05-28 15:38:24','2026-05-28 15:38:24');
/*!40000 ALTER TABLE `ingrediente_plato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingredientes`
--

DROP TABLE IF EXISTS `ingredientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingredientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad_medida` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_actual` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_vencimiento` date DEFAULT NULL,
  `proveedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingredientes`
--

LOCK TABLES `ingredientes` WRITE;
/*!40000 ALTER TABLE `ingredientes` DISABLE KEYS */;
INSERT INTO `ingredientes` VALUES (6,'Arros','g',10.00,7.00,20.00,'2026-09-04',NULL,'ESTA RICOoo','2026-05-21 05:44:46','2026-05-28 23:58:52',NULL),(7,'Papa','kg',10.00,11.99,50.00,'2026-05-31',NULL,NULL,'2026-05-21 05:46:22','2026-05-21 05:47:04',NULL),(8,'Arroz','kg',35.00,8.00,8.50,'2026-08-15','Distribuidora Andina','Base para platos principales.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(9,'Carne de res','kg',22.00,5.00,42.00,'2026-06-20','Carnes La Paz','Insumo para platos tradicionales.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(10,'Pollo','kg',28.00,6.00,28.00,'2026-06-18','Avícola del Valle','Para platos horneados y guarniciones.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(11,'Trucha','kg',16.00,4.00,38.00,'2026-06-16','Proveedor Lacustre','Producto recomendado para turistas.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(12,'Huevo','unidad',120.00,30.00,0.90,'2026-06-30','Granja San Miguel','Ingrediente para desayunos y platos principales.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(13,'Lechuga','unidad',35.00,8.00,3.00,'2026-06-12','Verduras del Altiplano','Insumo para ensaladas.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(14,'Tomate','kg',18.00,5.00,7.00,'2026-06-14','Verduras del Altiplano','Para ensaladas y salsas.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(15,'Quinua','kg',20.00,5.00,18.00,'2026-09-20','Granos Andinos Bolivia','Ingrediente tradicional para sopas.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL),(16,'Pan artesanal','unidad',80.00,20.00,1.50,'2026-06-10','Panadería Central','Acompañamiento para desayunos.','2026-05-28 15:38:13','2026-05-28 15:38:13',NULL);
/*!40000 ALTER TABLE `ingredientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_user`
--

DROP TABLE IF EXISTS `logs_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `rol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_user_user_id_foreign` (`user_id`),
  CONSTRAINT `logs_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_user`
--

LOCK TABLES `logs_user` WRITE;
/*!40000 ALTER TABLE `logs_user` DISABLE KEYS */;
INSERT INTO `logs_user` VALUES (10,9,'SUPER ADMIN','CAMBIAR_PASSWORD','Autenticación','Usuario cambió su propia contraseña.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 01:36:43'),(11,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 01:43:53'),(12,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 01:43:53'),(13,9,'SUPER ADMIN','CREAR','Usuarios','Usuario administrativo creado: Fabián Jove Aliaga (fabianaliaga785@gmail.com) con rol ADMIN.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:35:53'),(14,9,'SUPER ADMIN','ENVIAR_CREDENCIALES','Usuarios','Correo de credenciales enviado a fabianaliaga785@gmail.com para el usuario Fabián Jove Aliaga.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:36:02'),(15,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:40:54'),(16,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:44:44'),(17,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:46:22'),(18,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:59:03'),(19,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 02:59:03'),(20,10,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:06:28'),(21,10,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:06:28'),(22,10,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:11:13'),(23,10,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:11:13'),(24,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:11:57'),(25,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:12:32'),(26,9,'SUPER ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabián Jove Fernandes (fabianaliaga785@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 03:12:42'),(27,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:43:07'),(28,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:43:07'),(29,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:45:38'),(30,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:45:38'),(31,9,'SUPER ADMIN','CREAR','Usuarios','Usuario administrativo creado: Daniel  Molina (hotelpredict90@gmail.com) con rol CHEF.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:46:43'),(32,9,'SUPER ADMIN','ENVIAR_CREDENCIALES','Usuarios','Correo de credenciales enviado a hotelpredict90@gmail.com para el usuario Daniel  Molina.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:46:50'),(33,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:48:56'),(34,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:48:56'),(35,9,'SUPER ADMIN','CREAR','Usuarios','Usuario administrativo creado: FABI Ali Aliaga (fabianaliaga452@gmail.com) con rol RECEPCIONISTA.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:52:37'),(36,9,'SUPER ADMIN','ENVIAR_CREDENCIALES','Usuarios','Correo de credenciales enviado a fabianaliaga452@gmail.com para el usuario FABI Ali Aliaga.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 04:52:45'),(37,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:02:19'),(38,9,'SUPER ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:02:19'),(39,10,'ADMIN','CREAR','Huéspedes','Huésped creado: Marco  Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:04:39'),(40,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Marco  Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:08:37'),(41,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Marco  Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:11:06'),(42,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Marco  Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:20:15'),(43,10,'ADMIN','CREAR','Habitaciones','Habitación creada: 101 tipo Simple.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:23:31'),(44,10,'ADMIN','EDITAR','Habitaciones','Habitación editada: 101 estado Disponible.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:24:24'),(45,12,'RECEPCIONISTA','CREAR','Reservaciones','Reservación creada #10 para huésped Marco  en habitación 101.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:31:39'),(46,12,'RECEPCIONISTA','CONFIRMAR_PAGO','Pagos','Pago confirmado al crear reservación #10 por Bs. 500.00.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:31:39'),(47,12,'RECEPCIONISTA','CHECK_IN','Check-in','Check-in confirmado para reservación #10 con código CHK-B2663B.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:34:42'),(48,10,'ADMIN','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #10 con código CHK-B2663B.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:37:51'),(49,10,'ADMIN','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #10 con código CHK-B2663B.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:37:52'),(50,11,'CHEF','CREAR','Ingredientes','Ingrediente creado: Arros, stock actual 10.00 kg.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:44:46'),(51,11,'CHEF','EDITAR','Ingredientes','Ingrediente actualizado: Arros, stock actual 10.00 kg.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:44:56'),(52,11,'CHEF','CREAR','Ingredientes','Ingrediente creado: Papa, stock actual 10.00 kg.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:46:22'),(53,11,'CHEF','EDITAR','Ingredientes','Ingrediente actualizado: Papa, stock actual 10.00 kg.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:47:04'),(54,11,'CHEF','CREAR','Platos','Plato creado: Silpancho.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:52:18'),(55,11,'CHEF','CREAR','Menús','Menú creado: Almuerzo del 2026-05-20.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:54:10'),(56,11,'CHEF','EDITAR','Menús','Menú actualizado: Almuerzo del 2026-05-20.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 05:54:33'),(57,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: FABI Ali Aliaga (fabianaliaga452@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 06:49:31'),(58,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: FABI Ali Aliaga (fabianaliaga452@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 06:50:36'),(59,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:02:28'),(60,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:02:28'),(61,10,'ADMIN','CREAR','Huéspedes','Huésped creado: Fabián Antelo documento 12893700.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:21:20'),(62,10,'ADMIN','CREAR','Usuarios','Usuario huésped creado: Fabián Antelo Aliaga (lpze.jorgefabian.jove.al@unifranz.edu.bo).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:21:20'),(63,10,'ADMIN','ENVIAR_CREDENCIALES','Huéspedes','Correo de credenciales enviado al huésped lpze.jorgefabian.jove.al@unifranz.edu.bo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:21:29'),(64,12,'RECEPCIONISTA','ENVIAR_RECIBO_CORREO','Recibos','Recibo enviado por correo a jfabijove67@gmail.com para reservación #10.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:54:58'),(65,12,'RECEPCIONISTA','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #10 con código CHK-B2663B.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:56:37'),(66,12,'RECEPCIONISTA','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #10 con código CHK-B2663B.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 07:56:40'),(67,12,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 09:10:38'),(68,12,'RECEPCIONISTA','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 09:10:38'),(69,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 09:34:33'),(70,10,'ADMIN','LOGOUT','Autenticación','Usuario cerró sesión del panel administrativo.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 09:34:33'),(71,10,'ADMIN','EDITAR','Menús','Menú actualizado: Almuerzo del 2026-05-21.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-21 22:32:33'),(72,10,'ADMIN','ELIMINAR','Usuarios','Usuario administrativo dado de baja: Daniel  Molina (hotelpredict90@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 02:31:13'),(73,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: 46574___ Ali Aliaga (fabianaliaga452@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 02:32:55'),(74,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabian  Ali Aliaga (fabianaliaga452@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 02:33:06'),(75,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Fabian  Ali (fabianaliaga452@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 02:33:45'),(76,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Marco Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:18:07'),(77,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Marco Antelo documento 34667900.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:28:00'),(78,10,'ADMIN','EDITAR','Usuarios','Usuario administrativo editado: Daniel Molina (hotelpredict90@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:53:06'),(79,10,'ADMIN','ELIMINAR','Usuarios','Usuario administrativo dado de baja: Daniel Molina (hotelpredict90@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:54:04'),(80,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Fabián Antelo documento 12893700.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:57:55'),(81,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: Fabián Antelo documento 12893700.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-22 03:59:06'),(82,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: María Fernanda Rojas documento 8457391.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 22:51:39'),(83,10,'ADMIN','EDITAR','Huéspedes','Huésped editado: María Fernanda Rojas documento 8457391.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 22:53:03'),(84,10,'ADMIN','EDITAR','Reservaciones','Reservación editada #19 para huésped Gabriela Andrea en habitación 607.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:10:56'),(85,10,'ADMIN','CONFIRMAR_PAGO','Pagos','Pago confirmado desde reservación #19 por Bs. 1400.00.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:10:56'),(86,10,'ADMIN','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #19 con código CHK-05A591.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:12:06'),(87,10,'ADMIN','GENERAR_RECIBO_PDF','Recibos','Recibo PDF generado/descargado para reservación #19 con código CHK-05A591.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:12:10'),(88,10,'ADMIN','EDITAR','Reservaciones','Reservación editada #17 para huésped Daniela Alejandra en habitación 605.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:15:05'),(89,10,'ADMIN','EDITAR','Reservaciones','Reservación editada #21 para huésped Paola Beatriz en habitación 609.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:45:19'),(90,10,'ADMIN','EDITAR','Ingredientes','Ingrediente actualizado: Arros, stock actual 10.00 kg.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:58:47'),(91,10,'ADMIN','EDITAR','Ingredientes','Ingrediente actualizado: Arros, stock actual 10.00 g.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-28 23:58:52'),(92,10,'ADMIN','CREAR','Huéspedes','Huésped creado: Jose Camilo Tapia documento 13623676.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-30 02:17:04'),(93,10,'ADMIN','CREAR','Usuarios','Usuario huésped creado: Jose Camilo Tapia Barrientos (camilotapia1983@gmail.com).','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-30 02:17:04'),(94,10,'ADMIN','ENVIAR_CREDENCIALES','Huéspedes','Correo de credenciales enviado al huésped camilotapia1983@gmail.com.','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','2026-05-30 02:17:08');
/*!40000 ALTER TABLE `logs_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_plato`
--

DROP TABLE IF EXISTS `menu_plato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_plato` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` bigint unsigned NOT NULL,
  `plato_id` bigint unsigned NOT NULL,
  `orden` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_plato_menu_id_plato_id_unique` (`menu_id`,`plato_id`),
  KEY `menu_plato_plato_id_foreign` (`plato_id`),
  CONSTRAINT `menu_plato_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  CONSTRAINT `menu_plato_plato_id_foreign` FOREIGN KEY (`plato_id`) REFERENCES `platos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_plato`
--

LOCK TABLES `menu_plato` WRITE;
/*!40000 ALTER TABLE `menu_plato` DISABLE KEYS */;
INSERT INTO `menu_plato` VALUES (3,3,5,1,'2026-05-21 05:54:10','2026-05-21 22:32:33'),(4,4,6,1,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(5,4,7,2,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(6,4,13,3,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(7,5,8,1,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(8,5,9,2,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(9,5,10,3,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(10,6,11,1,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(11,6,12,2,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(12,7,8,1,'2026-05-28 15:29:54','2026-05-28 15:29:54'),(13,7,10,2,'2026-05-28 15:29:54','2026-05-28 15:29:54');
/*!40000 ALTER TABLE `menu_plato` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chef_id` bigint unsigned DEFAULT NULL,
  `fecha_menu` date NOT NULL,
  `tipo_menu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menus_chef_id_foreign` (`chef_id`),
  CONSTRAINT `menus_chef_id_foreign` FOREIGN KEY (`chef_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES (3,NULL,'2026-05-21','Almuerzo','Publicado',NULL,'2026-05-21 05:54:10','2026-05-21 22:32:33',NULL),(4,NULL,'2026-05-28','Desayuno','Publicado','Menú de desayuno disponible para huéspedes.','2026-05-28 15:29:43','2026-05-28 15:29:43',NULL),(5,NULL,'2026-05-28','Almuerzo','Publicado','Menú de almuerzo publicado para el día.','2026-05-28 15:29:43','2026-05-28 15:29:43',NULL),(6,NULL,'2026-05-28','Cena','Publicado','Menú de cena disponible para huéspedes.','2026-05-28 15:29:43','2026-05-28 15:29:43',NULL),(7,NULL,'2026-05-29','Almuerzo','Borrador','Menú preliminar para el día siguiente.','2026-05-28 15:29:43','2026-05-28 15:29:43',NULL);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (12,'0001_01_01_000000_create_users_table',1),(13,'0001_01_01_000001_create_cache_table',1),(14,'0001_01_01_000002_create_jobs_table',1),(15,'2026_05_15_150322_create_roles_table',1),(16,'2026_05_15_150339_add_role_id_to_users_table',1),(17,'2026_05_15_195534_add_nombre_completo_fields_to_users_table',1),(18,'2026_05_16_001554_create_huespedes_table',1),(19,'2026_05_16_004505_add_deleted_at_to_huespedes_table',1),(20,'2026_05_16_005500_add_fields_to_huespedes_table',1),(21,'2026_05_16_030458_create_habitacions_table',1),(22,'2026_05_16_195246_create_reservaciones_table',1),(23,'2026_05_16_232000_add_checkin_fields_to_reservaciones_table',2),(24,'2026_05_17_003300_add_checkout_fields_to_reservaciones_table',3),(25,'2026_05_17_160712_create_pagos_table',4),(26,'2026_05_17_202414_create_ingredientes_table',5),(27,'2026_05_17_214941_create_platos_table',6),(28,'2026_05_18_002522_create_ingrediente_plato_table',7),(29,'2026_05_18_010000_create_menus_table',8),(30,'2026_05_18_010100_create_menu_plato_table',8),(31,'2026_05_18_202201_create_personal_access_tokens_table',9),(32,'2026_05_19_220000_create_logs_user_table',10),(33,'2026_05_20_000000_create_paquetes_table',11),(34,'2026_05_20_010000_create_tours_table',12),(35,'2026_05_20_010100_add_tour_id_to_paquetes_table',12),(36,'2026_05_21_000000_add_papelera_vaciada_fields_to_users_and_huespedes_tables',13);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservacion_id` bigint unsigned NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `fecha_pago` datetime DEFAULT NULL,
  `comprobante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `registrado_por` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pagos_reservacion_id_foreign` (`reservacion_id`),
  KEY `pagos_registrado_por_foreign` (`registrado_por`),
  CONSTRAINT `pagos_registrado_por_foreign` FOREIGN KEY (`registrado_por`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `pagos_reservacion_id_foreign` FOREIGN KEY (`reservacion_id`) REFERENCES `reservaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (6,10,500.00,'QR','Confirmado','2026-05-21 01:31:39',NULL,'Pago registrado automáticamente desde reservación',12,'2026-05-21 05:31:39','2026-05-21 05:31:39'),(7,13,300.00,'QR','Confirmado','2026-05-21 10:30:00','COMP-QR-001','Pago confirmado mediante QR.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(8,14,300.00,'QR','Pendiente',NULL,NULL,'Pago pendiente de confirmación.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(9,15,720.00,'Transferencia','Confirmado','2026-05-22 14:15:00','COMP-TRF-002','Transferencia bancaria verificada.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(10,16,720.00,'Efectivo','Pendiente',NULL,NULL,'Pago pendiente al momento del check-in.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(11,17,660.00,'QR','Confirmado','2026-05-24 09:45:00','COMP-QR-003','Pago confirmado mediante QR.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(12,18,880.00,'Tarjeta','Confirmado','2026-05-25 16:20:00','COMP-TAR-004','Pago confirmado con tarjeta.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(13,19,1400.00,'QR','Pendiente',NULL,NULL,'Pago QR generado, pendiente de validación.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(14,20,1600.00,'Transferencia','Confirmado','2026-05-26 11:10:00','COMP-TRF-005','Transferencia confirmada por recepción.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(15,21,1500.00,'QR','Confirmado','2026-05-27 13:40:00','COMP-QR-006','Pago confirmado para reservación finalizada.',NULL,'2026-05-28 15:18:18','2026-05-28 15:18:18'),(16,19,1400.00,'QR','Confirmado','2026-05-28 19:10:56',NULL,'Pago registrado automáticamente desde reservación',10,'2026-05-28 23:10:56','2026-05-28 23:10:56');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paquetes`
--

DROP TABLE IF EXISTS `paquetes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paquetes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_habitacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tour_incluido` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tour_id` bigint unsigned DEFAULT NULL,
  `incluye_desayuno` tinyint(1) NOT NULL DEFAULT '0',
  `incluye_almuerzo` tinyint(1) NOT NULL DEFAULT '0',
  `incluye_cena` tinyint(1) NOT NULL DEFAULT '0',
  `duracion_dias` int unsigned NOT NULL DEFAULT '1',
  `precio_total` decimal(10,2) NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Borrador',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paquetes_tour_id_foreign` (`tour_id`),
  CONSTRAINT `paquetes_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paquetes`
--

LOCK TABLES `paquetes` WRITE;
/*!40000 ALTER TABLE `paquetes` DISABLE KEYS */;
INSERT INTO `paquetes` VALUES (2,'paquete test',NULL,'paquetes/01KS44DJAEDNTHWNAYW43GNZCX.jpg','Simple',NULL,2,1,0,0,2,250.00,'Publicado',NULL,'2026-05-21 06:04:19','2026-05-21 06:04:19',NULL),(3,'Paquete Escapada Sorata','Incluye habitación simple, desayuno y tour por Sorata Colonial.',NULL,'Simple','Tour a Sorata Colonial',3,1,0,0,2,280.00,'Publicado','Paquete corto para estadía de fin de semana.','2026-05-28 15:45:00','2026-05-28 15:45:00',NULL),(4,'Paquete Aventura San Pedro','Incluye habitación doble, desayuno, almuerzo y caminata guiada a la Gruta de San Pedro.',NULL,'Doble','Caminata a la Gruta de San Pedro',4,1,1,0,3,560.00,'Publicado','Paquete turístico de aventura.','2026-05-28 15:45:00','2026-05-28 15:45:00',NULL),(5,'Paquete Familiar Campestre','Incluye habitación familiar, desayuno diario y tour paisajístico familiar.',NULL,'Familiar','Tour Paisajístico Familiar',7,1,0,0,3,720.00,'Publicado','Paquete para familias y grupos pequeños.','2026-05-28 15:45:00','2026-05-28 15:45:00',NULL),(6,'Paquete Cultural La Mansión','Incluye habitación matrimonial, cena especial y experiencia cultural local.',NULL,'Matrimonial','Experiencia Cultural Local',6,1,0,1,2,460.00,'Publicado','Paquete orientado a parejas o visitantes culturales.','2026-05-28 15:45:00','2026-05-28 15:45:00',NULL),(7,'Paquete Miradores Premium','Incluye suite, desayuno, cena y ruta por miradores naturales.',NULL,'Suite','Ruta de Miradores Naturales',5,1,0,1,3,890.00,'Publicado','Paquete premium con experiencia paisajística.','2026-05-28 15:45:00','2026-05-28 15:45:00',NULL);
/*!40000 ALTER TABLE `paquetes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (8,'App\\Models\\User',13,'panel-huesped','afbd41e3f6f7f19ccb2ed202039abdc992429b3d7d8e584adc75a76477fa259d','[\"*\"]','2026-05-21 07:23:41',NULL,'2026-05-21 07:23:39','2026-05-21 07:23:41'),(9,'App\\Models\\User',13,'panel-huesped','d196bb17acb3bc5ad86a49791f6f60854caece5abb13b6eac8c51a4f6492961b','[\"*\"]','2026-05-21 10:03:07',NULL,'2026-05-21 10:00:12','2026-05-21 10:03:07'),(10,'App\\Models\\User',13,'panel-huesped','2da61986aba75e3934f5cb132ec9376c2a045a5d5869137ab967bb481fa17eb1','[\"*\"]','2026-05-21 22:14:42',NULL,'2026-05-21 10:03:11','2026-05-21 22:14:42'),(11,'App\\Models\\User',13,'panel-huesped','b9708d70eb8db80f3c31f1d578c9b88a131e8684212119bd202709f59ab04566','[\"*\"]','2026-05-21 22:33:18',NULL,'2026-05-21 22:14:45','2026-05-21 22:33:18'),(12,'App\\Models\\User',13,'panel-huesped','bcb6f272e09eed7ff12080cef771850ecdde9337baaa54a5c9d9ab7371553496','[\"*\"]','2026-05-23 02:13:52',NULL,'2026-05-21 23:10:08','2026-05-23 02:13:52'),(13,'App\\Models\\User',13,'panel-huesped','df3717c04aacd2554d0c24e80f6643f8fa271387ae62917b8128bbc38e372cc0','[\"*\"]','2026-05-23 02:02:42',NULL,'2026-05-23 01:59:56','2026-05-23 02:02:42'),(14,'App\\Models\\User',13,'panel-huesped','ede5f3ecc57364468d968347ee7ff4be893d3ff29aeb12af312534333b36a0cf','[\"*\"]',NULL,NULL,'2026-05-23 02:03:15','2026-05-23 02:03:15'),(15,'App\\Models\\User',13,'panel-huesped','16151b4a09f85b519e452c652eef07d9be3aa7ee214ca84e8917180f9064c4e6','[\"*\"]','2026-05-23 02:07:27',NULL,'2026-05-23 02:06:50','2026-05-23 02:07:27'),(16,'App\\Models\\User',24,'panel-huesped','b7489841e8676476ba59c9ea5f7e2c8da22825d641d0451fb5501ed02506a91d','[\"*\"]',NULL,NULL,'2026-05-29 19:26:46','2026-05-29 19:26:46'),(17,'App\\Models\\User',13,'panel-huesped','ae760cee28f620cf092a05c35c6c00d7a118ef5f7ed09e609a8de02f42b9f281','[\"*\"]','2026-05-30 02:20:14',NULL,'2026-05-30 01:49:08','2026-05-30 02:20:14');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `platos`
--

DROP TABLE IF EXISTS `platos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `platos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chef_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `categoria` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tiempo_preparacion` int DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `platos_chef_id_foreign` (`chef_id`),
  CONSTRAINT `platos_chef_id_foreign` FOREIGN KEY (`chef_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `platos`
--

LOCK TABLES `platos` WRITE;
/*!40000 ALTER TABLE `platos` DISABLE KEYS */;
INSERT INTO `platos` VALUES (5,NULL,'Silpancho',NULL,'Almuerzo',50.00,'platos/plato_6a0e655298dc17.97589513.jpg','Disponible',NULL,NULL,'2026-05-21 05:52:18','2026-05-21 05:52:18',NULL),(6,NULL,'Desayuno continental','Café, pan artesanal, mantequilla, mermelada y jugo natural.','Desayuno',35.00,NULL,'Disponible',15,'Opción ligera para huéspedes.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(7,NULL,'Omelette de la casa','Omelette con queso, jamón, verduras frescas y pan tostado.','Desayuno',42.00,NULL,'Disponible',20,'Preparación recomendada para desayuno.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(8,NULL,'Sopa de quinua','Sopa tradicional con quinua, verduras y hierbas aromáticas.','Entrada',28.00,NULL,'Disponible',25,'Entrada caliente del día.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(9,NULL,'Silpancho cochabambino','Carne apanada, arroz, papa, huevo, ensalada y salsa tradicional.','Almuerzo',58.00,NULL,'Disponible',35,'Plato principal tradicional.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(10,NULL,'Trucha a la plancha','Trucha fresca servida con papas doradas, arroz y ensalada.','Almuerzo',65.00,NULL,'Disponible',30,'Plato recomendado para turistas.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(11,NULL,'Pollo al horno con hierbas','Pollo horneado con hierbas, guarnición de arroz y vegetales salteados.','Cena',55.00,NULL,'Disponible',40,'Cena principal del hotel.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(12,NULL,'Ensalada campestre','Ensalada fresca con lechuga, tomate, queso, palta y aderezo de la casa.','Cena',38.00,NULL,'Disponible',15,'Opción liviana para la noche.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL),(13,NULL,'Api con pastel','Bebida tradicional caliente acompañada de pastel frito.','Desayuno',25.00,NULL,'Disponible',15,'Opción tradicional boliviana.','2026-05-28 15:29:01','2026-05-28 15:29:01',NULL);
/*!40000 ALTER TABLE `platos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservaciones`
--

DROP TABLE IF EXISTS `reservaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `huesped_id` bigint unsigned NOT NULL,
  `habitacion_id` bigint unsigned NOT NULL,
  `origen_reservacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Recepción presencial',
  `fecha_entrada` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `cantidad_personas` int unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado_reservacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente de pago',
  `checkin_at` datetime DEFAULT NULL,
  `checkin_user_id` bigint unsigned DEFAULT NULL,
  `codigo_checkout` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checkout_at` datetime DEFAULT NULL,
  `checkout_user_id` bigint unsigned DEFAULT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pendiente',
  `codigo_checkin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reservaciones_codigo_checkin_unique` (`codigo_checkin`),
  UNIQUE KEY `reservaciones_codigo_checkout_unique` (`codigo_checkout`),
  KEY `reservaciones_huesped_id_foreign` (`huesped_id`),
  KEY `reservaciones_habitacion_id_foreign` (`habitacion_id`),
  KEY `reservaciones_checkin_user_id_foreign` (`checkin_user_id`),
  KEY `reservaciones_checkout_user_id_foreign` (`checkout_user_id`),
  CONSTRAINT `reservaciones_checkin_user_id_foreign` FOREIGN KEY (`checkin_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservaciones_checkout_user_id_foreign` FOREIGN KEY (`checkout_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservaciones_habitacion_id_foreign` FOREIGN KEY (`habitacion_id`) REFERENCES `habitaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `reservaciones_huesped_id_foreign` FOREIGN KEY (`huesped_id`) REFERENCES `huespedes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservaciones`
--

LOCK TABLES `reservaciones` WRITE;
/*!40000 ALTER TABLE `reservaciones` DISABLE KEYS */;
INSERT INTO `reservaciones` VALUES (10,10,6,'Recepción presencial','2026-05-21','2026-05-22',3,500.00,'En estadía','2026-05-21 01:34:42',12,'OUT-QHRRMP',NULL,NULL,'QR','Confirmado','CHK-B2663B',NULL,'2026-05-21 05:31:39','2026-05-21 05:34:42'),(11,11,6,'Web huésped','2026-05-23','2026-05-24',1,500.00,'Cancelada',NULL,NULL,NULL,NULL,NULL,NULL,'Pendiente',NULL,'Reservación creada desde panel huésped','2026-05-21 22:29:46','2026-05-22 01:19:03'),(12,11,17,'Web huésped','2026-05-22','2026-05-24',1,200.00,'Pendiente de pago',NULL,NULL,NULL,NULL,NULL,NULL,'Pendiente',NULL,'Reservación creada desde panel huésped','2026-05-22 02:19:45','2026-05-22 02:19:45'),(13,12,17,'Web','2026-05-24','2026-05-27',1,300.00,'Finalizada','2026-05-24 14:10:00',11,NULL,'2026-05-27 10:30:00',11,'QR','Confirmado','CHK-601-2405',NULL,'2026-05-28 15:12:52','2026-05-28 15:22:18'),(14,13,18,'Web','2026-05-25','2026-05-28',2,300.00,'Pendiente de pago',NULL,NULL,NULL,NULL,NULL,'QR','Pendiente',NULL,NULL,'2026-05-28 15:12:52','2026-05-28 15:12:52'),(15,14,19,'Recepción','2026-05-26','2026-05-30',2,720.00,'En estadía','2026-05-26 15:20:00',11,NULL,NULL,NULL,'Transferencia','Confirmado','CHK-603-2605',NULL,'2026-05-28 15:12:52','2026-05-28 15:22:18'),(16,15,20,'Recepción','2026-05-28','2026-06-01',2,720.00,'Pendiente de pago',NULL,NULL,NULL,NULL,NULL,'Efectivo','Pendiente',NULL,NULL,'2026-05-28 15:12:52','2026-05-28 15:12:52'),(17,16,21,'Web','2026-06-02','2026-06-05',2,660.00,'Finalizada','2026-06-02 13:50:00',11,NULL,'2026-06-05 09:45:00',11,'QR','Pendiente','CHK-605-0206',NULL,'2026-05-28 15:12:52','2026-05-28 23:15:05'),(18,17,22,'Web','2026-06-04','2026-06-08',2,880.00,'En estadía','2026-06-04 16:00:00',11,NULL,NULL,NULL,'Tarjeta','Confirmado','CHK-606-0406',NULL,'2026-05-28 15:12:52','2026-05-28 15:22:18'),(19,18,23,'Web','2026-06-06','2026-06-10',4,1400.00,'Confirmada',NULL,NULL,NULL,NULL,NULL,'QR','Confirmado','CHK-05A591',NULL,'2026-05-28 15:12:52','2026-05-28 23:10:56'),(20,19,24,'Recepción','2026-06-09','2026-06-13',5,1600.00,'Finalizada','2026-06-09 14:35:00',11,NULL,'2026-06-13 10:15:00',11,'Transferencia','Confirmado','CHK-608-0906',NULL,'2026-05-28 15:12:52','2026-05-28 15:22:18'),(21,20,25,'Web','2026-06-12','2026-06-15',2,1500.00,'Finalizada',NULL,NULL,NULL,NULL,NULL,'QR','Confirmado','CHK-F42EF3',NULL,'2026-05-28 15:12:52','2026-05-28 23:45:19');
/*!40000 ALTER TABLE `reservaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'SUPER ADMIN','Acceso total al sistema y gestión completa de usuarios administrativos.',1,'2026-05-17 05:40:39','2026-05-21 00:57:54',NULL),(2,'ADMIN','Acceso administrativo general, gestión de huéspedes, recepcionistas, chefs y reportes.',1,'2026-05-17 05:40:40','2026-05-17 05:40:40',NULL),(3,'RECEPCIONISTA','Acceso a huéspedes, reservaciones y habitaciones para controlar el flujo hotelero.',1,'2026-05-17 05:40:40','2026-05-17 05:40:40',NULL),(4,'CHEF','Acceso al inventario de materia prima y menú diario.',1,'2026-05-17 05:40:40','2026-05-17 05:40:40',NULL),(5,'HUESPED','Acceso al panel de huésped, reservas y perfil propio.',1,'2026-05-17 05:40:40','2026-05-17 05:40:40',NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('cLBnm2ufmgoJuriUMY3PT6PK1olYO8HkBcfyC6Cf',10,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJqT3BOcEk1WHl1cHZvVDNTQlZUWWV5WU5ZMFJBVjRIRHdEa0YzaHN3IiwidXJsIjpbXSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluIiwicm91dGUiOiJmaWxhbWVudC5hZG1pbi5wYWdlcy5kYXNoYm9hcmQifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEwLCJwYXNzd29yZF9oYXNoX3dlYiI6ImFiZjEyYjc2MzlmNDM2ODMwNGQwNzJmYWI3MDA4MGQ5OTUzMDQzMWViY2Q3OTdkYmE5ZDFiODNmNWJiNWE4YjAiLCJ0YWJsZXMiOnsiZTY0NDgzM2Y0ZTRlMDg3MTIzMTVkYTcxYjMzZmFjZDJfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJuYW1lIiwibGFiZWwiOiJOb21icmUiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZW1haWwiLCJsYWJlbCI6IkNvcnJlbyBlbGVjdHJcdTAwZjNuaWNvIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InJvbGUubm9tYnJlIiwibGFiZWwiOiJSb2wiLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZXN0YWRvIiwibGFiZWwiOiJBY3Rpdm8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiZW1haWxfdmVyaWZpZWRfYXQiLCJsYWJlbCI6IkNvcnJlbyB2ZXJpZmljYWRvIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOmZhbHNlLCJpc1RvZ2dsZWFibGUiOnRydWUsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6dHJ1ZX0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IkNyZWFkbyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJ1cGRhdGVkX2F0IiwibGFiZWwiOiJBY3R1YWxpemFkbyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjpmYWxzZSwiaXNUb2dnbGVhYmxlIjp0cnVlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOnRydWV9XSwiYzI5ODhkYjRmZmE2N2JiMTdkZmM0YzA2YTMyMTg3MzNfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJub21icmUiLCJsYWJlbCI6IkluZ3JlZGllbnRlIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6InVuaWRhZF9tZWRpZGEiLCJsYWJlbCI6IlVuaWRhZCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzdG9ja19hY3R1YWwiLCJsYWJlbCI6IlN0b2NrIGFjdHVhbCIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJzdG9ja19taW5pbW8iLCJsYWJlbCI6IlN0b2NrIG1cdTAwZWRuaW1vIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImVzdGFkb192aXN1YWwiLCJsYWJlbCI6IkVzdGFkbyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9XSwiY2EyODBiYmExMmRiY2UxZGM2ZDkwN2MwODI4M2JhY2ZfY29sdW1ucyI6W3sidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJub21icmVzIiwibGFiZWwiOiJOb21icmVzIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImFwZWxsaWRvX3BhdGVybm8iLCJsYWJlbCI6IkFwZWxsaWRvIHBhdGVybm8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiYXBlbGxpZG9fbWF0ZXJubyIsImxhYmVsIjoiQXBlbGxpZG8gbWF0ZXJubyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJudW1lcm9fZG9jdW1lbnRvIiwibGFiZWwiOiJEb2N1bWVudG8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoidGVsZWZvbm8iLCJsYWJlbCI6IlRlbFx1MDBlOWZvbm8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6dHJ1ZSwiaXNUb2dnbGVhYmxlIjpmYWxzZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjpudWxsfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoiY29ycmVvX2VsZWN0cm9uaWNvIiwibGFiZWwiOiJDb3JyZW8gZWxlY3RyXHUwMGYzbmljbyIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJlc3RhZG8iLCJsYWJlbCI6IkFjdGl2byIsImlzSGlkZGVuIjpmYWxzZSwiaXNUb2dnbGVkIjp0cnVlLCJpc1RvZ2dsZWFibGUiOmZhbHNlLCJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiOm51bGx9LHsidHlwZSI6ImNvbHVtbiIsIm5hbWUiOiJlc3RhZG9fcmVzZXJ2YV92aXN1YWwiLCJsYWJlbCI6IkVzdGFkbyByZXNlcnZhIiwiaXNIaWRkZW4iOmZhbHNlLCJpc1RvZ2dsZWQiOnRydWUsImlzVG9nZ2xlYWJsZSI6ZmFsc2UsImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI6bnVsbH0seyJ0eXBlIjoiY29sdW1uIiwibmFtZSI6ImNyZWF0ZWRfYXQiLCJsYWJlbCI6IlJlZ2lzdHJhZG8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6ZmFsc2UsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0Ijp0cnVlfSx7InR5cGUiOiJjb2x1bW4iLCJuYW1lIjoidXBkYXRlZF9hdCIsImxhYmVsIjoiQWN0dWFsaXphZG8iLCJpc0hpZGRlbiI6ZmFsc2UsImlzVG9nZ2xlZCI6ZmFsc2UsImlzVG9nZ2xlYWJsZSI6dHJ1ZSwiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0Ijp0cnVlfV19LCJmaWxhbWVudCI6W119',1780093747),('jxVC1VBBr6HfpSYTJy0Nx6Km3g5ELnmRplgsNdeM',10,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJmak9PNFN3a0VjTnJvU3FSWlN5ajdXd2Y4TGhFOFV5SDJOdmlkUWVLIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbiIsInJvdXRlIjoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEwLCJwYXNzd29yZF9oYXNoX3dlYiI6ImFiZjEyYjc2MzlmNDM2ODMwNGQwNzJmYWI3MDA4MGQ5OTUzMDQzMWViY2Q3OTdkYmE5ZDFiODNmNWJiNWE4YjAifQ==',1780092227),('WxK6gIq5EyKjRMKujhHwThnTDv3AvSUJZwUxnYvO',11,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJBaU9TRllEY1MxSk5semZuamVUZWRWdWtLMXl0R3FZNW9iUjNYQW4wIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hZG1pbiIsInJvdXRlIjoiZmlsYW1lbnQuYWRtaW4ucGFnZXMuZGFzaGJvYXJkIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwidXJsIjpbXSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjExLCJwYXNzd29yZF9oYXNoX3dlYiI6IjFjZGM1NDJmYmQ2OWMwMjgwYjdiNzhkYmQxMzQxYjM1ZWU2MzQxOGM1MjAzZGIyYWU3MzYzMDU4NTFiNTAxMmEifQ==',1780093290);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tours`
--

DROP TABLE IF EXISTS `tours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `ubicacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duracion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cupos_disponibles` int unsigned DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tours`
--

LOCK TABLES `tours` WRITE;
/*!40000 ALTER TABLE `tours` DISABLE KEYS */;
INSERT INTO `tours` VALUES (2,'tour test',NULL,'aca','2',150.00,10,'tours/01KS448Y59VCWKRWWSVBQGF76K.jpg','Disponible',NULL,'2026-05-21 06:01:47','2026-05-21 06:01:47',NULL),(3,'Tour a Sorata Colonial','Recorrido guiado por el centro de Sorata, miradores principales y zonas históricas cercanas al hotel.','Sorata, La Paz','Medio día',80.00,15,NULL,'Disponible','Tour recomendado para visitantes nuevos.','2026-05-28 15:42:39','2026-05-28 15:42:39',NULL),(4,'Caminata a la Gruta de San Pedro','Experiencia turística hacia la Gruta de San Pedro con guía local y recorrido fotográfico.','Gruta de San Pedro','1 día',120.00,12,NULL,'Disponible','Actividad ideal para huéspedes aventureros.','2026-05-28 15:42:39','2026-05-28 15:42:39',NULL),(5,'Ruta de Miradores Naturales','Visita a miradores naturales de Sorata con explicación paisajística y tiempo libre para fotografías.','Sorata','Medio día',95.00,18,NULL,'Disponible','Recorrido paisajístico de dificultad baja.','2026-05-28 15:42:39','2026-05-28 15:42:39',NULL),(6,'Experiencia Cultural Local','Recorrido cultural con visita a espacios tradicionales, gastronomía local y explicación de costumbres de la zona.','Sorata Centro','1 día',110.00,10,NULL,'Disponible','Enfoque cultural y gastronómico.','2026-05-28 15:42:39','2026-05-28 15:42:39',NULL),(7,'Tour Paisajístico Familiar','Actividad familiar con recorrido suave, zonas verdes y espacios de descanso cercanos al hotel.','Hotel y alrededores','3 horas',70.00,20,NULL,'Disponible','Recomendado para familias con niños.','2026-05-28 15:42:39','2026-05-28 15:42:39',NULL);
/*!40000 ALTER TABLE `tours` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido_paterno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido_materno` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `papelera_vaciada_at` timestamp NULL DEFAULT NULL,
  `papelera_vaciada_por` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  KEY `users_papelera_vaciada_por_foreign` (`papelera_vaciada_por`),
  CONSTRAINT `users_papelera_vaciada_por_foreign` FOREIGN KEY (`papelera_vaciada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (9,1,'Super','Admin',NULL,'Super Admin','superadmin@lamansion.test','2026-05-21 00:57:54','$2y$12$PPeWBFNRMELnyx9WLa68cuTKfOVYFpEkiCjAkJRoLbnguQ4hdPkvO',1,NULL,'2026-05-21 00:57:54','2026-05-21 01:36:43',NULL,NULL,NULL),(10,2,'Fabián','Jove','Fernandes','Fabián Jove Fernandes','fabianaliaga785@gmail.com',NULL,'$2y$12$T0MYsOVZnpW3yiynDgGSyuZcyCXfZ/E44zzPIWAJRGfyffjhWX9DK',1,NULL,'2026-05-21 02:35:53','2026-05-21 03:11:57',NULL,NULL,NULL),(11,3,'Daniel','Molina',NULL,'Daniel Molina','hotelpredict90@gmail.com',NULL,'$2y$12$GXwLYXhuZLo.lmKEAU.iz.rtSCl2DVWR9jQyUHKJvOIxYbtTu6FCe',1,NULL,'2026-05-21 04:46:43','2026-05-22 03:54:15',NULL,NULL,NULL),(12,3,'Fabian ','Ali',NULL,'Fabian  Ali','fabianaliaga452@gmail.com',NULL,'$2y$12$hRU1ML2/m4GsNMvUcc0ABO2KZBT2a7etUdmqCYf69iVMzQM5GvECm',1,NULL,'2026-05-21 04:52:37','2026-05-22 02:33:45',NULL,NULL,NULL),(13,5,'Fabián','Antelo','Aliaga','Fabián Antelo Aliaga','lpze.jorgefabian.jove.al@unifranz.edu.bo',NULL,'$2y$12$4JLI2Hayth9tLOdyz7.GTu.Y.SSHAlTqkAxBn6AbETTzUlIF.uNOa',1,NULL,'2026-05-21 07:21:20','2026-05-21 07:21:20',NULL,NULL,NULL),(14,5,'María Fernanda','Rojas','Pérez','María Fernanda Rojas Pérez','mafer.rojas.95@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(15,5,'Carlos Andrés','Mendoza','Lima','Carlos Andrés Mendoza Lima','carlosml.88@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(16,5,'Valeria Camila','Flores','Gutiérrez','Valeria Camila Flores Gutiérrez','vale.flores99@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(17,5,'Javier Alejandro','Quiroga','Salinas','Javier Alejandro Quiroga Salinas','javiquiroga92@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(18,5,'Daniela Alejandra','Vargas','Molina','Daniela Alejandra Vargas Molina','dani.vargas97@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(19,5,'Luis Fernando','Arce','Villarroel','Luis Fernando Arce Villarroel','luchito.arce85@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(20,5,'Gabriela Andrea','Paredes','Choque','Gabriela Andrea Paredes Choque','gaby.paredes94@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(21,5,'Sebastián Nicolás','Rivero','Suárez','Sebastián Nicolás Rivero Suárez','sebas.rivero91@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(22,5,'Paola Beatriz','Aguilar','Fernández','Paola Beatriz Aguilar Fernández','paolita.aguilar96@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(23,5,'Rodrigo Matías','Salazar','Medina','Rodrigo Matías Salazar Medina','rodrigo.salazar89@gmail.com',NULL,'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.',1,NULL,'2026-05-28 14:59:52','2026-05-28 14:59:52',NULL,NULL,NULL),(24,5,'Jorge  Fabian','Jove','Aliaga','Jorge  Fabian Jove Aliaga','pruebaregister28@gmail.com',NULL,'$2y$12$VmeL5enB1caDAoTYZ6U/W.x7MM96RJ1Cut88bGzNcDBfU466Sft5W',1,NULL,'2026-05-29 19:26:11','2026-05-29 19:26:11',NULL,NULL,NULL),(25,5,'Jose Camilo','Tapia','Barrientos','Jose Camilo Tapia Barrientos','camilotapia1983@gmail.com',NULL,'$2y$12$t0tvZA4pxmQ0Yokc7aeQNugc1JiPrJrLfAlrk/VPFNHXZWxfeB74q',1,NULL,'2026-05-30 02:17:04','2026-05-30 02:17:04',NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-01 12:36:15

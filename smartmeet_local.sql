-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: smartmeet
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.3

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
-- Table structure for table `dismissed_activities`
--

DROP TABLE IF EXISTS `dismissed_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dismissed_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dismissed_activities_activity_key_unique` (`activity_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dismissed_activities`
--

LOCK TABLES `dismissed_activities` WRITE;
/*!40000 ALTER TABLE `dismissed_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `dismissed_activities` ENABLE KEYS */;
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
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
  `attempts` tinyint unsigned NOT NULL,
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
-- Table structure for table `meeting_invites`
--

DROP TABLE IF EXISTS `meeting_invites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_invites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invite_token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meeting_invites_invite_token_unique` (`invite_token`),
  KEY `meeting_invites_meeting_id_foreign` (`meeting_id`),
  CONSTRAINT `meeting_invites_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_invites`
--

LOCK TABLES `meeting_invites` WRITE;
/*!40000 ALTER TABLE `meeting_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `meeting_invites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_participants`
--

DROP TABLE IF EXISTS `meeting_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `meeting_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('invited','accepted','declined') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'invited',
  `joined_at` timestamp NULL DEFAULT NULL,
  `left_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_participants_meeting_id_foreign` (`meeting_id`),
  KEY `meeting_participants_user_id_foreign` (`user_id`),
  CONSTRAINT `meeting_participants_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meeting_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_participants`
--

LOCK TABLES `meeting_participants` WRITE;
/*!40000 ALTER TABLE `meeting_participants` DISABLE KEYS */;
INSERT INTO `meeting_participants` VALUES (2,'2026-07-18 10:15:53','2026-07-18 11:26:48',3,5,'invited',NULL,'2026-07-18 11:26:48'),(3,'2026-07-18 10:15:53','2026-07-18 10:15:53',3,4,'invited',NULL,NULL),(4,'2026-07-18 10:15:53','2026-07-18 10:15:53',3,2,'invited',NULL,NULL),(5,'2026-07-18 10:15:53','2026-07-18 10:15:53',3,6,'invited',NULL,NULL),(7,'2026-07-18 14:24:42','2026-07-18 14:33:08',4,4,'invited',NULL,'2026-07-18 14:33:08'),(8,'2026-07-18 14:24:42','2026-07-18 14:32:21',4,5,'invited',NULL,'2026-07-18 14:32:21'),(9,'2026-07-18 14:24:42','2026-07-18 14:24:42',4,6,'invited',NULL,NULL),(10,'2026-07-19 10:35:34','2026-07-19 10:35:34',5,4,'invited',NULL,NULL),(11,'2026-07-19 10:35:35','2026-07-19 10:35:35',5,5,'invited',NULL,NULL),(12,'2026-07-19 10:35:35','2026-07-19 11:16:16',5,6,'invited','2026-07-19 11:16:16',NULL),(13,'2026-07-21 07:36:38','2026-07-21 09:50:00',6,6,'invited','2026-07-21 09:50:00',NULL),(14,'2026-07-21 07:36:38','2026-07-21 07:36:38',6,5,'invited',NULL,NULL),(15,'2026-07-21 07:36:38','2026-07-21 07:36:38',6,4,'invited',NULL,NULL),(16,'2026-07-21 12:31:50','2026-07-21 14:11:44',7,4,'invited','2026-07-21 14:11:44',NULL),(17,'2026-07-21 12:31:50','2026-07-21 12:31:50',7,5,'invited',NULL,NULL),(18,'2026-07-21 12:31:50','2026-07-21 14:14:20',7,6,'invited','2026-07-21 14:14:20',NULL),(19,'2026-07-22 05:28:27','2026-07-22 06:26:56',8,4,'invited','2026-07-22 06:26:56',NULL),(20,'2026-07-22 05:28:27','2026-07-22 05:28:27',8,5,'invited',NULL,NULL),(21,'2026-07-22 05:28:27','2026-07-22 05:35:03',8,2,'invited','2026-07-22 05:35:03',NULL),(22,'2026-07-22 05:28:27','2026-07-22 05:28:27',8,6,'invited',NULL,NULL),(23,'2026-07-22 10:55:30','2026-07-22 11:55:09',9,4,'invited',NULL,'2026-07-22 11:55:09'),(24,'2026-07-22 10:55:30','2026-07-22 10:55:30',9,5,'invited',NULL,NULL),(25,'2026-07-22 10:55:30','2026-07-22 11:55:10',9,6,'invited',NULL,'2026-07-22 11:55:10'),(26,'2026-07-22 10:55:30','2026-07-22 10:55:30',9,2,'invited',NULL,NULL),(27,'2026-07-22 11:57:25','2026-07-22 13:00:54',10,4,'invited',NULL,'2026-07-22 13:00:54'),(28,'2026-07-22 11:57:25','2026-07-22 11:57:25',10,5,'invited',NULL,NULL),(29,'2026-07-22 11:57:25','2026-07-22 12:59:29',10,6,'invited',NULL,'2026-07-22 12:59:29'),(30,'2026-07-22 11:57:25','2026-07-22 11:57:25',10,9,'invited',NULL,NULL),(31,'2026-07-22 11:57:25','2026-07-22 11:57:25',10,2,'invited',NULL,NULL),(32,'2026-07-23 11:14:54','2026-07-23 11:14:54',11,2,'invited',NULL,NULL),(33,'2026-07-23 11:14:54','2026-07-23 12:15:35',11,4,'invited',NULL,'2026-07-23 12:15:35'),(34,'2026-07-23 11:14:54','2026-07-23 11:14:54',11,5,'invited',NULL,NULL),(35,'2026-07-23 11:14:54','2026-07-23 12:55:02',11,6,'invited',NULL,'2026-07-23 12:55:02'),(36,'2026-07-23 13:41:40','2026-07-23 13:41:40',12,2,'invited',NULL,NULL),(37,'2026-07-23 13:41:40','2026-07-23 13:41:40',12,5,'invited',NULL,NULL),(38,'2026-07-23 13:41:40','2026-07-23 13:57:37',12,4,'invited',NULL,'2026-07-23 13:57:37'),(39,'2026-07-23 13:41:40','2026-07-23 13:41:40',12,6,'invited',NULL,NULL),(40,'2026-07-23 13:59:02','2026-07-23 13:59:02',13,2,'invited',NULL,NULL),(41,'2026-07-23 13:59:02','2026-07-23 14:03:42',13,4,'invited',NULL,'2026-07-23 14:03:42'),(42,'2026-07-23 13:59:02','2026-07-23 13:59:02',13,5,'invited',NULL,NULL),(44,'2026-07-23 14:01:39','2026-07-23 14:01:39',13,6,'invited',NULL,NULL),(45,'2026-08-09 09:52:22','2026-08-09 11:49:58',14,4,'invited',NULL,'2026-08-09 11:49:58'),(46,'2026-08-09 12:24:07','2026-08-09 12:24:07',15,2,'invited',NULL,NULL),(47,'2026-08-09 12:24:07','2026-08-09 12:50:52',15,4,'invited',NULL,'2026-08-09 12:50:52'),(48,'2026-08-09 12:24:08','2026-08-09 12:24:08',15,5,'invited',NULL,NULL),(49,'2026-08-09 12:24:08','2026-08-09 12:24:08',15,10,'invited',NULL,NULL),(50,'2026-08-09 12:24:08','2026-08-09 12:24:08',15,6,'invited',NULL,NULL),(51,'2026-08-19 00:17:48','2026-08-19 00:17:48',16,5,'invited',NULL,NULL),(52,'2026-08-19 00:17:49','2026-08-19 02:04:22',16,4,'invited',NULL,'2026-08-19 02:04:22'),(53,'2026-08-19 00:17:49','2026-08-19 00:17:49',16,2,'invited',NULL,NULL),(54,'2026-08-19 00:17:49','2026-08-19 00:17:49',16,6,'invited',NULL,NULL),(55,'2026-08-27 10:21:49','2026-08-27 10:21:49',17,5,'invited',NULL,NULL),(56,'2026-08-27 10:21:49','2026-08-27 10:21:49',17,2,'invited',NULL,NULL),(57,'2026-08-27 10:21:49','2026-08-27 12:19:05',17,4,'invited',NULL,'2026-08-27 12:19:05');
/*!40000 ALTER TABLE `meeting_participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meeting_transcripts`
--

DROP TABLE IF EXISTS `meeting_transcripts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meeting_transcripts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `meeting_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `spoken_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meeting_transcripts_meeting_id_foreign` (`meeting_id`),
  KEY `meeting_transcripts_user_id_foreign` (`user_id`),
  CONSTRAINT `meeting_transcripts_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `meeting_transcripts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meeting_transcripts`
--

LOCK TABLES `meeting_transcripts` WRITE;
/*!40000 ALTER TABLE `meeting_transcripts` DISABLE KEYS */;
INSERT INTO `meeting_transcripts` VALUES (1,3,3,'hello hello','2026-07-18 11:20:22','2026-07-18 11:20:22','2026-07-18 11:20:22'),(2,3,3,'hello','2026-07-18 11:20:23','2026-07-18 11:20:23','2026-07-18 11:20:23'),(3,3,3,'Bluetooth','2026-07-18 11:25:40','2026-07-18 11:25:40','2026-07-18 11:25:40'),(4,4,3,'hello','2026-07-18 14:29:29','2026-07-18 14:29:29','2026-07-18 14:29:29'),(5,4,3,'hello','2026-07-18 14:29:30','2026-07-18 14:29:30','2026-07-18 14:29:30'),(6,4,3,'are you listening','2026-07-18 14:29:33','2026-07-18 14:29:33','2026-07-18 14:29:33'),(7,6,6,'hello','2026-07-21 07:59:13','2026-07-21 07:59:13','2026-07-21 07:59:13'),(8,6,6,'hello','2026-07-21 07:59:25','2026-07-21 07:59:25','2026-07-21 07:59:25'),(9,6,8,'hello','2026-07-21 07:59:46','2026-07-21 07:59:46','2026-07-21 07:59:46'),(10,6,8,'hello','2026-07-21 07:59:49','2026-07-21 07:59:49','2026-07-21 07:59:49'),(11,6,6,'hello','2026-07-21 08:00:27','2026-07-21 08:00:27','2026-07-21 08:00:27'),(12,6,6,'hello','2026-07-21 08:00:40','2026-07-21 08:00:40','2026-07-21 08:00:40'),(13,6,8,'hello hello','2026-07-21 08:01:14','2026-07-21 08:01:14','2026-07-21 08:01:14'),(14,6,8,'hello','2026-07-21 08:01:32','2026-07-21 08:01:32','2026-07-21 08:01:32'),(15,6,8,'hello','2026-07-21 08:01:35','2026-07-21 08:01:35','2026-07-21 08:01:35'),(16,6,8,'hello hello hello','2026-07-21 08:01:37','2026-07-21 08:01:37','2026-07-21 08:01:37'),(17,6,8,'cartoon','2026-07-21 08:02:41','2026-07-21 08:02:41','2026-07-21 08:02:41'),(18,6,8,'hello hello','2026-07-21 08:04:15','2026-07-21 08:04:15','2026-07-21 08:04:15'),(19,6,6,'Awaaz a rahi hai','2026-07-21 08:05:04','2026-07-21 08:05:04','2026-07-21 08:05:04'),(20,6,6,'hello','2026-07-21 08:07:52','2026-07-21 08:07:52','2026-07-21 08:07:52'),(21,6,6,'hello','2026-07-21 08:08:03','2026-07-21 08:08:03','2026-07-21 08:08:03'),(22,6,6,'Awaaz a rahi hai hello hello','2026-07-21 08:08:21','2026-07-21 08:08:21','2026-07-21 08:08:21'),(23,6,6,'hello','2026-07-21 08:10:07','2026-07-21 08:10:07','2026-07-21 08:10:07'),(24,6,6,'hello','2026-07-21 08:10:10','2026-07-21 08:10:10','2026-07-21 08:10:10'),(25,6,6,'hello','2026-07-21 08:10:34','2026-07-21 08:10:34','2026-07-21 08:10:34'),(26,6,6,'hello','2026-07-21 08:10:38','2026-07-21 08:10:38','2026-07-21 08:10:38'),(27,6,6,'hello hello','2026-07-21 08:11:15','2026-07-21 08:11:15','2026-07-21 08:11:15'),(28,6,6,'Awaaz a rahi hai hello hello','2026-07-21 08:11:51','2026-07-21 08:11:51','2026-07-21 08:11:51'),(29,6,8,'hello Awaaz a rahi hai','2026-07-21 08:12:49','2026-07-21 08:12:49','2026-07-21 08:12:49'),(30,6,6,'hello','2026-07-21 08:13:49','2026-07-21 08:13:49','2026-07-21 08:13:49'),(31,6,6,'hello Awaaz a rahi hai hello','2026-07-21 08:13:55','2026-07-21 08:13:55','2026-07-21 08:13:55'),(32,6,6,'hello','2026-07-21 08:16:43','2026-07-21 08:16:43','2026-07-21 08:16:43'),(33,6,6,'hello','2026-07-21 08:16:45','2026-07-21 08:16:45','2026-07-21 08:16:45'),(34,6,6,'hello','2026-07-21 08:18:07','2026-07-21 08:18:07','2026-07-21 08:18:07'),(35,6,6,'hello','2026-07-21 08:18:10','2026-07-21 08:18:10','2026-07-21 08:18:10'),(36,6,6,'Mera Kuch Loge','2026-07-21 08:23:33','2026-07-21 08:23:33','2026-07-21 08:23:33'),(37,6,8,'hello','2026-07-21 08:50:46','2026-07-21 08:50:46','2026-07-21 08:50:46'),(38,6,8,'hello','2026-07-21 08:50:50','2026-07-21 08:50:50','2026-07-21 08:50:50'),(39,6,8,'hello','2026-07-21 08:50:59','2026-07-21 08:50:59','2026-07-21 08:50:59'),(40,6,8,'hello','2026-07-21 09:03:53','2026-07-21 09:03:53','2026-07-21 09:03:53'),(41,6,8,'hello','2026-07-21 09:03:55','2026-07-21 09:03:55','2026-07-21 09:03:55'),(42,6,6,'hello hello','2026-07-21 09:11:49','2026-07-21 09:11:49','2026-07-21 09:11:49'),(43,7,6,'hello','2026-07-21 12:35:18','2026-07-21 12:35:18','2026-07-21 12:35:18'),(44,7,6,'excited','2026-07-21 12:35:37','2026-07-21 12:35:37','2026-07-21 12:35:37'),(45,7,6,'hello','2026-07-21 12:35:39','2026-07-21 12:35:39','2026-07-21 12:35:39'),(46,7,6,'hello','2026-07-21 12:35:45','2026-07-21 12:35:45','2026-07-21 12:35:45'),(47,7,6,'hi hi','2026-07-21 12:35:52','2026-07-21 12:35:52','2026-07-21 12:35:52'),(48,7,8,'hello hello','2026-07-21 12:36:08','2026-07-21 12:36:08','2026-07-21 12:36:08'),(49,7,6,'hello hello','2026-07-21 12:36:31','2026-07-21 12:36:31','2026-07-21 12:36:31'),(50,7,8,'hello hello','2026-07-21 12:36:31','2026-07-21 12:36:31','2026-07-21 12:36:31'),(51,7,8,'Awaaz a rahi hai','2026-07-21 12:40:38','2026-07-21 12:40:38','2026-07-21 12:40:38'),(52,7,6,'hello','2026-07-21 12:40:47','2026-07-21 12:40:47','2026-07-21 12:40:47'),(53,7,6,'hello','2026-07-21 12:40:53','2026-07-21 12:40:53','2026-07-21 12:40:53'),(54,7,6,'how are you','2026-07-21 12:40:59','2026-07-21 12:40:59','2026-07-21 12:40:59'),(55,7,8,'hello','2026-07-21 12:43:46','2026-07-21 12:43:46','2026-07-21 12:43:46'),(56,7,6,'Minu','2026-07-21 12:45:37','2026-07-21 12:45:37','2026-07-21 12:45:37'),(57,7,8,'Dekho','2026-07-21 12:45:45','2026-07-21 12:45:45','2026-07-21 12:45:45'),(58,7,8,'Nahin','2026-07-21 12:46:31','2026-07-21 12:46:31','2026-07-21 12:46:31'),(59,7,6,'hello','2026-07-21 12:46:58','2026-07-21 12:46:58','2026-07-21 12:46:58'),(60,7,8,'hello hello','2026-07-21 13:42:38','2026-07-21 13:42:38','2026-07-21 13:42:38'),(61,7,4,'hello','2026-07-21 13:43:33','2026-07-21 13:43:33','2026-07-21 13:43:33'),(62,7,4,'hello','2026-07-21 13:43:43','2026-07-21 13:43:43','2026-07-21 13:43:43'),(63,7,8,'hello hello','2026-07-21 13:45:11','2026-07-21 13:45:11','2026-07-21 13:45:11'),(64,7,8,'hello','2026-07-21 13:45:50','2026-07-21 13:45:50','2026-07-21 13:45:50'),(65,7,8,'hello','2026-07-21 13:46:35','2026-07-21 13:46:35','2026-07-21 13:46:35'),(66,7,8,'hello','2026-07-21 13:47:52','2026-07-21 13:47:52','2026-07-21 13:47:52'),(67,7,8,'hello','2026-07-21 13:47:55','2026-07-21 13:47:55','2026-07-21 13:47:55'),(68,7,8,'hello hello','2026-07-21 13:49:22','2026-07-21 13:49:22','2026-07-21 13:49:22'),(69,7,8,'message','2026-07-21 13:50:40','2026-07-21 13:50:40','2026-07-21 13:50:40'),(70,7,8,'hello','2026-07-21 13:53:48','2026-07-21 13:53:48','2026-07-21 13:53:48'),(71,7,8,'hello','2026-07-21 13:54:01','2026-07-21 13:54:01','2026-07-21 13:54:01'),(72,7,8,'hello hello','2026-07-21 13:54:34','2026-07-21 13:54:34','2026-07-21 13:54:34'),(73,7,8,'hello','2026-07-21 13:55:27','2026-07-21 13:55:27','2026-07-21 13:55:27'),(74,7,8,'hello','2026-07-21 13:55:30','2026-07-21 13:55:30','2026-07-21 13:55:30'),(75,7,8,'hello hello','2026-07-21 14:10:53','2026-07-21 14:10:53','2026-07-21 14:10:53'),(76,7,8,'are you listening','2026-07-21 14:10:58','2026-07-21 14:10:58','2026-07-21 14:10:58'),(77,7,8,'hello','2026-07-21 14:11:05','2026-07-21 14:11:05','2026-07-21 14:11:05'),(78,7,8,'hello','2026-07-21 14:11:11','2026-07-21 14:11:11','2026-07-21 14:11:11'),(79,7,8,'hello','2026-07-21 14:12:09','2026-07-21 14:12:09','2026-07-21 14:12:09'),(80,7,4,'hello hello','2026-07-21 14:12:10','2026-07-21 14:12:10','2026-07-21 14:12:10'),(81,7,4,'hello','2026-07-21 14:17:54','2026-07-21 14:17:54','2026-07-21 14:17:54'),(82,7,8,'hello','2026-07-21 14:18:00','2026-07-21 14:18:00','2026-07-21 14:18:00'),(83,7,6,'hello hello','2026-07-21 14:18:01','2026-07-21 14:18:01','2026-07-21 14:18:01'),(84,7,8,'hello','2026-07-21 14:18:02','2026-07-21 14:18:02','2026-07-21 14:18:02'),(85,7,4,'hello','2026-07-21 14:18:02','2026-07-21 14:18:02','2026-07-21 14:18:02'),(86,7,8,'hello','2026-07-21 14:19:12','2026-07-21 14:19:12','2026-07-21 14:19:12'),(87,7,6,'Hamari video','2026-07-21 14:22:46','2026-07-21 14:22:46','2026-07-21 14:22:46'),(88,8,2,'hello','2026-07-22 05:35:52','2026-07-22 05:35:52','2026-07-22 05:35:52'),(89,8,7,'hello','2026-07-22 05:35:52','2026-07-22 05:35:52','2026-07-22 05:35:52'),(90,8,2,'hello','2026-07-22 05:36:06','2026-07-22 05:36:06','2026-07-22 05:36:06'),(91,8,7,'hello','2026-07-22 05:36:06','2026-07-22 05:36:06','2026-07-22 05:36:06'),(92,8,2,'ladies and gentleman','2026-07-22 05:36:10','2026-07-22 05:36:10','2026-07-22 05:36:10'),(93,8,7,'meri jaan gentleman','2026-07-22 05:36:12','2026-07-22 05:36:12','2026-07-22 05:36:12'),(94,8,2,'hello','2026-07-22 05:36:32','2026-07-22 05:36:32','2026-07-22 05:36:32'),(95,8,7,'hello','2026-07-22 05:36:32','2026-07-22 05:36:32','2026-07-22 05:36:32'),(96,8,2,'hello','2026-07-22 05:36:33','2026-07-22 05:36:33','2026-07-22 05:36:33'),(97,8,7,'hello','2026-07-22 05:36:34','2026-07-22 05:36:34','2026-07-22 05:36:34'),(98,8,2,'hello','2026-07-22 05:39:17','2026-07-22 05:39:17','2026-07-22 05:39:17'),(99,8,2,'hello','2026-07-22 05:40:09','2026-07-22 05:40:09','2026-07-22 05:40:09'),(100,8,2,'hello','2026-07-22 05:40:15','2026-07-22 05:40:15','2026-07-22 05:40:15'),(101,8,7,'hello','2026-07-22 05:44:39','2026-07-22 05:44:39','2026-07-22 05:44:39'),(102,8,7,'hello','2026-07-22 05:44:41','2026-07-22 05:44:41','2026-07-22 05:44:41'),(103,8,7,'hello','2026-07-22 05:44:58','2026-07-22 05:44:58','2026-07-22 05:44:58'),(104,8,4,'hello hello','2026-07-22 05:44:59','2026-07-22 05:44:59','2026-07-22 05:44:59'),(105,8,7,'hello','2026-07-22 05:50:32','2026-07-22 05:50:32','2026-07-22 05:50:32'),(106,9,8,'hello','2026-07-22 11:04:45','2026-07-22 11:04:45','2026-07-22 11:04:45'),(107,9,8,'hello','2026-07-22 11:04:53','2026-07-22 11:04:53','2026-07-22 11:04:53'),(108,9,4,'hello','2026-07-22 11:04:57','2026-07-22 11:04:57','2026-07-22 11:04:57'),(109,9,4,'hello','2026-07-22 11:05:01','2026-07-22 11:05:01','2026-07-22 11:05:01'),(110,9,4,'hello','2026-07-22 11:05:26','2026-07-22 11:05:26','2026-07-22 11:05:26'),(111,9,8,'hello hello','2026-07-22 11:05:40','2026-07-22 11:05:40','2026-07-22 11:05:40'),(112,9,8,'hello','2026-07-22 11:06:33','2026-07-22 11:06:33','2026-07-22 11:06:33'),(113,9,8,'hello','2026-07-22 11:06:38','2026-07-22 11:06:38','2026-07-22 11:06:38'),(114,9,8,'hello','2026-07-22 11:06:42','2026-07-22 11:06:42','2026-07-22 11:06:42'),(115,9,8,'Awaaz a rahi hai','2026-07-22 11:06:49','2026-07-22 11:06:49','2026-07-22 11:06:49'),(116,9,4,'Awaaz a rahi hai','2026-07-22 11:06:59','2026-07-22 11:06:59','2026-07-22 11:06:59'),(117,9,4,'hello','2026-07-22 11:07:30','2026-07-22 11:07:30','2026-07-22 11:07:30'),(118,9,8,'hello','2026-07-22 11:07:40','2026-07-22 11:07:40','2026-07-22 11:07:40'),(119,9,8,'hello','2026-07-22 11:07:44','2026-07-22 11:07:44','2026-07-22 11:07:44'),(120,9,8,'hello','2026-07-22 11:08:51','2026-07-22 11:08:51','2026-07-22 11:08:51'),(121,9,8,'hello','2026-07-22 11:09:48','2026-07-22 11:09:48','2026-07-22 11:09:48'),(122,9,8,'hello','2026-07-22 11:09:50','2026-07-22 11:09:50','2026-07-22 11:09:50'),(123,9,8,'hello hello','2026-07-22 11:10:10','2026-07-22 11:10:10','2026-07-22 11:10:10'),(124,9,6,'hello','2026-07-22 11:12:49','2026-07-22 11:12:49','2026-07-22 11:12:49'),(125,9,6,'hello','2026-07-22 11:12:55','2026-07-22 11:12:55','2026-07-22 11:12:55'),(126,9,4,'hello hello','2026-07-22 11:20:23','2026-07-22 11:20:23','2026-07-22 11:20:23'),(127,9,4,'hello','2026-07-22 11:25:07','2026-07-22 11:25:07','2026-07-22 11:25:07'),(128,9,8,'ہیلو','2026-07-22 11:30:05','2026-07-22 11:30:05','2026-07-22 11:30:05'),(129,9,8,'ہیلو اواز ارہی ہے','2026-07-22 11:30:09','2026-07-22 11:30:09','2026-07-22 11:30:09'),(130,9,8,'ہیلو ہاؤ ار یو','2026-07-22 11:30:12','2026-07-22 11:30:12','2026-07-22 11:30:12'),(131,9,8,'ہاؤ ار یو','2026-07-22 11:30:20','2026-07-22 11:30:20','2026-07-22 11:30:20'),(132,9,8,'ہیلو','2026-07-22 11:30:27','2026-07-22 11:30:27','2026-07-22 11:30:27'),(133,9,4,'ہیلو ہے کیا','2026-07-22 11:31:16','2026-07-22 11:31:16','2026-07-22 11:31:16'),(134,9,4,'اس کو','2026-07-22 11:31:30','2026-07-22 11:31:30','2026-07-22 11:31:30'),(135,9,4,'کوئی یار','2026-07-22 11:31:49','2026-07-22 11:31:49','2026-07-22 11:31:49'),(136,9,4,'ہیلو تھوڑی دیر بعد','2026-07-22 11:35:45','2026-07-22 11:35:45','2026-07-22 11:35:45'),(137,9,4,'کرو','2026-07-22 11:35:51','2026-07-22 11:35:51','2026-07-22 11:35:51'),(138,9,4,'ہیلو','2026-07-22 11:35:53','2026-07-22 11:35:53','2026-07-22 11:35:53'),(139,9,4,'صحیح ہے بڑا سا اچھا','2026-07-22 11:36:12','2026-07-22 11:36:12','2026-07-22 11:36:12'),(140,9,4,'اچھا','2026-07-22 11:36:34','2026-07-22 11:36:34','2026-07-22 11:36:34'),(141,9,4,'ہیلو','2026-07-22 11:36:51','2026-07-22 11:36:51','2026-07-22 11:36:51'),(142,9,4,'ہیلو','2026-07-22 11:36:50','2026-07-22 11:36:50','2026-07-22 11:36:50'),(143,9,4,'ہیلو','2026-07-22 11:36:53','2026-07-22 11:36:53','2026-07-22 11:36:53'),(144,9,4,'ہیلو','2026-07-22 11:36:54','2026-07-22 11:36:54','2026-07-22 11:36:54'),(145,9,4,'ہیلو','2026-07-22 11:37:02','2026-07-22 11:37:02','2026-07-22 11:37:02'),(146,9,4,'ہیلو','2026-07-22 11:37:02','2026-07-22 11:37:02','2026-07-22 11:37:02'),(147,9,4,'hello hello','2026-07-22 11:44:10','2026-07-22 11:44:10','2026-07-22 11:44:10'),(148,9,4,'Abbu Abbu','2026-07-22 11:45:38','2026-07-22 11:45:38','2026-07-22 11:45:38'),(149,9,6,'hello','2026-07-22 11:48:27','2026-07-22 11:48:27','2026-07-22 11:48:27'),(150,9,4,'Tumko','2026-07-22 11:50:12','2026-07-22 11:50:12','2026-07-22 11:50:12'),(151,9,4,'hello','2026-07-22 11:50:45','2026-07-22 11:50:45','2026-07-22 11:50:45'),(152,10,4,'hello','2026-07-22 12:02:53','2026-07-22 12:02:53','2026-07-22 12:02:53'),(153,10,6,'hello','2026-07-22 12:02:53','2026-07-22 12:02:53','2026-07-22 12:02:53'),(154,10,7,'hello','2026-07-22 12:03:00','2026-07-22 12:03:00','2026-07-22 12:03:00'),(155,10,6,'hello','2026-07-22 12:03:01','2026-07-22 12:03:01','2026-07-22 12:03:01'),(156,10,7,'hello','2026-07-22 12:03:09','2026-07-22 12:03:09','2026-07-22 12:03:09'),(157,10,7,'Awaaz a rahi hai','2026-07-22 12:03:41','2026-07-22 12:03:41','2026-07-22 12:03:41'),(158,10,7,'hello','2026-07-22 12:04:09','2026-07-22 12:04:09','2026-07-22 12:04:09'),(159,10,7,'hello','2026-07-22 12:04:09','2026-07-22 12:04:09','2026-07-22 12:04:09'),(160,10,6,'hello hello','2026-07-22 12:17:43','2026-07-22 12:17:43','2026-07-22 12:17:43'),(161,10,4,'hello kaise ho','2026-07-22 12:21:59','2026-07-22 12:21:59','2026-07-22 12:21:59'),(162,10,4,'hello kaise ho kya tum theek ho','2026-07-22 12:22:07','2026-07-22 12:22:07','2026-07-22 12:22:07'),(163,10,4,'main bhi theek hoon','2026-07-22 12:22:11','2026-07-22 12:22:11','2026-07-22 12:22:11'),(164,10,4,'kya hello','2026-07-22 12:23:35','2026-07-22 12:23:35','2026-07-22 12:23:35'),(165,10,4,'hello','2026-07-22 12:23:38','2026-07-22 12:23:38','2026-07-22 12:23:38'),(166,10,4,'assignment','2026-07-22 12:24:35','2026-07-22 12:24:35','2026-07-22 12:24:35'),(167,10,4,'hello','2026-07-22 12:25:43','2026-07-22 12:25:43','2026-07-22 12:25:43'),(168,10,4,'hello','2026-07-22 12:25:53','2026-07-22 12:25:53','2026-07-22 12:25:53'),(169,10,7,'hello','2026-07-22 12:25:58','2026-07-22 12:25:58','2026-07-22 12:25:58'),(170,10,7,'hello','2026-07-22 12:39:34','2026-07-22 12:39:34','2026-07-22 12:39:34'),(171,10,6,'hello','2026-07-22 12:39:35','2026-07-22 12:39:35','2026-07-22 12:39:35'),(172,10,7,'hello','2026-07-22 12:39:38','2026-07-22 12:39:38','2026-07-22 12:39:38'),(173,10,6,'hello','2026-07-22 12:39:40','2026-07-22 12:39:40','2026-07-22 12:39:40'),(174,10,7,'Awaaz a rahi hai','2026-07-22 12:39:50','2026-07-22 12:39:50','2026-07-22 12:39:50'),(175,10,6,'Awaaz a rahi hai','2026-07-22 12:39:50','2026-07-22 12:39:50','2026-07-22 12:39:50'),(176,10,6,'Awaaz a rahi hai','2026-07-22 12:39:54','2026-07-22 12:39:54','2026-07-22 12:39:54'),(177,10,7,'hello','2026-07-22 12:46:46','2026-07-22 12:46:46','2026-07-22 12:46:46'),(178,10,7,'hello hello','2026-07-22 12:48:26','2026-07-22 12:48:26','2026-07-22 12:48:26'),(179,10,7,'hello','2026-07-22 13:00:40','2026-07-22 13:00:40','2026-07-22 13:00:40'),(180,11,3,'hello','2026-07-23 11:20:38','2026-07-23 11:20:38','2026-07-23 11:20:38'),(181,11,3,'hello','2026-07-23 11:21:18','2026-07-23 11:21:18','2026-07-23 11:21:18'),(182,11,3,'am I audible','2026-07-23 11:21:24','2026-07-23 11:21:24','2026-07-23 11:21:24'),(183,11,4,'hello','2026-07-23 11:22:19','2026-07-23 11:22:19','2026-07-23 11:22:19'),(184,11,4,'Awaaz a rahi hai','2026-07-23 11:22:21','2026-07-23 11:22:21','2026-07-23 11:22:21'),(185,11,4,'hello','2026-07-23 11:22:23','2026-07-23 11:22:23','2026-07-23 11:22:23'),(186,11,3,'hello hello hello','2026-07-23 11:23:37','2026-07-23 11:23:37','2026-07-23 11:23:37'),(187,11,4,'hello hello','2026-07-23 11:25:39','2026-07-23 11:25:39','2026-07-23 11:25:39'),(188,11,3,'hello hello','2026-07-23 11:26:27','2026-07-23 11:26:27','2026-07-23 11:26:27'),(189,11,3,'hello hello','2026-07-23 11:29:43','2026-07-23 11:29:43','2026-07-23 11:29:43'),(190,11,3,'hello','2026-07-23 11:47:32','2026-07-23 11:47:32','2026-07-23 11:47:32'),(191,11,3,'hello','2026-07-23 11:47:34','2026-07-23 11:47:34','2026-07-23 11:47:34'),(192,11,3,'hello','2026-07-23 11:48:15','2026-07-23 11:48:15','2026-07-23 11:48:15'),(193,11,6,'hello hello','2026-07-23 11:48:27','2026-07-23 11:48:27','2026-07-23 11:48:27'),(194,11,3,'hello hello hello hello','2026-07-23 11:48:27','2026-07-23 11:48:27','2026-07-23 11:48:27'),(195,11,3,'hello','2026-07-23 11:48:59','2026-07-23 11:48:59','2026-07-23 11:48:59'),(196,11,3,'hello','2026-07-23 12:03:41','2026-07-23 12:03:41','2026-07-23 12:03:41'),(197,11,6,'hello hello','2026-07-23 12:03:41','2026-07-23 12:03:41','2026-07-23 12:03:41'),(198,11,3,'hello','2026-07-23 12:03:43','2026-07-23 12:03:43','2026-07-23 12:03:43'),(199,11,6,'hello','2026-07-23 12:03:53','2026-07-23 12:03:53','2026-07-23 12:03:53'),(200,11,3,'hello','2026-07-23 12:06:05','2026-07-23 12:06:05','2026-07-23 12:06:05'),(201,11,3,'hello hello hello','2026-07-23 12:06:23','2026-07-23 12:06:23','2026-07-23 12:06:23'),(202,11,3,'hello','2026-07-23 12:16:57','2026-07-23 12:16:57','2026-07-23 12:16:57'),(203,12,7,'hello','2026-07-23 13:53:15','2026-07-23 13:53:15','2026-07-23 13:53:15'),(204,12,7,'Awaaz a rahi hai','2026-07-23 13:53:17','2026-07-23 13:53:17','2026-07-23 13:53:17'),(205,12,4,'hello hello','2026-07-23 13:53:29','2026-07-23 13:53:29','2026-07-23 13:53:29'),(206,12,7,'hello hello','2026-07-23 13:53:31','2026-07-23 13:53:31','2026-07-23 13:53:31'),(207,12,4,'hello','2026-07-23 13:55:20','2026-07-23 13:55:20','2026-07-23 13:55:20'),(208,12,7,'hello','2026-07-23 13:57:09','2026-07-23 13:57:09','2026-07-23 13:57:09'),(209,14,4,'hello hello','2026-08-09 11:46:07','2026-08-09 11:46:07','2026-08-09 11:46:07'),(210,15,3,'hello','2026-08-09 12:48:41','2026-08-09 12:48:41','2026-08-09 12:48:41'),(211,15,3,'hello','2026-08-09 12:48:44','2026-08-09 12:48:44','2026-08-09 12:48:44'),(212,15,3,'Awaaz a rahi hai','2026-08-09 12:48:49','2026-08-09 12:48:49','2026-08-09 12:48:49'),(213,15,3,'hello are you listening','2026-08-09 12:48:51','2026-08-09 12:48:51','2026-08-09 12:48:51'),(214,15,3,'very good','2026-08-09 12:48:57','2026-08-09 12:48:57','2026-08-09 12:48:57'),(215,16,3,'mobile','2026-08-19 00:24:22','2026-08-19 00:24:22','2026-08-19 00:24:22'),(216,16,3,'hello hello','2026-08-19 00:52:08','2026-08-19 00:52:08','2026-08-19 00:52:08'),(217,16,4,'hello','2026-08-19 00:52:15','2026-08-19 00:52:15','2026-08-19 00:52:15'),(218,16,4,'hello','2026-08-19 00:52:22','2026-08-19 00:52:22','2026-08-19 00:52:22'),(219,16,4,'hello hello','2026-08-19 01:44:34','2026-08-19 01:44:34','2026-08-19 01:44:34'),(220,16,4,'hello','2026-08-19 01:44:45','2026-08-19 01:44:45','2026-08-19 01:44:45'),(221,16,3,'hello','2026-08-19 01:45:01','2026-08-19 01:45:01','2026-08-19 01:45:01'),(222,16,4,'hello hello','2026-08-19 01:45:47','2026-08-19 01:45:47','2026-08-19 01:45:47'),(223,17,3,'hello','2026-08-27 10:39:38','2026-08-27 10:39:38','2026-08-27 10:39:38'),(224,17,3,'hello','2026-08-27 10:39:40','2026-08-27 10:39:40','2026-08-27 10:39:40'),(225,17,3,'hello','2026-08-27 10:51:57','2026-08-27 10:51:57','2026-08-27 10:51:57'),(226,17,3,'hello','2026-08-27 10:52:01','2026-08-27 10:52:01','2026-08-27 10:52:01'),(227,17,3,'hello','2026-08-27 10:54:27','2026-08-27 10:54:27','2026-08-27 10:54:27'),(228,17,3,'hello','2026-08-27 10:54:31','2026-08-27 10:54:31','2026-08-27 10:54:31'),(229,17,3,'hello','2026-08-27 11:54:09','2026-08-27 11:54:09','2026-08-27 11:54:09'),(230,17,3,'hello','2026-08-27 11:54:09','2026-08-27 11:54:09','2026-08-27 11:54:09'),(231,17,3,'hello hello','2026-08-27 11:55:32','2026-08-27 11:55:32','2026-08-27 11:55:32'),(232,17,3,'hello hello','2026-08-27 12:13:06','2026-08-27 12:13:06','2026-08-27 12:13:06');
/*!40000 ALTER TABLE `meeting_transcripts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `meetings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `unique_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agenda` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Karachi',
  `duration` int NOT NULL DEFAULT '60',
  `starts_at` timestamp NULL DEFAULT NULL,
  `actual_start` timestamp NULL DEFAULT NULL,
  `organizer_joined_at` timestamp NULL DEFAULT NULL,
  `organizer_left_at` timestamp NULL DEFAULT NULL,
  `status` enum('upcoming','active','completed','cancelled','flagged','live') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `organizer_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meetings_unique_code_unique` (`unique_code`),
  KEY `meetings_organizer_id_foreign` (`organizer_id`),
  CONSTRAINT `meetings_organizer_id_foreign` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
INSERT INTO `meetings` VALUES (3,'YLNjQ7ydgL','2026-07-18 10:15:53','2026-07-18 12:15:00','SmartMeet Development Review','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','The development team will review the current progress of the SmartMeet application, discuss completed modules such as meeting management, video calling, and role management, identify pending tasks, and finalize priorities for the next sprint.','2026-07-18','20:15:00','Asia/Karachi',120,NULL,'2026-07-18 10:15:00','2026-07-18 11:18:38','2026-07-18 11:26:42','completed',3),(4,'22QQuUfoPC','2026-07-18 14:24:42','2026-07-19 09:08:59','Application Security Review','\"[{\\\"title\\\":\\\"Application Security Review\\\",\\\"description\\\":\\\"\\\"},{\\\"title\\\":\\\"Bug review\\\",\\\"description\\\":\\\"\\\"},{\\\"title\\\":\\\"Verification results\\\",\\\"description\\\":\\\"\\\"}]\"','This meeting focuses on reviewing application security, discussing identified vulnerabilities, planning security improvements, and ensuring compliance with security standards.','2026-07-19','00:23:00','Asia/Karachi',120,NULL,'2026-07-18 14:23:00','2026-07-18 14:27:00','2026-07-18 14:33:02','completed',3),(5,'xgwmrAoXps','2026-07-19 10:35:34','2026-07-20 03:37:02','Product Development Planning','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','This meeting focuses on discussing new product features, reviewing development progress, identifying technical challenges, and planning the implementation timeline for the upcoming release.','2026-07-19','20:35:00','Asia/Karachi',120,NULL,'2026-07-19 10:35:00','2026-07-19 12:04:14','2026-07-19 12:04:14','completed',3),(6,'ZfEPxIh3JA','2026-07-21 07:36:38','2026-07-21 09:50:02','UI/UX Design Review','\"[{\\\"title\\\":\\\"Present updated designs\\\",\\\"description\\\":\\\"\\\"},{\\\"title\\\":\\\"Review feedback\\\",\\\"description\\\":\\\"\\\"}]\"','Review the latest user interface designs, evaluate user experience improvements, discuss feedback from stakeholders, and finalize design changes before implementation.','2026-07-21','17:35:00','Asia/Karachi',120,NULL,'2026-07-21 07:35:00','2026-07-21 09:50:02',NULL,'completed',8),(7,'9RcO2tf7YB','2026-07-21 12:31:50','2026-07-21 14:31:01','Project Testing','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Welcome & opening remarks,Review of previous quarter goals','2026-07-21','22:31:00','Asia/Karachi',120,NULL,'2026-07-21 12:31:00','2026-07-21 14:12:00','2026-07-21 14:31:01','completed',8),(8,'UJX2FamaEf','2026-07-22 05:28:26','2026-07-22 07:20:00','Client Requirement Discussion','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Meet with the client to understand business requirements, gather feedback, clarify project expectations, and discuss the next development phase.','2026-07-22','15:20:00','Asia/Karachi',120,NULL,'2026-07-22 05:20:00','2026-07-22 06:57:47','2026-07-22 06:57:51','completed',7),(9,'4ksMGaJHT5','2026-07-22 10:55:30','2026-07-22 11:55:01','Testing','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Welcome & opening remarks,Review of previous quarter goals','2026-07-22','20:55:00','Asia/Karachi',60,NULL,'2026-07-22 10:55:00','2026-07-22 11:54:19',NULL,'completed',8),(10,'mn8MTSwSlo','2026-07-22 11:57:25','2026-07-22 13:00:52','SmartMeet Development Review','\"[{\\\"title\\\":\\\"Review completed features\\\",\\\"description\\\":\\\"\\\"},{\\\"title\\\":\\\"Discuss pending modules\\\",\\\"description\\\":\\\"\\\"}]\"','The development team will review the current progress of the SmartMeet application, discuss completed modules such as meeting management, video calling, and role management, identify pending tasks, and finalize priorities for the next sprint.','2026-07-22','21:56:00','Asia/Karachi',120,NULL,'2026-07-22 11:56:00','2026-07-22 13:00:19','2026-07-22 13:00:52','cancelled',7),(11,'QMmiM4rDq1','2026-07-23 11:14:54','2026-07-23 13:03:32','Team Collaboration Session','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"},{\\\"title\\\":\\\"Team updates\\\",\\\"description\\\":\\\"\\\"}]\"','This meeting is organized to improve team collaboration by discussing ongoing projects, resolving communication gaps, and ensuring all team members are aligned with project objectives.','2026-07-23','21:18:00','Asia/Karachi',120,NULL,'2026-07-23 11:18:00','2026-07-23 12:03:09','2026-07-23 13:03:32','cancelled',3),(12,'i3RZqSNDgs','2026-07-23 13:41:40','2026-07-24 03:57:01','Stakeholder Review Meeting','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','The project team presents the current project status, demonstrates completed features, gathers stakeholder feedback, and discusses future objectives.','2026-07-23','23:43:00','Asia/Karachi',120,NULL,'2026-07-23 13:43:00','2026-07-23 13:52:47','2026-07-23 13:57:28','completed',7),(13,'TynrL3dMtT','2026-07-23 13:59:02','2026-07-24 03:58:00','Cross-Department Coordination Meeting','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Representatives from different departments will discuss ongoing activities, improve communication, resolve interdepartmental issues, and coordinate future initiatives.','2026-07-24','12:00:00','Asia/Karachi',90,NULL,NULL,NULL,NULL,'completed',7),(14,'sH6hOZaTv0','2026-08-09 09:49:16','2026-08-09 11:50:00','Project Planning Meeting','\"[{\\\"title\\\":\\\"Project Goals & Planning\\\",\\\"description\\\":\\\"\\\"}]\"','Discuss project goals, requirements, tasks, and development timeline.','2026-08-09','19:50:00','Asia/Karachi',120,NULL,'2026-08-09 09:50:00','2026-08-09 11:49:09','2026-08-09 11:49:59','completed',3),(15,'8Uuu29anSq','2026-08-09 12:23:47','2026-08-09 12:51:02','Project Review Meeting','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Discuss database structure, tables, relationships, and data requirements.','2026-08-09','22:28:00','Asia/Karachi',120,NULL,'2026-08-09 12:28:00','2026-08-09 12:50:58','2026-08-09 12:51:02','cancelled',3),(16,'1RkWWYcGrR','2026-08-19 00:17:48','2026-08-19 02:18:01','Software Testing','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Welcome & opening remarks,Review of previous quarter goals','2026-08-19','10:18:00','Asia/Karachi',120,NULL,'2026-08-19 00:18:00','2026-08-19 01:51:03','2026-08-19 01:52:30','completed',3),(17,'MB9DLlnXCY','2026-08-27 10:21:49','2026-08-27 12:25:00','Testing','\"[{\\\"title\\\":\\\"Welcome & opening remarks,Review of previous quarter goals\\\",\\\"description\\\":\\\"\\\"}]\"','Welcome & opening remarks,Review of previous quarter goals','2026-08-27','20:25:00','Asia/Karachi',120,NULL,'2026-08-27 10:25:00','2026-08-27 11:53:38','2026-08-27 12:14:12','completed',3);
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000002_create_jobs_table',1),(3,'2026_05_10_053333_create_cache_table',1),(4,'2026_05_12_072203_create_meetings_table',1),(5,'2026_05_12_095638_create_meeting_participants_table',1),(6,'2026_05_15_133400_create_meeting_transcripts_table',1),(7,'2026_06_29_051759_add_joined_at_to_meeting_participants_table',1),(8,'2026_07_01_172456_create_dismissed_activities_table',1),(9,'2026_07_06_093928_add_unique_code_to_meetings_table',1),(10,'2026_07_07_064812_add_image_to_users_table',1),(11,'2026_07_07_112036_create_meeting_invites_table',1),(12,'2026_07_08_164745_add_status_to_users_table',1),(13,'2026_07_09_172701_create_notifications_table',1),(14,'2026_07_10_071536_create_role_requests_table',1),(15,'2026_07_10_074948_add_settings_fields_to_users_table',1),(16,'2026_07_11_100950_add_participant_settings_fields_to_users_table',1),(17,'2026_07_17_092129_add_organizer_join_tracking_to_meetings_table',1),(18,'2026_07_22_155818_add_organizer_presence_to_meetings_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `meeting_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  KEY `notifications_meeting_id_foreign` (`meeting_id`),
  CONSTRAINT `notifications_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,NULL,'Account Deactivated','Your account has been deactivated by an administrator.',NULL,'2026-07-18 10:25:27','2026-07-18 10:22:16','2026-07-18 10:25:27'),(2,3,NULL,'Account Activated','Your account has been activated by an administrator. You now have full access again.',NULL,'2026-07-18 10:25:27','2026-07-18 10:22:37','2026-07-18 10:25:27'),(3,3,NULL,'Your Role Has Been Updated','Your role has been changed from Organizer to Admin.',NULL,'2026-07-18 10:25:27','2026-07-18 10:23:02','2026-07-18 10:25:27'),(4,3,NULL,'Your Role Has Been Updated','Your role has been changed from Admin to Organizer.',NULL,'2026-07-18 10:25:27','2026-07-18 10:23:25','2026-07-18 10:25:27'),(5,6,NULL,'Account Deactivated','Your account has been deactivated by an administrator.',NULL,'2026-07-19 10:36:23','2026-07-18 10:23:44','2026-07-19 10:36:23'),(6,6,NULL,'Account Activated','Your account has been activated by an administrator. You now have full access again.',NULL,'2026-07-19 10:36:23','2026-07-18 10:23:49','2026-07-19 10:36:23'),(8,1,NULL,'New Role Change Request','Ali has requested to become a Participant','http://127.0.0.1:8000/admin/role-requests','2026-07-18 10:52:31','2026-07-18 10:51:36','2026-07-18 10:52:31'),(9,1,NULL,'New Role Change Request','Dua has requested to become an Organizer','http://localhost:8000/admin/role-requests','2026-07-18 10:54:28','2026-07-18 10:53:56','2026-07-18 10:54:28'),(10,7,NULL,'Your Role Has Been Updated','Your role has been changed from Participant to Organizer.',NULL,'2026-07-18 11:02:57','2026-07-18 11:02:44','2026-07-18 11:02:57'),(11,1,NULL,'New Role Change Request','Umar Ashraf has requested to become a Participant','http://localhost:8000/admin/role-requests','2026-07-18 11:10:35','2026-07-18 11:07:11','2026-07-18 11:10:35'),(12,1,NULL,'New Role Change Request','Amna has requested to become an Organizer','http://localhost:8000/admin/role-requests','2026-07-21 04:10:26','2026-07-18 11:07:40','2026-07-21 04:10:26'),(13,3,NULL,'Role Change Rejected','Your request to become an Organizer has been rejected.',NULL,'2026-07-18 11:10:57','2026-07-18 11:10:43','2026-07-18 11:10:57'),(14,4,NULL,'Role Change Rejected','Your request to become an Organizer has been rejected.',NULL,'2026-07-18 14:33:22','2026-07-18 11:10:45','2026-07-18 14:33:22'),(15,7,NULL,'Role Change Rejected','Your request to become an Organizer has been rejected.',NULL,'2026-07-18 11:18:46','2026-07-18 11:10:46','2026-07-18 11:18:46'),(16,5,NULL,'Role Change Rejected','Your request to become an Organizer has been rejected.',NULL,'2026-07-18 11:26:53','2026-07-18 11:10:48','2026-07-18 11:26:53'),(17,7,NULL,'Your Role Has Been Updated','Your role has been changed from Organizer to Participant.',NULL,'2026-07-18 11:33:34','2026-07-18 11:32:50','2026-07-18 11:33:34'),(18,7,NULL,'Your Role Has Been Updated','Your role has been changed from Participant to Organizer.',NULL,'2026-07-22 05:18:16','2026-07-18 11:34:57','2026-07-22 05:18:16'),(19,10,NULL,'Account Deactivated','Your account has been deactivated by an administrator.',NULL,NULL,'2026-08-09 09:43:02','2026-08-09 09:43:02'),(20,10,NULL,'Account Activated','Your account has been activated by an administrator. You now have full access again.',NULL,NULL,'2026-08-09 09:43:04','2026-08-09 09:43:04');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
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
INSERT INTO `password_reset_tokens` VALUES ('asad@gmail.com','$2y$12$Tp7ALWzehy3lGBddfYaj9.ZvCXXrdgjHp4OhHGtfFevv.amvS2On2','2026-07-18 08:40:16');
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_requests`
--

DROP TABLE IF EXISTS `role_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_requests_user_id_foreign` (`user_id`),
  CONSTRAINT `role_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_requests`
--

LOCK TABLES `role_requests` WRITE;
/*!40000 ALTER TABLE `role_requests` DISABLE KEYS */;
INSERT INTO `role_requests` VALUES (1,3,'Request for participant request..','I want to attend the meeting.','participant','rejected',NULL,'2026-07-18 10:51:36','2026-07-18 11:10:43'),(2,4,'Request for organizer access','I want to create a meeting..','organizer','rejected',NULL,'2026-07-18 10:53:56','2026-07-18 11:10:45'),(3,7,'Request for participant request..','hburhdfir','participant','rejected',NULL,'2026-07-18 11:07:11','2026-07-18 11:10:46'),(4,5,'Request for organizer access','dv','organizer','rejected',NULL,'2026-07-18 11:07:40','2026-07-18 11:10:48');
/*!40000 ALTER TABLE `role_requests` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('3fVU9DXJf1u67Oq6jdSMQ53NHq9Yq5QiEcEiwR27',NULL,'172.18.144.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJZbGc1ck83b0NqVU9PMmNBcW1QbXVNenhkdU9DWkVITW1SZnNtTHYyIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1787855777),('55hOw3xDxIuMzIAsilKY48Na5F0InMLcZOdNuOrJ',4,'172.18.144.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJ5SVZFVDZxdDRuMVlVWHh4UTBjY2hMR0twNmYwejJ1dG1iZU5hdTI1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9wYXJ0aWNpcGFudFwvbWVldGluZ3MiLCJyb3V0ZSI6InBhcnRpY2lwYW50Lm1lZXRpbmdzLmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjR9',1787857041),('wmzHcaVaY5oCjMRYmdj3HXJqW4pLzHAuY2sgavL1',NULL,'172.18.144.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJwUHdoOGk5NFByZWhKRHZzZ21kVDR2MUhBUUczNWJ2UnEzMGEzWXdRIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1787907243);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notif_meeting_reminders` tinyint(1) NOT NULL DEFAULT '1',
  `notif_email` tinyint(1) NOT NULL DEFAULT '1',
  `notif_sound` tinyint(1) NOT NULL DEFAULT '0',
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'participant',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_alerts` tinyint(1) NOT NULL DEFAULT '1',
  `reminders_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `system_alerts` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Asad','asad@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$lgFPDs7qH4QEdbqyu3XKJe/.B2oAvnP5uatCp5SaeGxU5TE3sKtnC',NULL,NULL,'admin','avatars/5ZU8nc4k6Ab1OlR7O3CnqX3XdTmWMQLUUYJkSPz8.avif',1,1,1,1,NULL,'2026-07-17 10:55:27','2026-07-21 04:00:46','active'),(2,'SANA','bc220403526san@vu.edu.pk',NULL,NULL,NULL,1,1,0,NULL,'https://lh3.googleusercontent.com/a/ACg8ocKTsedw6NAuXxWp_iRabg1Bpe5Pxa4i0bYUbxZjzH5p3aJZyQ=s96-c','2026-07-23 13:14:44',NULL,'google','104998533573908951318','participant','avatars/fdYRuRSN7UVIGAzltZVlWSpDqtybcRN99X6T8cxL.avif',1,1,0,1,NULL,'2026-07-18 08:41:12','2026-07-23 13:15:09','active'),(3,'Ali','ali@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$OaY2wY8R0ZcpnUIibFDtGOAuIZRYGiIA/.6hVaNz25VyJo4gKHg/C',NULL,NULL,'organizer','avatars/ImXCfC1fveh5zZa4g9L0P6hlo7jHBH426ECpD3Nv.avif',1,1,1,1,NULL,'2026-07-18 08:45:43','2026-08-19 01:52:59','active'),(4,'Dua','dua@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$pzRKEQCRbK4s3wJqWLvMZOyyK1Okk7L5fBKQetWOpglwgrT274kZq',NULL,NULL,'participant','avatars/PmbusX35JfcEm1U8Cw0Cp9FVKDa4hbdMYAtlv0F1.avif',1,1,0,1,NULL,'2026-07-18 09:09:14','2026-08-19 01:52:23','active'),(5,'Amna','amna@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$hT8gO.8eWtvEy8jc5v4RP.gh9SJuhS9ZK9fj814x9Si8ngeQ.bMJ.',NULL,NULL,'participant','avatars/CgRF2UmLACEuYXu5PzanbK40zwFDr8TxVnpY9Idy.avif',1,1,0,1,NULL,'2026-07-18 09:10:15','2026-07-18 11:29:49','active'),(6,'Bilal','bilal@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$kimST7UPcOjCeZVqV0JNOehKCjbpoz42FTihe5J8Bvw7w4nOHePZy',NULL,NULL,'participant',NULL,1,1,0,1,NULL,'2026-07-18 09:11:46','2026-07-18 10:23:49','active'),(7,'Umar Ashraf','umar@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$8LUE8W2uMunrOf2pr/0hc.Oti84frlucD9qnm5KoJRyUtiGvXntH2',NULL,NULL,'organizer',NULL,1,1,0,1,NULL,'2026-07-18 11:02:00','2026-07-18 11:34:57','active'),(8,'Ayesha','ayesha@gmail.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$FQ/ApJChbuGnCuQUG0vrkOqNgwQgt1mgiMbIojuhOD2Rxsp89Ggl.',NULL,NULL,'organizer','avatars/NonMpIoXa5WjhPNQ7h2hae4s1bxnd9jJbXc2QB6n.avif',1,1,0,1,NULL,'2026-07-18 14:03:36','2026-07-21 10:55:22','active'),(9,'Yardley Townsend','lebajyjy@mailinator.com',NULL,NULL,NULL,1,1,0,NULL,NULL,NULL,'$2y$12$wFWPnAzzYxGp2iTqJLkwU.qXgjWgOugFTpCXDaWF5MZ..YxhJlTKu',NULL,NULL,'participant',NULL,1,1,0,1,NULL,'2026-07-18 14:05:51','2026-07-18 14:05:51','active'),(10,'Kashaf Arshad','kashafarshad775@gmail.com',NULL,NULL,NULL,1,1,0,NULL,'https://lh3.googleusercontent.com/a/ACg8ocJizOyr8jc15j7x9vVUaAolFali5ZrlI-OpzoG-dZx_wCviCA=s96-c','2026-07-23 14:00:44',NULL,'google','105591952682794393586','participant',NULL,1,1,0,1,NULL,'2026-07-23 14:00:44','2026-08-09 09:43:04','active');
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

-- Dump completed on 2026-08-29 10:24:43

CREATE DATABASE  IF NOT EXISTS `sipeta` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `sipeta`;
-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sipeta
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.27-MariaDB

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
-- Table structure for table `pemilik_alat`
--

DROP TABLE IF EXISTS `pemilik_alat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemilik_alat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pemilik` varchar(255) NOT NULL,
  `jenis_pemilik` enum('Perusahaan','Perorangan') DEFAULT 'Perusahaan',
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nama_pemilik` (`nama_pemilik`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemilik_alat`
--

LOCK TABLES `pemilik_alat` WRITE;
/*!40000 ALTER TABLE `pemilik_alat` DISABLE KEYS */;
/*!40000 ALTER TABLE `pemilik_alat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `penyedia`
--

DROP TABLE IF EXISTS `penyedia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyedia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `penyedia_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `penyedia`
--

LOCK TABLES `penyedia` WRITE;
/*!40000 ALTER TABLE `penyedia` DISABLE KEYS */;
INSERT INTO `penyedia` VALUES (17,NULL,'CV. Maju Mundur Cantik',NULL,NULL,NULL,'pokja_sipeta','2026-01-29 14:43:02');
/*!40000 ALTER TABLE `penyedia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peralatan`
--

DROP TABLE IF EXISTS `peralatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peralatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penyedia_id` int(11) NOT NULL,
  `nama_alat` varchar(255) NOT NULL,
  `merk` varchar(100) DEFAULT NULL,
  `tipe` varchar(100) DEFAULT NULL,
  `kapasitas` varchar(100) DEFAULT NULL,
  `plat_serial` varchar(100) DEFAULT NULL,
  `bukti_kepemilikan` varchar(255) DEFAULT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `file_dokumentasi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  `pemilik_alat_id` int(11) DEFAULT NULL,
  `jenis_alat` varchar(255) DEFAULT NULL COMMENT 'Contoh: Batching Plant, Truck Mixer, Excavator',
  `tahun_pembuatan` int(4) DEFAULT NULL,
  `status_kepemilikan` enum('Milik Sendiri','Sewa','Kerjasama') DEFAULT 'Milik Sendiri',
  `nama_pemilik_alat` varchar(255) DEFAULT NULL COMMENT 'Nama pemilik jika berbeda dengan penyedia',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `plat_serial` (`plat_serial`),
  KEY `penyedia_id` (`penyedia_id`),
  KEY `fk_peralatan_pemilik` (`pemilik_alat_id`),
  KEY `idx_jenis_alat` (`jenis_alat`),
  KEY `idx_status_kepemilikan` (`status_kepemilikan`),
  CONSTRAINT `fk_peralatan_pemilik` FOREIGN KEY (`pemilik_alat_id`) REFERENCES `pemilik_alat` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peralatan_ibfk_1` FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peralatan`
--

LOCK TABLES `peralatan` WRITE;
/*!40000 ALTER TABLE `peralatan` DISABLE KEYS */;
INSERT INTO `peralatan` VALUES (45,17,'excavator','komatsu','a','100','333','nota',NULL,NULL,'2026-01-29 14:43:02','pokja_sipeta',NULL,'excavator',NULL,'','aco','2026-01-29 14:43:02'),(46,17,'dozer','komatsu','a','100','1277','nota',NULL,NULL,'2026-01-30 00:28:09','pokja_sipeta',NULL,'dozer',NULL,'','baco kepang','2026-01-30 00:28:09');
/*!40000 ALTER TABLE `peralatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personel_k3`
--

DROP TABLE IF EXISTS `personel_k3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personel_k3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penyedia_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `jabatan_k3` varchar(100) DEFAULT NULL COMMENT 'Contoh: Ahli K3 Konstruksi, Petugas K3',
  `jenis_sertifikat_k3` varchar(100) DEFAULT NULL COMMENT 'Contoh: Sertifikat Ahli K3 Konstruksi',
  `nomor_sertifikat_k3` varchar(100) DEFAULT NULL,
  `masa_berlaku_sertifikat` date DEFAULT NULL,
  `file_sertifikat_k3` varchar(255) DEFAULT NULL,
  `file_ktp` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nik` (`nik`),
  KEY `nomor_sertifikat_k3` (`nomor_sertifikat_k3`),
  KEY `penyedia_id` (`penyedia_id`),
  CONSTRAINT `personel_k3_ibfk_1` FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personel_k3`
--

LOCK TABLES `personel_k3` WRITE;
/*!40000 ALTER TABLE `personel_k3` DISABLE KEYS */;
INSERT INTO `personel_k3` VALUES (3,17,'Alessandro Delpiero','5555','Ahli K3 Konstruksi','Sertifikat KOmpetensi Ahli Madya K3 Konstruksi','666','0000-00-00',NULL,NULL,'pokja_sipeta','2026-01-29 14:43:02','2026-01-29 14:43:02'),(4,17,'Alessandro Delp','647106','Ahli K3 Konstruksi','Sertifikat KOmpetensi Ahli Madya K3 Konstruksi','74322','2026-02-28',NULL,NULL,'pokja_sipeta','2026-01-30 00:28:09','2026-01-30 00:28:09');
/*!40000 ALTER TABLE `personel_k3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personel_lapangan`
--

DROP TABLE IF EXISTS `personel_lapangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personel_lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penyedia_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `jenis_skk` varchar(100) DEFAULT NULL,
  `nomor_skk` varchar(100) DEFAULT NULL,
  `masa_berlaku_skk_sertifikat` int(11) DEFAULT NULL,
  `file_skk` varchar(255) DEFAULT NULL,
  `file_surat_pernyataan` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `masa_berlaku_skk` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nik` (`nik`),
  KEY `nomor_skk` (`nomor_skk`),
  KEY `penyedia_id` (`penyedia_id`),
  CONSTRAINT `personel_lapangan_ibfk_1` FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personel_lapangan`
--

LOCK TABLES `personel_lapangan` WRITE;
/*!40000 ALTER TABLE `personel_lapangan` DISABLE KEYS */;
INSERT INTO `personel_lapangan` VALUES (5,17,'Dian Aries Sandra','2222','Manajer Proyek/Pelaksana Lapangan','Sertifikat Kompetensi Pelaksana Lapangan Pekerjaan Gedung Kantor','333',NULL,NULL,NULL,'pokja_sipeta','2026-01-29 14:43:02','2026-01-29 14:43:02','0000-00-00'),(6,17,'Dian Ar','2223','Manajer Proyek/Pelaksana Lapangan','Sertifikat Kompetensi Pelaksana Lapangan Pekerjaan Gedung Kantor','3333',NULL,NULL,NULL,'pokja_sipeta','2026-01-30 00:28:09','2026-01-30 00:28:09','0000-00-00');
/*!40000 ALTER TABLE `personel_lapangan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regulasi`
--

DROP TABLE IF EXISTS `regulasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regulasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instansi` varchar(255) DEFAULT NULL,
  `jenis_regulasi` enum('Peraturan','Undang-Undang','Peraturan Menteri','Peraturan Daerah','Keputusan','Instruksi','Surat Edaran','Lainnya') NOT NULL,
  `nomor_regulasi` varchar(100) NOT NULL,
  `tahun` int(4) NOT NULL,
  `judul` text NOT NULL,
  `tentang` text DEFAULT NULL,
  `tanggal_ditetapkan` date DEFAULT NULL,
  `tanggal_diundangkan` date DEFAULT NULL,
  `status` enum('Berlaku','Dicabut','Direvisi') DEFAULT 'Berlaku',
  `file_regulasi` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `jenis_regulasi` (`jenis_regulasi`),
  KEY `tahun` (`tahun`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regulasi`
--

LOCK TABLES `regulasi` WRITE;
/*!40000 ALTER TABLE `regulasi` DISABLE KEYS */;
/*!40000 ALTER TABLE `regulasi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tender`
--

DROP TABLE IF EXISTS `tender`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `penyedia_id` int(11) NOT NULL,
  `kode_tender` varchar(50) NOT NULL,
  `satuan_kerja` varchar(255) NOT NULL,
  `tanggal_input` timestamp NOT NULL DEFAULT current_timestamp(),
  `tahun_anggaran` int(4) NOT NULL DEFAULT 2026,
  `created_by` varchar(50) DEFAULT NULL,
  `nama_pokmil` varchar(100) DEFAULT NULL,
  `judul_paket` text DEFAULT NULL,
  `tanggal_bahp` date DEFAULT NULL,
  `hps` decimal(20,2) DEFAULT NULL,
  `pemenang_tender` varchar(255) DEFAULT NULL,
  `segmentasi` enum('Kecil','Non Kecil') DEFAULT 'Non Kecil',
  `manajer_proyek` varchar(255) DEFAULT NULL,
  `nik_manajer_proyek` varchar(20) DEFAULT NULL,
  `manajer_teknik` varchar(255) DEFAULT NULL,
  `nik_manajer_teknik` varchar(20) DEFAULT NULL,
  `manajer_keuangan` varchar(255) DEFAULT NULL,
  `nik_manajer_keuangan` varchar(20) DEFAULT NULL,
  `ahli_k3` varchar(255) DEFAULT NULL,
  `nik_ahli_k3` varchar(20) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kode_tender` (`kode_tender`),
  KEY `penyedia_id` (`penyedia_id`),
  CONSTRAINT `tender_ibfk_1` FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tender`
--

LOCK TABLES `tender` WRITE;
/*!40000 ALTER TABLE `tender` DISABLE KEYS */;
INSERT INTO `tender` VALUES (15,17,'10075788000','pupr','2026-01-29 07:43:02',2026,'pokja_sipeta','pokmil 1','y','2026-01-31',1500000000.00,'CV. Maju Mundur Cantik','Kecil','Dian Aries Sandra','2222','','','','','Alessandro Delpiero','5555','2026-01-29 14:43:02'),(16,17,'10075788001','pupr','2026-01-29 17:28:09',2026,'pokja_sipeta','Pokmil 28','jembatan','0000-00-00',1500000000.00,'CV. Maju Mundur Cantik','Kecil','Dian Ar','2223','','','','','Alessandro Delp','647106','2026-01-30 00:28:09');
/*!40000 ALTER TABLE `tender` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tender_peralatan`
--

DROP TABLE IF EXISTS `tender_peralatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tender_peralatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tender_id` int(11) NOT NULL,
  `peralatan_id` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT 1,
  `bukti_kepemilikan_alat` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tender_id` (`tender_id`),
  KEY `peralatan_id` (`peralatan_id`),
  CONSTRAINT `tender_peralatan_ibfk_1` FOREIGN KEY (`tender_id`) REFERENCES `tender` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tender_peralatan_ibfk_2` FOREIGN KEY (`peralatan_id`) REFERENCES `peralatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tender_peralatan`
--

LOCK TABLES `tender_peralatan` WRITE;
/*!40000 ALTER TABLE `tender_peralatan` DISABLE KEYS */;
INSERT INTO `tender_peralatan` VALUES (9,15,45,1,NULL,NULL),(10,16,46,1,NULL,NULL);
/*!40000 ALTER TABLE `tender_peralatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tender_personel_k3`
--

DROP TABLE IF EXISTS `tender_personel_k3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tender_personel_k3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tender_id` int(11) NOT NULL,
  `personel_k3_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tender_k3` (`tender_id`,`personel_k3_id`),
  KEY `personel_k3_id` (`personel_k3_id`),
  CONSTRAINT `tender_personel_k3_ibfk_1` FOREIGN KEY (`tender_id`) REFERENCES `tender` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tender_personel_k3_ibfk_2` FOREIGN KEY (`personel_k3_id`) REFERENCES `personel_k3` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tender_personel_k3`
--

LOCK TABLES `tender_personel_k3` WRITE;
/*!40000 ALTER TABLE `tender_personel_k3` DISABLE KEYS */;
INSERT INTO `tender_personel_k3` VALUES (9,15,3),(10,16,4);
/*!40000 ALTER TABLE `tender_personel_k3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tender_personel_lapangan`
--

DROP TABLE IF EXISTS `tender_personel_lapangan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tender_personel_lapangan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tender_id` int(11) NOT NULL,
  `personel_lapangan_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tender_personel` (`tender_id`,`personel_lapangan_id`),
  KEY `personel_lapangan_id` (`personel_lapangan_id`),
  CONSTRAINT `tender_personel_lapangan_ibfk_1` FOREIGN KEY (`tender_id`) REFERENCES `tender` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tender_personel_lapangan_ibfk_2` FOREIGN KEY (`personel_lapangan_id`) REFERENCES `personel_lapangan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tender_personel_lapangan`
--

LOCK TABLES `tender_personel_lapangan` WRITE;
/*!40000 ALTER TABLE `tender_personel_lapangan` DISABLE KEYS */;
INSERT INTO `tender_personel_lapangan` VALUES (23,15,5),(24,16,6);
/*!40000 ALTER TABLE `tender_personel_lapangan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','penyedia','pokja','sekretariat') NOT NULL,
  `status_aktif` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (15,'dummy','','admin',0,'2026-01-29 09:34:45'),(16,'admin_sipeta','$2y$10$MLN/fnt65E10PkM1F.UUheT2cu.FHSSfZbg9QGOjdqxIfE6MT5iDu','admin',1,'2026-01-29 09:45:49'),(17,'sekretariat_sipeta','$2y$10$KmF66uxRAkLsgiQVESd.z.LoZOsZtg1BVERf73jmiYPOakwvUs84.','sekretariat',1,'2026-01-29 09:45:49'),(18,'pokja_sipeta','$2y$10$UmHEn0ikHD.8NEWussvAt.zW.Klc9hfLogXtTOz2gE4cOsxATpmYC','pokja',1,'2026-01-29 09:45:49');
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

-- Dump completed on 2026-01-30  8:54:40

/*
SQLyog Ultimate v12.4.3 (64 bit)
MySQL - 8.0.30 : Database - kkn
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`kkn` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `kkn`;

/*Table structure for table `bidang_proker` */

DROP TABLE IF EXISTS `bidang_proker`;

CREATE TABLE `bidang_proker` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `syarat_jkem` int NOT NULL,
  `tipe` enum('unit','individu') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bidang_proker` */

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `dana_kegiatan` */

DROP TABLE IF EXISTS `dana_kegiatan`;

CREATE TABLE `dana_kegiatan` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_logbook_kegiatan` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` int NOT NULL,
  `sumber` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dana_kegiatan_id_logbook_kegiatan_foreign` (`id_logbook_kegiatan`),
  CONSTRAINT `dana_kegiatan_id_logbook_kegiatan_foreign` FOREIGN KEY (`id_logbook_kegiatan`) REFERENCES `logbook_kegiatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dana_kegiatan` */

/*Table structure for table `dpl` */

DROP TABLE IF EXISTS `dpl`;

CREATE TABLE `dpl` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user_role` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dpl_id_user_role_foreign` (`id_user_role`),
  KEY `dpl_id_kkn_foreign` (`id_kkn`),
  CONSTRAINT `dpl_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`),
  CONSTRAINT `dpl_id_user_role_foreign` FOREIGN KEY (`id_user_role`) REFERENCES `user_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `dpl` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

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

/*Data for the table `failed_jobs` */

/*Table structure for table `informasi` */

DROP TABLE IF EXISTS `informasi`;

CREATE TABLE `informasi` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `informasi_author_foreign` (`author`),
  CONSTRAINT `informasi_author_foreign` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `informasi` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

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

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

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

/*Data for the table `jobs` */

/*Table structure for table `kabupaten` */

DROP TABLE IF EXISTS `kabupaten`;

CREATE TABLE `kabupaten` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kabupaten` */

/*Table structure for table `kecamatan` */

DROP TABLE IF EXISTS `kecamatan`;

CREATE TABLE `kecamatan` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kabupaten` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kecamatan_id_kabupaten_foreign` (`id_kabupaten`),
  CONSTRAINT `kecamatan_id_kabupaten_foreign` FOREIGN KEY (`id_kabupaten`) REFERENCES `kabupaten` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kecamatan` */

/*Table structure for table `kegiatan` */

DROP TABLE IF EXISTS `kegiatan`;

CREATE TABLE `kegiatan` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_proker` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mahasiswa` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frekuensi` int NOT NULL,
  `jkem` int NOT NULL,
  `total_jkem` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kegiatan_id_proker_foreign` (`id_proker`),
  KEY `kegiatan_id_mahasiswa_foreign` (`id_mahasiswa`),
  CONSTRAINT `kegiatan_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kegiatan_id_proker_foreign` FOREIGN KEY (`id_proker`) REFERENCES `proker` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kegiatan` */

/*Table structure for table `kkn` */

DROP TABLE IF EXISTS `kkn`;

CREATE TABLE `kkn` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thn_ajaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `kkn` */

/*Table structure for table `logbook_harian` */

DROP TABLE IF EXISTS `logbook_harian`;

CREATE TABLE `logbook_harian` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mahasiswa` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jkem` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbook_harian_id_mahasiswa_foreign` (`id_mahasiswa`),
  KEY `logbook_harian_id_unit_foreign` (`id_unit`),
  CONSTRAINT `logbook_harian_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_harian_id_unit_foreign` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `logbook_harian` */

/*Table structure for table `logbook_kegiatan` */

DROP TABLE IF EXISTS `logbook_kegiatan`;

CREATE TABLE `logbook_kegiatan` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_logbook_harian` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kegiatan` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mahasiswa` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `jenis` enum('bantu','bersama','individu') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jkem` int NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbook_kegiatan_id_logbook_harian_foreign` (`id_logbook_harian`),
  KEY `logbook_kegiatan_id_mahasiswa_foreign` (`id_mahasiswa`),
  KEY `logbook_kegiatan_id_kegiatan_foreign` (`id_kegiatan`),
  KEY `logbook_kegiatan_id_unit_foreign` (`id_unit`),
  CONSTRAINT `logbook_kegiatan_id_kegiatan_foreign` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_kegiatan_id_logbook_harian_foreign` FOREIGN KEY (`id_logbook_harian`) REFERENCES `logbook_harian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_kegiatan_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `logbook_kegiatan_id_unit_foreign` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `logbook_kegiatan` */

/*Table structure for table `logbook_sholat` */

DROP TABLE IF EXISTS `logbook_sholat`;

CREATE TABLE `logbook_sholat` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mahasiswa` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `waktu` enum('subuh','dzuhur','ashar','maghrib','isya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum diisi',
  `tanggal` date NOT NULL,
  `jumlah_jamaah` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logbook_sholat_id_mahasiswa_foreign` (`id_mahasiswa`),
  CONSTRAINT `logbook_sholat_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `logbook_sholat` */

/*Table structure for table `lokasi` */

DROP TABLE IF EXISTS `lokasi`;

CREATE TABLE `lokasi` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kecamatan` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lokasi_id_kecamatan_foreign` (`id_kecamatan`),
  CONSTRAINT `lokasi_id_kecamatan_foreign` FOREIGN KEY (`id_kecamatan`) REFERENCES `kecamatan` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lokasi` */

/*Table structure for table `mahasiswa` */

DROP TABLE IF EXISTS `mahasiswa`;

CREATE TABLE `mahasiswa` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user_role` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_prodi` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nim` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jkem` int NOT NULL DEFAULT '0',
  `jabatan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mahasiswa_id_user_role_foreign` (`id_user_role`),
  KEY `mahasiswa_id_prodi_foreign` (`id_prodi`),
  KEY `mahasiswa_id_unit_foreign` (`id_unit`),
  KEY `mahasiswa_id_kkn_foreign` (`id_kkn`),
  CONSTRAINT `mahasiswa_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`),
  CONSTRAINT `mahasiswa_id_prodi_foreign` FOREIGN KEY (`id_prodi`) REFERENCES `prodi` (`id`),
  CONSTRAINT `mahasiswa_id_unit_foreign` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id`),
  CONSTRAINT `mahasiswa_id_user_role_foreign` FOREIGN KEY (`id_user_role`) REFERENCES `user_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `mahasiswa` */

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2024_07_03_102908_create_kkn_table',1),
(5,'2024_07_04_102831_create_roles_table',1),
(6,'2024_07_04_102851_create_user_role_table',1),
(7,'2024_07_04_102902_create_prodi_table',1),
(8,'2024_07_04_102905_create_kabupaten_table',1),
(9,'2024_07_04_102906_create_kecamatan_table',1),
(10,'2024_07_04_102907_create_lokasi_table',1),
(11,'2024_07_04_102909_create_dpl_table',1),
(12,'2024_07_04_102910_create_unit_table',1),
(13,'2024_07_04_102913_create_mahasiswa_table',1),
(14,'2024_07_04_102959_create_tim_monev_table',1),
(15,'2024_07_04_103014_create_informasi_table',1),
(16,'2024_07_04_103057_create_bidang_proker_table',1),
(17,'2024_07_04_103058_create_proker_table',1),
(18,'2024_07_04_103129_create_logbook_harian_table',1),
(19,'2024_07_04_103148_create_tempat_sasarans_table',1),
(20,'2024_07_04_103149_create_kegiatans_table',1),
(21,'2024_07_04_103150_create_tanggal_rencana_proker_table',1),
(22,'2024_07_04_104219_create_logbook_kegiatan_table',1),
(23,'2024_07_04_104228_create_logbook_sholat_table',1),
(24,'2024_07_06_114102_queue_progress',1),
(25,'2024_07_16_081313_create_organizers_table',1),
(26,'2024_07_25_175811_create_dana_kegiatans_table',1);

/*Table structure for table `organizer` */

DROP TABLE IF EXISTS `organizer`;

CREATE TABLE `organizer` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_proker` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `peran` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organizer_id_proker_foreign` (`id_proker`),
  CONSTRAINT `organizer_id_proker_foreign` FOREIGN KEY (`id_proker`) REFERENCES `proker` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `organizer` */

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `prodi` */

DROP TABLE IF EXISTS `prodi`;

CREATE TABLE `prodi` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_prodi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `prodi` */

/*Table structure for table `proker` */

DROP TABLE IF EXISTS `proker`;

CREATE TABLE `proker` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_unit` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_bidang` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proker_id_unit_foreign` (`id_unit`),
  KEY `proker_id_bidang_foreign` (`id_bidang`),
  CONSTRAINT `proker_id_bidang_foreign` FOREIGN KEY (`id_bidang`) REFERENCES `bidang_proker` (`id`) ON DELETE CASCADE,
  CONSTRAINT `proker_id_unit_foreign` FOREIGN KEY (`id_unit`) REFERENCES `unit` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `proker` */

/*Table structure for table `queue_progress` */

DROP TABLE IF EXISTS `queue_progress`;

CREATE TABLE `queue_progress` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `progress` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `step` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `queue_progress` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_role` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`nama_role`,`created_at`,`updated_at`) values 
('0197f660-d41f-4962-9319-4030d45dc8fa','Tim Monev','2025-02-06 02:36:56','2025-02-06 02:36:56'),
('58b908e3-899d-4f5e-90c5-6ec57cff1d5c','Admin','2025-02-06 02:36:56','2025-02-06 02:36:56'),
('e18f6282-b35d-46a2-a4bd-005e3b44b735','DPL','2025-02-06 02:36:56','2025-02-06 02:36:56'),
('fbb82303-18ae-4240-a27f-b28d3c89ee10','Mahasiswa','2025-02-06 02:36:56','2025-02-06 02:36:56');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

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

/*Data for the table `sessions` */

/*Table structure for table `tanggal_rencana_proker` */

DROP TABLE IF EXISTS `tanggal_rencana_proker`;

CREATE TABLE `tanggal_rencana_proker` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kegiatan` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tanggal_rencana_proker_id_kegiatan_foreign` (`id_kegiatan`),
  KEY `tanggal_rencana_proker_id_kkn_foreign` (`id_kkn`),
  CONSTRAINT `tanggal_rencana_proker_id_kegiatan_foreign` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tanggal_rencana_proker_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tanggal_rencana_proker` */

/*Table structure for table `tempat_sasaran` */

DROP TABLE IF EXISTS `tempat_sasaran`;

CREATE TABLE `tempat_sasaran` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_mahasiswa` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sasaran` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_proker` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tempat_sasaran_id_mahasiswa_foreign` (`id_mahasiswa`),
  KEY `tempat_sasaran_id_proker_foreign` (`id_proker`),
  CONSTRAINT `tempat_sasaran_id_mahasiswa_foreign` FOREIGN KEY (`id_mahasiswa`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tempat_sasaran_id_proker_foreign` FOREIGN KEY (`id_proker`) REFERENCES `proker` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tempat_sasaran` */

/*Table structure for table `tim_monev` */

DROP TABLE IF EXISTS `tim_monev`;

CREATE TABLE `tim_monev` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user_role` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tim_monev_id_user_role_foreign` (`id_user_role`),
  KEY `tim_monev_id_kkn_foreign` (`id_kkn`),
  CONSTRAINT `tim_monev_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`),
  CONSTRAINT `tim_monev_id_user_role_foreign` FOREIGN KEY (`id_user_role`) REFERENCES `user_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tim_monev` */

/*Table structure for table `unit` */

DROP TABLE IF EXISTS `unit`;

CREATE TABLE `unit` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_dpl` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_lokasi` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_penerjunan` date DEFAULT NULL,
  `tanggal_penarikan` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unit_id_kkn_foreign` (`id_kkn`),
  KEY `unit_id_dpl_foreign` (`id_dpl`),
  KEY `unit_id_lokasi_foreign` (`id_lokasi`),
  CONSTRAINT `unit_id_dpl_foreign` FOREIGN KEY (`id_dpl`) REFERENCES `dpl` (`id`),
  CONSTRAINT `unit_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`),
  CONSTRAINT `unit_id_lokasi_foreign` FOREIGN KEY (`id_lokasi`) REFERENCES `lokasi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `unit` */

/*Table structure for table `user_role` */

DROP TABLE IF EXISTS `user_role`;

CREATE TABLE `user_role` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_role` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_kkn` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_role_id_user_foreign` (`id_user`),
  KEY `user_role_id_role_foreign` (`id_role`),
  KEY `user_role_id_kkn_foreign` (`id_kkn`),
  CONSTRAINT `user_role_id_kkn_foreign` FOREIGN KEY (`id_kkn`) REFERENCES `kkn` (`id`),
  CONSTRAINT `user_role_id_role_foreign` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`),
  CONSTRAINT `user_role_id_user_foreign` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `user_role` */

insert  into `user_role`(`id`,`id_user`,`id_role`,`id_kkn`,`created_at`,`updated_at`) values 
('ed521f8a-dd52-4976-9f86-5ecae3c0a1c7','f281a09e-708a-4326-b13a-34e80be153ed','58b908e3-899d-4f5e-90c5-6ec57cff1d5c',NULL,'2025-02-06 02:37:48','2025-02-06 02:37:48');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`nama`,`email`,`password`,`jenis_kelamin`,`no_telp`,`created_at`,`updated_at`) values 
('f281a09e-708a-4326-b13a-34e80be153ed','Gemilang Tirto','gemilangtirto2002@gmail.com','$2y$12$jMV24N/DHEbqPUjhJMAzdewmVSR/YjqaIUdhyiU4jNei1bmDiOOvy',NULL,NULL,'2025-02-06 02:31:59','2025-02-06 02:31:59');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

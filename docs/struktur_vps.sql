-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 07, 2026 at 06:31 PM
-- Server version: 10.11.19-MariaDB
-- PHP Version: 8.4.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `portal_kom_digital`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_answereds`
--

CREATE TABLE `kmd_answereds` (
  `id` char(36) NOT NULL,
  `questions_id` char(36) NOT NULL,
  `answer` text NOT NULL,
  `is_true` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_books`
--

CREATE TABLE `kmd_books` (
  `id` char(36) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `no_books` varchar(255) DEFAULT NULL,
  `certificate_id` char(36) DEFAULT NULL,
  `class_id` char(36) DEFAULT NULL,
  `date_publish` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_cabangs`
--

CREATE TABLE `kmd_cabangs` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_certificates`
--

CREATE TABLE `kmd_certificates` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `no_certificate` varchar(255) NOT NULL,
  `date_publish` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_classes`
--

CREATE TABLE `kmd_classes` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_class_tutorial`
--

CREATE TABLE `kmd_class_tutorial` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `rayon_id` char(36) DEFAULT NULL,
  `pengajar_id` char(36) NOT NULL,
  `class_id` char(36) NOT NULL,
  `modul_id` char(36) NOT NULL,
  `category_diklat` varchar(255) DEFAULT NULL,
  `quota` int(11) NOT NULL DEFAULT 0,
  `jml_ikut` int(11) NOT NULL DEFAULT 0,
  `sisa_quota` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `status_diklat` varchar(100) DEFAULT 'active',
  `notifikasi` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `zoom_meeting_id` varchar(255) DEFAULT NULL,
  `topic` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 60,
  `host_email` varchar(255) NOT NULL,
  `start_url` text DEFAULT NULL,
  `join_url` text DEFAULT NULL,
  `no_whatsapp` varchar(255) DEFAULT NULL,
  `email_cabang` varchar(255) DEFAULT NULL,
  `notification_before` int(11) DEFAULT NULL,
  `notif_sent_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_donations`
--

CREATE TABLE `kmd_donations` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `userclasstutorial_id` char(36) DEFAULT NULL,
  `user_class_progress_id` char(36) DEFAULT NULL,
  `modul_id` char(36) DEFAULT NULL,
  `category_diklat` varchar(150) DEFAULT NULL,
  `class_id` char(36) DEFAULT NULL,
  `cabang_id` char(36) DEFAULT NULL,
  `order_id` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `fee_payment_gateway` int(11) DEFAULT NULL,
  `payment_type` varchar(255) DEFAULT NULL,
  `transaction_time` timestamp NULL DEFAULT NULL,
  `transaction_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_token` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_moduls`
--

CREATE TABLE `kmd_moduls` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `kom_class_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_pertanyaan_user`
--

CREATE TABLE `kmd_pertanyaan_user` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `rayon_id` char(36) DEFAULT NULL,
  `class_id` char(36) NOT NULL,
  `modul_id` char(36) NOT NULL,
  `category_diklat` varchar(150) DEFAULT NULL,
  `kelas_tutorial_id` char(36) NOT NULL,
  `pengajar_id` char(36) NOT NULL,
  `pertanyaan` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `tgl_submit` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_progress_moduls`
--

CREATE TABLE `kmd_progress_moduls` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `moduls_id` char(36) NOT NULL,
  `status` enum('locked','unlock','completed') NOT NULL DEFAULT 'locked',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_questions`
--

CREATE TABLE `kmd_questions` (
  `id` char(36) NOT NULL,
  `questions` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_question_videos`
--

CREATE TABLE `kmd_question_videos` (
  `id` char(36) NOT NULL,
  `videos_id` char(36) NOT NULL,
  `questions_id` char(36) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_tarik_saldo`
--

CREATE TABLE `kmd_tarik_saldo` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `cabang_id` char(36) DEFAULT NULL,
  `donasi_id` char(36) DEFAULT NULL,
  `userclasstutorial_id` char(36) DEFAULT NULL,
  `tanggal_penarikan` date NOT NULL,
  `jumlah` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_users`
--

CREATE TABLE `kmd_users` (
  `id` char(36) NOT NULL,
  `nik` varchar(10) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('peserta','pengajar','admin_cabang','superadmin') NOT NULL,
  `cabang_id` char(36) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `asal_gereja` varchar(255) DEFAULT NULL,
  `tahun_lahir` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `idnkp` varchar(100) DEFAULT NULL,
  `statactive` varchar(100) DEFAULT NULL,
  `jenis` varchar(100) DEFAULT NULL,
  `no_sim` varchar(100) DEFAULT NULL,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `email_verification_sent_at` timestamp NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_absensi`
--

CREATE TABLE `kmd_user_absensi` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `userclasstutorial_id` char(36) NOT NULL,
  `hadir` tinyint(1) NOT NULL DEFAULT 0,
  `tidak_hadir` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_class_progress`
--

CREATE TABLE `kmd_user_class_progress` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `kelas_tutorial_id` char(36) NOT NULL,
  `pengajar_id` char(36) NOT NULL,
  `class_id` char(36) NOT NULL,
  `modul_id` char(36) NOT NULL,
  `rayon_id` char(36) NOT NULL,
  `donasi_id` char(36) DEFAULT NULL,
  `pertanyaan_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pertanyaan_json`)),
  `step` enum('pilih','pertanyaan','donasi','selesai') NOT NULL DEFAULT 'pilih',
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_class_tutorial`
--

CREATE TABLE `kmd_user_class_tutorial` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `class_tutorial_id` char(36) NOT NULL,
  `modul_id` char(36) NOT NULL,
  `category_diklat` varchar(150) DEFAULT NULL,
  `class_id` char(36) NOT NULL,
  `rayon_id` char(36) NOT NULL,
  `pengajar_id` char(36) DEFAULT NULL,
  `date_booking` date NOT NULL,
  `zoom_join_url` text DEFAULT NULL,
  `zoom_registrant_id` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `step` enum('pilih','pertanyaan','donasi','selesai') NOT NULL DEFAULT 'pilih',
  `status_progress` enum('pending','paid') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_module`
--

CREATE TABLE `kmd_user_module` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `tutorial_unlocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_progress_video`
--

CREATE TABLE `kmd_user_progress_video` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `video_id` char(36) NOT NULL,
  `status` enum('locked','unlock','completed','in_progress') NOT NULL DEFAULT 'locked',
  `is_watched` tinyint(1) NOT NULL DEFAULT 0,
  `answered_question` varchar(36) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `date_publish` timestamp NULL DEFAULT NULL,
  `watched_at` timestamp NULL DEFAULT NULL,
  `last_time` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_questions`
--

CREATE TABLE `kmd_user_questions` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `question_id` char(36) NOT NULL,
  `answer_id` char(36) NOT NULL,
  `is_true` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_rekening`
--

CREATE TABLE `kmd_user_rekening` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `cabang_id` char(36) DEFAULT NULL,
  `nama_rek` varchar(255) NOT NULL,
  `no_rek` varchar(255) NOT NULL,
  `cabang_bank` varchar(255) DEFAULT NULL,
  `nama_bank` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_review`
--

CREATE TABLE `kmd_user_review` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `pengajar_id` char(36) NOT NULL,
  `userclasstutorial_id` char(36) NOT NULL,
  `isi_pengajaran` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `kebenaran_teologis` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `pengajar_sampaikan_seluruh_materi` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `pengajar_sampaikan_materi_utama` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `pembukaan_materi` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `kesinambungan_materi` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `kesaksian_pribadi` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `materi_dipahami` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `sesuai_dengan_materi` enum('sangat_baik','baik','cukup','kurang') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_user_tutorial_status`
--

CREATE TABLE `kmd_user_tutorial_status` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `status` enum('completed','skipped') NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_videos`
--

CREATE TABLE `kmd_videos` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `durations` varchar(255) DEFAULT NULL,
  `sort_video` int(11) DEFAULT NULL,
  `url_video` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `modul_id` char(36) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kmd_zoom_meetings`
--

CREATE TABLE `kmd_zoom_meetings` (
  `id` char(36) NOT NULL,
  `zoom_meeting_id` varchar(255) DEFAULT NULL,
  `topic` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT 60,
  `host_email` varchar(255) NOT NULL,
  `start_url` text DEFAULT NULL,
  `join_url` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'scheduled',
  `user_id` char(36) DEFAULT NULL,
  `pengajar_id` char(36) DEFAULT NULL,
  `cabang_id` char(36) DEFAULT NULL,
  `kelas_id` char(36) DEFAULT NULL,
  `modul_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions_kom`
--

CREATE TABLE `sessions_kom` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kmd_answereds`
--
ALTER TABLE `kmd_answereds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_answereds_questions_id_foreign` (`questions_id`);

--
-- Indexes for table `kmd_books`
--
ALTER TABLE `kmd_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_books_user_id_foreign` (`user_id`),
  ADD KEY `kmd_books_certificate_id_foreign` (`certificate_id`),
  ADD KEY `kmd_books_class_id_foreign` (`class_id`);

--
-- Indexes for table `kmd_cabangs`
--
ALTER TABLE `kmd_cabangs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kmd_certificates`
--
ALTER TABLE `kmd_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_certificates_no_certificate_unique` (`no_certificate`),
  ADD KEY `kmd_certificates_user_id_foreign` (`user_id`);

--
-- Indexes for table `kmd_classes`
--
ALTER TABLE `kmd_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kmd_class_tutorial`
--
ALTER TABLE `kmd_class_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_class_tutorial_user_id_foreign` (`user_id`),
  ADD KEY `kmd_class_tutorial_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_class_tutorial_class_id_foreign` (`class_id`),
  ADD KEY `kmd_class_tutorial_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_class_tutorial_rayon_id_foreign` (`rayon_id`);

--
-- Indexes for table `kmd_donations`
--
ALTER TABLE `kmd_donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_donations_order_id_unique` (`order_id`),
  ADD KEY `kmd_donations_user_id_foreign` (`user_id`),
  ADD KEY `kmd_donations_userclasstutorial_id_foreign` (`userclasstutorial_id`),
  ADD KEY `kmd_donations_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_donations_class_id_foreign` (`class_id`),
  ADD KEY `kmd_donations_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_donations_userclassprogress_id_foreign` (`user_class_progress_id`);

--
-- Indexes for table `kmd_moduls`
--
ALTER TABLE `kmd_moduls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_moduls_kom_class_id_foreign` (`kom_class_id`);

--
-- Indexes for table `kmd_pertanyaan_user`
--
ALTER TABLE `kmd_pertanyaan_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_pertanyaan_user_user_id_foreign` (`user_id`),
  ADD KEY `kmd_pertanyaan_user_class_id_foreign` (`class_id`),
  ADD KEY `kmd_pertanyaan_user_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_pertanyaan_user_kelas_tutorial_id_foreign` (`kelas_tutorial_id`),
  ADD KEY `kmd_pertanyaan_user_rayon_id_index` (`rayon_id`),
  ADD KEY `kmd_pertanyaan_user_pengajar_id_foreign` (`pengajar_id`);

--
-- Indexes for table `kmd_progress_moduls`
--
ALTER TABLE `kmd_progress_moduls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_progress_moduls_user_id_moduls_id_unique` (`user_id`,`moduls_id`),
  ADD KEY `kmd_progress_moduls_moduls_id_foreign` (`moduls_id`);

--
-- Indexes for table `kmd_questions`
--
ALTER TABLE `kmd_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kmd_question_videos`
--
ALTER TABLE `kmd_question_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_question_videos_videos_id_foreign` (`videos_id`),
  ADD KEY `kmd_question_videos_questions_id_foreign` (`questions_id`);

--
-- Indexes for table `kmd_tarik_saldo`
--
ALTER TABLE `kmd_tarik_saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_tarik_saldo_user_id_foreign` (`user_id`),
  ADD KEY `kmd_tarik_saldo_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_tarik_saldo_donasi_id_foreign` (`donasi_id`),
  ADD KEY `kmd_tarik_saldo_userclasstutorial_id_foreign` (`userclasstutorial_id`);

--
-- Indexes for table `kmd_users`
--
ALTER TABLE `kmd_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_users_email_unique` (`email`),
  ADD KEY `kmd_users_cabang_id_foreign` (`cabang_id`);

--
-- Indexes for table `kmd_user_absensi`
--
ALTER TABLE `kmd_user_absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_absensi_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_absensi_userclasstutorial_id_foreign` (`userclasstutorial_id`);

--
-- Indexes for table `kmd_user_class_progress`
--
ALTER TABLE `kmd_user_class_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kmd_user_class_progress_user_id` (`user_id`),
  ADD KEY `fk_kmd_user_class_progress_kelas_tutorial_id` (`kelas_tutorial_id`),
  ADD KEY `fk_kmd_user_class_progress_class_id` (`class_id`),
  ADD KEY `fk_kmd_user_class_progress_modul_id` (`modul_id`),
  ADD KEY `fk_kmd_user_class_progress_rayon_id` (`rayon_id`),
  ADD KEY `fk_kmd_user_class_progress_donasi_id` (`donasi_id`),
  ADD KEY `fk_kmd_user_class_progress_pengajar_id` (`pengajar_id`);

--
-- Indexes for table `kmd_user_class_tutorial`
--
ALTER TABLE `kmd_user_class_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_class_tutorial_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_class_tutorial_class_tutorial_id_foreign` (`class_tutorial_id`),
  ADD KEY `kmd_user_class_tutorial_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_user_class_tutorial_class_id_foreign` (`class_id`),
  ADD KEY `kmd_user_class_tutorial_rayon_id_foreign` (`rayon_id`);

--
-- Indexes for table `kmd_user_module`
--
ALTER TABLE `kmd_user_module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_module_user_id_module_id_unique` (`user_id`,`module_id`),
  ADD KEY `kmd_user_module_module_id_foreign` (`module_id`);

--
-- Indexes for table `kmd_user_progress_video`
--
ALTER TABLE `kmd_user_progress_video`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_progress_video_user_id_video_id_unique` (`user_id`,`video_id`),
  ADD KEY `kmd_user_progress_video_video_id_foreign` (`video_id`);

--
-- Indexes for table `kmd_user_questions`
--
ALTER TABLE `kmd_user_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_questions_user_id_question_id_unique` (`user_id`,`question_id`),
  ADD KEY `kmd_user_questions_question_id_foreign` (`question_id`),
  ADD KEY `kmd_user_questions_answer_id_foreign` (`answer_id`);

--
-- Indexes for table `kmd_user_rekening`
--
ALTER TABLE `kmd_user_rekening`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_rekening_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_rekening_cabang_id_foreign` (`cabang_id`);

--
-- Indexes for table `kmd_user_review`
--
ALTER TABLE `kmd_user_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_review_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_review_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_user_review_userclasstutorial_id_foreign` (`userclasstutorial_id`);

--
-- Indexes for table `kmd_user_tutorial_status`
--
ALTER TABLE `kmd_user_tutorial_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_tutorial_status_user_id_module_id_unique` (`user_id`,`module_id`),
  ADD KEY `kmd_user_tutorial_status_module_id_foreign` (`module_id`);

--
-- Indexes for table `kmd_videos`
--
ALTER TABLE `kmd_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_videos_modul_id_foreign` (`modul_id`);

--
-- Indexes for table `kmd_zoom_meetings`
--
ALTER TABLE `kmd_zoom_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_zoom_meetings_user_id_foreign` (`user_id`),
  ADD KEY `kmd_zoom_meetings_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_zoom_meetings_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_zoom_meetings_kelas_id_foreign` (`kelas_id`),
  ADD KEY `kmd_zoom_meetings_modul_id_foreign` (`modul_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sessions_kom`
--
ALTER TABLE `sessions_kom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_kom_user_id_index` (`user_id`),
  ADD KEY `sessions_kom_last_activity_index` (`last_activity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kmd_answereds`
--
ALTER TABLE `kmd_answereds`
  ADD CONSTRAINT `kmd_answereds_questions_id_foreign` FOREIGN KEY (`questions_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_books`
--
ALTER TABLE `kmd_books`
  ADD CONSTRAINT `kmd_books_certificate_id_foreign` FOREIGN KEY (`certificate_id`) REFERENCES `kmd_certificates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_books_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kmd_certificates`
--
ALTER TABLE `kmd_certificates`
  ADD CONSTRAINT `kmd_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_class_tutorial`
--
ALTER TABLE `kmd_class_tutorial`
  ADD CONSTRAINT `kmd_class_tutorial_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_class_tutorial_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_donations`
--
ALTER TABLE `kmd_donations`
  ADD CONSTRAINT `kmd_donations_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_donations_userclassprogress_id_foreign` FOREIGN KEY (`user_class_progress_id`) REFERENCES `kmd_user_class_progress` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_donations_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kmd_moduls`
--
ALTER TABLE `kmd_moduls`
  ADD CONSTRAINT `kmd_moduls_kom_class_id_foreign` FOREIGN KEY (`kom_class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_pertanyaan_user`
--
ALTER TABLE `kmd_pertanyaan_user`
  ADD CONSTRAINT `kmd_pertanyaan_user_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_kelas_tutorial_id_foreign` FOREIGN KEY (`kelas_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_pertanyaan_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_progress_moduls`
--
ALTER TABLE `kmd_progress_moduls`
  ADD CONSTRAINT `kmd_progress_moduls_moduls_id_foreign` FOREIGN KEY (`moduls_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_progress_moduls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_question_videos`
--
ALTER TABLE `kmd_question_videos`
  ADD CONSTRAINT `kmd_question_videos_questions_id_foreign` FOREIGN KEY (`questions_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_question_videos_videos_id_foreign` FOREIGN KEY (`videos_id`) REFERENCES `kmd_videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_tarik_saldo`
--
ALTER TABLE `kmd_tarik_saldo`
  ADD CONSTRAINT `kmd_tarik_saldo_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_tarik_saldo_donasi_id_foreign` FOREIGN KEY (`donasi_id`) REFERENCES `kmd_donations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_tarik_saldo_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_tarik_saldo_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kmd_users`
--
ALTER TABLE `kmd_users`
  ADD CONSTRAINT `kmd_users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kmd_user_absensi`
--
ALTER TABLE `kmd_user_absensi`
  ADD CONSTRAINT `kmd_user_absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_absensi_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_class_progress`
--
ALTER TABLE `kmd_user_class_progress`
  ADD CONSTRAINT `fk_kmd_user_class_progress_class_id` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_donasi_id` FOREIGN KEY (`donasi_id`) REFERENCES `kmd_donations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kmd_user_class_progress_kelas_tutorial_id` FOREIGN KEY (`kelas_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_modul_id` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_pengajar_id` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_rayon_id` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_user_id` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_class_tutorial`
--
ALTER TABLE `kmd_user_class_tutorial`
  ADD CONSTRAINT `kmd_user_class_tutorial_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_class_tutorial_id_foreign` FOREIGN KEY (`class_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_module`
--
ALTER TABLE `kmd_user_module`
  ADD CONSTRAINT `kmd_user_module_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_module_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_progress_video`
--
ALTER TABLE `kmd_user_progress_video`
  ADD CONSTRAINT `kmd_user_progress_video_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_progress_video_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `kmd_videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_questions`
--
ALTER TABLE `kmd_user_questions`
  ADD CONSTRAINT `kmd_user_questions_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `kmd_answereds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_rekening`
--
ALTER TABLE `kmd_user_rekening`
  ADD CONSTRAINT `kmd_user_rekening_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_user_rekening_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_review`
--
ALTER TABLE `kmd_user_review`
  ADD CONSTRAINT `kmd_user_review_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_review_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_review_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_user_tutorial_status`
--
ALTER TABLE `kmd_user_tutorial_status`
  ADD CONSTRAINT `kmd_user_tutorial_status_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_tutorial_status_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kmd_videos`
--
ALTER TABLE `kmd_videos`
  ADD CONSTRAINT `kmd_videos_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kmd_zoom_meetings`
--
ALTER TABLE `kmd_zoom_meetings`
  ADD CONSTRAINT `kmd_zoom_meetings_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

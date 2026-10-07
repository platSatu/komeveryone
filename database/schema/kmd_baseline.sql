-- =====================================================================
-- BASELINE STRUKTUR TABEL KOM DIGITAL (kmd_*)  — kom_v02
-- Sumber : struktur database production (phpMyAdmin dump, 07 Okt 2026,
--          MariaDB 10.11). HANYA STRUKTUR, tanpa data.
-- Dipakai: migration 2026_10_08_000000_create_kmd_baseline_tables.php,
--          HANYA dijalankan pada database KOSONG (tabel kmd_users belum ada).
--          Di database production yang sudah berisi, file ini TIDAK dieksekusi.
-- =====================================================================
SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE IF NOT EXISTS `kmd_answereds` (
  `id` char(36) NOT NULL,
  `questions_id` char(36) NOT NULL,
  `answer` text NOT NULL,
  `is_true` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_books` (
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

CREATE TABLE IF NOT EXISTS `kmd_cabangs` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_certificates` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `no_certificate` varchar(255) NOT NULL,
  `date_publish` date NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_classes` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_class_tutorial` (
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

CREATE TABLE IF NOT EXISTS `kmd_donations` (
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

CREATE TABLE IF NOT EXISTS `kmd_moduls` (
  `id` char(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `kom_class_id` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_pertanyaan_user` (
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

CREATE TABLE IF NOT EXISTS `kmd_progress_moduls` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `moduls_id` char(36) NOT NULL,
  `status` enum('locked','unlock','completed') NOT NULL DEFAULT 'locked',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_questions` (
  `id` char(36) NOT NULL,
  `questions` text NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_question_videos` (
  `id` char(36) NOT NULL,
  `videos_id` char(36) NOT NULL,
  `questions_id` char(36) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_tarik_saldo` (
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

CREATE TABLE IF NOT EXISTS `kmd_users` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_absensi` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `userclasstutorial_id` char(36) NOT NULL,
  `hadir` tinyint(1) NOT NULL DEFAULT 0,
  `tidak_hadir` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_user_class_progress` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_class_tutorial` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_module` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `tutorial_unlocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_user_progress_video` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_questions` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `question_id` char(36) NOT NULL,
  `answer_id` char(36) NOT NULL,
  `is_true` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_user_rekening` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_review` (
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

CREATE TABLE IF NOT EXISTS `kmd_user_tutorial_status` (
  `id` char(36) NOT NULL,
  `user_id` char(36) NOT NULL,
  `module_id` char(36) NOT NULL,
  `status` enum('completed','skipped') NOT NULL,
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kmd_videos` (
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

CREATE TABLE IF NOT EXISTS `kmd_zoom_meetings` (
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

CREATE TABLE IF NOT EXISTS `sessions_kom` (
  `id` varchar(255) NOT NULL,
  `user_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Index & primary key
ALTER TABLE `kmd_answereds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_answereds_questions_id_foreign` (`questions_id`);

ALTER TABLE `kmd_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_books_user_id_foreign` (`user_id`),
  ADD KEY `kmd_books_certificate_id_foreign` (`certificate_id`),
  ADD KEY `kmd_books_class_id_foreign` (`class_id`);

ALTER TABLE `kmd_cabangs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `kmd_certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_certificates_no_certificate_unique` (`no_certificate`),
  ADD KEY `kmd_certificates_user_id_foreign` (`user_id`);

ALTER TABLE `kmd_classes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `kmd_class_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_class_tutorial_user_id_foreign` (`user_id`),
  ADD KEY `kmd_class_tutorial_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_class_tutorial_class_id_foreign` (`class_id`),
  ADD KEY `kmd_class_tutorial_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_class_tutorial_rayon_id_foreign` (`rayon_id`);

ALTER TABLE `kmd_donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_donations_order_id_unique` (`order_id`),
  ADD KEY `kmd_donations_user_id_foreign` (`user_id`),
  ADD KEY `kmd_donations_userclasstutorial_id_foreign` (`userclasstutorial_id`),
  ADD KEY `kmd_donations_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_donations_class_id_foreign` (`class_id`),
  ADD KEY `kmd_donations_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_donations_userclassprogress_id_foreign` (`user_class_progress_id`);

ALTER TABLE `kmd_moduls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_moduls_kom_class_id_foreign` (`kom_class_id`);

ALTER TABLE `kmd_pertanyaan_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_pertanyaan_user_user_id_foreign` (`user_id`),
  ADD KEY `kmd_pertanyaan_user_class_id_foreign` (`class_id`),
  ADD KEY `kmd_pertanyaan_user_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_pertanyaan_user_kelas_tutorial_id_foreign` (`kelas_tutorial_id`),
  ADD KEY `kmd_pertanyaan_user_rayon_id_index` (`rayon_id`),
  ADD KEY `kmd_pertanyaan_user_pengajar_id_foreign` (`pengajar_id`);

ALTER TABLE `kmd_progress_moduls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_progress_moduls_user_id_moduls_id_unique` (`user_id`,`moduls_id`),
  ADD KEY `kmd_progress_moduls_moduls_id_foreign` (`moduls_id`);

ALTER TABLE `kmd_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `kmd_question_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_question_videos_videos_id_foreign` (`videos_id`),
  ADD KEY `kmd_question_videos_questions_id_foreign` (`questions_id`);

ALTER TABLE `kmd_tarik_saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_tarik_saldo_user_id_foreign` (`user_id`),
  ADD KEY `kmd_tarik_saldo_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_tarik_saldo_donasi_id_foreign` (`donasi_id`),
  ADD KEY `kmd_tarik_saldo_userclasstutorial_id_foreign` (`userclasstutorial_id`);

ALTER TABLE `kmd_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_users_email_unique` (`email`),
  ADD KEY `kmd_users_cabang_id_foreign` (`cabang_id`);

ALTER TABLE `kmd_user_absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_absensi_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_absensi_userclasstutorial_id_foreign` (`userclasstutorial_id`);

ALTER TABLE `kmd_user_class_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kmd_user_class_progress_user_id` (`user_id`),
  ADD KEY `fk_kmd_user_class_progress_kelas_tutorial_id` (`kelas_tutorial_id`),
  ADD KEY `fk_kmd_user_class_progress_class_id` (`class_id`),
  ADD KEY `fk_kmd_user_class_progress_modul_id` (`modul_id`),
  ADD KEY `fk_kmd_user_class_progress_rayon_id` (`rayon_id`),
  ADD KEY `fk_kmd_user_class_progress_donasi_id` (`donasi_id`),
  ADD KEY `fk_kmd_user_class_progress_pengajar_id` (`pengajar_id`);

ALTER TABLE `kmd_user_class_tutorial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_class_tutorial_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_class_tutorial_class_tutorial_id_foreign` (`class_tutorial_id`),
  ADD KEY `kmd_user_class_tutorial_modul_id_foreign` (`modul_id`),
  ADD KEY `kmd_user_class_tutorial_class_id_foreign` (`class_id`),
  ADD KEY `kmd_user_class_tutorial_rayon_id_foreign` (`rayon_id`);

ALTER TABLE `kmd_user_module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_module_user_id_module_id_unique` (`user_id`,`module_id`),
  ADD KEY `kmd_user_module_module_id_foreign` (`module_id`);

ALTER TABLE `kmd_user_progress_video`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_progress_video_user_id_video_id_unique` (`user_id`,`video_id`),
  ADD KEY `kmd_user_progress_video_video_id_foreign` (`video_id`);

ALTER TABLE `kmd_user_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_questions_user_id_question_id_unique` (`user_id`,`question_id`),
  ADD KEY `kmd_user_questions_question_id_foreign` (`question_id`),
  ADD KEY `kmd_user_questions_answer_id_foreign` (`answer_id`);

ALTER TABLE `kmd_user_rekening`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_rekening_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_rekening_cabang_id_foreign` (`cabang_id`);

ALTER TABLE `kmd_user_review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_user_review_user_id_foreign` (`user_id`),
  ADD KEY `kmd_user_review_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_user_review_userclasstutorial_id_foreign` (`userclasstutorial_id`);

ALTER TABLE `kmd_user_tutorial_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kmd_user_tutorial_status_user_id_module_id_unique` (`user_id`,`module_id`),
  ADD KEY `kmd_user_tutorial_status_module_id_foreign` (`module_id`);

ALTER TABLE `kmd_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_videos_modul_id_foreign` (`modul_id`);

ALTER TABLE `kmd_zoom_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kmd_zoom_meetings_user_id_foreign` (`user_id`),
  ADD KEY `kmd_zoom_meetings_pengajar_id_foreign` (`pengajar_id`),
  ADD KEY `kmd_zoom_meetings_cabang_id_foreign` (`cabang_id`),
  ADD KEY `kmd_zoom_meetings_kelas_id_foreign` (`kelas_id`),
  ADD KEY `kmd_zoom_meetings_modul_id_foreign` (`modul_id`);

ALTER TABLE `sessions_kom`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_kom_user_id_index` (`user_id`),
  ADD KEY `sessions_kom_last_activity_index` (`last_activity`);

-- Foreign key
ALTER TABLE `kmd_answereds`
  ADD CONSTRAINT `kmd_answereds_questions_id_foreign` FOREIGN KEY (`questions_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_books`
  ADD CONSTRAINT `kmd_books_certificate_id_foreign` FOREIGN KEY (`certificate_id`) REFERENCES `kmd_certificates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_books_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL;

ALTER TABLE `kmd_certificates`
  ADD CONSTRAINT `kmd_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_class_tutorial`
  ADD CONSTRAINT `kmd_class_tutorial_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_class_tutorial_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_class_tutorial_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_donations`
  ADD CONSTRAINT `kmd_donations_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_donations_userclassprogress_id_foreign` FOREIGN KEY (`user_class_progress_id`) REFERENCES `kmd_user_class_progress` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_donations_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE SET NULL;

ALTER TABLE `kmd_moduls`
  ADD CONSTRAINT `kmd_moduls_kom_class_id_foreign` FOREIGN KEY (`kom_class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_pertanyaan_user`
  ADD CONSTRAINT `kmd_pertanyaan_user_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_kelas_tutorial_id_foreign` FOREIGN KEY (`kelas_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_pertanyaan_user_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_pertanyaan_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_progress_moduls`
  ADD CONSTRAINT `kmd_progress_moduls_moduls_id_foreign` FOREIGN KEY (`moduls_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_progress_moduls_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_question_videos`
  ADD CONSTRAINT `kmd_question_videos_questions_id_foreign` FOREIGN KEY (`questions_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_question_videos_videos_id_foreign` FOREIGN KEY (`videos_id`) REFERENCES `kmd_videos` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_tarik_saldo`
  ADD CONSTRAINT `kmd_tarik_saldo_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_tarik_saldo_donasi_id_foreign` FOREIGN KEY (`donasi_id`) REFERENCES `kmd_donations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_tarik_saldo_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_tarik_saldo_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE SET NULL;

ALTER TABLE `kmd_users`
  ADD CONSTRAINT `kmd_users_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL;

ALTER TABLE `kmd_user_absensi`
  ADD CONSTRAINT `kmd_user_absensi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_absensi_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_class_progress`
  ADD CONSTRAINT `fk_kmd_user_class_progress_class_id` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_donasi_id` FOREIGN KEY (`donasi_id`) REFERENCES `kmd_donations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kmd_user_class_progress_kelas_tutorial_id` FOREIGN KEY (`kelas_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_modul_id` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_pengajar_id` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_rayon_id` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kmd_user_class_progress_user_id` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_class_tutorial`
  ADD CONSTRAINT `kmd_user_class_tutorial_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `kmd_classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_class_tutorial_id_foreign` FOREIGN KEY (`class_tutorial_id`) REFERENCES `kmd_class_tutorial` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_rayon_id_foreign` FOREIGN KEY (`rayon_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_class_tutorial_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_module`
  ADD CONSTRAINT `kmd_user_module_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_module_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_progress_video`
  ADD CONSTRAINT `kmd_user_progress_video_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_progress_video_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `kmd_videos` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_questions`
  ADD CONSTRAINT `kmd_user_questions_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `kmd_answereds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `kmd_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_questions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_rekening`
  ADD CONSTRAINT `kmd_user_rekening_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_user_rekening_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_review`
  ADD CONSTRAINT `kmd_user_review_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_review_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_review_userclasstutorial_id_foreign` FOREIGN KEY (`userclasstutorial_id`) REFERENCES `kmd_user_class_tutorial` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_user_tutorial_status`
  ADD CONSTRAINT `kmd_user_tutorial_status_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kmd_user_tutorial_status_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE CASCADE;

ALTER TABLE `kmd_videos`
  ADD CONSTRAINT `kmd_videos_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL;

ALTER TABLE `kmd_zoom_meetings`
  ADD CONSTRAINT `kmd_zoom_meetings_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `kmd_cabangs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kmd_classes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_modul_id_foreign` FOREIGN KEY (`modul_id`) REFERENCES `kmd_moduls` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `kmd_zoom_meetings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `kmd_users` (`id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS=1;

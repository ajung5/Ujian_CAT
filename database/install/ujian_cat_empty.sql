-- Ujian CAT - Empty Database Installer
-- Generated for the current application schema as of 25 September 2026.
--
-- PURPOSE
--   Fresh installation only: create the application database structure
--   without operational/sample data. The only application row inserted is
--   one Administrator account.
--
-- SAFETY
--   Import this file ONLY into an EMPTY database.
--   This script intentionally does not DROP existing tables. If a table
--   already exists, MySQL will stop rather than deleting existing data.
--
-- INITIAL ADMIN
--   Email    : admin@ujian-cat.local
--   Role     : A (Administrator)
--   Password : intentionally unavailable/disabled.
--
-- After import, set a new strong password with Laravel Tinker. See README.md.

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE `schools` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(255) NULL,
    `alamat` TEXT NULL,
    `logo` VARCHAR(255) NULL,
    `header` VARCHAR(255) NULL,
    `motto` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `kelas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_kelas` INT UNSIGNED NULL,
    `nama` VARCHAR(255) NOT NULL DEFAULT '',
    `no_induk` VARCHAR(255) NULL,
    `jk` VARCHAR(1) NULL,
    `status` VARCHAR(1) NOT NULL,
    `gambar` VARCHAR(255) NOT NULL DEFAULT '',
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `active_session_hash` CHAR(64) NULL,
    `student_session_revoked_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_at` TIMESTAMP NULL DEFAULT NULL,
    `last_login_ip` VARCHAR(45) NULL,
    `last_login_user_agent` TEXT NULL,
    `sekolah_asal` VARCHAR(255) NOT NULL DEFAULT '',
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `materis` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_user` INT UNSIGNED NOT NULL,
    `judul` VARCHAR(255) NOT NULL,
    `isi` LONGTEXT NOT NULL,
    `gambar` VARCHAR(255) NULL,
    `status` VARCHAR(1) NOT NULL DEFAULT 'Y',
    `hits` INT NOT NULL DEFAULT 0,
    `sesi` VARCHAR(32) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `soals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_user` VARCHAR(50) NOT NULL,
    `jenis` VARCHAR(1) NOT NULL DEFAULT '1',
    `materi` INT UNSIGNED NULL,
    `paket` VARCHAR(255) NOT NULL,
    `deskripsi` VARCHAR(255) NOT NULL,
    `kkm` VARCHAR(5) NOT NULL,
    `waktu` VARCHAR(25) NOT NULL,
    `tampil` VARCHAR(1) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detailsoals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_soal` VARCHAR(150) NOT NULL,
    `jenis` VARCHAR(5) NOT NULL,
    `soal` LONGTEXT NOT NULL,
    `audio` VARCHAR(255) NULL,
    `pila` LONGTEXT NOT NULL,
    `pilb` LONGTEXT NOT NULL,
    `pilc` LONGTEXT NOT NULL,
    `pild` LONGTEXT NOT NULL,
    `pile` LONGTEXT NOT NULL,
    `kunci` VARCHAR(1) NOT NULL,
    `score` VARCHAR(50) NULL,
    `id_user` VARCHAR(15) NOT NULL,
    `status` VARCHAR(1) NOT NULL,
    `sesi` VARCHAR(32) NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `distribusisoals` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_soal` VARCHAR(15) NOT NULL,
    `id_kelas` VARCHAR(15) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assessment_attempts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_soal` INT UNSIGNED NOT NULL,
    `id_user` INT UNSIGNED NOT NULL,
    `attempt_no` TINYINT UNSIGNED NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'in_progress',
    `score` DECIMAL(10,2) NULL,
    `started_at` TIMESTAMP NULL DEFAULT NULL,
    `finished_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `assessment_attempt_unique` (`id_soal`, `id_user`, `attempt_no`),
    KEY `assessment_attempts_id_user_status_index` (`id_user`, `status`),
    KEY `assessment_attempts_id_soal_id_user_index` (`id_soal`, `id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jawabs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attempt_id` BIGINT UNSIGNED NULL,
    `no_soal_id` INT UNSIGNED NOT NULL,
    `id_soal` VARCHAR(150) NOT NULL,
    `id_user` VARCHAR(15) NOT NULL,
    `id_kelas` VARCHAR(15) NULL,
    `nama` VARCHAR(255) NULL,
    `pilihan` VARCHAR(5) NOT NULL DEFAULT '',
    `score` VARCHAR(50) NULL,
    `status` VARCHAR(1) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `jawabs_attempt_id_index` (`attempt_id`),
    UNIQUE KEY `uq_jawabs_attempt_question` (`attempt_id`, `no_soal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `countexamtimes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `attempt_id` BIGINT UNSIGNED NULL,
    `id_soal` VARCHAR(150) NOT NULL,
    `id_user` VARCHAR(15) NOT NULL,
    `waktu` VARCHAR(25) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `countexamtimes_attempt_id_index` (`attempt_id`),
    UNIQUE KEY `uq_countexamtimes_attempt` (`attempt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `aktifitas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_user` INT UNSIGNED NOT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_security_events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NULL,
    `actor_user_id` INT UNSIGNED NULL,
    `email` VARCHAR(255) NULL,
    `role` VARCHAR(1) NULL,
    `event` VARCHAR(50) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `session_hash` CHAR(64) NULL,
    `metadata` JSON NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_security_events_user_id_created_at_index` (`user_id`, `created_at`),
    KEY `user_security_events_event_created_at_index` (`event`, `created_at`),
    KEY `user_security_events_actor_user_id_index` (`actor_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- The schema above already includes the final result of these migrations.
-- Recording them prevents Laravel from trying to reapply changes that are
-- already represented in this fresh-install schema.
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
    (1, '2026_09_24_132000_create_assessment_attempts', 1),
    (2, '2026_09_24_144726_update_countexamtimes_unique_index_for_attempts', 1),
    (3, '2026_09_24_145042_update_countexamtimes_unique_index_for_attempts', 1),
    (4, '2026_09_24_145430_update_jawabs_unique_index_for_attempts', 1),
    (5, '2026_09_25_100632_add_active_session_hash_to_users_table', 1),
    (6, '2026_09_25_110000_add_student_session_security_columns_to_users_table', 1),
    (7, '2026_09_25_110100_create_user_security_events_table', 1);

-- Only application data seeded by this installer: one Administrator.
-- The bcrypt hash below was generated from a random secret that is NOT
-- distributed, so this account cannot be used until you set your own password.
INSERT INTO `users` (
    `id`,
    `id_kelas`,
    `nama`,
    `no_induk`,
    `jk`,
    `status`,
    `gambar`,
    `email`,
    `password`,
    `remember_token`,
    `active_session_hash`,
    `student_session_revoked_at`,
    `last_login_at`,
    `last_login_ip`,
    `last_login_user_agent`,
    `sekolah_asal`,
    `created_at`,
    `updated_at`
) VALUES (
    1,
    NULL,
    'Administrator',
    'ADMIN',
    'L',
    'A',
    '',
    'admin@ujian-cat.local',
    '$2y$12$JDJ5Aw0Bn3SwxNmMMSn0t.kXisvYqgpJ5kEFeP1XsgCH.WOcph8wm',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    '',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP
);

-- Database Dump for Proxmox / Docker Deployment
-- Generated: 2026-11-07 20:59:36

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `tanampohon_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tanampohon_db`;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK user pelaku (null untuk sistem)',
  `action` varchar(50) NOT NULL COMMENT 'Aksi (create, update, delete, login, dll)',
  `description` text DEFAULT NULL COMMENT 'Deskripsi aktivitas',
  `loggable_type` varchar(50) DEFAULT NULL COMMENT 'Tipe entitas (users, donations, dll)',
  `loggable_id` int(10) unsigned DEFAULT NULL COMMENT 'ID entitas terkait',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Alamat IP',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent browser',
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data sebelum perubahan' CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data setelah perubahan' CHECK (json_valid(`new_data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_logs_user_id` (`user_id`),
  KEY `idx_activity_logs_action` (`action`),
  KEY `idx_activity_logs_loggable` (`loggable_type`,`loggable_id`),
  KEY `idx_activity_logs_created_at` (`created_at`),
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log aktivitas pengguna dan sistem';

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `loggable_type`, `loggable_id`, `ip_address`, `user_agent`, `old_data`, `new_data`, `created_at`) VALUES
('1', NULL, 'cron_payment_expired', 'Pembayaran invoice INV-20261105-C7C5 dibatalkan otomatis karena kedaluwarsa.', 'payments', '4', '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:36:50'),
('2', NULL, 'cron_payment_expired', 'Pembayaran invoice INV-20261105-EAD4 dibatalkan otomatis karena kedaluwarsa.', 'payments', '2', '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:36:50'),
('3', NULL, 'cron_payment_expired', 'Pembayaran invoice INV-20261105-A425 dibatalkan otomatis karena kedaluwarsa.', 'payments', '5', '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:36:50'),
('4', NULL, 'cron_run_all', 'Eksekusi cron job lengkap: {\"timestamp\":\"2026-11-07 18:38:07\",\"execution_time_sec\":0.002,\"expired_payments\":{\"total_cancelled\":0,\"invoices\":[],\"restored_stock_items\":0,\"restored_event_quotas\":0},\"payment_reminders\":{\"total_reminded\":0,\"invoices\":[]},\"event_reminders\":{\"h3_reminders_sent\":0,\"h1_reminders_sent\":0,\"total_reminders\":0},\"cleanup\":{\"abandoned_carts_deleted\":0,\"maintenance_completed\":true}}', NULL, NULL, '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:38:07'),
('5', NULL, 'cron_run_all', 'Eksekusi cron job lengkap: {\"timestamp\":\"2026-11-07 18:38:23\",\"execution_time_sec\":0.0038,\"expired_payments\":{\"total_cancelled\":0,\"invoices\":[],\"restored_stock_items\":0,\"restored_event_quotas\":0},\"payment_reminders\":{\"total_reminded\":0,\"invoices\":[]},\"event_reminders\":{\"h3_reminders_sent\":0,\"h1_reminders_sent\":0,\"total_reminders\":0},\"cleanup\":{\"abandoned_carts_deleted\":0,\"maintenance_completed\":true}}', NULL, NULL, '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:38:23'),
('6', NULL, 'cron_run_all', 'Eksekusi cron job lengkap: {\"timestamp\":\"2026-11-07 18:38:27\",\"execution_time_sec\":0.0032,\"expired_payments\":{\"total_cancelled\":0,\"invoices\":[],\"restored_stock_items\":0,\"restored_event_quotas\":0},\"payment_reminders\":{\"total_reminded\":0,\"invoices\":[]},\"event_reminders\":{\"h3_reminders_sent\":0,\"h1_reminders_sent\":0,\"total_reminders\":0},\"cleanup\":{\"abandoned_carts_deleted\":0,\"maintenance_completed\":true}}', NULL, NULL, '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:38:27'),
('7', NULL, 'cron_run_all', 'Eksekusi cron job lengkap: {\"timestamp\":\"2026-11-07 18:39:02\",\"execution_time_sec\":0.0051,\"expired_payments\":{\"total_cancelled\":0,\"invoices\":[],\"restored_stock_items\":0,\"restored_event_quotas\":0},\"payment_reminders\":{\"total_reminded\":0,\"invoices\":[]},\"event_reminders\":{\"h3_reminders_sent\":0,\"h1_reminders_sent\":0,\"total_reminders\":0},\"cleanup\":{\"abandoned_carts_deleted\":0,\"maintenance_completed\":true}}', NULL, NULL, '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:39:02'),
('8', NULL, 'cron_run_all', 'Eksekusi cron job lengkap: {\"timestamp\":\"2026-11-07 18:42:06\",\"execution_time_sec\":0.0047,\"expired_payments\":{\"total_cancelled\":0,\"invoices\":[],\"restored_stock_items\":0,\"restored_event_quotas\":0},\"payment_reminders\":{\"total_reminded\":0,\"invoices\":[]},\"event_reminders\":{\"h3_reminders_sent\":0,\"h1_reminders_sent\":0,\"total_reminders\":0},\"cleanup\":{\"abandoned_carts_deleted\":0,\"maintenance_completed\":true}}', NULL, NULL, '127.0.0.1 (CLI/Cron)', NULL, NULL, NULL, '2026-11-07 18:42:06');

DROP TABLE IF EXISTS `badges`;
CREATE TABLE `badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nama lencana',
  `slug` varchar(100) NOT NULL COMMENT 'Slug unik',
  `description` varchar(255) DEFAULT NULL COMMENT 'Deskripsi lencana',
  `icon` varchar(255) DEFAULT NULL COMMENT 'Path ikon lencana',
  `min_trees` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Minimum pohon untuk mendapat lencana',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_badges_slug` (`slug`),
  KEY `idx_badges_min_trees` (`min_trees`),
  KEY `idx_badges_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lencana pencapaian pengguna';

INSERT INTO `badges` (`id`, `name`, `slug`, `description`, `icon`, `min_trees`, `sort_order`, `is_active`, `created_at`) VALUES
('1', 'Penanam Pemula', 'penanam-pemula', 'Selamat! Kamu sudah menanam pohon pertamamu.', NULL, '1', '1', '1', '2026-11-05 04:33:44'),
('2', 'Pejuang Hijau', 'pejuang-hijau', 'Hebat! Kamu sudah berkontribusi menanam 5 pohon.', NULL, '5', '2', '1', '2026-11-05 04:33:44'),
('3', 'Sahabat Bumi', 'sahabat-bumi', 'Luar biasa! 10 pohon sudah kamu tanam untuk bumi.', NULL, '10', '3', '1', '2026-11-05 04:33:44'),
('4', 'Penjaga Hutan', 'penjaga-hutan', 'Kamu sudah menanam 25 pohon. Kontribusi yang luar biasa!', NULL, '25', '4', '1', '2026-11-05 04:33:44'),
('5', 'Pahlawan Lingkungan', 'pahlawan-lingkungan', 'Pencapaian tertinggi! 50 pohon sudah kamu tanam.', NULL, '50', '5', '1', '2026-11-05 04:33:44'),
('6', 'Legenda Hijau', 'legenda-hijau', 'Kamu adalah legenda! 100 pohon telah kamu sumbangkan.', NULL, '100', '6', '1', '2026-11-05 04:33:44');

DROP TABLE IF EXISTS `bank_accounts`;
CREATE TABLE `bank_accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(50) NOT NULL COMMENT 'Nama bank',
  `channel_type` enum('bank_transfer','qris','e_wallet') NOT NULL DEFAULT 'bank_transfer',
  `account_number` varchar(30) NOT NULL COMMENT 'Nomor rekening',
  `account_holder` varchar(100) NOT NULL COMMENT 'Nama pemilik rekening',
  `branch` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `bank_logo` varchar(255) DEFAULT NULL COMMENT 'Path logo bank',
  `qris_image` varchar(255) DEFAULT NULL COMMENT 'Path gambar QRIS',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_bank_accounts_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daftar rekening bank penerima donasi/pembayaran';

INSERT INTO `bank_accounts` (`id`, `bank_name`, `channel_type`, `account_number`, `account_holder`, `branch`, `instructions`, `bank_logo`, `qris_image`, `sort_order`, `is_active`, `is_primary`, `created_at`, `updated_at`) VALUES
('1', 'Bank Central Asia (BCA)', 'bank_transfer', '1390-8821-44', 'Yayasan Tanam Pohon andu', 'KCU Asia Afrika Bandung', 'Transfer via ATM, BCA Mobile, atau KlikBCA. Tambahkan kode unik nominal tagihan agar sistem otomatis mencocokkan pembayaran Anda.', NULL, NULL, '1', '1', '1', '2026-11-05 04:33:44', '2026-11-08 01:40:58'),
('3', 'Bank Mandiri', 'bank_transfer', '131-00-1892837-1', 'Yayasan Konservasi Agroforestry Kerenceng', 'KCP Bandung Cicalengka', 'Transfer melalui Livin\' by Mandiri, ATM Mandiri, atau transfer BI-Fast dari bank mana saja.', NULL, NULL, '2', '1', '0', '2026-11-07 14:49:07', '2026-11-07 14:49:07'),
('4', 'Bank Syariah Indonesia (BSI)', 'bank_transfer', '718-2938-401', 'Tanam Pohon Karsa Kerentjeng', 'KC Bandung Dago', 'Transfer syariah bebas riba via BSI Mobile atau jaringan ATM Bersama / PRIMA.', NULL, NULL, '3', '1', '0', '2026-11-07 14:49:07', '2026-11-07 14:49:07'),
('5', 'QRIS Standar Nasional (GPN)', 'qris', 'ID102003004005', 'PLANT FOR FUN - ANDU COFFEE LAB', 'Interoperable Semua Bank & E-Wallet (NMID: ID102003004005)', 'Scan barcode QRIS menggunakan BCA, Livin Mandiri, BRImo, BSI Mobile, GoPay, OVO, ShopeePay, atau DANA.', NULL, 'uploads/qris/qris_standar_tanampohon.png', '4', '1', '0', '2026-11-07 14:49:07', '2026-11-08 01:40:58');

DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL COMMENT 'Judul banner',
  `image` varchar(255) NOT NULL COMMENT 'Path gambar banner',
  `link` varchar(500) DEFAULT NULL COMMENT 'URL tujuan saat diklik',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_banners_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Banner slider halaman depan';

DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `product_variant_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel product_variants',
  `quantity` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Jumlah item',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_carts_user_variant` (`user_id`,`product_variant_id`),
  KEY `idx_carts_product_variant_id` (`product_variant_id`),
  CONSTRAINT `fk_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_carts_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Keranjang belanja pengguna';

DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `certifiable_type` enum('donation','participant') NOT NULL COMMENT 'Tipe sertifikat',
  `certifiable_id` int(10) unsigned NOT NULL COMMENT 'ID donasi atau partisipan',
  `certificate_number` varchar(50) NOT NULL COMMENT 'Nomor sertifikat unik',
  `type` enum('donasi','partisipasi') NOT NULL COMMENT 'Jenis sertifikat',
  `data_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot data saat sertifikat diterbitkan' CHECK (json_valid(`data_snapshot`)),
  `pdf_path` varchar(255) DEFAULT NULL COMMENT 'Path file PDF sertifikat',
  `qr_code_path` varchar(255) DEFAULT NULL COMMENT 'Path QR code verifikasi',
  `issued_at` datetime NOT NULL COMMENT 'Tanggal penerbitan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_certificates_number` (`certificate_number`),
  KEY `idx_certificates_user_id` (`user_id`),
  KEY `idx_certificates_certifiable` (`certifiable_type`,`certifiable_id`),
  CONSTRAINT `fk_certificates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sertifikat donasi dan partisipasi';

INSERT INTO `certificates` (`id`, `user_id`, `certifiable_type`, `certifiable_id`, `certificate_number`, `type`, `data_snapshot`, `pdf_path`, `qr_code_path`, `issued_at`, `created_at`) VALUES
('1', '1', 'donation', '2', 'CERT-20261105-767216', 'donasi', '{\"certificate_number\":\"CERT-20261105-767216\",\"recipient_name\":\"Donatur Uji Coba\",\"total_trees\":3,\"species_summary\":\"3 Pohon Mangga\",\"total_co2_kg\":85.5,\"donation_amount\":\"150000.00\",\"donation_date\":\"2026-11-05 11:38:20\",\"signer_name\":\"Koordinator Ekspedisi Plant For Fun\",\"signer_title\":\"Plant For Fun • Kerenceng Expedition\",\"verify_url\":\"http:\\/\\/localhost\\/cocok_tanam\\/public\\/verifikasi-sertifikat\\/CERT-20261105-767216\"}', NULL, NULL, '2026-11-05 12:03:36', '2026-11-05 12:03:36'),
('2', '1', 'participant', '1', 'CERT-REL-20261105-9988A1', 'partisipasi', '{\"certificate_number\":\"CERT-REL-20261105-9988A1\",\"recipient_name\":\"Budi Santoso\",\"event_title\":\"Aksi Tanam 1.000 Mangrove Pesisir Indramayu\",\"event_date\":\"2026-11-19\",\"location\":\"Pesisir Pantai Indramayu, Indramayu\",\"role\":\"Relawan Penanaman Pohon\",\"signer_name\":\"Koordinator Ekspedisi Plant For Fun\",\"signer_title\":\"Plant For Fun • Kerenceng Expedition\",\"verify_url\":\"http:\\/\\/localhost\\/cocok_tanam\\/public\\/verifikasi-sertifikat\\/CERT-REL-20261105-9988A1\"}', NULL, NULL, '2026-11-07 18:28:01', '2026-11-07 18:28:01'),
('3', '1', 'donation', '4', 'CERT-20261102-4A8812', 'donasi', NULL, NULL, NULL, '2026-11-02 10:35:00', '2026-11-02 10:35:00'),
('4', '1', 'donation', '5', 'CERT-20261103-5B9921', 'donasi', NULL, NULL, NULL, '2026-11-03 15:50:00', '2026-11-03 15:50:00'),
('5', '1', 'donation', '6', 'CERT-20261104-6C1123', 'donasi', NULL, NULL, NULL, '2026-11-04 11:15:00', '2026-11-04 11:15:00'),
('6', '1', 'donation', '7', 'CERT-20261104-7D4456', 'donasi', NULL, NULL, NULL, '2026-11-04 17:25:00', '2026-11-04 17:25:00'),
('7', '1', 'donation', '12', 'CERT-20261107-5B822F', 'donasi', '{\"certificate_number\":\"CERT-20261107-5B822F\",\"recipient_name\":\"Keluarga Besar Karsa Kerentjeng (Hadiah dari Budi Prakoso Kerenceng)\",\"total_trees\":10,\"species_summary\":\"10 Pohon Pohon Kopi Arabika Kerenceng\",\"total_co2_kg\":225,\"donation_amount\":\"450000.00\",\"donation_date\":\"2026-11-07 22:00:38\",\"signer_name\":\"Koordinator Ekspedisi Plant For Fun\",\"signer_title\":\"Plant For Fun • Kerenceng Expedition\",\"verify_url\":\"http:\\/\\/localhost:8000\\/verifikasi-sertifikat\\/CERT-20261107-5B822F\"}', NULL, NULL, '2026-11-07 22:00:38', '2026-11-07 22:00:38');

DROP TABLE IF EXISTS `donation_items`;
CREATE TABLE `donation_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `donation_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel donations',
  `tree_species_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel tree_species',
  `quantity` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Jumlah pohon',
  `price_per_tree` decimal(12,2) NOT NULL COMMENT 'Harga per pohon saat transaksi',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'Subtotal (quantity x price_per_tree)',
  PRIMARY KEY (`id`),
  KEY `idx_donation_items_donation_id` (`donation_id`),
  KEY `idx_donation_items_tree_species_id` (`tree_species_id`),
  CONSTRAINT `fk_donation_items_donation` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_donation_items_species` FOREIGN KEY (`tree_species_id`) REFERENCES `tree_species` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail item dalam setiap donasi';

INSERT INTO `donation_items` (`id`, `donation_id`, `tree_species_id`, `quantity`, `price_per_tree`, `subtotal`) VALUES
('2', '2', '1', '3', '50000.00', '150000.00'),
('3', '3', '3', '5', '35000.00', '175000.00'),
('4', '4', '4', '50', '50000.00', '2500000.00'),
('5', '5', '2', '40', '50000.00', '2000000.00'),
('6', '5', '6', '60', '50000.00', '3000000.00'),
('7', '6', '6', '24', '50000.00', '1200000.00'),
('8', '7', '7', '15', '50000.00', '750000.00'),
('9', '8', '3', '5', '35000.00', '175000.00'),
('10', '9', '6', '5', '35000.00', '175000.00'),
('11', '10', '6', '5', '35000.00', '175000.00'),
('12', '11', '1', '3', '35000.00', '105000.00'),
('13', '12', '6', '10', '45000.00', '450000.00');

DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK user (null untuk donasi tamu)',
  `donor_name` varchar(100) NOT NULL COMMENT 'Nama donatur',
  `donor_email` varchar(150) NOT NULL COMMENT 'Email donatur',
  `donor_phone` varchar(20) DEFAULT NULL COMMENT 'Telepon donatur',
  `donor_message` text DEFAULT NULL COMMENT 'Pesan dari donatur',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Donasi anonim',
  `is_gift` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Apakah sebagai hadiah',
  `gift_recipient_name` varchar(100) DEFAULT NULL COMMENT 'Nama penerima hadiah',
  `gift_message` text DEFAULT NULL COMMENT 'Pesan hadiah',
  `payment_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke tabel payments',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total nominal donasi',
  `total_trees` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total jumlah pohon',
  `status` enum('pending','menunggu_pembayaran','berhasil','gagal','dibatalkan') NOT NULL DEFAULT 'pending' COMMENT 'Status donasi',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_donations_user_id` (`user_id`),
  KEY `idx_donations_status` (`status`),
  KEY `idx_donations_payment_id` (`payment_id`),
  KEY `idx_donations_donor_email` (`donor_email`),
  KEY `idx_donations_created_at` (`created_at`),
  CONSTRAINT `fk_donations_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_donations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data donasi penanaman pohon';

INSERT INTO `donations` (`id`, `user_id`, `donor_name`, `donor_email`, `donor_phone`, `donor_message`, `is_anonymous`, `is_gift`, `gift_recipient_name`, `gift_message`, `payment_id`, `total_amount`, `total_trees`, `status`, `created_at`, `updated_at`) VALUES
('2', '1', 'Donatur Uji Coba', 'test@tanampohon.id', '081234567890', 'Semoga pohon tumbuh subur', '0', '0', NULL, NULL, '1', '150000.00', '3', 'berhasil', '2026-11-05 11:38:20', '2026-11-05 05:03:28'),
('3', NULL, 'Als Media', 'alsmedia499@gmail.com', '085158397756', 'Rizki Fahrezi', '0', '0', '', '', '2', '175000.00', '5', 'dibatalkan', '2026-11-05 11:45:58', '2026-11-07 18:36:50'),
('4', NULL, 'Rian Kusuma', 'rian@example.com', NULL, NULL, '0', '0', NULL, NULL, '12', '2500000.00', '50', 'berhasil', '2026-11-02 09:15:00', '2026-11-07 14:57:52'),
('5', NULL, 'PT Lestari Alam Hijau', 'csr@lestarialam.co.id', NULL, NULL, '0', '0', NULL, NULL, '13', '5000000.00', '100', 'berhasil', '2026-11-03 14:20:00', '2026-11-07 14:57:52'),
('6', NULL, 'Hamba Allah', 'anon@example.com', NULL, NULL, '1', '0', NULL, NULL, '14', '1200000.00', '24', 'berhasil', '2026-11-04 10:00:00', '2026-11-07 14:57:52'),
('7', NULL, 'Dewi Anggraini', 'dewi@example.com', NULL, NULL, '0', '0', NULL, NULL, '15', '750000.00', '15', 'berhasil', '2026-11-04 16:45:00', '2026-11-07 14:57:52'),
('8', '1', 'Dimas Anggara', 'dimas.anggara@gmail.com', '081298765432', NULL, '0', '0', NULL, NULL, NULL, '175421.00', '5', 'menunggu_pembayaran', '2026-11-07 14:37:23', '2026-11-07 14:37:23'),
('9', '1', 'Dimas Anggara', 'dimas.anggara@gmail.com', '081298765432', NULL, '0', '0', NULL, NULL, '6', '175421.00', '5', 'berhasil', '2026-11-07 14:37:47', '2026-11-07 14:57:52'),
('10', '1', 'Dimas Anggara', 'dimas.anggara@gmail.com', '081298765432', NULL, '0', '0', NULL, NULL, NULL, '175421.00', '5', 'menunggu_pembayaran', '2026-11-07 14:38:08', '2026-11-07 14:38:08'),
('11', '1', 'Rendi Kusuma', 'rendi.kusuma@gmail.com', '081321456789', NULL, '0', '0', NULL, NULL, '10', '105210.00', '3', 'menunggu_pembayaran', '2026-11-07 14:42:23', '2026-11-07 14:57:52'),
('12', '1', 'Budi Prakoso Kerenceng', 'budi.prakoso@example.com', '081299887766', 'Semoga kopi kerenceng semakin subur dan menyejukkan bumi!', '0', '1', 'Keluarga Besar Karsa Kerentjeng', 'Selamat hari konservasi alam!', '16', '450000.00', '10', 'berhasil', '2026-11-07 22:00:38', '2026-11-07 22:00:38');

DROP TABLE IF EXISTS `event_feedbacks`;
CREATE TABLE `event_feedbacks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel participants',
  `event_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel planting_events',
  `rating` tinyint(3) unsigned NOT NULL COMMENT 'Rating 1-5',
  `comment` text DEFAULT NULL COMMENT 'Komentar',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_event_feedbacks_participant` (`participant_id`),
  KEY `idx_event_feedbacks_event_id` (`event_id`),
  CONSTRAINT `fk_event_feedbacks_event` FOREIGN KEY (`event_id`) REFERENCES `planting_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_event_feedbacks_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_event_feedbacks_rating` CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Umpan balik peserta setelah event';

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL COMMENT 'Pertanyaan',
  `answer` text NOT NULL COMMENT 'Jawaban',
  `category` varchar(50) DEFAULT 'umum' COMMENT 'Kategori FAQ',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_faqs_category` (`category`),
  KEY `idx_faqs_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pertanyaan yang sering ditanyakan';

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Bagaimana cara berdonasi pohon?', 'Kamu bisa berdonasi melalui halaman Donasi di situs kami. Pilih jenis pohon yang ingin kamu tanam, tentukan jumlahnya, lalu lakukan pembayaran melalui transfer bank. Setelah bukti transfer diunggah dan diverifikasi, donasi kamu akan diproses.', 'donasi', '1', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('2', 'Apakah saya bisa berdonasi tanpa membuat akun?', 'Ya, kamu bisa berdonasi sebagai tamu tanpa harus membuat akun. Namun, dengan membuat akun kamu bisa melacak pohon yang sudah kamu tanam, mendapatkan sertifikat, dan mengumpulkan poin.', 'donasi', '2', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('3', 'Berapa lama batas waktu pembayaran?', 'Batas waktu pembayaran adalah 24 jam sejak invoice dibuat. Jika melebihi batas waktu, pembayaran akan otomatis kedaluwarsa dan kamu perlu membuat donasi baru.', 'pembayaran', '3', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('4', 'Bagaimana cara mengunggah bukti transfer?', 'Setelah melakukan transfer, buka halaman detail pembayaran lalu klik tombol \"Unggah Bukti Transfer\". Isi informasi yang diminta dan unggah foto/screenshot bukti transfer. Tim kami akan memverifikasi dalam 1x24 jam kerja.', 'pembayaran', '4', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('5', 'Apakah saya mendapat sertifikat?', 'Ya! Setelah donasi diverifikasi, kamu akan mendapatkan sertifikat digital yang bisa diunduh dalam format PDF. Sertifikat juga dilengkapi QR code untuk verifikasi keaslian.', 'donasi', '5', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('6', 'Bagaimana cara mendaftar sebagai relawan?', 'Kamu bisa melihat daftar event penanaman di halaman Event. Pilih event yang ingin kamu ikuti, daftar, dan lakukan pembayaran pendaftaran (jika berbayar). Setelah terkonfirmasi, kamu akan mendapatkan e-ticket.', 'event', '6', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('7', 'Apakah saya bisa membatalkan pendaftaran event?', 'Ya, kamu bisa membatalkan pendaftaran sebelum batas waktu pembatalan yang tertera di detail event. Silakan hubungi tim kami melalui halaman Bantuan.', 'event', '7', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('8', 'Di mana pohon saya ditanam?', 'Setiap pohon memiliki kode unik yang bisa dilacak melalui halaman Lacak Pohon. Kamu bisa melihat lokasi penanaman, status pertumbuhan, dan foto terbaru pohonmu.', 'pohon', '8', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('9', 'Bagaimana sistem poin dan lencana bekerja?', 'Kamu mendapat poin setiap kali berdonasi pohon, menghadiri event, atau mengajak teman bergabung. Poin yang terkumpul akan membuka lencana pencapaian di profilmu.', 'akun', '9', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('10', 'Bagaimana cara menghubungi tim TanamPohon.id?', 'Kamu bisa menghubungi kami melalui halaman Bantuan di akun kamu, atau melalui WhatsApp di nomor yang tertera di bagian bawah situs. Tim kami siap membantu di jam kerja (Senin-Jumat, 09.00-17.00 WIB).', 'umum', '10', '1', '2026-11-05 04:33:44', '2026-11-05 04:33:44');

DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke news (opsional)',
  `event_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke planting_events (opsional)',
  `title` varchar(200) DEFAULT NULL COMMENT 'Judul foto',
  `image_path` varchar(255) NOT NULL COMMENT 'Path file gambar',
  `description` text DEFAULT NULL COMMENT 'Deskripsi foto',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_galleries_news_id` (`news_id`),
  KEY `idx_galleries_event_id` (`event_id`),
  CONSTRAINT `fk_galleries_event` FOREIGN KEY (`event_id`) REFERENCES `planting_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_galleries_news` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Galeri foto kegiatan';

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT 'Judul berita',
  `slug` varchar(280) NOT NULL COMMENT 'Slug URL-friendly',
  `category` varchar(100) NOT NULL DEFAULT 'Kabar Lapangan',
  `excerpt` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL COMMENT 'Isi konten (HTML)',
  `image` varchar(255) DEFAULT NULL COMMENT 'Gambar utama',
  `author_id` int(10) unsigned DEFAULT NULL COMMENT 'FK penulis (admin)',
  `views_count` int(10) unsigned NOT NULL DEFAULT 0,
  `read_time_minutes` int(10) unsigned NOT NULL DEFAULT 3,
  `is_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Status publikasi',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` datetime DEFAULT NULL COMMENT 'Tanggal publikasi',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_news_slug` (`slug`),
  KEY `idx_news_author_id` (`author_id`),
  KEY `idx_news_is_published` (`is_published`),
  KEY `idx_news_published_at` (`published_at`),
  CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Berita dan artikel';

INSERT INTO `news` (`id`, `title`, `slug`, `category`, `excerpt`, `tags`, `content`, `image`, `author_id`, `views_count`, `read_time_minutes`, `is_published`, `is_featured`, `published_at`, `created_at`, `updated_at`) VALUES
('3', 'Panduan Menjadi Relawan Hijau TanamPohon.id', 'panduan-menjadi-relawan-hijau-tanampohon', 'Panduan & Relawan', 'Panduan lengkap langkah demi langkah bagi sahabat hijau yang ingin berkontribusi langsung di lapangan sebagai relawan aksi penanaman pohon.', 'Relawan, Panduan Lapangan, Komunitas, Aksi Nyata', 'Tertarik ikut terjun langsung ke lapangan dan menanam pohon bersama kami? Berikut langkah mudah bergabung sebagai relawan hijau:\n\n1. Daftarkan akun Anda di TanamPohon.id\n2. Buka menu Kegiatan Penanaman dan pilih lokasi kegiatan terdekat\n3. Klik Daftar dan pilih opsi keikutsertaan Anda (tersedia paket dengan kaos merchandise eksklusif)\n4. Simpan E-Ticket Anda dan tunjukkan QR Code pada meja registrasi check-in panitia di lokasi acara.\n\nSampai jumpa di lapangan, Sahabat Hijau!', '/assets/images/tree-kopi.jpg', '1', '620', '3', '1', '0', '2026-11-05 09:45:22', '2026-11-05 09:45:22', '2026-11-07 14:23:09'),
('4', 'Ekspedisi Penanaman Kopi Kerenceng #001: Membangun Ekosistem Agroforestry Berkelanjutan di Kaki Gunung', 'ekspedisi-penanaman-kopi-kerenceng-001-agroforestry', 'Ekspedisi Kerenceng', 'Liputan mendalam kolaborasi Plant For Fun bersama Karsa Kerentjeng dan andu roastery dalam merestorasi lahan kritis lereng Gunung Kerenceng menjadi kebun kopi agroforestry berdaya serap karbon tinggi.', 'Kerenceng, Agroforestry, andu, Karsa Kerentjeng, Konservasi', 'Di lereng barat Gunung Kerenceng, kabut pagi perlahan membuka jalan bagi puluhan langkah relawan, petani kopi lokal, dan pegiat lingkungan yang berkumpul dalam agenda bertajuk \"Ekspedisi Penanaman Kopi Kerenceng #001\".\n\nAksi ini bukan sekadar menancapkan bibit pohon ke dalam tanah basah. Kolaborasi antara inisiatif Plant For Fun, komunitas pelestari alam Karsa Kerentjeng, dan andu roastery mengusung misi jangka panjang: membangun model perkebunan kopi berbasis agroforestry (wana tani) yang memadukan tanaman kopi dengan pohon pelindung hutan asli.\n\n### Mengapa Agroforestry?\nKonsep monokultur kopi sering kali memicu erosi di lereng curam pegunungan. Melalui pendekatan agroforestry, tanaman kopi ditanam berdampingan dengan pohon naungan bernilai ekologis tinggi seperti Mahoni, Alpukat, Trembesi, dan tanaman buah lokal.\nPohon-pohon pelindung ini memiliki manfaat strategis:\n1. Menahan struktur tanah dari bahaya longsor dan limpasan air hujan deras.\n2. Menyediakan seresah daun alami yang menjadi pupuk organik mikroorganisme tanah.\n3. Menciptakan iklim mikro yang stabil sehingga ceri kopi matang perlahan dan menghasilkan rasa yang lebih kompleks dan manis alami.\n\n### Peran Karsa Kerentjeng dan andu Roastery\nKarsa Kerentjeng bertindak sebagai jangkar konservasi terdepan, memetakan koordinat lahan penanaman serta mengorganisir bibit unggul bersama kelompok tani binaan. Di sisi hilir, andu menyerap ceri kopi panen para petani dengan harga yang adil (*fair trade*), kemudian menyangrainya secara cermat untuk diperkenalkan kepada pecinta kopi nusantara.\n\n\"Kopi yang nikmat tidak boleh lahir dari tanah yang menderita,\" ungkap perwakilan andu saat sesi seduh kopi bersama di bivak posko. Setiap tegukan kopi yang dinikmati para pendukung program secara langsung mendanai pemeliharaan bibit yang baru ditanam hari ini.\n\n### Transparansi Berkelanjutan\nSeluruh pohon yang ditanam dalam ekspedisi ini diberi nomor identitas pohon (*tree tagging*) yang tercatat dalam sistem transparansi digital Plant For Fun. Donatur dan relawan dapat melacak titik GPS penanaman, tinggi pohon berkala, serta estimasi serapan karbon yang dihasilkan dari masa ke masa.\n\nMari terus melangkah, menanam, dan merayakan harmoni antara manusia, kopi, dan hutan.', '/assets/images/plant-for-fun-kerenceng.jpg', '1', '1592', '5', '1', '1', '2026-11-07 14:23:09', '2026-11-07 14:23:09', '2026-11-08 01:42:59'),
('5', 'Mengenal Proses Anaerobic Slow Dry Kopi Kerenceng Bersama andu Roastery', 'mengenal-proses-anaerobic-slow-dry-kopi-kerenceng-an-du', 'Kopi & Agroforestry', 'Ulasan teknik fermentasi anaerobik 72 jam yang menghasilkan profil rasa manis berry dan floral eksotis dari ceri kopi hasil panen petani mitra Karsa Kerentjeng.', 'andu, Anaerobic, Specialty Coffee, Pasca Panen', 'Dunia specialty coffee terus berkembang dengan inovasi pasca-panen yang mengagumkan. Salah satu primadona hasil kebun lereng Kerenceng musim ini adalah varian Kerenceng Anaerobic Slow Dry yang digarap langsung melalui kemitraan kurasi andu.\n\n### Apa Itu Proses Anaerobic Slow Dry?\nSecara sederhana, fermentasi anaerobik dilakukan dengan menempatkan ceri kopi merah petik matang optimal (*red cherry*) ke dalam tangki kedap udara berbahan *food-grade* selama 72 jam.\nDalam kondisi hampa oksigen tersebut, ragi dan bakteri asam laktat alami memecah lendir (*mucilage*) ceri dengan laju terkontrol, melepaskan senyawa aroma buah matang yang meresap ke dalam biji kopi (*green beans*).\n\nSetelah fase fermentasi anaerob selesai, ceri kopi tidak langsung dikupas, melainkan dijemur perlahan (*slow drying*) di atas bedeng jaring beratap pelindung ultraviolet (*raised beds*) selama 21 hingga 28 hari. Proses pengeringan yang lambat ini menjaga stabilitas kelembaban biji hingga mencapai batas aman 10-11%.\n\n### Profil Rasa yang Dihasilkan\nHasil cupping test di laboratorium andu menunjukkan keunikan rasa yang sangat istimewa:\n- **Aroma:** Jasmine floral, dried plum, dan sentuhan aroma madu liar.\n- **Flavour Notes:** Black cherry, dark grape, cocoa nibs dengan aftertaste manis yang panjang (*lingering sweet finish*).\n- **Body & Acidity:** Medium-syrupy body dengan keasaman apel merah yang cerah dan seimbang.\n\n### Dampak Nilai Tambah bagi Petani\nDengan menguasai pengolahan anaerobik, nilai jual ceri kopi petani Kerenceng meningkat hingga 40% dibandingkan menjual ceri basah ke tengkulak konvensional. Inilah wujud nyata bagaimana kecintaan pada kopi bersinergi dengan kesejahteraan petani lokal dan pelestarian hutan naungan tempat kopi tersebut tumbuh.', '/assets/images/coffee-anaerobic.jpg', '1', '920', '4', '1', '0', '2026-11-06 14:23:09', '2026-11-07 14:23:09', '2026-11-08 01:40:58'),
('6', 'Filosofi di Balik \"Plant For Fun\": Menanam Pohon Sambil Menyeruput Secangkir Kopi Terbaik', 'filosofi-di-balik-plant-for-fun-kopi-dan-konservasi', 'Kopi & Agroforestry', 'Bagaimana kebiasaan minum kopi harian bertransformasi menjadi aksi nyata konservasi lingkungan melalui skema satu cangkir satu pohon di lereng Cicalengka.', 'Plant For Fun, Kopi Robusta, Green Coffee, Gaya Hidup Hijau', 'Sering kali isu lingkungan digambarkan dengan nada muram, penuh peringatan bencana, dan terkesan memberatkan. Namun gerakan **Plant For Fun** hadir dengan sudut pandang yang berbeda: *Bagaimana jika menjaga bumi dilakukan dengan rasa gembira, santai, dan menjadi bagian dari gaya hidup harian kita?*\n\n### Dari Cangkir ke Hutan\nBagi jutaan orang di Indonesia, secangkir kopi adalah ritual pagi yang sakral. Kami menghubungkan ritual tersebut langsung dengan aksi penanaman pohon. Setiap pembelian paket kopi atau donasi bibit di platform ini bukan sekadar transaksi ekonomi, melainkan tiket keterlibatan Anda dalam merestorasi tanah nusantara.\n\nAda kegembiraan saat:\n- Menyeruput kopi yang Anda tahu persis berasal dari kebun lereng gunung yang dirawat tanpa pestisida kimia berlebih.\n- Menerima sertifikat digital penanaman dengan koordinat GPS pohon yang Anda danai.\n- Bergabung di akhir pekan menyusuri jalan setapak pegunungan, menghirup udara sejuk pinus, dan menancapkan tunas kehidupan baru bersama kawan-kawan sehobi.\n\nMari kita buktikan bahwa menyelamatkan masa depan bumi tidak harus membosankan. Kita bisa melakukannya sambil tersenyum, menyeduh kopi terbaik, dan bersenang-senang di alam bebas.', '/assets/images/coffee-plantation.jpg', '1', '1240', '3', '1', '0', '2026-11-05 14:23:09', '2026-11-07 14:23:09', '2026-11-07 14:23:09');

DROP TABLE IF EXISTS `news_comments`;
CREATE TABLE `news_comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `news_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `comment` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_news_id` (`news_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_news_comments_news` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `news_comments` (`id`, `news_id`, `name`, `email`, `comment`, `status`, `created_at`, `updated_at`) VALUES
('1', '4', 'Budi Pratama', 'budi.pratama@example.com', 'Keren sekali konsep agroforestry di Kerenceng ini! Semoga makin banyak petani lokal yang merasakan manfaatnya.', 'approved', '2026-11-07 14:23:14', '2026-11-07 14:23:14'),
('2', '5', 'Siti Rahmawati', 'siti.rahma@example.com', 'Proses anaerobic-nya sangat menarik! Kemarin sudah coba beans andu Kerenceng Anaerobic, rasanya fruity banget!', 'approved', '2026-11-07 13:23:14', '2026-11-08 01:40:58'),
('3', '4', 'Rian Hidayat', 'rian@example.com', 'Sangat menginspirasi! Penasaran dengan varian anaerobic-nya dan jadwal tanam berikutnya.', 'approved', '2026-11-07 21:27:53', '2026-11-07 21:27:53');

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `type` varchar(50) NOT NULL COMMENT 'Tipe notifikasi (payment, donation, event, dll)',
  `title` varchar(200) NOT NULL COMMENT 'Judul notifikasi',
  `message` text NOT NULL COMMENT 'Isi notifikasi',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Data tambahan dalam format JSON' CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Sudah dibaca',
  `read_at` datetime DEFAULT NULL COMMENT 'Waktu dibaca',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user_id` (`user_id`),
  KEY `idx_notifications_is_read` (`is_read`),
  KEY `idx_notifications_type` (`type`),
  KEY `idx_notifications_created_at` (`created_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Notifikasi pengguna';

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `read_at`, `created_at`) VALUES
('1', '1', 'badge', 'Selamat! Lencana Baru Terbuka 🏆', 'Kamu telah berhasil membuka lencana \"Penanam Pemula\" atas kontribusi menanam 1 pohon demi bumi Indonesia!', '{\"badge_id\":1,\"badge_name\":\"Penanam Pemula\"}', '0', NULL, '2026-11-05 12:24:06'),
('2', '1', 'badge', 'Selamat! Lencana Baru Terbuka 🏆', 'Kamu telah berhasil membuka lencana \"Pejuang Hijau\" atas kontribusi menanam 5 pohon demi bumi Indonesia!', '{\"badge_id\":2,\"badge_name\":\"Pejuang Hijau\"}', '0', NULL, '2026-11-05 12:24:06'),
('3', '1', 'badge', 'Selamat! Lencana Baru Terbuka 🏆', 'Kamu telah berhasil membuka lencana \"Sahabat Bumi\" atas kontribusi menanam 10 pohon demi bumi Indonesia!', '{\"badge_id\":3,\"badge_name\":\"Sahabat Bumi\"}', '0', NULL, '2026-11-05 12:24:06'),
('4', '1', 'payment', 'Pesanan Kaos Kedaluwarsa', 'Tagihan INV-20261105-C7C5 sebesar Rp 260.342 telah kedaluwarsa karena tidak ada konfirmasi bukti transfer hingga batas waktu yang ditentukan. Alokasi stok atau kuota telah dikembalikan.', '{\"invoice_no\":\"INV-20261105-C7C5\",\"status\":\"kedaluwarsa\"}', '0', NULL, '2026-11-07 18:36:50'),
('5', '1', 'payment', 'Pendaftaran Relawan Kedaluwarsa', 'Tagihan INV-20261105-A425 sebesar Rp 75.310 telah kedaluwarsa karena tidak ada konfirmasi bukti transfer hingga batas waktu yang ditentukan. Alokasi stok atau kuota telah dikembalikan.', '{\"invoice_no\":\"INV-20261105-A425\",\"status\":\"kedaluwarsa\"}', '0', NULL, '2026-11-07 18:36:50');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel orders',
  `product_variant_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke product_variants',
  `product_name` varchar(150) NOT NULL COMMENT 'Nama produk saat checkout',
  `variant_info` varchar(100) DEFAULT NULL COMMENT 'Info varian (ukuran, warna)',
  `quantity` int(10) unsigned NOT NULL DEFAULT 1 COMMENT 'Jumlah item',
  `price` decimal(12,2) NOT NULL COMMENT 'Harga satuan saat checkout',
  `subtotal` decimal(15,2) NOT NULL COMMENT 'Subtotal baris',
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_variant_id` (`product_variant_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_variant` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Detail item dalam pesanan';

INSERT INTO `order_items` (`id`, `order_id`, `product_variant_id`, `product_name`, `variant_info`, `quantity`, `price`, `subtotal`) VALUES
('2', '2', '1', 'Kaos Organik TanamPohon', 'Ukuran: S, Warna: Forest Green', '2', '125000.00', '250000.00');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `order_number` varchar(30) NOT NULL COMMENT 'Nomor pesanan (ORD-YYYYMMDD-NNNN)',
  `payment_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke tabel payments',
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal sebelum ongkir',
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya pengiriman',
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total pembayaran',
  `shipping_address_snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Snapshot alamat saat checkout' CHECK (json_valid(`shipping_address_snapshot`)),
  `shipping_method` varchar(255) DEFAULT NULL COMMENT 'Metode pengiriman',
  `pickup_at_event` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Ambil di lokasi event',
  `event_id` int(10) unsigned DEFAULT NULL COMMENT 'FK event jika ambil di event',
  `notes` text DEFAULT NULL COMMENT 'Catatan pesanan',
  `status` enum('menunggu_pembayaran','diproses','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran' COMMENT 'Status pesanan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_order_number` (`order_number`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_payment_id` (`payment_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_event_id` (`event_id`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `fk_orders_event` FOREIGN KEY (`event_id`) REFERENCES `planting_events` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pesanan produk merchandise';

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `payment_id`, `subtotal`, `shipping_cost`, `total_amount`, `shipping_address_snapshot`, `shipping_method`, `pickup_at_event`, `event_id`, `notes`, `status`, `created_at`, `updated_at`) VALUES
('2', '1', 'ORD-20261105-F86A', '4', '250000.00', '10000.00', '260000.00', '{\"recipient_name\":\"Budi Santoso\",\"phone\":\"081234567890\",\"email\":\"budi@example.com\",\"province\":\"DKI Jakarta\",\"city\":\"Jakarta Selatan\",\"full_address\":\"Jl. Sudirman No. 45\"}', 'JNE - REG (Reguler)', '0', NULL, 'Tolong packing rapi', 'dibatalkan', '2026-11-05 11:50:18', '2026-11-07 18:36:50'),
('3', '1', 'ORD-20261107-0092', NULL, '190000.00', '25000.00', '215380.00', NULL, 'JNE Express (Reguler)', '0', NULL, NULL, 'diproses', '2026-11-07 14:37:47', '2026-11-07 21:42:11'),
('4', '1', 'ORD-20261107-0095', NULL, '95000.00', '10000.00', '105150.00', NULL, 'SiCepat Reguler', '0', NULL, NULL, 'menunggu_pembayaran', '2026-11-07 14:42:23', '2026-11-07 14:42:23');

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL COMMENT 'Slug URL halaman',
  `title` varchar(200) NOT NULL COMMENT 'Judul halaman',
  `content` longtext NOT NULL COMMENT 'Konten halaman (HTML)',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pages_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Halaman statis (tentang, syarat, privasi)';

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `created_at`, `updated_at`) VALUES
('1', 'tentang-kami', 'Tentang Kami', '<h2>Tentang TanamPohon.id</h2>\n<p>TanamPohon.id adalah platform penghijauan digital yang menghubungkan masyarakat Indonesia dengan aksi nyata penanaman pohon. Kami percaya bahwa setiap pohon yang ditanam adalah langkah kecil menuju bumi yang lebih hijau dan masa depan yang lebih baik.</p>\n<h3>Misi Kami</h3>\n<ul>\n<li>Memudahkan siapa saja untuk berkontribusi dalam penghijauan Indonesia</li>\n<li>Menyediakan transparansi penuh atas setiap pohon yang ditanam</li>\n<li>Membangun komunitas pecinta lingkungan yang aktif dan berdampak</li>\n</ul>', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('2', 'syarat-ketentuan', 'Syarat dan Ketentuan', '<h2>Syarat dan Ketentuan Penggunaan</h2>\n<p>Dengan menggunakan layanan TanamPohon.id, Anda menyetujui syarat dan ketentuan berikut:</p>\n<ol>\n<li>Pengguna harus berusia minimal 17 tahun atau memiliki izin dari orang tua/wali.</li>\n<li>Donasi yang telah diverifikasi tidak dapat dibatalkan atau dikembalikan.</li>\n<li>TanamPohon.id berhak menggunakan data donasi untuk keperluan pelaporan dan transparansi.</li>\n<li>Pengguna bertanggung jawab atas keamanan akun dan kata sandi masing-masing.</li>\n<li>TanamPohon.id berhak menonaktifkan akun yang melanggar ketentuan.</li>\n</ol>', '2026-11-05 04:33:44', '2026-11-05 04:33:44'),
('3', 'kebijakan-privasi', 'Kebijakan Privasi', '<h2>Kebijakan Privasi</h2>\n<p>TanamPohon.id berkomitmen melindungi privasi pengguna. Kami mengumpulkan dan menggunakan data pribadi sesuai dengan ketentuan berikut:</p>\n<h3>Data yang Dikumpulkan</h3>\n<p>Kami mengumpulkan nama, email, nomor telepon, dan alamat yang diperlukan untuk memproses donasi, pendaftaran event, dan pengiriman merchandise.</p>\n<h3>Penggunaan Data</h3>\n<p>Data digunakan untuk memproses transaksi, mengirim notifikasi terkait layanan, dan menerbitkan sertifikat. Kami tidak menjual data pengguna kepada pihak ketiga.</p>\n<h3>Keamanan Data</h3>\n<p>Kami menggunakan enkripsi dan praktik keamanan standar industri untuk melindungi data Anda.</p>', '2026-11-05 04:33:44', '2026-11-05 04:33:44');

DROP TABLE IF EXISTS `participants`;
CREATE TABLE `participants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `event_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel planting_events',
  `registration_status` enum('menunggu','terkonfirmasi','hadir','batal') NOT NULL DEFAULT 'menunggu' COMMENT 'Status pendaftaran',
  `payment_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke payments (null jika gratis)',
  `tshirt_size` enum('S','M','L','XL','XXL') DEFAULT NULL COMMENT 'Ukuran kaos peserta',
  `notes` text DEFAULT NULL COMMENT 'Catatan peserta',
  `checked_in_at` datetime DEFAULT NULL COMMENT 'Waktu check-in',
  `checked_in_by` int(10) unsigned DEFAULT NULL COMMENT 'Admin yang melakukan check-in',
  `cancelled_at` datetime DEFAULT NULL COMMENT 'Waktu pembatalan',
  `cancel_reason` text DEFAULT NULL COMMENT 'Alasan pembatalan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_participants_user_event` (`user_id`,`event_id`),
  KEY `idx_participants_event_id` (`event_id`),
  KEY `idx_participants_registration_status` (`registration_status`),
  KEY `idx_participants_payment_id` (`payment_id`),
  KEY `idx_participants_checked_in_by` (`checked_in_by`),
  CONSTRAINT `fk_participants_checked_in_by` FOREIGN KEY (`checked_in_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_participants_event` FOREIGN KEY (`event_id`) REFERENCES `planting_events` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_participants_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_participants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data peserta event penanaman';

INSERT INTO `participants` (`id`, `user_id`, `event_id`, `registration_status`, `payment_id`, `tshirt_size`, `notes`, `checked_in_at`, `checked_in_by`, `cancelled_at`, `cancel_reason`, `created_at`, `updated_at`) VALUES
('1', '1', '1', 'terkonfirmasi', NULL, 'L', 'Komunitas Pecinta Alam', NULL, NULL, NULL, NULL, '2026-11-05 11:49:50', '2026-11-05 11:49:50'),
('2', '1', '2', 'batal', '5', 'L', '', NULL, NULL, '2026-11-07 18:36:50', 'Batas waktu pembayaran telah kedaluwarsa secara otomatis oleh sistem.', '2026-11-05 16:49:51', '2026-11-07 18:36:50'),
('5', '1', '3', 'terkonfirmasi', NULL, 'L', NULL, NULL, NULL, NULL, NULL, '2026-11-07 14:38:52', '2026-11-07 21:42:11');

DROP TABLE IF EXISTS `payment_proofs`;
CREATE TABLE `payment_proofs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel payments',
  `bank_account_id` int(10) unsigned DEFAULT NULL COMMENT 'FK rekening tujuan',
  `sender_bank` varchar(50) NOT NULL COMMENT 'Bank pengirim',
  `sender_name` varchar(100) NOT NULL COMMENT 'Nama pengirim',
  `amount_transferred` decimal(15,2) NOT NULL COMMENT 'Jumlah yang ditransfer',
  `transfer_date` date NOT NULL COMMENT 'Tanggal transfer',
  `transfer_time` time DEFAULT NULL COMMENT 'Waktu transfer',
  `proof_file` varchar(255) NOT NULL COMMENT 'Path file bukti transfer',
  `file_hash` varchar(64) DEFAULT NULL COMMENT 'Hash SHA-256 file untuk deteksi duplikat',
  `note` text DEFAULT NULL COMMENT 'Catatan tambahan',
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu' COMMENT 'Status verifikasi',
  `rejected_reason` text DEFAULT NULL COMMENT 'Alasan penolakan',
  `verified_by` int(10) unsigned DEFAULT NULL COMMENT 'Admin yang memverifikasi',
  `verified_at` datetime DEFAULT NULL COMMENT 'Waktu verifikasi',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_payment_proofs_payment_id` (`payment_id`),
  KEY `idx_payment_proofs_status` (`status`),
  KEY `idx_payment_proofs_file_hash` (`file_hash`),
  KEY `idx_payment_proofs_verified_by` (`verified_by`),
  KEY `fk_payment_proofs_bank` (`bank_account_id`),
  CONSTRAINT `fk_payment_proofs_bank` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_proofs_payment` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_payment_proofs_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bukti pembayaran/transfer dari pengguna';

INSERT INTO `payment_proofs` (`id`, `payment_id`, `bank_account_id`, `sender_bank`, `sender_name`, `amount_transferred`, `transfer_date`, `transfer_time`, `proof_file`, `file_hash`, `note`, `status`, `rejected_reason`, `verified_by`, `verified_at`, `created_at`) VALUES
('1', '6', NULL, 'BCA', 'Dimas Anggara', '175421.00', '2026-11-07', '14:37:47', 'payment_proofs/transfer_bca_sample.jpg', 'ac3a2425ab416cabefc2f1b8d29b8b01e34d679e2e4a71f4f982d5f0867e6395', 'Transfer via BCA Mobile donasi 5 pohon naungan lereng Kerenceng', 'disetujui', NULL, '1', '2026-11-07 21:42:04', '2026-11-07 14:37:47'),
('2', '7', NULL, 'Bank Mandiri', 'Larasati Putri', '215380.00', '2026-11-07', '14:37:47', 'payment_proofs/transfer_mandiri_sample.jpg', 'd4d83ca3b71b973c27c93a3a1805cb33b9ce2a734d3f4511a1e0adbdf8bb75ad', 'Livin by Mandiri untuk 2 pack Kopi Anaerobic & Drip Bag', 'disetujui', NULL, '1', '2026-11-07 21:42:11', '2026-11-07 14:37:47'),
('3', '9', NULL, 'BCA (QRIS)', 'Fajar Nugraha', '75250.00', '2026-11-07', '14:38:52', 'payment_proofs/transfer_qris_sample.jpg', 'f60a854fafd9dcf756962f0c58a92ac5f9be0cfa556e4140c066d4d282782a63', 'Scan QRIS Dinamis m-BCA untuk tiket ekspedisi Kerenceng', 'disetujui', NULL, '1', '2026-11-07 21:42:11', '2026-11-07 14:38:52'),
('4', '10', NULL, 'BCA', 'Rendi Kusuma', '105210.00', '2026-11-07', '14:42:23', 'payment_proofs/transfer_bca_sample.jpg', 'ac3a2425ab416cabefc2f1b8d29b8b01e34d679e2e4a71f4f982d5f0867e6395', 'm-BCA donasi 3 pohon trembesi', 'menunggu', NULL, NULL, NULL, '2026-11-07 14:42:23'),
('5', '11', NULL, 'Bank Mandiri', 'Nadia Safitri', '105150.00', '2026-11-07', '14:42:23', 'payment_proofs/transfer_mandiri_sample.jpg', 'd4d83ca3b71b973c27c93a3a1805cb33b9ce2a734d3f4511a1e0adbdf8bb75ad', 'Transfer Mandiri untuk Kerenceng Anaerobic 200g', 'menunggu', NULL, NULL, NULL, '2026-11-07 14:42:23');

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_no` varchar(30) NOT NULL COMMENT 'Nomor invoice unik (INV-YYYYMMDD-NNNN)',
  `payable_type` enum('donation','order','participant') NOT NULL COMMENT 'Tipe entitas yang dibayar',
  `payable_id` int(10) unsigned NOT NULL COMMENT 'ID entitas terkait',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK user (null untuk tamu)',
  `guest_email` varchar(150) DEFAULT NULL COMMENT 'Email tamu jika belum login',
  `total_amount` decimal(15,2) NOT NULL COMMENT 'Total jumlah pembayaran',
  `unique_code` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'Kode unik 3 digit untuk verifikasi',
  `grand_total` decimal(15,2) NOT NULL COMMENT 'Total akhir (total_amount + unique_code)',
  `status` enum('menunggu_pembayaran','menunggu_verifikasi','berhasil','ditolak','kedaluwarsa','dibatalkan') NOT NULL DEFAULT 'menunggu_pembayaran' COMMENT 'Status pembayaran',
  `expires_at` datetime DEFAULT NULL COMMENT 'Batas waktu pembayaran',
  `paid_at` datetime DEFAULT NULL COMMENT 'Waktu pembayaran berhasil',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_payments_invoice_no` (`invoice_no`),
  KEY `idx_payments_payable` (`payable_type`,`payable_id`),
  KEY `idx_payments_user_id` (`user_id`),
  KEY `idx_payments_status` (`status`),
  KEY `idx_payments_expires_at` (`expires_at`),
  KEY `idx_payments_guest_email` (`guest_email`),
  CONSTRAINT `fk_payments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catatan pembayaran (donasi, pesanan, pendaftaran event)';

INSERT INTO `payments` (`id`, `invoice_no`, `payable_type`, `payable_id`, `user_id`, `guest_email`, `total_amount`, `unique_code`, `grand_total`, `status`, `expires_at`, `paid_at`, `created_at`, `updated_at`) VALUES
('1', 'INV-20261105-16D1', 'donation', '2', NULL, 'test@tanampohon.id', '150000.00', '521', '150521.00', 'berhasil', '2026-11-06 04:38:20', '2026-11-05 05:03:28', '2026-11-05 11:38:20', '2026-11-05 05:03:28'),
('2', 'INV-20261105-EAD4', 'donation', '3', NULL, 'alsmedia499@gmail.com', '175000.00', '222', '175222.00', 'kedaluwarsa', '2026-11-06 11:45:58', NULL, '2026-11-05 11:45:58', '2026-11-07 18:36:50'),
('4', 'INV-20261105-C7C5', 'order', '2', '1', NULL, '260000.00', '342', '260342.00', 'kedaluwarsa', '2026-11-06 04:50:18', NULL, '2026-11-05 11:50:18', '2026-11-07 18:36:50'),
('5', 'INV-20261105-A425', 'participant', '2', '1', NULL, '75000.00', '310', '75310.00', 'kedaluwarsa', '2026-11-06 16:49:51', NULL, '2026-11-05 16:49:51', '2026-11-07 18:36:50'),
('6', 'INV-20261107-D501', 'donation', '9', '1', NULL, '175000.00', '421', '175421.00', 'berhasil', '2026-11-08 14:37:47', '2026-11-07 21:42:04', '2026-11-07 14:37:47', '2026-11-07 21:42:04'),
('7', 'INV-20261107-K920', 'order', '3', '1', NULL, '215000.00', '380', '215380.00', 'berhasil', '2026-11-08 14:37:47', '2026-11-07 21:42:11', '2026-11-07 14:37:47', '2026-11-07 21:42:11'),
('9', 'INV-20261107-E019', 'participant', '5', '1', NULL, '75000.00', '250', '75250.00', 'berhasil', '2026-11-08 14:38:52', '2026-11-07 21:42:11', '2026-11-07 14:38:52', '2026-11-07 21:42:11'),
('10', 'INV-20261107-D882', 'donation', '11', '1', NULL, '105000.00', '210', '105210.00', 'menunggu_verifikasi', '2026-11-08 14:42:23', NULL, '2026-11-07 14:42:23', '2026-11-07 14:42:23'),
('11', 'INV-20261107-K950', 'order', '4', '1', NULL, '105000.00', '150', '105150.00', 'menunggu_verifikasi', '2026-11-08 14:42:23', NULL, '2026-11-07 14:42:23', '2026-11-07 14:42:23'),
('12', 'INV-20261102-A104', 'donation', '4', '1', NULL, '2500000.00', '114', '2500114.00', 'berhasil', '2026-11-03 09:15:00', '2026-11-02 10:30:00', '2026-11-02 09:15:00', '2026-11-02 10:30:00'),
('13', 'INV-20261103-B105', 'donation', '5', '1', NULL, '5000000.00', '205', '5000205.00', 'berhasil', '2026-11-04 14:20:00', '2026-11-03 15:45:00', '2026-11-03 14:20:00', '2026-11-03 15:45:00'),
('14', 'INV-20261104-C106', 'donation', '6', '1', NULL, '1200000.00', '318', '1200318.00', 'berhasil', '2026-11-05 10:00:00', '2026-11-04 11:10:00', '2026-11-04 10:00:00', '2026-11-04 11:10:00'),
('15', 'INV-20261104-D107', 'donation', '7', '1', NULL, '750000.00', '421', '750421.00', 'berhasil', '2026-11-05 16:45:00', '2026-11-04 17:20:00', '2026-11-04 16:45:00', '2026-11-04 17:20:00'),
('16', 'INV-20261107-2679', 'donation', '12', '1', NULL, '450000.00', '924', '450924.00', 'berhasil', '2026-11-08 22:00:38', '2026-11-07 22:00:38', '2026-11-07 22:00:38', '2026-11-07 22:00:38');

DROP TABLE IF EXISTS `planting_events`;
CREATE TABLE `planting_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT 'Judul event',
  `description` text DEFAULT NULL COMMENT 'Deskripsi event',
  `location_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke planting_locations',
  `event_date` date NOT NULL COMMENT 'Tanggal pelaksanaan',
  `gathering_time` time NOT NULL COMMENT 'Jam berkumpul',
  `gathering_point` varchar(255) NOT NULL COMMENT 'Titik kumpul',
  `google_maps_link` varchar(500) DEFAULT NULL COMMENT 'Link Google Maps titik kumpul',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `tree_species_info` text DEFAULT NULL COMMENT 'Info jenis pohon yang ditanam',
  `items_to_bring` text DEFAULT NULL COMMENT 'Barang yang perlu dibawa',
  `itinerary` text DEFAULT NULL,
  `coordinator_name` varchar(100) DEFAULT NULL COMMENT 'Nama koordinator',
  `coordinator_phone` varchar(20) DEFAULT NULL COMMENT 'Telepon koordinator',
  `quota` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Kuota peserta',
  `registered_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah peserta terdaftar',
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming' COMMENT 'Status event',
  `difficulty_level` enum('mudah','sedang','menantang') NOT NULL DEFAULT 'sedang',
  `elevation_masl` int(10) unsigned DEFAULT 1550,
  `category` varchar(60) NOT NULL DEFAULT 'Agroforestri Kopi',
  `cover_image` varchar(255) DEFAULT NULL,
  `registration_fee` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya pendaftaran (0 = gratis)',
  `includes_tshirt` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Termasuk kaos',
  `tshirt_included_note` varchar(255) DEFAULT NULL COMMENT 'Catatan kaos yang disertakan',
  `cancel_deadline` date DEFAULT NULL COMMENT 'Batas pembatalan pendaftaran',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_planting_events_location_id` (`location_id`),
  KEY `idx_planting_events_event_date` (`event_date`),
  KEY `idx_planting_events_status` (`status`),
  CONSTRAINT `fk_planting_events_location` FOREIGN KEY (`location_id`) REFERENCES `planting_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Event/kegiatan penanaman pohon';

INSERT INTO `planting_events` (`id`, `title`, `description`, `location_id`, `event_date`, `gathering_time`, `gathering_point`, `google_maps_link`, `latitude`, `longitude`, `tree_species_info`, `items_to_bring`, `itinerary`, `coordinator_name`, `coordinator_phone`, `quota`, `registered_count`, `status`, `difficulty_level`, `elevation_masl`, `category`, `cover_image`, `registration_fee`, `includes_tshirt`, `tshirt_included_note`, `cancel_deadline`, `created_at`, `updated_at`) VALUES
('1', 'Aksi Tanam 1.000 Mangrove Pesisir Indramayu', 'Gerakan restorasi pesisir pantai Indramayu untuk membentengi daratan dari abrasi dan mengembalikan ekosistem mangrove alami. Kegiatan terbuka untuk umum, mahasiswa, komunitas, dan keluarga.', '2', '2026-11-19', '07:30:00', 'Posko Konservasi Mangrove Pantai Karangsong Indramayu', 'https://maps.google.com/?q=-6.3300000,108.3200000', '-6.3300000', '108.3200000', 'Mangrove (Rhizophora mucronata & Avicennia marina)', 'Topi/pelindung matahari, sepatu boot/alas kaki tahan lumpur, botol minum pribadi (tumbler), pakaian ganti.', '07:00 - 07:30 | Registrasi Relawan & Pembagian Toolkit\n07:30 - 08:00 | Safety Briefing & Edukasi Ekosistem Pesisir\n08:00 - 10:30 | Aksi Penanaman 1.000 Bibit Mangrove di Zona Pasang Surut\n10:30 - 11:30 | Pembersihan Pesisir (Coastal Clean-Up)\n11:30 - 12:30 | Istirahat, Kelapa Muda & Ramah Tamah Petani Pesisir\n12:30 - 13:00 | Penyerahan Simbolis Sertifikat & Penutupan', 'Budi Raharjo', '081234567891', '100', '1', 'cancelled', 'mudah', '4', 'Restorasi Mangrove Pesisir', 'assets/images/lokasi/hutan-lindung-mangrove-pantai-utara.jpg', '0.00', '0', NULL, '2026-11-17', '2026-11-05 04:44:57', '2026-11-07 23:51:17'),
('2', 'Penghijauan Hutan Kota & Edukasi Lingkungan Tangsel', 'Aksi penanaman pohon peneduh (Trembesi & Mahoni) di kawasan ruang terbuka hijau Tangerang Selatan, dirangkai dengan workshop edukasi lingkungan hidup dan pembagian bibit pohon.', '1', '2026-11-26', '08:00:00', 'Amfiteater Hutan Kota Tangerang Selatan', 'https://maps.google.com/?q=-6.3200000,106.6800000', '-6.3200000', '106.6800000', 'Trembesi (Samanea saman) dan Mahoni (Swietenia mahagoni)', 'Pakaian olahraga nyaman, sarung tangan kebun, botol minum isi ulang.', '07:30 - 08:00 | Registrasi Ulang & Pengambilan Kaos Relawan\n08:00 - 08:45 | Workshop Edukasi Kanopi Kota & Krisis Iklim\n08:45 - 11:00 | Aksi Penanaman Pohon Peneduh (Trembesi & Mahoni)\n11:00 - 12:00 | Diskusi Interaktif Bersama Komunitas Peduli Lingkungan\n12:00 - 13:00 | Pembagian Bibit Bawa Pulang & Santap Siang Ramah Lingkungan', 'Siti Rahmawati', '081234567892', '60', '0', 'cancelled', 'mudah', '45', 'Hutan Kota & Edukasi', 'assets/images/lokasi/hutan-kota-dan-arboretum-babakan-siliwangi.jpg', '75000.00', '1', 'Termasuk kaos relawan eksklusif, snack, bibit pohon untuk dibawa pulang, dan e-ticket/sertifikat.', '2026-11-23', '2026-11-05 04:44:57', '2026-11-07 23:51:13'),
('3', 'Plant For Fun: Kerenceng Coffee Planting', 'Aksi penanaman pohon kopi dan pohon pelindung di lereng Gunung Kerenceng. Menghijaukan kembali kawasan tangkapan air sekaligus memperkuat ekonomi petani kopi lokal.', '3', '2026-11-29', '06:00:00', 'Kebon Kopi Kerenceng, Cicalengka, Kab. Bandung', 'https://maps.google.com/?q=-6.9833,107.8333', '-6.9833000', '107.8333000', 'Kopi Arabika Kerenceng & Pohon Pelindung Karsa', 'Sepatu bot / tracking, sarung tangan, botol minum, pakaian ganti', '05:30 - 06:15 | Kumpul di Basecamp andu Roastery & Registrasi Relawan\n06:15 - 07:00 | Briefing Jalur, Safety Induction & Doa Bersama\n07:00 - 08:30 | Trekking Ringan Menuju Zona Tanam Kebon Kopi (1.550 mdpl)\n08:30 - 11:00 | Aksi Tanam Pohon Kopi Arabika & Penaung Bersama Petani Karsa\n11:00 - 12:30 | Sesi Cupping Kopi Spesialti Kerenceng & Edukasi Pascapanen\n12:30 - 13:30 | Makan Siang Bersama Hasil Bumi Dapur Petani Kerenceng\n13:30 - 15:00 | Penurunan Kembali ke Basecamp & Pembagian Merchandise Eksklusif', 'Koordinator Kerenceng', '081234567890', '100', '18', 'upcoming', 'menantang', '1550', 'Agroforestri Gunung Kopi', 'assets/images/plant-for-fun-kerenceng.jpg', '0.00', '1', 'Termasuk Kaos Edisi Khusus Plant For Fun', NULL, '2026-11-07 18:43:26', '2026-11-08 01:40:58'),
('4', 'Ekspedisi Restorasi Sub-DAS Cimanuk Garut', 'Aksi penghijauan zona tangkapan air hulu sungai Cimanuk untuk mitigasi longsor dan erosi tanah. Kolaborasi lintas komunitas pecinta alam Jawa Barat.', '3', '2026-12-13', '06:30:00', 'Balai Konservasi Alam Tarogong Kaler, Kab. Garut', 'https://maps.google.com/?q=-7.2278,107.9087', '-7.2278000', '107.9087000', 'Bambu Betung, Beringin (Ficus benjamina), dan Aren (Arenga pinnata)', 'Jas hujan, sepatu trekking anti selip, botol minum, sarung tangan, obat pribadi.', '06:30 - 07:15 | Briefing di Balai Konservasi\n07:15 - 08:30 | Mobilitas Menuju Lereng Tangkapan Air\n08:30 - 11:30 | Penanaman Bibit Bambu & Akar Pengikat Tebing\n11:30 - 13:00 | Diskusi Konservasi DAS & Ishoma\n13:00 - 14:00 | Penutupan & Sertifikat Relawan', 'Kang Dedi Supriadi', '082122334455', '80', '34', 'cancelled', 'sedang', '1150', 'Konservasi Hulu DAS', 'assets/images/lokasi/hutan-konservasi-lereng-gunung-salak.jpg', '0.00', '0', NULL, '2026-12-10', '2026-11-07 15:34:16', '2026-11-07 23:51:08'),
('5', 'Ekspedisi Hijau Kerenceng Pioneer: Penanaman Perdana', 'Tonggak awal program Plant For Fun di lereng Gunung Kerenceng bersama 50 relawan perintis dan kelompok tani Karsa Kerentjeng. Berhasil menanam 1.200 pohon penaung pertama.', '3', '2026-10-18', '06:00:00', 'Kedai andu Roastery, Cicalengka', 'https://maps.google.com/?q=-6.9833,107.8333', '-6.9833000', '107.8333000', 'Kopi Typica & Bibit Suren Penghijauan', 'Pakaian lapangan, sepatu boot, botol minum.', '06:00 - 07:00 | Kumpul Relawan Perintis\n07:00 - 11:00 | Penanaman 1.200 Pohon di Blok 1\n11:00 - 13:00 | Syukuran Panen & Coffee Sharing Session', 'Ahmad Fauzi (andu)', '081234567890', '50', '50', 'cancelled', 'sedang', '1450', 'Agroforestri Gunung Kopi', 'assets/images/plant-for-fun-kerenceng.jpg', '0.00', '1', 'Kaos Pioneer Edition Plant For Fun', '2026-10-15', '2026-10-01 08:00:00', '2026-11-08 01:40:58');

DROP TABLE IF EXISTS `planting_locations`;
CREATE TABLE `planting_locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL COMMENT 'Nama lokasi',
  `description` text DEFAULT NULL COMMENT 'Deskripsi lokasi',
  `province` varchar(100) NOT NULL COMMENT 'Provinsi',
  `city` varchar(100) NOT NULL COMMENT 'Kota/Kabupaten',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT 'Koordinat lintang',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT 'Koordinat bujur',
  `total_trees_target` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Target jumlah pohon',
  `total_trees_planted` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah pohon tertanam',
  `photo` varchar(255) DEFAULT NULL COMMENT 'Foto lokasi',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_planting_locations_is_active` (`is_active`),
  KEY `idx_planting_locations_province` (`province`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lokasi penanaman pohon';

INSERT INTO `planting_locations` (`id`, `name`, `description`, `province`, `city`, `latitude`, `longitude`, `total_trees_target`, `total_trees_planted`, `photo`, `is_active`, `created_at`, `updated_at`) VALUES
('1', 'Hutan Kota Tangerang Selatan', 'Area penghijauan di kawasan hutan kota Tangerang Selatan yang sedang dikembangkan sebagai ruang terbuka hijau.', 'Banten', 'Tangerang Selatan', '-6.3200000', '106.6800000', '5000', '7', NULL, '1', '2026-11-05 04:33:44', '2026-11-07 18:27:38'),
('2', 'Pesisir Pantai Indramayu', 'Kawasan pesisir pantai Indramayu yang memerlukan rehabilitasi mangrove untuk mencegah abrasi pantai.', 'Jawa Barat', 'Indramayu', '-6.3300000', '108.3200000', '10000', '3', NULL, '1', '2026-11-05 04:33:44', '2026-11-07 18:27:38'),
('3', 'Kebon Kopi Kerenceng', 'Kawasan agroforestri lereng Gunung Kerenceng Cicalengka untuk konservasi pohon kopi dan pelestarian mata air.', 'Jawa Barat', 'Kabupaten Bandung', '-6.9833000', '107.8333000', '3000', '150', NULL, '1', '2026-11-07 18:43:26', '2026-11-07 18:43:26');

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE `product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel products',
  `image_path` varchar(255) NOT NULL COMMENT 'Path file gambar',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Gambar utama',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_product_images_product_id` (`product_id`),
  CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Galeri gambar produk';

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `sort_order`, `is_primary`, `created_at`) VALUES
('1', '4', 'assets/images/coffee-honey.jpg', '1', '1', '2026-11-07 13:00:34'),
('2', '5', 'assets/images/coffee-anaerobic.jpg', '1', '1', '2026-11-07 13:00:34'),
('3', '6', 'assets/images/coffee-drip-bag.jpg', '1', '1', '2026-11-07 13:00:34'),
('4', '7', 'assets/images/coffee-cold-brew.jpg', '1', '1', '2026-11-07 13:00:34'),
('5', '1', 'assets/images/kaos-plant-for-fun.jpg', '1', '1', '2026-11-08 00:00:00'),
('6', '3', 'assets/images/kaos-plant-for-fun.jpg', '1', '1', '2026-11-08 00:00:00');

DROP TABLE IF EXISTS `product_variants`;
CREATE TABLE `product_variants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel products',
  `size` varchar(50) DEFAULT NULL COMMENT 'Ukuran / Netto / Kemasan',
  `color` varchar(50) DEFAULT NULL COMMENT 'Nama warna',
  `color_code` varchar(7) DEFAULT NULL COMMENT 'Kode warna hex (#FFFFFF)',
  `sku` varchar(50) DEFAULT NULL COMMENT 'Stock Keeping Unit',
  `price` decimal(12,2) NOT NULL COMMENT 'Harga varian',
  `stock` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Stok tersedia',
  `reserved_stock` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Stok dipesan (belum dibayar)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_product_variants_sku` (`sku`),
  KEY `idx_product_variants_product_id` (`product_id`),
  KEY `idx_product_variants_is_active` (`is_active`),
  CONSTRAINT `fk_product_variants_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Varian produk (ukuran, warna)';

INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `color_code`, `sku`, `price`, `stock`, `reserved_stock`, `is_active`, `created_at`) VALUES
('1', '1', 'S', 'Forest Green', '#1b4332', 'TP-PJG-GRN-S', '125000.00', '22', '2', '1', '2026-11-05 04:44:57'),
('2', '1', 'M', 'Forest Green', '#1b4332', 'TP-PJG-GRN-M', '125000.00', '30', '0', '1', '2026-11-05 04:44:57'),
('3', '1', 'L', 'Forest Green', '#1b4332', 'TP-PJG-GRN-L', '125000.00', '35', '0', '1', '2026-11-05 04:44:57'),
('4', '1', 'XL', 'Forest Green', '#1b4332', 'TP-PJG-GRN-XL', '125000.00', '25', '0', '1', '2026-11-05 04:44:57'),
('5', '1', 'XXL', 'Forest Green', '#1b4332', 'TP-PJG-GRN-XXL', '135000.00', '15', '0', '1', '2026-11-05 04:44:57'),
('11', '3', 'M', 'Sage Green', '#52796f', 'TP-BND-SGE-M', '210000.00', '20', '0', '1', '2026-11-05 04:44:57'),
('12', '3', 'L', 'Sage Green', '#52796f', 'TP-BND-SGE-L', '210000.00', '25', '0', '1', '2026-11-05 04:44:57'),
('13', '3', 'XL', 'Sage Green', '#52796f', 'TP-BND-SGE-XL', '210000.00', '15', '0', '1', '2026-11-05 04:44:57'),
('14', '4', '200gr', 'Biji Utuh (Whole Bean)', NULL, 'ANDU-KRC-HNY-WB200', '88000.00', '50', '0', '1', '2026-11-07 12:49:25'),
('15', '4', '200gr', 'Giling Filter (Medium)', NULL, 'ANDU-KRC-HNY-MED200', '88000.00', '40', '0', '1', '2026-11-07 12:49:25'),
('16', '4', '200gr', 'Giling Halus (Espresso)', NULL, 'ANDU-KRC-HNY-FIN200', '88000.00', '30', '0', '1', '2026-11-07 12:49:25'),
('17', '4', '500gr', 'Biji Utuh (Pouch Hemat)', NULL, 'ANDU-KRC-HNY-WB500', '195000.00', '20', '0', '1', '2026-11-07 12:49:25'),
('18', '5', '200gr', 'Biji Utuh (Whole Bean)', NULL, 'ANDU-KRC-ANR-WB200', '115000.00', '35', '0', '1', '2026-11-07 12:49:25'),
('19', '5', '200gr', 'Giling Filter (Medium)', NULL, 'ANDU-KRC-ANR-MED200', '115000.00', '25', '0', '1', '2026-11-07 12:49:25'),
('20', '5', '500gr', 'Biji Utuh (Pouch Hemat)', NULL, 'ANDU-KRC-ANR-WB500', '255000.00', '15', '0', '1', '2026-11-07 12:49:25'),
('21', '6', 'Box 10x', 'Original Trail Edition', NULL, 'ANDU-DRIP-BOX10', '75000.00', '60', '0', '1', '2026-11-07 12:49:25'),
('22', '6', 'Bundle 2x', 'Dua Box (20 Sachet)', NULL, 'ANDU-DRIP-BND20', '139000.00', '30', '0', '1', '2026-11-07 12:49:25'),
('23', '7', '500ml', 'Botol Kaca Ekstraksi Dingin', NULL, 'ANDU-CB-500ML', '65000.00', '45', '0', '1', '2026-11-07 12:49:25'),
('24', '7', '1 Liter', 'Dua Botol (Paket Hemat)', NULL, 'ANDU-CB-1L', '120000.00', '25', '0', '1', '2026-11-07 12:49:25');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL COMMENT 'Nama produk',
  `slug` varchar(170) NOT NULL COMMENT 'Slug URL-friendly',
  `description` text DEFAULT NULL COMMENT 'Deskripsi produk',
  `category` varchar(50) DEFAULT NULL COMMENT 'Kategori produk',
  `base_price` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga dasar',
  `weight_gram` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Berat produk (gram)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_products_slug` (`slug`),
  KEY `idx_products_category` (`category`),
  KEY `idx_products_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Produk merchandise';

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `category`, `base_price`, `weight_gram`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
('1', 'Kaos Organik TanamPohon - Edisi Penjaga Bumi', 'kaos-organik-penjaga-bumi', 'Kaos merchandise resmi TanamPohon.id berbahan 100% Organic Cotton Combed 30s berkualitas tinggi. Lembut, sejuk dipakai, dan ramah lingkungan dengan sablon plastisol berbasis air (water-based ink). Sebagian keuntungan penjualan dialokasikan langsung untuk mendanai program penanaman pohon di seluruh Indonesia.', 'kaos', '125000.00', '220', '0', '1', '2026-11-05 04:44:57', '2026-11-08 02:14:21'),
('3', 'Bundle Spesial: Kaos + Donasi 2 Pohon Mangga', 'bundle-kaos-donasi-2-pohon', 'Paket bundle eksklusif: Dapatkan 1 Kaos TanamPohon edisi pilihan plus Anda otomatis berdonasi 2 batang pohon buah Mangga yang ditanam atas nama Anda lengkap dengan sertifikat digital dan koordinat pohon di peta.', 'bundle', '210000.00', '250', '1', '3', '2026-11-05 04:44:57', '2026-11-05 04:44:57'),
('4', 'Arabica Kerenceng Honey Process x andu', 'arabica-kerenceng-honey-an-du', 'Biji kopi Arabika spesialti hasil kurasi andu Roastery bersama kelompok tani Karsa Kerentjeng. Ditanam pada ketinggian 1.450 - 1.700 mdpl di lereng Gunung Kerenceng dengan naungan pohon hutan agroforestri. Proses Honey menghasilkan cita rasa manis madu alami yang kompleks dengan keasaman segar buah-buahan.\n\nOrigin: Kebon Kopi Kerenceng, Cicalengka, Jawa Barat\nVarietas: Sigarar Utang & Kartika\nElevasi: 1.550 mdpl\nProses: Honey Process (Semi-Washed)\nTasting Notes: Red Apple, Wild Honey, Jasmine Floral, Black Tea Finish\nRoast Level: Medium Light (Filter Roast by andu)', 'kopi', '88000.00', '220', '1', '1', '2026-11-07 12:49:05', '2026-11-08 01:40:58'),
('5', 'Kerenceng Natural Anaerobic Reserve x andu', 'kerenceng-natural-anaerobic-an-du', 'Edisi micro-lot terbatas kolaborasi eksklusif andu. Biji kopi dipetik merah sempurna (red cherry 100%) dan difermentasi anaerobik selama 72 jam di dalam tabung kedap udara sebelum dijemur perlahan di atas raised beds pegunungan. Menghasilkan body yang tebal, aroma fermentasi elegan, dan aftertaste panjang.\n\nOrigin: Lereng Timur Gunung Kerenceng\nVarietas: Typica & Ateng Super\nElevasi: 1.650 mdpl\nProses: Natural Anaerobic 72 Jam\nTasting Notes: Dark Cherry, Winey Plum, Dark Chocolate, Molasses, Silky Body\nRoast Level: Medium (Omni-Roast by andu)', 'kopi', '115000.00', '220', '1', '2', '2026-11-07 12:49:05', '2026-11-08 01:40:58'),
('6', 'Drip Bag Coffee Box (Isi 10 Sachet) - andu Trail Edition', 'drip-bag-kerenceng-an-du-trail', 'Kopi filter praktis single-serve drip bag dirancang khusus oleh andu untuk para petualang, pendaki, dan pecinta alam. Seduh kopi spesialti Kerenceng di mana saja — di puncak gunung, saat camping, atau di meja kerja Anda hanya dengan air panas. Setiap box berisi 10 sachet nitrogen-flushed untuk kesegaran maksimal.\n\nIsi: 10 sachet x 12g bubuk kopi spesialti\nOrigin: Arabica Kebon Kopi Kerenceng\nRoast by: andu Coffee Lab\nTasting Notes: Brown Sugar, Caramel, Hint of Sweet Orange', 'kopi', '75000.00', '180', '1', '3', '2026-11-07 12:49:05', '2026-11-08 01:40:58'),
('7', 'Cold Brew Concentrate 500ml - andu Botanic Brew', 'cold-brew-concentrate-an-du', 'Konsentrat cold brew artisanal diekstraksi dingin selama 18 jam menggunakan air pegunungan murni dan biji kopi Arabika Kerenceng hasil panen hutan lestari. Cukup campurkan dengan susu atau air dingin + es batu untuk menikmati signature iced coffee andu di rumah.\n\nVolume: 500ml (Dapat disajikan untuk 4-5 gelas)\nKomposisi: 100% Ekstraksi Kopi Arabika Kerenceng & Air Pegunungan\nTanpa Pengawet & Gula Tambahan\nTahan 14 hari di dalam lemari pendingin', 'kopi', '65000.00', '550', '1', '4', '2026-11-07 12:49:05', '2026-11-08 01:40:58');

DROP TABLE IF EXISTS `referrals`;
CREATE TABLE `referrals` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `referrer_id` int(10) unsigned NOT NULL COMMENT 'FK user yang mengajak',
  `referred_id` int(10) unsigned NOT NULL COMMENT 'FK user yang diajak',
  `bonus_points` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Poin bonus yang diberikan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_referrals_referred` (`referred_id`),
  KEY `idx_referrals_referrer_id` (`referrer_id`),
  CONSTRAINT `fk_referrals_referred` FOREIGN KEY (`referred_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_referrals_referrer` FOREIGN KEY (`referrer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Riwayat referral antar pengguna';

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT 'Nama peran (super_admin, admin_keuangan, dll)',
  `slug` varchar(50) NOT NULL COMMENT 'Slug URL-friendly',
  `description` varchar(255) DEFAULT NULL COMMENT 'Deskripsi peran',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roles_name` (`name`),
  UNIQUE KEY `uk_roles_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Daftar peran pengguna';

INSERT INTO `roles` (`id`, `name`, `slug`, `description`) VALUES
('1', 'super_admin', 'super-admin', 'Administrator utama dengan akses penuh'),
('2', 'admin_keuangan', 'admin-keuangan', 'Admin yang mengelola keuangan dan verifikasi pembayaran'),
('3', 'admin_event', 'admin-event', 'Admin yang mengelola event penanaman'),
('4', 'admin_konten', 'admin-konten', 'Admin yang mengelola konten, berita, dan galeri'),
('5', 'peserta', 'peserta', 'Pengguna biasa / peserta');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL COMMENT 'Kunci pengaturan',
  `value` text DEFAULT NULL COMMENT 'Nilai pengaturan',
  `group` varchar(50) NOT NULL DEFAULT 'general' COMMENT 'Grup pengaturan',
  `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'Tipe input (text, number, textarea, boolean, file)',
  `description` varchar(255) DEFAULT NULL COMMENT 'Deskripsi pengaturan',
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_settings_key` (`key`),
  KEY `idx_settings_group` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pengaturan umum situs';

INSERT INTO `settings` (`id`, `key`, `value`, `group`, `type`, `description`, `updated_at`) VALUES
('1', 'site_name', 'TanamPohon.id', 'general', 'text', 'Nama situs', '2026-11-05 04:33:44'),
('2', 'site_tagline', 'Tanam Pohon, Hijaukan Bumi Indonesia', 'general', 'text', 'Tagline situs', '2026-11-05 04:33:44'),
('3', 'site_description', 'Platform donasi pohon, relawan penanaman, dan merchandise untuk lingkungan Indonesia yang lebih hijau.', 'general', 'textarea', 'Deskripsi situs untuk SEO', '2026-11-05 04:33:44'),
('4', 'site_email', 'info@tanampohon.id', 'general', 'text', 'Email kontak utama', '2026-11-05 04:33:44'),
('5', 'site_phone', '0812-0000-0001', 'general', 'text', 'Nomor telepon kontak', '2026-11-05 04:33:44'),
('6', 'site_whatsapp', '6281200000001', 'general', 'text', 'Nomor WhatsApp (format internasional)', '2026-11-05 04:33:44'),
('7', 'site_address', 'Jl. Hijau Daun No. 1, Jakarta Selatan, DKI Jakarta', 'general', 'textarea', 'Alamat kantor', '2026-11-05 04:33:44'),
('8', 'site_logo', NULL, 'general', 'file', 'Logo situs', '2026-11-05 04:33:44'),
('9', 'site_favicon', NULL, 'general', 'file', 'Favicon situs', '2026-11-05 04:33:44'),
('10', 'payment_deadline_hours', '24', 'payment', 'number', 'Batas waktu pembayaran (jam)', '2026-11-05 04:33:44'),
('11', 'unique_code_enabled', '1', 'payment', 'boolean', 'Aktifkan kode unik pembayaran', '2026-11-05 04:33:44'),
('12', 'unique_code_min', '100', 'payment', 'number', 'Kode unik minimum', '2026-11-05 04:33:44'),
('13', 'unique_code_max', '999', 'payment', 'number', 'Kode unik maksimum', '2026-11-05 04:33:44'),
('14', 'price_per_tree', '50000', 'donation', 'number', 'Harga default per pohon (Rupiah)', '2026-11-05 04:33:44'),
('15', 'min_trees_per_donation', '1', 'donation', 'number', 'Minimum pohon per donasi', '2026-11-05 04:33:44'),
('16', 'max_trees_per_donation', '1000', 'donation', 'number', 'Maksimum pohon per donasi', '2026-11-05 04:33:44'),
('17', 'co2_formula', 'total_trees * species_co2_absorption', 'donation', 'text', 'Rumus perhitungan serapan CO2', '2026-11-05 04:33:44'),
('18', 'referral_bonus_points', '10', 'referral', 'number', 'Poin bonus untuk pengajak', '2026-11-05 04:33:44'),
('19', 'referred_bonus_points', '5', 'referral', 'number', 'Poin bonus untuk yang diajak', '2026-11-05 04:33:44'),
('20', 'points_per_tree', '5', 'referral', 'number', 'Poin per pohon yang didonasikan', '2026-11-05 04:33:44'),
('21', 'points_per_event', '15', 'referral', 'number', 'Poin per kehadiran event', '2026-11-05 04:33:44'),
('22', 'social_instagram', 'https://instagram.com/tanampohon.id', 'social', 'text', 'URL Instagram', '2026-11-05 04:33:44'),
('23', 'social_facebook', 'https://facebook.com/tanampohon.id', 'social', 'text', 'URL Facebook', '2026-11-05 04:33:44'),
('24', 'social_twitter', 'https://twitter.com/tanampohon_id', 'social', 'text', 'URL Twitter/X', '2026-11-05 04:33:44'),
('25', 'social_youtube', 'https://youtube.com/@tanampohon', 'social', 'text', 'URL YouTube', '2026-11-05 04:33:44'),
('26', 'social_tiktok', 'https://tiktok.com/@tanampohon.id', 'social', 'text', 'URL TikTok', '2026-11-05 04:33:44'),
('27', 'certificate_signer_name', 'Ahmad Hijau', 'certificate', 'text', 'Nama penanda tangan sertifikat', '2026-11-05 04:33:44'),
('28', 'certificate_signer_title', 'Koordinator Ekspedisi Plant For Fun', 'certificate', 'text', 'Jabatan penanda tangan sertifikat', '2026-11-05 04:33:44'),
('29', 'certificate_signature_image', NULL, 'certificate', 'file', 'Gambar tanda tangan sertifikat', '2026-11-05 04:33:44'),
('30', 'last_cron_run', '2026-11-07 18:42:06', 'system', 'text', NULL, '2026-11-07 18:42:06'),
('34', 'fonnte_token', '', 'whatsapp', 'text', 'Fonnte fonnte_token', '2026-11-07 19:27:13'),
('35', 'fonnte_enabled', '0', 'whatsapp', 'text', 'Fonnte fonnte_enabled', '2026-11-07 19:27:13'),
('36', 'fonnte_sender', '', 'whatsapp', 'text', 'Fonnte fonnte_sender', '2026-11-07 19:27:13');

DROP TABLE IF EXISTS `shipments`;
CREATE TABLE `shipments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel orders',
  `courier` varchar(100) NOT NULL COMMENT 'Nama kurir (JNE, JNT, SiCepat, dll)',
  `service` varchar(150) DEFAULT NULL COMMENT 'Layanan kurir (REG, YES, dll)',
  `tracking_number` varchar(100) DEFAULT NULL COMMENT 'Nomor resi',
  `shipped_at` datetime DEFAULT NULL COMMENT 'Waktu pengiriman',
  `delivered_at` datetime DEFAULT NULL COMMENT 'Waktu diterima',
  `status` enum('diproses','dikirim','transit','diterima','gagal') NOT NULL DEFAULT 'diproses' COMMENT 'Status pengiriman',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_shipments_order_id` (`order_id`),
  KEY `idx_shipments_tracking_number` (`tracking_number`),
  KEY `idx_shipments_status` (`status`),
  CONSTRAINT `fk_shipments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data pengiriman pesanan';

INSERT INTO `shipments` (`id`, `order_id`, `courier`, `service`, `tracking_number`, `shipped_at`, `delivered_at`, `status`, `created_at`, `updated_at`) VALUES
('1', '2', 'JNE', 'REG (Reguler)', NULL, NULL, NULL, 'diproses', '2026-11-05 11:50:18', '2026-11-05 11:50:18');

DROP TABLE IF EXISTS `shipping_cities`;
CREATE TABLE `shipping_cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `province` varchar(100) NOT NULL,
  `city_name` varchar(150) NOT NULL,
  `city_type` enum('Kota','Kabupaten') NOT NULL DEFAULT 'Kabupaten',
  `zone_id` int(10) unsigned NOT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_city_search` (`city_name`),
  KEY `idx_province` (`province`),
  KEY `zone_id` (`zone_id`),
  CONSTRAINT `shipping_cities_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=306 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shipping_cities` (`id`, `province`, `city_name`, `city_type`, `zone_id`, `postal_code`, `created_at`) VALUES
('1', 'Jawa Barat', 'Kabupaten Bandung (Cicalengka/Soreang)', 'Kabupaten', '1', '40395', '2026-11-07 13:25:38'),
('2', 'Jawa Barat', 'Kota Bandung', 'Kota', '1', '40111', '2026-11-07 13:25:38'),
('3', 'Jawa Barat', 'Kota Cimahi', 'Kota', '1', '40511', '2026-11-07 13:25:38'),
('4', 'Jawa Barat', 'Kabupaten Bandung Barat (Padalarang/Lembang)', 'Kabupaten', '1', '40553', '2026-11-07 13:25:38'),
('5', 'Jawa Barat', 'Kabupaten Sumedang (Jatinangor/Tanjungsari)', 'Kabupaten', '1', '45311', '2026-11-07 13:25:38'),
('6', 'Jawa Barat', 'Kabupaten Garut (Tarogong/Kadungora)', 'Kabupaten', '1', '44111', '2026-11-07 13:25:38'),
('7', 'DKI Jakarta', 'Jakarta Pusat', 'Kota', '2', '10110', '2026-11-07 13:25:38'),
('8', 'DKI Jakarta', 'Jakarta Selatan', 'Kota', '2', '12110', '2026-11-07 13:25:38'),
('9', 'DKI Jakarta', 'Jakarta Barat', 'Kota', '2', '11110', '2026-11-07 13:25:38'),
('10', 'DKI Jakarta', 'Jakarta Timur', 'Kota', '2', '13110', '2026-11-07 13:25:38'),
('11', 'DKI Jakarta', 'Jakarta Utara', 'Kota', '2', '14110', '2026-11-07 13:25:38'),
('12', 'DKI Jakarta', 'Kepulauan Seribu', 'Kabupaten', '2', '14510', '2026-11-07 13:25:38'),
('13', 'Jawa Barat', 'Kota Bogor', 'Kota', '2', '16111', '2026-11-07 13:25:38'),
('14', 'Jawa Barat', 'Kabupaten Bogor (Cibinong/Ciawi)', 'Kabupaten', '2', '16911', '2026-11-07 13:25:38'),
('15', 'Jawa Barat', 'Kota Depok', 'Kota', '2', '16411', '2026-11-07 13:25:38'),
('16', 'Jawa Barat', 'Kota Bekasi', 'Kota', '2', '17111', '2026-11-07 13:25:38'),
('17', 'Jawa Barat', 'Kabupaten Bekasi (Cikarang)', 'Kabupaten', '2', '17530', '2026-11-07 13:25:38'),
('18', 'Banten', 'Kota Tangerang', 'Kota', '2', '15111', '2026-11-07 13:25:38'),
('19', 'Banten', 'Kota Tangerang Selatan (BSD/Bintaro)', 'Kota', '2', '15411', '2026-11-07 13:25:38'),
('20', 'Banten', 'Kabupaten Tangerang (Tigaraksa)', 'Kabupaten', '2', '15710', '2026-11-07 13:25:38'),
('21', 'Banten', 'Kota Serang', 'Kota', '2', '42111', '2026-11-07 13:25:38'),
('22', 'Banten', 'Kota Cilegon', 'Kota', '2', '42411', '2026-11-07 13:25:38'),
('23', 'Banten', 'Kabupaten Serang', 'Kabupaten', '2', '42100', '2026-11-07 13:25:38'),
('24', 'Banten', 'Kabupaten Pandeglang', 'Kabupaten', '2', '42211', '2026-11-07 13:25:38'),
('25', 'Banten', 'Kabupaten Lebak (Rangkasbitung)', 'Kabupaten', '2', '42311', '2026-11-07 13:25:38'),
('26', 'Jawa Barat', 'Kabupaten Karawang', 'Kabupaten', '3', '41311', '2026-11-07 13:25:38'),
('27', 'Jawa Barat', 'Kabupaten Purwakarta', 'Kabupaten', '3', '41111', '2026-11-07 13:25:38'),
('28', 'Jawa Barat', 'Kabupaten Subang', 'Kabupaten', '3', '41211', '2026-11-07 13:25:38'),
('29', 'Jawa Barat', 'Kota Sukabumi', 'Kota', '3', '43111', '2026-11-07 13:25:38'),
('30', 'Jawa Barat', 'Kabupaten Sukabumi (Palabuhanratu)', 'Kabupaten', '3', '43311', '2026-11-07 13:25:38'),
('31', 'Jawa Barat', 'Kabupaten Cianjur (Cipanas/Puncak)', 'Kabupaten', '3', '43211', '2026-11-07 13:25:38'),
('32', 'Jawa Barat', 'Kota Cirebon', 'Kota', '3', '45111', '2026-11-07 13:25:38'),
('33', 'Jawa Barat', 'Kabupaten Cirebon (Sumber)', 'Kabupaten', '3', '45611', '2026-11-07 13:25:38'),
('34', 'Jawa Barat', 'Kabupaten Indramayu', 'Kabupaten', '3', '45211', '2026-11-07 13:25:38'),
('35', 'Jawa Barat', 'Kabupaten Majalengka', 'Kabupaten', '3', '45411', '2026-11-07 13:25:38'),
('36', 'Jawa Barat', 'Kabupaten Kuningan', 'Kabupaten', '3', '45511', '2026-11-07 13:25:38'),
('37', 'Jawa Barat', 'Kota Tasikmalaya', 'Kota', '3', '46111', '2026-11-07 13:25:38'),
('38', 'Jawa Barat', 'Kabupaten Tasikmalaya (Singaparna)', 'Kabupaten', '3', '46411', '2026-11-07 13:25:38'),
('39', 'Jawa Barat', 'Kabupaten Ciamis', 'Kabupaten', '3', '46211', '2026-11-07 13:25:38'),
('40', 'Jawa Barat', 'Kota Banjar', 'Kota', '3', '46311', '2026-11-07 13:25:38'),
('41', 'Jawa Barat', 'Kabupaten Pangandaran', 'Kabupaten', '3', '46396', '2026-11-07 13:25:38'),
('42', 'DI Yogyakarta', 'Kota Yogyakarta', 'Kota', '4', '55111', '2026-11-07 13:25:38'),
('43', 'DI Yogyakarta', 'Kabupaten Sleman', 'Kabupaten', '4', '55511', '2026-11-07 13:25:38'),
('44', 'DI Yogyakarta', 'Kabupaten Bantul', 'Kabupaten', '4', '55711', '2026-11-07 13:25:38'),
('45', 'DI Yogyakarta', 'Kabupaten Kulon Progo (Wates/YIA)', 'Kabupaten', '4', '55611', '2026-11-07 13:25:38'),
('46', 'DI Yogyakarta', 'Kabupaten Gunungkidul (Wonosari)', 'Kabupaten', '4', '55811', '2026-11-07 13:25:38'),
('47', 'Jawa Tengah', 'Kota Semarang', 'Kota', '4', '50111', '2026-11-07 13:25:38'),
('48', 'Jawa Tengah', 'Kota Surakarta (Solo)', 'Kota', '4', '57111', '2026-11-07 13:25:38'),
('49', 'Jawa Tengah', 'Kota Magelang', 'Kota', '4', '56111', '2026-11-07 13:25:38'),
('50', 'Jawa Tengah', 'Kota Salatiga', 'Kota', '4', '50711', '2026-11-07 13:25:38'),
('51', 'Jawa Tengah', 'Kota Pekalongan', 'Kota', '4', '51111', '2026-11-07 13:25:38'),
('52', 'Jawa Tengah', 'Kota Tegal', 'Kota', '4', '52111', '2026-11-07 13:25:38'),
('53', 'Jawa Tengah', 'Kabupaten Banyumas (Purwokerto)', 'Kabupaten', '4', '53111', '2026-11-07 13:25:38'),
('54', 'Jawa Tengah', 'Kabupaten Cilacap', 'Kabupaten', '4', '53211', '2026-11-07 13:25:38'),
('55', 'Jawa Tengah', 'Kabupaten Kudus', 'Kabupaten', '4', '59311', '2026-11-07 13:25:38'),
('56', 'Jawa Tengah', 'Kabupaten Jepara', 'Kabupaten', '4', '59411', '2026-11-07 13:25:38'),
('57', 'Jawa Tengah', 'Kabupaten Klaten', 'Kabupaten', '4', '57411', '2026-11-07 13:25:38'),
('58', 'Jawa Tengah', 'Kabupaten Sukoharjo', 'Kabupaten', '4', '57511', '2026-11-07 13:25:38'),
('59', 'Jawa Tengah', 'Kabupaten Boyolali', 'Kabupaten', '4', '57311', '2026-11-07 13:25:38'),
('60', 'Jawa Tengah', 'Kabupaten Karanganyar', 'Kabupaten', '4', '57711', '2026-11-07 13:25:38'),
('61', 'Jawa Tengah', 'Kabupaten Wonogiri', 'Kabupaten', '4', '57611', '2026-11-07 13:25:38'),
('62', 'Jawa Tengah', 'Kabupaten Sragen', 'Kabupaten', '4', '57211', '2026-11-07 13:25:38'),
('63', 'Jawa Tengah', 'Kabupaten Semarang (Ungaran)', 'Kabupaten', '4', '50511', '2026-11-07 13:25:38'),
('64', 'Jawa Tengah', 'Kabupaten Kendal', 'Kabupaten', '4', '51311', '2026-11-07 13:25:38'),
('65', 'Jawa Tengah', 'Kabupaten Demak', 'Kabupaten', '4', '59511', '2026-11-07 13:25:38'),
('66', 'Jawa Tengah', 'Kabupaten Grobogan (Purwodadi)', 'Kabupaten', '4', '58111', '2026-11-07 13:25:38'),
('67', 'Jawa Tengah', 'Kabupaten Pati', 'Kabupaten', '4', '59111', '2026-11-07 13:25:38'),
('68', 'Jawa Tengah', 'Kabupaten Rembang', 'Kabupaten', '4', '59211', '2026-11-07 13:25:38'),
('69', 'Jawa Tengah', 'Kabupaten Blora', 'Kabupaten', '4', '58211', '2026-11-07 13:25:38'),
('70', 'Jawa Tengah', 'Kabupaten Brebes', 'Kabupaten', '4', '52211', '2026-11-07 13:25:38'),
('71', 'Jawa Tengah', 'Kabupaten Pemalang', 'Kabupaten', '4', '52311', '2026-11-07 13:25:38'),
('72', 'Jawa Tengah', 'Kabupaten Batang', 'Kabupaten', '4', '51211', '2026-11-07 13:25:38'),
('73', 'Jawa Tengah', 'Kabupaten Pekalongan (Kajen)', 'Kabupaten', '4', '51161', '2026-11-07 13:25:38'),
('74', 'Jawa Tengah', 'Kabupaten Tegal (Slawi)', 'Kabupaten', '4', '52411', '2026-11-07 13:25:38'),
('75', 'Jawa Tengah', 'Kabupaten Purbalingga', 'Kabupaten', '4', '53311', '2026-11-07 13:25:38'),
('76', 'Jawa Tengah', 'Kabupaten Banjarnegara', 'Kabupaten', '4', '53411', '2026-11-07 13:25:38'),
('77', 'Jawa Tengah', 'Kabupaten Kebumen', 'Kabupaten', '4', '54311', '2026-11-07 13:25:38'),
('78', 'Jawa Tengah', 'Kabupaten Purworejo', 'Kabupaten', '4', '54111', '2026-11-07 13:25:38'),
('79', 'Jawa Tengah', 'Kabupaten Wonosobo (Dieng)', 'Kabupaten', '4', '56311', '2026-11-07 13:25:38'),
('80', 'Jawa Tengah', 'Kabupaten Temanggung', 'Kabupaten', '4', '56211', '2026-11-07 13:25:38'),
('81', 'Jawa Tengah', 'Kabupaten Magelang (Mungkid/Borobudur)', 'Kabupaten', '4', '56511', '2026-11-07 13:25:38'),
('82', 'Jawa Timur', 'Kota Surabaya', 'Kota', '5', '60111', '2026-11-07 13:25:38'),
('83', 'Jawa Timur', 'Kota Malang', 'Kota', '5', '65111', '2026-11-07 13:25:38'),
('84', 'Jawa Timur', 'Kota Batu', 'Kota', '5', '65311', '2026-11-07 13:25:38'),
('85', 'Jawa Timur', 'Kabupaten Sidoarjo', 'Kabupaten', '5', '61211', '2026-11-07 13:25:38'),
('86', 'Jawa Timur', 'Kabupaten Gresik', 'Kabupaten', '5', '61111', '2026-11-07 13:25:38'),
('87', 'Jawa Timur', 'Kota Kediri', 'Kota', '5', '64111', '2026-11-07 13:25:38'),
('88', 'Jawa Timur', 'Kabupaten Kediri (Pare)', 'Kabupaten', '5', '64184', '2026-11-07 13:25:38'),
('89', 'Jawa Timur', 'Kota Madiun', 'Kota', '5', '63111', '2026-11-07 13:25:38'),
('90', 'Jawa Timur', 'Kabupaten Madiun (Caruban)', 'Kabupaten', '5', '63153', '2026-11-07 13:25:38'),
('91', 'Jawa Timur', 'Kota Blitar', 'Kota', '5', '66111', '2026-11-07 13:25:38'),
('92', 'Jawa Timur', 'Kabupaten Blitar (Kanigoro)', 'Kabupaten', '5', '66171', '2026-11-07 13:25:38'),
('93', 'Jawa Timur', 'Kota Mojokerto', 'Kota', '5', '61311', '2026-11-07 13:25:38'),
('94', 'Jawa Timur', 'Kabupaten Mojokerto', 'Kabupaten', '5', '61382', '2026-11-07 13:25:38'),
('95', 'Jawa Timur', 'Kota Pasuruan', 'Kota', '5', '67111', '2026-11-07 13:25:38'),
('96', 'Jawa Timur', 'Kabupaten Pasuruan (Bangil)', 'Kabupaten', '5', '67153', '2026-11-07 13:25:38'),
('97', 'Jawa Timur', 'Kota Probolinggo', 'Kota', '5', '67211', '2026-11-07 13:25:38'),
('98', 'Jawa Timur', 'Kabupaten Probolinggo (Kraksaan/Bromo)', 'Kabupaten', '5', '67282', '2026-11-07 13:25:38'),
('99', 'Jawa Timur', 'Kabupaten Jember', 'Kabupaten', '5', '68111', '2026-11-07 13:25:38'),
('100', 'Jawa Timur', 'Kabupaten Banyuwangi', 'Kabupaten', '5', '68411', '2026-11-07 13:25:38'),
('101', 'Jawa Timur', 'Kabupaten Malang (Kepanjen)', 'Kabupaten', '5', '65163', '2026-11-07 13:25:38'),
('102', 'Jawa Timur', 'Kabupaten Lumajang', 'Kabupaten', '5', '67311', '2026-11-07 13:25:38'),
('103', 'Jawa Timur', 'Kabupaten Bondowoso', 'Kabupaten', '5', '68211', '2026-11-07 13:25:38'),
('104', 'Jawa Timur', 'Kabupaten Situbondo', 'Kabupaten', '5', '68311', '2026-11-07 13:25:38'),
('105', 'Jawa Timur', 'Kabupaten Lamongan', 'Kabupaten', '5', '62211', '2026-11-07 13:25:38'),
('106', 'Jawa Timur', 'Kabupaten Tuban', 'Kabupaten', '5', '62311', '2026-11-07 13:25:38'),
('107', 'Jawa Timur', 'Kabupaten Bojonegoro', 'Kabupaten', '5', '62111', '2026-11-07 13:25:38'),
('108', 'Jawa Timur', 'Kabupaten Ngawi', 'Kabupaten', '5', '63211', '2026-11-07 13:25:38'),
('109', 'Jawa Timur', 'Kabupaten Magetan', 'Kabupaten', '5', '63311', '2026-11-07 13:25:38'),
('110', 'Jawa Timur', 'Kabupaten Ponorogo', 'Kabupaten', '5', '63411', '2026-11-07 13:25:38'),
('111', 'Jawa Timur', 'Kabupaten Pacitan', 'Kabupaten', '5', '63511', '2026-11-07 13:25:38'),
('112', 'Jawa Timur', 'Kabupaten Trenggalek', 'Kabupaten', '5', '66311', '2026-11-07 13:25:38'),
('113', 'Jawa Timur', 'Kabupaten Tulungagung', 'Kabupaten', '5', '66211', '2026-11-07 13:25:38'),
('114', 'Jawa Timur', 'Kabupaten Nganjuk', 'Kabupaten', '5', '64411', '2026-11-07 13:25:38'),
('115', 'Jawa Timur', 'Kabupaten Jombang', 'Kabupaten', '5', '61411', '2026-11-07 13:25:38'),
('116', 'Jawa Timur', 'Kabupaten Bangkalan (Madura)', 'Kabupaten', '5', '69111', '2026-11-07 13:25:38'),
('117', 'Jawa Timur', 'Kabupaten Sampang (Madura)', 'Kabupaten', '5', '69211', '2026-11-07 13:25:38'),
('118', 'Jawa Timur', 'Kabupaten Pamekasan (Madura)', 'Kabupaten', '5', '69311', '2026-11-07 13:25:38'),
('119', 'Jawa Timur', 'Kabupaten Sumenep (Madura)', 'Kabupaten', '5', '69411', '2026-11-07 13:25:38'),
('120', 'Bali', 'Kota Denpasar', 'Kota', '6', '80111', '2026-11-07 13:25:38'),
('121', 'Bali', 'Kabupaten Badung (Kuta/Seminyak/Canggu)', 'Kabupaten', '6', '80361', '2026-11-07 13:25:38'),
('122', 'Bali', 'Kabupaten Gianyar (Ubud)', 'Kabupaten', '6', '80511', '2026-11-07 13:25:38'),
('123', 'Bali', 'Kabupaten Tabanan', 'Kabupaten', '6', '82111', '2026-11-07 13:25:38'),
('124', 'Bali', 'Kabupaten Buleleng (Singaraja)', 'Kabupaten', '6', '81111', '2026-11-07 13:25:38'),
('125', 'Bali', 'Kabupaten Karangasem (Amlapura)', 'Kabupaten', '6', '80811', '2026-11-07 13:25:38'),
('126', 'Bali', 'Kabupaten Klungkung', 'Kabupaten', '6', '80711', '2026-11-07 13:25:38'),
('127', 'Bali', 'Kabupaten Bangli (Kintamani)', 'Kabupaten', '6', '80611', '2026-11-07 13:25:38'),
('128', 'Bali', 'Kabupaten Jembrana (Negara)', 'Kabupaten', '6', '82211', '2026-11-07 13:25:38'),
('129', 'Nusa Tenggara Barat', 'Kota Mataram', 'Kota', '6', '83111', '2026-11-07 13:25:38'),
('130', 'Nusa Tenggara Barat', 'Kabupaten Lombok Barat (Gerung/Senggigi)', 'Kabupaten', '6', '83311', '2026-11-07 13:25:38'),
('131', 'Nusa Tenggara Barat', 'Kabupaten Lombok Tengah (Praya/Mandalika)', 'Kabupaten', '6', '83511', '2026-11-07 13:25:38'),
('132', 'Nusa Tenggara Barat', 'Kabupaten Lombok Timur (Selong/Rinjani)', 'Kabupaten', '6', '83611', '2026-11-07 13:25:38'),
('133', 'Nusa Tenggara Barat', 'Kabupaten Lombok Utara (Tanjung/Gili)', 'Kabupaten', '6', '83711', '2026-11-07 13:25:38'),
('134', 'Nusa Tenggara Barat', 'Kota Bima', 'Kota', '6', '84111', '2026-11-07 13:25:38'),
('135', 'Nusa Tenggara Barat', 'Kabupaten Bima (Woha)', 'Kabupaten', '6', '84171', '2026-11-07 13:25:38'),
('136', 'Nusa Tenggara Barat', 'Kabupaten Sumbawa (Sumbawa Besar)', 'Kabupaten', '6', '84311', '2026-11-07 13:25:38'),
('137', 'Nusa Tenggara Barat', 'Kabupaten Sumbawa Barat (Taliwang)', 'Kabupaten', '6', '84411', '2026-11-07 13:25:38'),
('138', 'Nusa Tenggara Barat', 'Kabupaten Dompu', 'Kabupaten', '6', '84211', '2026-11-07 13:25:38'),
('139', 'Lampung', 'Kota Bandar Lampung', 'Kota', '7', '35111', '2026-11-07 13:25:38'),
('140', 'Lampung', 'Kota Metro', 'Kota', '7', '34111', '2026-11-07 13:25:38'),
('141', 'Lampung', 'Kabupaten Lampung Selatan (Kalianda/Bakauheni)', 'Kabupaten', '7', '35511', '2026-11-07 13:25:38'),
('142', 'Lampung', 'Kabupaten Lampung Tengah (Gunung Sugih)', 'Kabupaten', '7', '34161', '2026-11-07 13:25:38'),
('143', 'Lampung', 'Kabupaten Lampung Timur (Sukadana)', 'Kabupaten', '7', '34194', '2026-11-07 13:25:38'),
('144', 'Lampung', 'Kabupaten Lampung Utara (Kotabumi)', 'Kabupaten', '7', '34511', '2026-11-07 13:25:38'),
('145', 'Lampung', 'Kabupaten Lampung Barat (Liwa)', 'Kabupaten', '7', '34811', '2026-11-07 13:25:38'),
('146', 'Lampung', 'Kabupaten Tanggamus (Kota Agung)', 'Kabupaten', '7', '35384', '2026-11-07 13:25:38'),
('147', 'Lampung', 'Kabupaten Pringsewu', 'Kabupaten', '7', '35373', '2026-11-07 13:25:38'),
('148', 'Lampung', 'Kabupaten Pesawaran (Gedong Tataan)', 'Kabupaten', '7', '35366', '2026-11-07 13:25:38'),
('149', 'Lampung', 'Kabupaten Pesisir Barat (Krui)', 'Kabupaten', '7', '34874', '2026-11-07 13:25:38'),
('150', 'Lampung', 'Kabupaten Tulang Bawang', 'Kabupaten', '7', '34596', '2026-11-07 13:25:38'),
('151', 'Sumatera Selatan', 'Kota Palembang', 'Kota', '7', '30111', '2026-11-07 13:25:38'),
('152', 'Sumatera Selatan', 'Kota Prabumulih', 'Kota', '7', '31111', '2026-11-07 13:25:38'),
('153', 'Sumatera Selatan', 'Kota Lubuklinggau', 'Kota', '7', '31611', '2026-11-07 13:25:38'),
('154', 'Sumatera Selatan', 'Kota Pagar Alam', 'Kota', '7', '31511', '2026-11-07 13:25:38'),
('155', 'Sumatera Selatan', 'Kabupaten Ogan Komering Ilir (Kayuagung)', 'Kabupaten', '7', '30611', '2026-11-07 13:25:38'),
('156', 'Sumatera Selatan', 'Kabupaten Ogan Ilir (Indralaya)', 'Kabupaten', '7', '30662', '2026-11-07 13:25:38'),
('157', 'Sumatera Selatan', 'Kabupaten Banyuasin (Pangkalan Balai)', 'Kabupaten', '7', '30711', '2026-11-07 13:25:38'),
('158', 'Sumatera Selatan', 'Kabupaten Muara Enim', 'Kabupaten', '7', '31311', '2026-11-07 13:25:38'),
('159', 'Sumatera Selatan', 'Kabupaten Lahat', 'Kabupaten', '7', '31411', '2026-11-07 13:25:38'),
('160', 'Bengkulu', 'Kota Bengkulu', 'Kota', '7', '38111', '2026-11-07 13:25:38'),
('161', 'Bengkulu', 'Kabupaten Rejang Lebong (Curup)', 'Kabupaten', '7', '39111', '2026-11-07 13:25:38'),
('162', 'Bengkulu', 'Kabupaten Bengkulu Selatan (Manna)', 'Kabupaten', '7', '38511', '2026-11-07 13:25:38'),
('163', 'Bengkulu', 'Kabupaten Mukomuko', 'Kabupaten', '7', '38711', '2026-11-07 13:25:38'),
('164', 'Jambi', 'Kota Jambi', 'Kota', '7', '36111', '2026-11-07 13:25:38'),
('165', 'Jambi', 'Kota Sungai Penuh', 'Kota', '7', '37111', '2026-11-07 13:25:38'),
('166', 'Jambi', 'Kabupaten Muaro Jambi (Sengeti)', 'Kabupaten', '7', '36311', '2026-11-07 13:25:38'),
('167', 'Jambi', 'Kabupaten Batanghari (Muara Bulian)', 'Kabupaten', '7', '36611', '2026-11-07 13:25:38'),
('168', 'Jambi', 'Kabupaten Kerinci (Siulak)', 'Kabupaten', '7', '37161', '2026-11-07 13:25:38'),
('169', 'Jambi', 'Kabupaten Bungo (Muara Bungo)', 'Kabupaten', '7', '37211', '2026-11-07 13:25:38'),
('170', 'Bangka Belitung', 'Kota Pangkal Pinang', 'Kota', '7', '33111', '2026-11-07 13:25:38'),
('171', 'Bangka Belitung', 'Kabupaten Bangka (Sungailiat)', 'Kabupaten', '7', '33211', '2026-11-07 13:25:38'),
('172', 'Bangka Belitung', 'Kabupaten Belitung (Tanjung Pandan)', 'Kabupaten', '7', '33411', '2026-11-07 13:25:38'),
('173', 'Sumatera Barat', 'Kota Padang', 'Kota', '8', '25111', '2026-11-07 13:25:38'),
('174', 'Sumatera Barat', 'Kota Bukittinggi', 'Kota', '8', '26111', '2026-11-07 13:25:38'),
('175', 'Sumatera Barat', 'Kota Payakumbuh', 'Kota', '8', '26211', '2026-11-07 13:25:38'),
('176', 'Sumatera Barat', 'Kota Pariaman', 'Kota', '8', '25511', '2026-11-07 13:25:38'),
('177', 'Sumatera Barat', 'Kota Solok', 'Kota', '8', '27311', '2026-11-07 13:25:38'),
('178', 'Sumatera Barat', 'Kabupaten Tanah Datar (Batusangkar)', 'Kabupaten', '8', '27211', '2026-11-07 13:25:38'),
('179', 'Sumatera Barat', 'Kabupaten Agam (Lubuk Basung)', 'Kabupaten', '8', '26411', '2026-11-07 13:25:38'),
('180', 'Sumatera Barat', 'Kabupaten Pasaman Barat (Simpang Empat)', 'Kabupaten', '8', '26366', '2026-11-07 13:25:38'),
('181', 'Riau', 'Kota Pekanbaru', 'Kota', '8', '28111', '2026-11-07 13:25:38'),
('182', 'Riau', 'Kota Dumai', 'Kota', '8', '28811', '2026-11-07 13:25:38'),
('183', 'Riau', 'Kabupaten Kampar (Bangkinang)', 'Kabupaten', '8', '28411', '2026-11-07 13:25:38'),
('184', 'Riau', 'Kabupaten Bengkalis', 'Kabupaten', '8', '28711', '2026-11-07 13:25:38'),
('185', 'Riau', 'Kabupaten Siak (Siak Sri Indrapura)', 'Kabupaten', '8', '28611', '2026-11-07 13:25:38'),
('186', 'Riau', 'Kabupaten Pelalawan (Pangkalan Kerinci)', 'Kabupaten', '8', '28383', '2026-11-07 13:25:38'),
('187', 'Riau', 'Kabupaten Rokan Hulu (Pasir Pengaraian)', 'Kabupaten', '8', '28557', '2026-11-07 13:25:38'),
('188', 'Kepulauan Riau', 'Kota Batam', 'Kota', '8', '29411', '2026-11-07 13:25:38'),
('189', 'Kepulauan Riau', 'Kota Tanjung Pinang', 'Kota', '8', '29111', '2026-11-07 13:25:38'),
('190', 'Kepulauan Riau', 'Kabupaten Bintan (Bandar Seri Bentan)', 'Kabupaten', '8', '29154', '2026-11-07 13:25:38'),
('191', 'Kepulauan Riau', 'Kabupaten Karimun (Tanjung Balai Karimun)', 'Kabupaten', '8', '29611', '2026-11-07 13:25:38'),
('192', 'Sumatera Utara', 'Kota Medan', 'Kota', '8', '20111', '2026-11-07 13:25:38'),
('193', 'Sumatera Utara', 'Kota Binjai', 'Kota', '8', '20711', '2026-11-07 13:25:38'),
('194', 'Sumatera Utara', 'Kota Pematangsiantar', 'Kota', '8', '21111', '2026-11-07 13:25:38'),
('195', 'Sumatera Utara', 'Kota Tebing Tinggi', 'Kota', '8', '20611', '2026-11-07 13:25:38'),
('196', 'Sumatera Utara', 'Kota Tanjungbalai', 'Kota', '8', '21311', '2026-11-07 13:25:38'),
('197', 'Sumatera Utara', 'Kota Padang Sidempuan', 'Kota', '8', '22711', '2026-11-07 13:25:38'),
('198', 'Sumatera Utara', 'Kota Gunungsitoli (Nias)', 'Kota', '8', '22811', '2026-11-07 13:25:38'),
('199', 'Sumatera Utara', 'Kabupaten Deli Serdang (Lubuk Pakam/Kualanamu)', 'Kabupaten', '8', '20511', '2026-11-07 13:25:38'),
('200', 'Sumatera Utara', 'Kabupaten Karo (Kabanjahe/Berastagi)', 'Kabupaten', '8', '22111', '2026-11-07 13:25:38'),
('201', 'Sumatera Utara', 'Kabupaten Simalungun (Raya/Danau Toba)', 'Kabupaten', '8', '21162', '2026-11-07 13:25:38'),
('202', 'Sumatera Utara', 'Kabupaten Toba (Balige)', 'Kabupaten', '8', '22311', '2026-11-07 13:25:38'),
('203', 'Sumatera Utara', 'Kabupaten Asahan (Kisaran)', 'Kabupaten', '8', '21211', '2026-11-07 13:25:38'),
('204', 'Sumatera Utara', 'Kabupaten Langkat (Stabat)', 'Kabupaten', '8', '20811', '2026-11-07 13:25:38'),
('205', 'Sumatera Utara', 'Kabupaten Labuhanbatu (Rantau Prapat)', 'Kabupaten', '8', '21411', '2026-11-07 13:25:38'),
('206', 'Sumatera Utara', 'Kabupaten Tapanuli Utara (Tarutung)', 'Kabupaten', '8', '22411', '2026-11-07 13:25:38'),
('207', 'Aceh', 'Kota Banda Aceh', 'Kota', '8', '23111', '2026-11-07 13:25:38'),
('208', 'Aceh', 'Kota Lhokseumawe', 'Kota', '8', '24311', '2026-11-07 13:25:38'),
('209', 'Aceh', 'Kota Sabang', 'Kota', '8', '23511', '2026-11-07 13:25:38'),
('210', 'Aceh', 'Kota Langsa', 'Kota', '8', '24411', '2026-11-07 13:25:38'),
('211', 'Aceh', 'Kabupaten Aceh Besar (Jantho)', 'Kabupaten', '8', '23911', '2026-11-07 13:25:38'),
('212', 'Aceh', 'Kabupaten Aceh Tengah (Takengon/Gayo)', 'Kabupaten', '8', '24511', '2026-11-07 13:25:38'),
('213', 'Aceh', 'Kabupaten Bener Meriah (Simpang Tiga Redelong)', 'Kabupaten', '8', '24582', '2026-11-07 13:25:38'),
('214', 'Aceh', 'Kabupaten Aceh Barat (Meulaboh)', 'Kabupaten', '8', '23611', '2026-11-07 13:25:38'),
('215', 'Aceh', 'Kabupaten Aceh Utara (Lhoksukon)', 'Kabupaten', '8', '24376', '2026-11-07 13:25:38'),
('216', 'Kalimantan Barat', 'Kota Pontianak', 'Kota', '9', '78111', '2026-11-07 13:25:38'),
('217', 'Kalimantan Barat', 'Kota Singkawang', 'Kota', '9', '79111', '2026-11-07 13:25:38'),
('218', 'Kalimantan Barat', 'Kabupaten Kubu Raya (Sungai Raya/Supadio)', 'Kabupaten', '9', '78391', '2026-11-07 13:25:38'),
('219', 'Kalimantan Barat', 'Kabupaten Sambas', 'Kabupaten', '9', '79411', '2026-11-07 13:25:38'),
('220', 'Kalimantan Barat', 'Kabupaten Ketapang', 'Kabupaten', '9', '78811', '2026-11-07 13:25:38'),
('221', 'Kalimantan Barat', 'Kabupaten Sintang', 'Kabupaten', '9', '78611', '2026-11-07 13:25:38'),
('222', 'Kalimantan Selatan', 'Kota Banjarmasin', 'Kota', '9', '70111', '2026-11-07 13:25:38'),
('223', 'Kalimantan Selatan', 'Kota Banjarbaru', 'Kota', '9', '70711', '2026-11-07 13:25:38'),
('224', 'Kalimantan Selatan', 'Kabupaten Banjar (Martapura)', 'Kabupaten', '9', '70611', '2026-11-07 13:25:38'),
('225', 'Kalimantan Selatan', 'Kabupaten Tanah Bumbu (Batulicin)', 'Kabupaten', '9', '72211', '2026-11-07 13:25:38'),
('226', 'Kalimantan Selatan', 'Kabupaten Kotabaru', 'Kabupaten', '9', '72111', '2026-11-07 13:25:38'),
('227', 'Kalimantan Selatan', 'Kabupaten Tabalong (Tanjung)', 'Kabupaten', '9', '71511', '2026-11-07 13:25:38'),
('228', 'Kalimantan Timur', 'Kota Samarinda', 'Kota', '9', '75111', '2026-11-07 13:25:38'),
('229', 'Kalimantan Timur', 'Kota Balikpapan', 'Kota', '9', '76111', '2026-11-07 13:25:38'),
('230', 'Kalimantan Timur', 'Kota Bontang', 'Kota', '9', '75311', '2026-11-07 13:25:38'),
('231', 'Kalimantan Timur', 'Kabupaten Penajam Paser Utara (IKN Nusantara)', 'Kabupaten', '9', '76141', '2026-11-07 13:25:38'),
('232', 'Kalimantan Timur', 'Kabupaten Kutai Kartanegara (Tenggarong)', 'Kabupaten', '9', '75511', '2026-11-07 13:25:38'),
('233', 'Kalimantan Timur', 'Kabupaten Berau (Tanjung Redeb/Derawan)', 'Kabupaten', '9', '77311', '2026-11-07 13:25:38'),
('234', 'Kalimantan Timur', 'Kabupaten Kutai Timur (Sangatta)', 'Kabupaten', '9', '75611', '2026-11-07 13:25:38'),
('235', 'Kalimantan Tengah', 'Kota Palangka Raya', 'Kota', '9', '73111', '2026-11-07 13:25:38'),
('236', 'Kalimantan Tengah', 'Kabupaten Kotawaringin Timur (Sampit)', 'Kabupaten', '9', '74311', '2026-11-07 13:25:38'),
('237', 'Kalimantan Tengah', 'Kabupaten Kotawaringin Barat (Pangkalan Bun)', 'Kabupaten', '9', '74111', '2026-11-07 13:25:38'),
('238', 'Kalimantan Tengah', 'Kabupaten Kapuas (Kuala Kapuas)', 'Kabupaten', '9', '73511', '2026-11-07 13:25:38'),
('239', 'Kalimantan Utara', 'Kota Tarakan', 'Kota', '9', '77111', '2026-11-07 13:25:38'),
('240', 'Kalimantan Utara', 'Kabupaten Bulungan (Tanjung Selor)', 'Kabupaten', '9', '77211', '2026-11-07 13:25:38'),
('241', 'Kalimantan Utara', 'Kabupaten Nunukan', 'Kabupaten', '9', '77411', '2026-11-07 13:25:38'),
('242', 'Sulawesi Selatan', 'Kota Makassar', 'Kota', '10', '90111', '2026-11-07 13:25:38'),
('243', 'Sulawesi Selatan', 'Kota Parepare', 'Kota', '10', '91111', '2026-11-07 13:25:38'),
('244', 'Sulawesi Selatan', 'Kota Palopo', 'Kota', '10', '91911', '2026-11-07 13:25:38'),
('245', 'Sulawesi Selatan', 'Kabupaten Gowa (Sungguminasa)', 'Kabupaten', '10', '92111', '2026-11-07 13:25:38'),
('246', 'Sulawesi Selatan', 'Kabupaten Maros', 'Kabupaten', '10', '90511', '2026-11-07 13:25:38'),
('247', 'Sulawesi Selatan', 'Kabupaten Bone (Watampone)', 'Kabupaten', '10', '92711', '2026-11-07 13:25:38'),
('248', 'Sulawesi Selatan', 'Kabupaten Tana Toraja (Makale)', 'Kabupaten', '10', '91811', '2026-11-07 13:25:38'),
('249', 'Sulawesi Selatan', 'Kabupaten Toraja Utara (Rantepao)', 'Kabupaten', '10', '91831', '2026-11-07 13:25:38'),
('250', 'Sulawesi Selatan', 'Kabupaten Bulukumba (Bira)', 'Kabupaten', '10', '92511', '2026-11-07 13:25:38'),
('251', 'Sulawesi Selatan', 'Kabupaten Wajo (Sengkang)', 'Kabupaten', '10', '90911', '2026-11-07 13:25:38'),
('252', 'Sulawesi Utara', 'Kota Manado', 'Kota', '10', '95111', '2026-11-07 13:25:38'),
('253', 'Sulawesi Utara', 'Kota Bitung', 'Kota', '10', '95511', '2026-11-07 13:25:38'),
('254', 'Sulawesi Utara', 'Kota Tomohon', 'Kota', '10', '95411', '2026-11-07 13:25:38'),
('255', 'Sulawesi Utara', 'Kota Kotamobagu', 'Kota', '10', '95711', '2026-11-07 13:25:38'),
('256', 'Sulawesi Utara', 'Kabupaten Minahasa (Tondano)', 'Kabupaten', '10', '95611', '2026-11-07 13:25:38'),
('257', 'Sulawesi Utara', 'Kabupaten Minahasa Utara (Airmadidi/Likupang)', 'Kabupaten', '10', '95371', '2026-11-07 13:25:38'),
('258', 'Sulawesi Tengah', 'Kota Palu', 'Kota', '10', '94111', '2026-11-07 13:25:38'),
('259', 'Sulawesi Tengah', 'Kabupaten Donggala', 'Kabupaten', '10', '94311', '2026-11-07 13:25:38'),
('260', 'Sulawesi Tengah', 'Kabupaten Poso', 'Kabupaten', '10', '94611', '2026-11-07 13:25:38'),
('261', 'Sulawesi Tengah', 'Kabupaten Banggai (Luwuk)', 'Kabupaten', '10', '94711', '2026-11-07 13:25:38'),
('262', 'Sulawesi Tenggara', 'Kota Kendari', 'Kota', '10', '93111', '2026-11-07 13:25:38'),
('263', 'Sulawesi Tenggara', 'Kota Baubau (Buton)', 'Kota', '10', '93711', '2026-11-07 13:25:38'),
('264', 'Sulawesi Tenggara', 'Kabupaten Kolaka', 'Kabupaten', '10', '93511', '2026-11-07 13:25:38'),
('265', 'Sulawesi Tenggara', 'Kabupaten Wakatobi (Wangi-Wangi)', 'Kabupaten', '10', '93791', '2026-11-07 13:25:38'),
('266', 'Gorontalo', 'Kota Gorontalo', 'Kota', '10', '96111', '2026-11-07 13:25:38'),
('267', 'Gorontalo', 'Kabupaten Gorontalo (Limboto)', 'Kabupaten', '10', '96211', '2026-11-07 13:25:38'),
('268', 'Sulawesi Barat', 'Kabupaten Mamuju', 'Kabupaten', '10', '91511', '2026-11-07 13:25:38'),
('269', 'Sulawesi Barat', 'Kabupaten Polewali Mandar', 'Kabupaten', '10', '91311', '2026-11-07 13:25:38'),
('270', 'Nusa Tenggara Timur', 'Kota Kupang', 'Kota', '11', '85111', '2026-11-07 13:25:38'),
('271', 'Nusa Tenggara Timur', 'Kabupaten Manggarai Barat (Labuan Bajo/Komodo)', 'Kabupaten', '11', '86711', '2026-11-07 13:25:38'),
('272', 'Nusa Tenggara Timur', 'Kabupaten Ende (Kelimutu)', 'Kabupaten', '11', '86311', '2026-11-07 13:25:38'),
('273', 'Nusa Tenggara Timur', 'Kabupaten Sikka (Maumere)', 'Kabupaten', '11', '86111', '2026-11-07 13:25:38'),
('274', 'Nusa Tenggara Timur', 'Kabupaten Sumba Timur (Waingapu)', 'Kabupaten', '11', '87111', '2026-11-07 13:25:38'),
('275', 'Nusa Tenggara Timur', 'Kabupaten Sumba Barat (Waikabubak)', 'Kabupaten', '11', '87211', '2026-11-07 13:25:38'),
('276', 'Nusa Tenggara Timur', 'Kabupaten Belu (Atambua/Perbatasan Timor Leste)', 'Kabupaten', '11', '85711', '2026-11-07 13:25:38'),
('277', 'Nusa Tenggara Timur', 'Kabupaten Alor (Kalabahi)', 'Kabupaten', '11', '85811', '2026-11-07 13:25:38'),
('278', 'Maluku', 'Kota Ambon', 'Kota', '11', '97111', '2026-11-07 13:25:38'),
('279', 'Maluku', 'Kota Tual', 'Kota', '11', '97611', '2026-11-07 13:25:38'),
('280', 'Maluku', 'Kabupaten Maluku Tengah (Masohi/Banda)', 'Kabupaten', '11', '97511', '2026-11-07 13:25:38'),
('281', 'Maluku', 'Kabupaten Maluku Tenggara (Langgur/Kei)', 'Kabupaten', '11', '97622', '2026-11-07 13:25:38'),
('282', 'Maluku', 'Kabupaten Buru (Namlea)', 'Kabupaten', '11', '97571', '2026-11-07 13:25:38'),
('283', 'Maluku Utara', 'Kota Ternate', 'Kota', '11', '97711', '2026-11-07 13:25:38'),
('284', 'Maluku Utara', 'Kota Tidore Kepulauan', 'Kota', '11', '97811', '2026-11-07 13:25:38'),
('285', 'Maluku Utara', 'Kabupaten Halmahera Utara (Tobelo)', 'Kabupaten', '11', '97762', '2026-11-07 13:25:38'),
('286', 'Maluku Utara', 'Kabupaten Halmahera Barat (Jailolo)', 'Kabupaten', '11', '97752', '2026-11-07 13:25:38'),
('287', 'Papua', 'Kota Jayapura', 'Kota', '12', '99111', '2026-11-07 13:25:38'),
('288', 'Papua', 'Kabupaten Jayapura (Sentani)', 'Kabupaten', '12', '99311', '2026-11-07 13:25:38'),
('289', 'Papua', 'Kabupaten Biak Numfor', 'Kabupaten', '12', '98111', '2026-11-07 13:25:38'),
('290', 'Papua', 'Kabupaten Keerom', 'Kabupaten', '12', '99468', '2026-11-07 13:25:38'),
('291', 'Papua', 'Kabupaten Sarmi', 'Kabupaten', '12', '99373', '2026-11-07 13:25:38'),
('292', 'Papua Barat', 'Kabupaten Manokwari', 'Kabupaten', '12', '98311', '2026-11-07 13:25:38'),
('293', 'Papua Barat', 'Kabupaten Fakfak', 'Kabupaten', '12', '98011', '2026-11-07 13:25:38'),
('294', 'Papua Barat', 'Kabupaten Kaimana', 'Kabupaten', '12', '98611', '2026-11-07 13:25:38'),
('295', 'Papua Barat Daya', 'Kota Sorong', 'Kota', '12', '98411', '2026-11-07 13:25:38'),
('296', 'Papua Barat Daya', 'Kabupaten Sorong (Aimas)', 'Kabupaten', '12', '98432', '2026-11-07 13:25:38'),
('297', 'Papua Barat Daya', 'Kabupaten Raja Ampat (Waisai)', 'Kabupaten', '12', '98482', '2026-11-07 13:25:38'),
('298', 'Papua Tengah', 'Kabupaten Mimika (Timika/Tembagapura)', 'Kabupaten', '12', '99910', '2026-11-07 13:25:38'),
('299', 'Papua Tengah', 'Kabupaten Nabire', 'Kabupaten', '12', '98811', '2026-11-07 13:25:38'),
('300', 'Papua Tengah', 'Kabupaten Puncak Jaya (Mulia)', 'Kabupaten', '12', '98915', '2026-11-07 13:25:38'),
('301', 'Papua Selatan', 'Kabupaten Merauke', 'Kabupaten', '12', '99611', '2026-11-07 13:25:38'),
('302', 'Papua Selatan', 'Kabupaten Boven Digoel (Tanah Merah)', 'Kabupaten', '12', '99663', '2026-11-07 13:25:38'),
('303', 'Papua Selatan', 'Kabupaten Asmat (Agats)', 'Kabupaten', '12', '99777', '2026-11-07 13:25:38'),
('304', 'Papua Pegunungan', 'Kabupaten Jayawijaya (Wamena)', 'Kabupaten', '12', '99511', '2026-11-07 13:25:38'),
('305', 'Papua Pegunungan', 'Kabupaten Tolikara (Karubaga)', 'Kabupaten', '12', '99562', '2026-11-07 13:25:38');

DROP TABLE IF EXISTS `shipping_zones`;
CREATE TABLE `shipping_zones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zone_code` varchar(50) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `base_rate_per_kg` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `etd_regular` varchar(50) NOT NULL DEFAULT '2-3 Hari',
  `etd_express` varchar(50) DEFAULT '1 Hari',
  `jne_active` tinyint(1) NOT NULL DEFAULT 1,
  `jnt_active` tinyint(1) NOT NULL DEFAULT 1,
  `sicepat_active` tinyint(1) NOT NULL DEFAULT 1,
  `pos_active` tinyint(1) NOT NULL DEFAULT 1,
  `local_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `zone_code` (`zone_code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `shipping_zones` (`id`, `zone_code`, `zone_name`, `description`, `base_rate_per_kg`, `etd_regular`, `etd_express`, `jne_active`, `jnt_active`, `sicepat_active`, `pos_active`, `local_active`, `created_at`, `updated_at`) VALUES
('1', 'ZONA_LOKAL', 'Bandung Raya & Sekitarnya (Lokal)', 'Kab. Bandung (Cicalengka), Kota Bandung, Cimahi, Garut, Sumedang', '8000.00', '1-2 Hari', 'Hari Ini / Esok', '1', '1', '1', '1', '1', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('2', 'ZONA_JABODETABEK', 'Jabodetabek & Banten', 'Jakarta, Bogor, Depok, Tangerang, Bekasi, Serang, Cilegon', '10000.00', '1-2 Hari', '1 Hari (YES/BEST)', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('3', 'ZONA_JABAR_LAIN', 'Jawa Barat (Luar Bandung & Bodebek)', 'Karawang, Purwakarta, Subang, Cirebon, Indramayu, Tasikmalaya, Sukabumi, Cianjur', '11000.00', '1-2 Hari', '1-2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('4', 'ZONA_JATENG_DIY', 'Jawa Tengah & DI Yogyakarta', 'Semarang, Surakarta/Solo, Yogyakarta, Magelang, Purwokerto, Tegal, Kudus', '14000.00', '2-3 Hari', '1-2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('5', 'ZONA_JATIM', 'Jawa Timur & Madura', 'Surabaya, Malang, Sidoarjo, Kediri, Jember, Banyuwangi, Madiun, Gresik', '17000.00', '2-3 Hari', '1-2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('6', 'ZONA_BALI_NTB', 'Bali & Nusa Tenggara Barat', 'Denpasar, Badung, Gianyar, Tabanan, Mataram, Lombok Barat/Tengah/Timur, Bima', '22000.00', '2-4 Hari', '2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('7', 'ZONA_SUMATERA_SELATAN', 'Sumatera Bagian Selatan', 'Lampung, Palembang, Prabumulih, Bengkulu, Jambi, Bangka Belitung', '22000.00', '2-4 Hari', '2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('8', 'ZONA_SUMATERA_UTARA', 'Sumatera Bagian Utara & Riau', 'Medan, Binjai, Padang, Bukittinggi, Pekanbaru, Batam, Tanjung Pinang, Banda Aceh', '28000.00', '3-4 Hari', '2 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('9', 'ZONA_KALIMANTAN', 'Pulau Kalimantan', 'Pontianak, Banjarmasin, Balikpapan, Samarinda, Palangkaraya, Tarakan, IKN Nusantara', '32000.00', '3-5 Hari', '2-3 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('10', 'ZONA_SULAWESI', 'Pulau Sulawesi', 'Makassar, Parepare, Palopo, Manado, Palu, Kendari, Gorontalo, Mamuju', '35000.00', '3-5 Hari', '2-3 Hari', '1', '1', '1', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('11', 'ZONA_NTT_MALUKU', 'Nusa Tenggara Timur & Maluku', 'Kupang, Labuan Bajo, Ende, Maumere, Ambon, Tual, Ternate, Tidore', '45000.00', '4-6 Hari', '3-4 Hari', '1', '1', '0', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48'),
('12', 'ZONA_PAPUA', 'Tanah Papua (6 Provinsi)', 'Jayapura, Sorong, Manokwari, Merauke, Timika/Mimika, Nabire, Biak, Wamena', '75000.00', '4-7 Hari', '3-5 Hari', '1', '0', '0', '1', '0', '2026-11-07 13:24:48', '2026-11-07 13:24:48');

DROP TABLE IF EXISTS `support_replies`;
CREATE TABLE `support_replies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel support_tickets',
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK pengirim (user atau admin)',
  `message` text NOT NULL COMMENT 'Isi balasan',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Apakah balasan dari admin',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_support_replies_ticket_id` (`ticket_id`),
  KEY `idx_support_replies_user_id` (`user_id`),
  CONSTRAINT `fk_support_replies_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_support_replies_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Balasan tiket bantuan';

DROP TABLE IF EXISTS `support_tickets`;
CREATE TABLE `support_tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `subject` varchar(200) NOT NULL COMMENT 'Subjek tiket',
  `message` text NOT NULL COMMENT 'Isi pesan',
  `status` enum('open','replied','closed') NOT NULL DEFAULT 'open' COMMENT 'Status tiket',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_support_tickets_user_id` (`user_id`),
  KEY `idx_support_tickets_status` (`status`),
  CONSTRAINT `fk_support_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tiket bantuan pengguna';

DROP TABLE IF EXISTS `testimonials`;
CREATE TABLE `testimonials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nama pemberi testimoni',
  `role` varchar(100) DEFAULT NULL COMMENT 'Jabatan/peran',
  `content` text NOT NULL COMMENT 'Isi testimoni',
  `photo` varchar(255) DEFAULT NULL COMMENT 'Foto pemberi testimoni',
  `rating` tinyint(3) unsigned NOT NULL DEFAULT 5 COMMENT 'Rating 1-5',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan tampilan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_testimonials_is_active` (`is_active`),
  CONSTRAINT `chk_testimonials_rating` CHECK (`rating` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Testimoni pengguna';

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `participant_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel participants',
  `ticket_code` varchar(30) NOT NULL COMMENT 'Kode tiket unik',
  `qr_data` text NOT NULL COMMENT 'Data untuk QR code',
  `qr_image_path` varchar(255) DEFAULT NULL COMMENT 'Path gambar QR code',
  `status` enum('active','used','cancelled') NOT NULL DEFAULT 'active' COMMENT 'Status tiket',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tickets_ticket_code` (`ticket_code`),
  KEY `idx_tickets_participant_id` (`participant_id`),
  KEY `idx_tickets_status` (`status`),
  CONSTRAINT `fk_tickets_participant` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tiket masuk event penanaman';

INSERT INTO `tickets` (`id`, `participant_id`, `ticket_code`, `qr_data`, `qr_image_path`, `status`, `created_at`) VALUES
('1', '1', 'TIKET-20261105-78EAAF', '{\"ticket_code\":\"TIKET-20261105-78EAAF\",\"participant_id\":1,\"verified_url\":\"http:\\/\\/localhost\\/cocok_tanam\\/public\\/tiket\\/TIKET-20261105-78EAAF\"}', NULL, 'active', '2026-11-05 11:49:50');

DROP TABLE IF EXISTS `tree_species`;
CREATE TABLE `tree_species` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nama jenis pohon',
  `description` text DEFAULT NULL COMMENT 'Deskripsi pohon',
  `image` varchar(255) DEFAULT NULL COMMENT 'Path gambar pohon',
  `co2_absorption_kg_per_year` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Serapan CO2 per tahun (kg)',
  `oxygen_production_kg_per_year` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Produksi O2 per tahun (kg)',
  `price_per_tree` decimal(12,2) NOT NULL DEFAULT 50000.00 COMMENT 'Harga per pohon (Rupiah)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status aktif',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tree_species_is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data jenis-jenis pohon';

INSERT INTO `tree_species` (`id`, `name`, `description`, `image`, `co2_absorption_kg_per_year`, `oxygen_production_kg_per_year`, `price_per_tree`, `is_active`, `created_at`) VALUES
('1', 'Mangga', 'Pohon mangga (Mangifera indica) merupakan pohon buah tropis yang bermanfaat untuk penghijauan dan buahnya dapat dikonsumsi. Cocok ditanam di dataran rendah hingga ketinggian 600 mdpl.', 'assets/images/tree-mangga.jpg', '28.50', '25.00', '50000.00', '1', '2026-11-05 04:33:44'),
('2', 'Trembesi', 'Trembesi (Samanea saman) dikenal sebagai pohon peneduh terbaik dengan kemampuan menyerap CO2 yang sangat tinggi. Kanopi lebar mampu menyerap air hujan dan menyediakan naungan.', 'assets/images/tree-trembesi.jpg', '28500.00', '9000.00', '75000.00', '1', '2026-11-05 04:33:44'),
('3', 'Mangrove', 'Mangrove (Rhizophora sp.) adalah pohon pesisir yang melindungi garis pantai dari abrasi. Ekosistem mangrove menjadi habitat penting bagi biota laut dan menyerap karbon biru.', 'assets/images/tree-mahoni.jpg', '12.30', '8.50', '35000.00', '1', '2026-11-05 04:33:44'),
('4', 'Mahoni', 'Mahoni (Swietenia mahagoni) adalah pohon berkayu keras yang tumbuh cepat. Banyak digunakan untuk reboisasi dan menghasilkan kayu berkualitas tinggi.', 'assets/images/tree-mahoni.jpg', '18.00', '15.00', '60000.00', '1', '2026-11-05 04:33:44'),
('5', 'Jati', 'Jati (Tectona grandis) merupakan pohon penghasil kayu paling berharga di Indonesia. Pohon berukuran besar dengan daun lebar yang baik untuk penghijauan dan konservasi tanah.', 'assets/images/tree-mahoni.jpg', '15.50', '12.00', '65000.00', '1', '2026-11-05 04:33:44'),
('6', 'Pohon Kopi Arabika Kerenceng', 'Bibit pohon Kopi Arabika varietas Sigarar Utang & Typica pilihan untuk penanaman agroforestri lereng Gunung Kerenceng (1.450 - 1.742 mdpl). Menghasilkan biji kopi bernilai tinggi bagi petani lokal sekaligus memulihkan tutupan hijau pegunungan.', 'assets/images/tree-kopi.jpg', '22.50', '18.00', '45000.00', '1', '2026-11-07 13:03:25'),
('7', 'Pohon Alpukat Mentega Kerenceng', 'Pohon buah penaung ideal untuk ekosistem kebun kopi lereng gunung. Berakar tunggang kuat yang efektif mencegah erosi tebing terjal dan menghasilkan buah bergizi tinggi.', 'assets/images/tree-alpukat.jpg', '38.00', '30.00', '55000.00', '1', '2026-11-07 13:03:25');

DROP TABLE IF EXISTS `tree_trackings`;
CREATE TABLE `tree_trackings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tree_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel trees',
  `status` enum('ditanam','tumbuh','dipantau','mati','diganti') NOT NULL COMMENT 'Status terbaru',
  `photo` varchar(255) DEFAULT NULL COMMENT 'Foto update pohon',
  `notes` text DEFAULT NULL COMMENT 'Catatan pemantauan',
  `tracked_by` int(10) unsigned DEFAULT NULL COMMENT 'Admin yang memantau',
  `tracked_at` datetime NOT NULL COMMENT 'Waktu pemantauan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tree_trackings_tree_id` (`tree_id`),
  KEY `idx_tree_trackings_tracked_by` (`tracked_by`),
  CONSTRAINT `fk_tree_trackings_admin` FOREIGN KEY (`tracked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_tree_trackings_tree` FOREIGN KEY (`tree_id`) REFERENCES `trees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Riwayat pelacakan/pemantauan pohon';

INSERT INTO `tree_trackings` (`id`, `tree_id`, `status`, `photo`, `notes`, `tracked_by`, `tracked_at`, `created_at`) VALUES
('1', '1', 'ditanam', NULL, 'Bibit pohon berhasil ditanam di lokasi konservasi.', NULL, '2026-11-05 12:03:36', '2026-11-05 12:03:36'),
('2', '2', 'ditanam', NULL, 'Bibit pohon berhasil ditanam di lokasi konservasi.', NULL, '2026-11-05 12:03:36', '2026-11-05 12:03:36'),
('3', '3', 'ditanam', NULL, 'Bibit pohon berhasil ditanam di lokasi konservasi.', NULL, '2026-11-05 12:03:36', '2026-11-05 12:03:36'),
('4', '4', 'tumbuh', NULL, 'Bibit Mangrove sehat di pesisir', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('5', '5', 'dipantau', NULL, 'Perakaran bakau mulai kokoh', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('6', '6', 'tumbuh', NULL, 'Tumbuh daun tunas baru', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('7', '7', 'ditanam', NULL, 'Trembesi ditanam di zona pelindung', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('8', '8', 'tumbuh', NULL, 'Mahoni tinggi 1.2 meter', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('9', '9', 'ditanam', NULL, 'Bibit jati unggul perhutani', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38'),
('10', '10', 'dipantau', NULL, 'Trembesi penyerap karbon prima', NULL, '2026-11-07 18:27:38', '2026-11-07 18:27:38');

DROP TABLE IF EXISTS `trees`;
CREATE TABLE `trees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL COMMENT 'Kode unik pohon (TREE-YYYYMMDD-NNNN)',
  `donation_item_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke donation_items',
  `tree_species_id` int(10) unsigned NOT NULL COMMENT 'FK ke tree_species',
  `planting_location_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke planting_locations',
  `planted_at` date DEFAULT NULL COMMENT 'Tanggal penanaman',
  `status` enum('pending','ditanam','tumbuh','dipantau') NOT NULL DEFAULT 'pending' COMMENT 'Status pohon',
  `latitude` decimal(10,7) DEFAULT NULL COMMENT 'Koordinat lintang pohon',
  `longitude` decimal(10,7) DEFAULT NULL COMMENT 'Koordinat bujur pohon',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trees_code` (`code`),
  KEY `idx_trees_donation_item_id` (`donation_item_id`),
  KEY `idx_trees_tree_species_id` (`tree_species_id`),
  KEY `idx_trees_planting_location_id` (`planting_location_id`),
  KEY `idx_trees_status` (`status`),
  CONSTRAINT `fk_trees_donation_item` FOREIGN KEY (`donation_item_id`) REFERENCES `donation_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_trees_location` FOREIGN KEY (`planting_location_id`) REFERENCES `planting_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_trees_species` FOREIGN KEY (`tree_species_id`) REFERENCES `tree_species` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data pohon individu yang ditanam';

INSERT INTO `trees` (`id`, `code`, `donation_item_id`, `tree_species_id`, `planting_location_id`, `planted_at`, `status`, `latitude`, `longitude`, `created_at`) VALUES
('1', 'TREE-20261105-E5A5E0', '2', '1', '1', '2026-11-05', 'ditanam', '-6.3190852', '106.6878094', '2026-11-05 12:03:36'),
('2', 'TREE-20261105-7BAE0D', '2', '1', '1', '2026-11-05', 'ditanam', '-6.3136975', '106.6880845', '2026-11-05 12:03:36'),
('3', 'TREE-20261105-64A521', '2', '1', '1', '2026-11-05', 'ditanam', '-6.3184850', '106.6833216', '2026-11-05 12:03:36'),
('4', 'TREE-20261101-001', NULL, '3', '2', '2026-11-01', 'tumbuh', '-6.3312000', '108.3245000', '2026-11-07 18:27:38'),
('5', 'TREE-20261101-002', NULL, '3', '2', '2026-11-01', 'dipantau', '-6.3295000', '108.3221000', '2026-11-07 18:27:38'),
('6', 'TREE-20261101-003', NULL, '3', '2', '2026-11-01', 'tumbuh', '-6.3328000', '108.3267000', '2026-11-07 18:27:38'),
('7', 'TREE-20261102-004', NULL, '2', '1', '2026-11-02', 'ditanam', '-6.3215000', '106.6812000', '2026-11-07 18:27:38'),
('8', 'TREE-20261102-005', NULL, '4', '1', '2026-11-02', 'tumbuh', '-6.3182000', '106.6791000', '2026-11-07 18:27:38'),
('9', 'TREE-20261103-006', NULL, '5', '1', '2026-11-03', 'ditanam', '-6.3229000', '106.6834000', '2026-11-07 18:27:38'),
('10', 'TREE-20261103-007', NULL, '2', '1', '2026-11-03', 'dipantau', '-6.3175000', '106.6820000', '2026-11-07 18:27:38');

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `label` varchar(50) NOT NULL DEFAULT 'Rumah' COMMENT 'Label alamat (Rumah, Kantor, dll)',
  `recipient_name` varchar(100) NOT NULL COMMENT 'Nama penerima',
  `phone` varchar(20) NOT NULL COMMENT 'Nomor telepon penerima',
  `province` varchar(100) NOT NULL COMMENT 'Provinsi',
  `city` varchar(100) NOT NULL COMMENT 'Kota/Kabupaten',
  `district` varchar(100) NOT NULL COMMENT 'Kecamatan',
  `postal_code` varchar(10) NOT NULL COMMENT 'Kode pos',
  `full_address` text NOT NULL COMMENT 'Alamat lengkap',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Alamat utama',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_addresses_user_id` (`user_id`),
  CONSTRAINT `fk_user_addresses_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alamat pengiriman pengguna';

DROP TABLE IF EXISTS `user_badges`;
CREATE TABLE `user_badges` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel users',
  `badge_id` int(10) unsigned NOT NULL COMMENT 'FK ke tabel badges',
  `earned_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu mendapat lencana',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_badges_user_badge` (`user_id`,`badge_id`),
  KEY `idx_user_badges_badge_id` (`badge_id`),
  CONSTRAINT `fk_user_badges_badge` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_badges_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Relasi user-lencana';

INSERT INTO `user_badges` (`id`, `user_id`, `badge_id`, `earned_at`) VALUES
('1', '1', '1', '2026-11-05 12:24:06'),
('2', '1', '2', '2026-11-05 12:24:06'),
('3', '1', '3', '2026-11-05 12:24:06');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Nama lengkap',
  `email` varchar(150) NOT NULL COMMENT 'Alamat email unik',
  `password` varchar(255) NOT NULL COMMENT 'Hash password (bcrypt)',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Nomor telepon',
  `city` varchar(100) DEFAULT NULL COMMENT 'Kota domisili',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Path file foto profil',
  `role_id` int(10) unsigned NOT NULL DEFAULT 5 COMMENT 'FK ke tabel roles, default peserta',
  `referral_code` varchar(20) DEFAULT NULL COMMENT 'Kode referral unik milik user',
  `referred_by` int(10) unsigned DEFAULT NULL COMMENT 'FK ke user yang mereferensikan',
  `points` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Jumlah poin yang dikumpulkan',
  `default_tshirt_size` enum('S','M','L','XL','XXL') DEFAULT NULL COMMENT 'Ukuran kaos bawaan',
  `is_anonymous_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Default donasi anonim',
  `email_verified_at` datetime DEFAULT NULL COMMENT 'Waktu verifikasi email',
  `email_verify_token` varchar(100) DEFAULT NULL COMMENT 'Token verifikasi email',
  `reset_token` varchar(100) DEFAULT NULL COMMENT 'Token reset password',
  `reset_expires` datetime DEFAULT NULL COMMENT 'Batas waktu token reset',
  `status` enum('active','inactive','banned') NOT NULL DEFAULT 'inactive' COMMENT 'Status akun',
  `google_id` varchar(100) DEFAULT NULL COMMENT 'Google OAuth ID',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  UNIQUE KEY `uk_users_referral_code` (`referral_code`),
  UNIQUE KEY `uk_users_google_id` (`google_id`),
  KEY `idx_users_role_id` (`role_id`),
  KEY `idx_users_status` (`status`),
  KEY `idx_users_referred_by` (`referred_by`),
  KEY `idx_users_email_verify_token` (`email_verify_token`),
  KEY `idx_users_reset_token` (`reset_token`),
  CONSTRAINT `fk_users_referred_by` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data pengguna platform';

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `city`, `avatar`, `role_id`, `referral_code`, `referred_by`, `points`, `default_tshirt_size`, `is_anonymous_default`, `email_verified_at`, `email_verify_token`, `reset_token`, `reset_expires`, `status`, `google_id`, `created_at`, `updated_at`) VALUES
('1', 'Super Admin', 'admin@tanampohon.id', '$2y$10$pqDYAx6R.FJE2osnKXkPfeaothgUa9r8pbbYnGmCdzjESFoNCN64W', '081200000001', NULL, NULL, '1', 'ADMIN001', NULL, '0', NULL, '0', '2026-11-05 04:33:44', NULL, NULL, NULL, 'active', NULL, '2026-11-05 04:33:44', '2026-11-07 19:11:55'),
('2', 'Rian Pratama', 'rian@example.com', '$2y$12$X/Bvh0XQcoa6n0qaEPd3ieds.vej3fD5fILJVo23RphHOo6LssUxi', '081299887766', 'Surabaya', NULL, '5', 'CC7DF1B5', NULL, '0', 'L', '0', NULL, 'e8671c8acbf19f31afa499f893cc12546891ad0c24464a418246982767457720', NULL, NULL, 'active', NULL, '2026-11-05 11:59:51', '2026-11-05 11:59:51');

SET FOREIGN_KEY_CHECKS = 1;

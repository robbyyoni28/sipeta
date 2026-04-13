-- ==========================================
-- SIPETA Admin Module Improvements
-- SQL Script for Additional Tables and Updates
-- ==========================================

-- 1. Create admin_activity_logs table for tracking admin activities
CREATE TABLE IF NOT EXISTS `admin_activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Create login_logs table for tracking user login activities
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `login_status` enum('success','failed') DEFAULT 'success',
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `login_time` (`login_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Update users table to add profile photo field if not exists
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `foto` varchar(255) DEFAULT 'default.png' AFTER `status_aktif`,
ADD COLUMN IF NOT EXISTS `nama` varchar(255) DEFAULT NULL AFTER `username`;

-- 4. Update personel_lapangan table to fix masa_berlaku_skk field consistency
ALTER TABLE `personel_lapangan` 
MODIFY COLUMN `masa_berlaku_skk` date DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `masa_berlaku_skk_sertifikat` int(11) DEFAULT NULL AFTER `nomor_skk`;

-- 5. Update peralatan table to ensure all required fields exist
ALTER TABLE `peralatan` 
ADD COLUMN IF NOT EXISTS `tahun_pembuatan` int(4) DEFAULT NULL AFTER `jenis_alat`,
ADD COLUMN IF NOT EXISTS `status_kepemilikan` enum('Milik Sendiri','Sewa','Kerjasama') DEFAULT 'Milik Sendiri' AFTER `tahun_pembuatan`;

-- 6. Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_tender_tahun_anggaran` ON `tender` (`tahun_anggaran`);
CREATE INDEX IF NOT EXISTS `idx_tender_created_by` ON `tender` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_tender_penyedia` ON `tender` (`penyedia_id`);
CREATE INDEX IF NOT EXISTS `idx_personel_lapangan_nik` ON `personel_lapangan` (`nik`);
CREATE INDEX IF NOT EXISTS `idx_personel_k3_nik` ON `personel_k3` (`nik`);
CREATE INDEX IF NOT EXISTS `idx_peralatan_plat_serial` ON `peralatan` (`plat_serial`);
CREATE INDEX IF NOT EXISTS `idx_peralatan_jenis_alat` ON `peralatan` (`jenis_alat`);

-- 7. Create view for tender statistics
CREATE OR REPLACE VIEW `v_tender_statistics` AS
SELECT 
    tahun_anggaran,
    COUNT(*) as total_tender,
    SUM(hps) as total_hps,
    AVG(hps) as avg_hps,
    MIN(hps) as min_hps,
    MAX(hps) as max_hps,
    COUNT(DISTINCT penyedia_id) as unique_penyedia
FROM `tender`
GROUP BY tahun_anggaran
ORDER BY tahun_anggaran DESC;

-- 8. Create view for personel statistics
CREATE OR REPLACE VIEW `v_personel_statistics` AS
SELECT 
    'Lapangan' as personel_type,
    COUNT(*) as total_personel,
    COUNT(DISTINCT penyedia_id) as unique_penyedia,
    COUNT(CASE WHEN masa_berlaku_skk >= CURDATE() THEN 1 END) as valid_skk,
    COUNT(CASE WHEN masa_berlaku_skk < CURDATE() THEN 1 END) as expired_skk
FROM personel_lapangan
UNION ALL
SELECT 
    'K3' as personel_type,
    COUNT(*) as total_personel,
    COUNT(DISTINCT penyedia_id) as unique_penyedia,
    COUNT(CASE WHEN masa_berlaku_sertifikat >= CURDATE() THEN 1 END) as valid_sertifikat,
    COUNT(CASE WHEN masa_berlaku_sertifikat < CURDATE() THEN 1 END) as expired_sertifikat
FROM personel_k3;

-- 9. Create view for peralatan statistics
CREATE OR REPLACE VIEW `v_peralatan_statistics` AS
SELECT 
    jenis_alat,
    COUNT(*) as total_alat,
    COUNT(DISTINCT penyedia_id) as unique_penyedia,
    COUNT(CASE WHEN status_kepemilikan = 'Milik Sendiri' THEN 1 END) as milik_sendiri,
    COUNT(CASE WHEN status_kepemilikan = 'Sewa' THEN 1 END) as sewa,
    COUNT(CASE WHEN status_kepemilikan = 'Kerjasama' THEN 1 END) as kerjasama
FROM peralatan
WHERE jenis_alat IS NOT NULL
GROUP BY jenis_alat
ORDER BY total_alat DESC;

-- 10. Insert default admin profile photo
-- This assumes you have a default.png file in assets/img/profile/
-- The file should be uploaded manually to the server

-- 11. Sample data for testing (optional)
-- Uncomment these lines if you want sample data for testing

-- INSERT INTO `admin_activity_logs` (`username`, `activity`, `details`) VALUES
-- ('admin', 'Login', '{"ip": "127.0.0.1", "time": "2026-01-30 10:00:00"}'),
-- ('admin', 'Edit Tender', '{"tender_id": 1, "changes": ["hps", "nama_penyedia"]}');

-- INSERT INTO `login_logs` (`username`, `ip_address`, `user_agent`, `login_status`) VALUES
-- ('admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'success'),
-- ('test_user', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', 'failed');

-- ==========================================
-- End of SQL Script
-- ==========================================

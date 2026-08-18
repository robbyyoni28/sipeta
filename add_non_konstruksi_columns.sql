-- ============================================
-- MIGRATION SCRIPT FOR SIPETA TENDER NON KONSTRUKSI
-- ============================================

USE `sipeta`;

-- 1. Tambahkan kolom kategori_tender dan jenis_pengadaan pada tabel tender jika belum ada
ALTER TABLE `tender` 
  ADD COLUMN IF NOT EXISTS `kategori_tender` VARCHAR(50) DEFAULT 'KONSTRUKSI' AFTER `segmentasi`,
  ADD COLUMN IF NOT EXISTS `jenis_pengadaan` VARCHAR(100) DEFAULT NULL AFTER `kategori_tender`;

-- 2. Tambahkan index untuk mempercepat query filtering kategori_tender
ALTER TABLE `tender` ADD INDEX IF NOT EXISTS `idx_kategori_tender` (`kategori_tender`);

-- 3. Update data historis agar selaras dengan kategori baru
UPDATE `tender` SET `kategori_tender` = 'KONSULTANSI' WHERE `is_konsultansi` = 1;
UPDATE `tender` SET `kategori_tender` = 'KONSTRUKSI' WHERE `is_konsultansi` = 0 OR `is_konsultansi` IS NULL;

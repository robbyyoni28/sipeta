-- SIPETA - Tabel Manajer Teknik dan Keuangan
-- Script ini membuat tabel manajer teknik dan keuangan sebagai master table
-- agar bisa memiliki banyak manajer per tender (bukan hanya 1 per role)

-- ============================================
-- 1. TABEL MANAJER TEKNIK (Master Table)
-- ============================================
CREATE TABLE IF NOT EXISTS `manajer_teknik` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `penyedia_id` INT(11) DEFAULT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `nik` VARCHAR(50) NOT NULL UNIQUE,
    `spesialisasi` VARCHAR(100) DEFAULT NULL COMMENT 'Spesialisasi teknik (Sipil, ME, dll)',
    `jenis_sertifikat` VARCHAR(100) DEFAULT NULL,
    `nomor_sertifikat` VARCHAR(100) DEFAULT NULL,
    `masa_berlaku_sertifikat` DATE DEFAULT NULL,
    `file_sertifikat` VARCHAR(255) DEFAULT NULL,
    `file_ktp` VARCHAR(255) DEFAULT NULL,
    `no_telepon` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `alamat` TEXT DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_nik_teknik` (`nik`),
    KEY `idx_penyedia_id` (`penyedia_id`),
    KEY `idx_spesialisasi` (`spesialisasi`),
    FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master data manajer teknik';

-- ============================================
-- 2. TABEL MANAJER KEUANGAN (Master Table)
-- ============================================
CREATE TABLE IF NOT EXISTS `manajer_keuangan` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `penyedia_id` INT(11) DEFAULT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `nik` VARCHAR(50) NOT NULL UNIQUE,
    `spesialisasi` VARCHAR(100) DEFAULT NULL COMMENT 'Spesialisasi keuangan (Akuntansi, Pajak, dll)',
    `jenis_sertifikat` VARCHAR(100) DEFAULT NULL,
    `nomor_sertifikat` VARCHAR(100) DEFAULT NULL,
    `masa_berlaku_sertifikat` DATE DEFAULT NULL,
    `file_sertifikat` VARCHAR(255) DEFAULT NULL,
    `file_ktp` VARCHAR(255) DEFAULT NULL,
    `no_telepon` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `alamat` TEXT DEFAULT NULL,
    `status_aktif` TINYINT(1) DEFAULT 1,
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_nik_keuangan` (`nik`),
    KEY `idx_penyedia_id` (`penyedia_id`),
    KEY `idx_spesialisasi` (`spesialisasi`),
    FOREIGN KEY (`penyedia_id`) REFERENCES `penyedia`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master data manajer keuangan';

-- ============================================
-- 3. TABEL TENDER_MANAJER_TEKNIK (Junction Table)
-- ============================================
CREATE TABLE IF NOT EXISTS `tender_manajer_teknik` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tender_id` INT(11) NOT NULL,
    `manajer_teknik_id` INT(11) NOT NULL,
    `peran` VARCHAR(50) DEFAULT 'Manajer Teknik' COMMENT 'Peran spesifik (Manajer Teknik, Koordinator, dll)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tender_manajer_teknik` (`tender_id`, `manajer_teknik_id`),
    KEY `idx_tender_id` (`tender_id`),
    KEY `idx_manajer_teknik_id` (`manajer_teknik_id`),
    FOREIGN KEY (`tender_id`) REFERENCES `tender`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`manajer_teknik_id`) REFERENCES `manajer_teknik`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hubungan tender dengan manajer teknik';

-- ============================================
-- 4. TABEL TENDER_MANAJER_KEUANGAN (Junction Table)
-- ============================================
CREATE TABLE IF NOT EXISTS `tender_manajer_keuangan` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `tender_id` INT(11) NOT NULL,
    `manajer_keuangan_id` INT(11) NOT NULL,
    `peran` VARCHAR(50) DEFAULT 'Manajer Keuangan' COMMENT 'Peran spesifik (Manajer Keuangan, Akuntan, dll)',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_tender_manajer_keuangan` (`tender_id`, `manajer_keuangan_id`),
    KEY `idx_tender_id` (`tender_id`),
    KEY `idx_manajer_keuangan_id` (`manajer_keuangan_id`),
    FOREIGN KEY (`tender_id`) REFERENCES `tender`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`manajer_keuangan_id`) REFERENCES `manajer_keuangan`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hubungan tender dengan manajer keuangan';

-- ============================================
-- 5. MIGRASI DATA EXISTING (Opsional)
-- ============================================
-- Jika ada data di kolom manajer_teknik dan manajer_keuangan di tabel tender,
-- script ini akan memigrasikannya ke tabel baru

-- Migrasi Manajer Teknik
INSERT INTO `manajer_teknik` (`penyedia_id`, `nama`, `nik`, `created_by`, `created_at`)
SELECT 
    t.penyedia_id,
    t.manajer_teknik,
    t.nik_manajer_teknik,
    t.created_by,
    t.created_at
FROM `tender` t
WHERE t.manajer_teknik IS NOT NULL 
  AND t.nik_manajer_teknik IS NOT NULL
  AND t.nik_manajer_teknik != ''
ON DUPLICATE KEY UPDATE 
    nama = VALUES(nama),
    updated_at = CURRENT_TIMESTAMP;

-- Link manajer teknik ke tender
INSERT INTO `tender_manajer_teknik` (`tender_id`, `manajer_teknik_id`)
SELECT 
    t.id as tender_id,
    mt.id as manajer_teknik_id
FROM `tender` t
JOIN `manajer_teknik` mt ON mt.nik = t.nik_manajer_teknik
WHERE t.manajer_teknik IS NOT NULL 
  AND t.nik_manajer_teknik IS NOT NULL
  AND t.nik_manajer_teknik != ''
ON DUPLICATE KEY UPDATE tender_id = VALUES(tender_id);

-- Migrasi Manajer Keuangan
INSERT INTO `manajer_keuangan` (`penyedia_id`, `nama`, `nik`, `created_by`, `created_at`)
SELECT 
    t.penyedia_id,
    t.manajer_keuangan,
    t.nik_manajer_keuangan,
    t.created_by,
    t.created_at
FROM `tender` t
WHERE t.manajer_keuangan IS NOT NULL 
  AND t.nik_manajer_keuangan IS NOT NULL
  AND t.nik_manajer_keuangan != ''
ON DUPLICATE KEY UPDATE 
    nama = VALUES(nama),
    updated_at = CURRENT_TIMESTAMP;

-- Link manajer keuangan ke tender
INSERT INTO `tender_manajer_keuangan` (`tender_id`, `manajer_keuangan_id`)
SELECT 
    t.id as tender_id,
    mk.id as manajer_keuangan_id
FROM `tender` t
JOIN `manajer_keuangan` mk ON mk.nik = t.nik_manajer_keuangan
WHERE t.manajer_keuangan IS NOT NULL 
  AND t.nik_manajer_keuangan IS NOT NULL
  AND t.nik_manajer_keuangan != ''
ON DUPLICATE KEY UPDATE tender_id = VALUES(tender_id);

-- ============================================
-- 6. INDEXES UNTUK PERFORMANCE
-- ============================================
CREATE INDEX idx_tender_teknik ON tender_manajer_teknik(tender_id, manajer_teknik_id);
CREATE INDEX idx_tender_keuangan ON tender_manajer_keuangan(tender_id, manajer_keuangan_id);

-- ============================================
-- 7. VIEWS UNTUK REPORTING
-- ============================================
CREATE OR REPLACE VIEW v_manajer_teknik_detail AS
SELECT 
    t.id as tender_id,
    t.kode_tender,
    t.judul_paket,
    mt.nama as nama_manajer_teknik,
    mt.nik as nik_manajer_teknik,
    mt.spesialisasi,
    mt.jenis_sertifikat,
    mt.nomor_sertifikat,
    mt.masa_berlaku_sertifikat,
    tmt.peran,
    p.nama_perusahaan
FROM tender_manajer_teknik tmt
JOIN tender t ON t.id = tmt.tender_id
JOIN manajer_teknik mt ON mt.id = tmt.manajer_teknik_id
LEFT JOIN penyedia p ON p.id = t.penyedia_id;

CREATE OR REPLACE VIEW v_manajer_keuangan_detail AS
SELECT 
    t.id as tender_id,
    t.kode_tender,
    t.judul_paket,
    mk.nama as nama_manajer_keuangan,
    mk.nik as nik_manajer_keuangan,
    mk.spesialisasi,
    mk.jenis_sertifikat,
    mk.nomor_sertifikat,
    mk.masa_berlaku_sertifikat,
    tmk.peran,
    p.nama_perusahaan
FROM tender_manajer_keuangan tmk
JOIN tender t ON t.id = tmk.tender_id
JOIN manajer_keuangan mk ON mk.id = tmk.manajer_keuangan_id
LEFT JOIN penyedia p ON p.id = t.penyedia_id;

-- ============================================
-- CATATAN:
-- 
-- 1. Tabel manajer_teknik dan manajer_keuangan adalah master table
--    sehingga bisa digunakan kembali di multiple tender
-- 
-- 2. Junction table (tender_manajer_teknik, tender_manajer_keuangan)
--    menghubungkan manajer dengan tender
-- 
-- 3. Kolom lama di tabel tender (manajer_teknik, manajer_keuangan)
--    bisa dihapus setelah migrasi berhasil
-- 
-- 4. Script migrasi akan menyalin data dari kolom lama ke tabel baru
-- 
-- ============================================

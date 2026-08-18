<?php
$mysqli = new mysqli("localhost", "root", "", "sipeta");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error . "\n";
    exit(1);
}

// 1. Add columns
$res1 = $mysqli->query("ALTER TABLE `tender` ADD COLUMN IF NOT EXISTS `kategori_tender` VARCHAR(50) DEFAULT 'KONSTRUKSI' AFTER `segmentasi`");
echo "Add kategori_tender: " . ($res1 ? "SUCCESS" : $mysqli->error) . "\n";

$res2 = $mysqli->query("ALTER TABLE `tender` ADD COLUMN IF NOT EXISTS `jenis_pengadaan` VARCHAR(100) DEFAULT NULL AFTER `kategori_tender`");
echo "Add jenis_pengadaan: " . ($res2 ? "SUCCESS" : $mysqli->error) . "\n";

// 2. Add index
$res3 = $mysqli->query("ALTER TABLE `tender` ADD INDEX IF NOT EXISTS `idx_kategori_tender` (`kategori_tender`)");
echo "Add index: " . ($res3 ? "SUCCESS" : $mysqli->error) . "\n";

// 3. Update existing records
$res4 = $mysqli->query("UPDATE `tender` SET `kategori_tender` = 'KONSULTANSI' WHERE `is_konsultansi` = 1");
echo "Update Konsultansi: " . ($res4 ? "SUCCESS" : $mysqli->error) . "\n";

$res5 = $mysqli->query("UPDATE `tender` SET `kategori_tender` = 'KONSTRUKSI' WHERE (`is_konsultansi` = 0 OR `is_konsultansi` IS NULL) AND (`kategori_tender` IS NULL OR `kategori_tender` = '' OR `kategori_tender` = 'KONSTRUKSI')");
echo "Update Konstruksi: " . ($res5 ? "SUCCESS" : $mysqli->error) . "\n";

// Check columns in table tender
$result = $mysqli->query("SHOW COLUMNS FROM `tender` LIKE 'kategori_tender'");
if ($result && $result->num_rows > 0) {
    echo "VERIFICATION: kategori_tender column exists!\n";
}
$result2 = $mysqli->query("SHOW COLUMNS FROM `tender` LIKE 'jenis_pengadaan'");
if ($result2 && $result2->num_rows > 0) {
    echo "VERIFICATION: jenis_pengadaan column exists!\n";
}
$mysqli->close();

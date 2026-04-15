<?php
$conn = mysqli_connect("localhost", "root", "", "sipeta");
$sql = "CREATE TABLE IF NOT EXISTS manajer_proyek (
    id int(11) NOT NULL AUTO_INCREMENT,
    penyedia_id int(11) DEFAULT NULL,
    nama varchar(255) DEFAULT NULL,
    nik varchar(20) DEFAULT NULL,
    jenis_skk varchar(100) DEFAULT NULL,
    nomor_skk varchar(100) DEFAULT NULL,
    masa_berlaku_skk date DEFAULT NULL,
    file_ktp varchar(255) DEFAULT NULL,
    file_skk varchar(255) DEFAULT NULL,
    created_by varchar(50) DEFAULT NULL,
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
if(mysqli_query($conn, $sql)) {
    echo "Table manajer_proyek created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}

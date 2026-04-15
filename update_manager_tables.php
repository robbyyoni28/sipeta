<?php
$conn = mysqli_connect("localhost", "root", "", "sipeta");
$queries = [
    "ALTER TABLE manajer_teknik ADD COLUMN IF NOT EXISTS tender_id int(11) AFTER penyedia_id",
    "ALTER TABLE manajer_keuangan ADD COLUMN IF NOT EXISTS tender_id int(11) AFTER penyedia_id",
    "ALTER TABLE manajer_proyek ADD COLUMN IF NOT EXISTS tender_id int(11) AFTER penyedia_id"
];
foreach($queries as $sql) {
    if(mysqli_query($conn, $sql)) {
        echo "Success: $sql\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\n";
    }
}

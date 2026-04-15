<?php
$conn = mysqli_connect("localhost", "root", "", "sipeta");
$sql = "ALTER TABLE tender ADD COLUMN IF NOT EXISTS is_konsultansi tinyint(1) DEFAULT 0 AFTER segmentasi";
if(mysqli_query($conn, $sql)) {
    echo "Column is_konsultansi added successfully\n";
} else {
    echo "Error: " . mysqli_error($conn) . "\n";
}

<?php
$conn = mysqli_connect("localhost", "root", "", "sipeta");
$res = mysqli_query($conn, "DESCRIBE manajer_teknik");
while($row = mysqli_fetch_array($res)) {
    echo $row[0] . " (" . $row[1] . ")\n";
}

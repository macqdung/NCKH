<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connect fail"); }

$res = mysqli_query($conn, "SHOW CREATE TABLE categories");
$row = mysqli_fetch_assoc($res);
echo $row['Create Table'] . "\n";
?>

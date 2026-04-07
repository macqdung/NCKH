<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connection fail"); }
echo "CATEGORIES:\n";
$r = mysqli_query($conn, "SELECT name FROM categories");
while($row = mysqli_fetch_assoc($r)) echo $row['name'] . "\n";

echo "\nPRODUCTS (first 10):\n";
$r = mysqli_query($conn, "SELECT tensanpham, category, subcategory_id FROM products LIMIT 10");
while($row = mysqli_fetch_assoc($r)) echo $row['tensanpham'] ." | cat: " . $row['category'] ." | subcat: ".$row['subcategory_id']. "\n";
?>

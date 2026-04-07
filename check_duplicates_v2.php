<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connection fail"); }
echo "CATEGORIES (Check for duplicate names):\n";
$r = mysqli_query($conn, "SELECT id, name FROM categories");
while($row = mysqli_fetch_assoc($r)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . "\n";
}

echo "\nPRODUCTS (Check for duplicate names in same category):\n";
$r = mysqli_query($conn, "SELECT ID_sanpham, tensanpham, category FROM products ORDER BY tensanpham");
while($row = mysqli_fetch_assoc($r)) {
    echo "ID: " . $row['ID_sanpham'] . " | Name: " . $row['tensanpham'] . " | Cat: " . $row['category'] . "\n";
}
?>

<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connection fail"); }

echo "--- CATEGORIES TABLE CONTENT ---\n";
$res = mysqli_query($conn, "SELECT * FROM categories");
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . "\n";
}

echo "\n--- PRODUCTS TABLE CONTENT (Duplicates by name) ---\n";
$res = mysqli_query($conn, "SELECT tensanpham, COUNT(*) as c FROM products GROUP BY tensanpham HAVING c > 1");
while($row = mysqli_fetch_assoc($res)) {
    echo "Product: " . $row['tensanpham'] . " | Count: " . $row['c'] . "\n";
}
?>

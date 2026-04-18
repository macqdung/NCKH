<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connection fail"); }

echo "--- CLEANING CATEGORIES ---\n";
// Create a temp table to store unique categories
mysqli_query($conn, "CREATE TEMPORARY TABLE temp_categories AS SELECT MIN(id) as id, name FROM categories GROUP BY name");
// Delete all from categories
mysqli_query($conn, "DELETE FROM categories");
// Insert unique ones back
mysqli_query($conn, "INSERT INTO categories (id, name) SELECT id, name FROM temp_categories");
echo "Categories cleaned. Remaining: " . mysqli_affected_rows($conn) . "\n";

echo "--- CLEANING PRODUCTS ---\n";
// Create a temp table for products to remove duplicates by name
mysqli_query($conn, "CREATE TEMPORARY TABLE temp_products AS SELECT MIN(ID_sanpham) as id FROM products GROUP BY tensanpham");
// Delete duplicates
mysqli_query($conn, "DELETE FROM products WHERE ID_sanpham NOT IN (SELECT id FROM temp_products)");
echo "Products cleaned. Deleted: " . mysqli_affected_rows($conn) . "\n";

echo "Fix complete.";
?>

<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connection fail"); }

echo "--- CATEGORIES ---\n";
$res = mysqli_query($conn, "SELECT id, name, COUNT(*) as count FROM categories GROUP BY name HAVING count > 1");
if (mysqli_num_rows($res) > 0) {
    echo "Duplicate categories found by name:\n";
    while($row = mysqli_fetch_assoc($res)) {
        echo "- " . $row['name'] . " (Count: " . $row['count'] . ")\n";
    }
} else {
    echo "No duplicate category names found.\n";
}

$res = mysqli_query($conn, "SELECT * FROM categories");
echo "Total categories in DB: " . mysqli_num_rows($res) . "\n";
while($row = mysqli_fetch_assoc($res)) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . "\n";
}

echo "\n--- PRODUCTS ---\n";
$res = mysqli_query($conn, "SELECT tensanpham, category, COUNT(*) as count FROM products GROUP BY tensanpham, category HAVING count > 1");
if (mysqli_num_rows($res) > 0) {
    echo "Duplicate products found by name within same category:\n";
    while($row = mysqli_fetch_assoc($res)) {
        echo "- " . $row['tensanpham'] . " | Cat ID: " . $row['category'] . " (Count: " . $row['count'] . ")\n";
    }
} else {
    echo "No duplicate products found by name/category.\n";
}
?>

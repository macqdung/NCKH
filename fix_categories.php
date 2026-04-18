<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connect fail"); }

echo "Fixing Categories Table IDs...\n";

// 1. Get all categories
$res = mysqli_query($conn, "SELECT name FROM categories");
$cats = [];
while($row = mysqli_fetch_assoc($res)){
    $cats[] = $row['name'];
}

// 2. Clear the table and reset auto increment
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE categories");

// 3. Make sure table has correct structure
mysqli_query($conn, "ALTER TABLE categories DROP PRIMARY KEY"); // ignore if error
mysqli_query($conn, "ALTER TABLE categories MODIFY id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");

// 4. Re-insert categories so they get unique IDs
$new_mapping = [];
foreach($cats as $i => $name) {
    // Only insert if not exists
    $name_esc = mysqli_real_escape_string($conn, $name);
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name_esc')");
    $new_mapping[$name] = mysqli_insert_id($conn);
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

echo "Categories fixed.\n";

$res = mysqli_query($conn, "SELECT * FROM categories");
while($r = mysqli_fetch_assoc($res)) {
    echo "- ID: " . $r['id'] . " Name: " . $r['name'] . "\n";
}
?>

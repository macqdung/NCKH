<?php
include('MODEL/connect.php');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Cleaning up categories table...\n";

// 1. Remove duplicates from categories table
// Keep the record with the smallest ID for each name
$sql_get_dupes = "SELECT name, MIN(id) as min_id FROM categories GROUP BY name";
$result = mysqli_query($conn, $sql_get_dupes);
$keep_ids = [];
while ($row = mysqli_fetch_assoc($result)) {
    $keep_ids[] = $row['min_id'];
}

if (!empty($keep_ids)) {
    $keep_ids_str = implode(',', $keep_ids);
    $sql_delete = "DELETE FROM categories WHERE id NOT IN ($keep_ids_str)";
    if (mysqli_query($conn, $sql_delete)) {
        echo "Successfully removed duplicate categories.\n";
    } else {
        echo "Error removing duplicate categories: " . mysqli_error($conn) . "\n";
    }
}

// 2. Add Primary Key and Auto Increment if missing
echo "Updating table structure...\n";

// Disable foreign key checks temporarily
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

// Check if primary key exists
$check_pk = mysqli_query($conn, "SHOW KEYS FROM categories WHERE Key_name = 'PRIMARY'");
if (mysqli_num_rows($check_pk) == 0) {
    // Check if ID 0 exists (can cause issues with auto-increment)
    mysqli_query($conn, "UPDATE categories SET id = (SELECT MAX(id) + 1 FROM categories) WHERE id = 0");
    
    // Add primary key and auto_increment
    $sql_fix = "ALTER TABLE categories MODIFY id INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)";
    if (mysqli_query($conn, $sql_fix)) {
        echo "Successfully added PRIMARY KEY and AUTO_INCREMENT to categories table.\n";
    } else {
        echo "Error fixing table structure: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "Primary key already exists on categories table.\n";
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

mysqli_close($conn);
echo "Done.\n";
?>

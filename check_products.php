<?php
include_once 'MODEL/connect.php';

global $conn;

// Check if products table exists and its structure
$result = $conn->query("SHOW TABLES LIKE 'products'");
if ($result->num_rows > 0) {
    echo "Table 'products' exists.\n";
    $columns = $conn->query("DESCRIBE products");
    echo "Columns:\n";
    while ($row = $columns->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Table 'products' does not exist.\n";
}

$conn->close();
?>

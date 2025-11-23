<?php
include_once 'MODEL/connect.php';

global $conn;

// Get existing columns in products table
$existing_columns = [];
$result = $conn->query("DESCRIBE products");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }
} else {
    echo "Error fetching table structure: " . $conn->error . "\n";
    $conn->close();
    exit;
}

// Columns to add with their definitions
$columns_to_add = [
    'author' => "ALTER TABLE products ADD COLUMN author VARCHAR(255) DEFAULT NULL",
    'publisher' => "ALTER TABLE products ADD COLUMN publisher VARCHAR(255) DEFAULT NULL",
    'isbn' => "ALTER TABLE products ADD COLUMN isbn VARCHAR(20) DEFAULT NULL",
    'subcategory_id' => "ALTER TABLE products ADD COLUMN subcategory_id INT DEFAULT NULL",
    'dongia' => "ALTER TABLE products ADD COLUMN dongia INT DEFAULT 0",
    'category' => "ALTER TABLE products ADD COLUMN category VARCHAR(255) DEFAULT NULL"
];

foreach ($columns_to_add as $column => $query) {
    if (!in_array($column, $existing_columns)) {
        try {
            if (mysqli_query($conn, $query)) {
                echo "Executed: $query\n";
            } else {
                echo "Failed: $query - " . mysqli_error($conn) . "\n";
            }
        } catch (Exception $e) {
            echo "Error: $query - " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column '$column' already exists, skipping.\n";
    }
}

echo "Column addition completed.\n";
$conn->close();
?>

<?php
include_once 'MODEL/connect.php';

global $conn;

// Check if users table exists and its structure
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "Table 'users' exists.\n";
    $columns = $conn->query("DESCRIBE users");
    echo "Columns:\n";
    while ($row = $columns->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Table 'users' does not exist.\n";
}

$conn->close();
?>

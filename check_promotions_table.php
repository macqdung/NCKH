<?php
include_once 'MODEL/connect.php';

global $conn;

$result = $conn->query("SHOW TABLES LIKE 'promotions'");
if ($result->num_rows > 0) {
    echo "Table 'promotions' exists.\n";
} else {
    echo "Table 'promotions' does not exist.\n";
}

$conn->close();
?>

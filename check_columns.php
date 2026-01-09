<?php
include 'MODEL/connect.php';
global $conn;

$result = $conn->query("DESCRIBE user_points");
if ($result) {
    echo "Columns in user_points table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>

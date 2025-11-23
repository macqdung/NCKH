<?php
include_once('MODEL/connect.php');

// Get existing columns in users table
$existing_columns = [];
$result = $conn->query("DESCRIBE users");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }
} else {
    echo "Error fetching table structure: " . $conn->error . "\n";
    exit;
}

// Check if 'hoten' column exists
if (!in_array('hoten', $existing_columns)) {
    $sql = "ALTER TABLE users ADD COLUMN hoten VARCHAR(255) DEFAULT ''";
    if (mysqli_query($conn, $sql)) {
        echo "Column 'hoten' added successfully.";
    } else {
        echo "Error adding column: " . mysqli_error($conn);
    }
} else {
    echo "Column 'hoten' already exists.";
}
?>

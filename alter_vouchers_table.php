<?php
include 'MODEL/connect.php';

// First, check if the column exists as max_uses
$result = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'max_uses'");
if (mysqli_num_rows($result) > 0) {
    $sql = "ALTER TABLE vouchers CHANGE max_uses max_uses_total INT DEFAULT NULL";
    if (mysqli_query($conn, $sql)) {
        echo "Column renamed successfully.";
    } else {
        echo "Error renaming column: " . mysqli_error($conn);
    }
} else {
    echo "Column 'max_uses' does not exist, checking for 'max_uses_total'.";
    $result2 = mysqli_query($conn, "SHOW COLUMNS FROM vouchers LIKE 'max_uses_total'");
    if (mysqli_num_rows($result2) > 0) {
        echo "Column 'max_uses_total' already exists.";
    } else {
        echo "Neither column exists, creating 'max_uses_total'.";
        $sql = "ALTER TABLE vouchers ADD COLUMN max_uses_total INT DEFAULT NULL";
        if (mysqli_query($conn, $sql)) {
            echo "Column added successfully.";
        } else {
            echo "Error adding column: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

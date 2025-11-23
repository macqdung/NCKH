<?php
include 'MODEL/connect.php';

$sql = "DROP TABLE IF EXISTS vouchers";

if (mysqli_query($conn, $sql)) {
    echo "Table dropped successfully.";
} else {
    echo "Error dropping table: " . mysqli_error($conn);
}

$sql = "CREATE TABLE IF NOT EXISTS vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0,
    max_uses_total INT DEFAULT NULL,
    expiry_date DATE DEFAULT NULL,
    applicable_to ENUM('all', 'specific') DEFAULT 'all',
    product_ids TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uses_count INT DEFAULT 0
)";

if (mysqli_query($conn, $sql)) {
    echo "Table created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

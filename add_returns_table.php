<?php
include_once 'MODEL/connect.php';

global $conn;

// Create returns table if not exists
$sql = "CREATE TABLE IF NOT EXISTS returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    reason TEXT,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'processed') DEFAULT 'pending',
    notes TEXT,
    processed_date TIMESTAMP NULL,
    FOREIGN KEY (order_id) REFERENCES muahangg(ID),
    FOREIGN KEY (user_id) REFERENCES users(ID_user),
    FOREIGN KEY (product_id) REFERENCES products(ID_sanpham)
)";

if ($conn->query($sql) === TRUE) {
    echo "Returns table created successfully.\n";
} else {
    echo "Error creating returns table: " . $conn->error . "\n";
}

$conn->close();
?>

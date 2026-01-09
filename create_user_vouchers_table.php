<?php
include_once('MODEL/connect.php');

global $conn;

$sql = "CREATE TABLE IF NOT EXISTS user_vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    voucher_id INT NOT NULL,
    claimed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(ID_user),
    FOREIGN KEY (voucher_id) REFERENCES vouchers(id)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table user_vouchers created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?>

<?php
include_once('MODEL/connect.php');
global $conn;

// Update existing products to copy gia to dongia and tacgia to author if dongia or author are NULL
$sql = "UPDATE products SET dongia = gia, author = tacgia WHERE dongia IS NULL OR author IS NULL";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "Existing products updated successfully.";
} else {
    echo "Error updating products: " . mysqli_error($conn);
}
?>

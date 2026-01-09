<?php
$conn = mysqli_connect('localhost', 'root', '', 'bookstore');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// Find duplicates and keep the one with the smallest ID
$sql = "SELECT name, MIN(id) as min_id FROM categories GROUP BY name";
$result = mysqli_query($conn, $sql);
$keep_ids = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $keep_ids[] = $row['min_id'];
    }
}

// Delete duplicates
$keep_ids_str = implode(',', $keep_ids);
$sql_delete = "DELETE FROM categories WHERE id NOT IN ($keep_ids_str)";
if (mysqli_query($conn, $sql_delete)) {
    echo "Duplicates removed successfully.";
} else {
    echo "Error removing duplicates: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

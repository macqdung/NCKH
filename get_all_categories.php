<?php
include('MODEL/connect.php');

$sql = "SELECT id, name FROM categories ORDER BY id ASC";
$result = mysqli_query($conn, $sql);

$categories = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($categories);
?>

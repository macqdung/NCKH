<?php
$conn = mysqli_connect('localhost', 'root', '');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = mysqli_query($conn, "SHOW DATABASES LIKE 'nckh'");
if (mysqli_num_rows($result) > 0) {
    echo "Database 'nckh' exists.\n";
} else {
    echo "Database 'nckh' does not exist.\n";
}

mysqli_close($conn);
?>

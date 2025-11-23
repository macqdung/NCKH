<?php
include 'MODEL/connect.php';

$table = 'vouchers';
$sql = "DESCRIBE $table";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "Columns in $table:<br>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

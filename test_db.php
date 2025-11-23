<?php
include 'MODEL/connect.php';

if ($conn) {
    echo "Database connected successfully<br>";

    // Check if user table exists and has data
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM user");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "Number of users in database: " . $row['count'] . "<br>";
    } else {
        echo "Error querying user table: " . mysqli_error($conn) . "<br>";
    }

    // List a few users for debugging
    $result = mysqli_query($conn, "SELECT ID_user, tendangnhap FROM user LIMIT 5");
    if ($result) {
        echo "Sample users:<br>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "ID: " . $row['ID_user'] . ", Username: " . $row['tendangnhap'] . "<br>";
        }
    } else {
        echo "Error fetching users: " . mysqli_error($conn) . "<br>";
    }

    mysqli_close($conn);
} else {
    echo "Connection failed";
}
?>

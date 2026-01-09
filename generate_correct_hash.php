<?php
$password = '01062006';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash for '$password': $hash\n";
?>

 <?php
$server="localhost";
$username="root";
$pass="";
$database="nckhh";

$conn=mysqli_connect($server,$username,$pass,$database);
mysqli_query($conn,"set names 'utf8'");
?>

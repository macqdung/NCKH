<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connect fail"); }

$res = mysqli_query($conn, "SELECT COUNT(*) as c FROM products");
$row = mysqli_fetch_assoc($res);
echo "Total products: " . $row['c'] . "\n";

$res = mysqli_query($conn, "SELECT ID_sanpham, tensanpham, category, subcategory_id FROM products ORDER BY ID_sanpham DESC LIMIT 10");
echo "Recent products:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo "- " . $r['ID_sanpham'] . ": " . $r['tensanpham'] . " | Cat: " . $r['category'] . "\n";
}

$res = mysqli_query($conn, "SHOW TRIGGERS");
echo "\nTriggers:\n";
while($r = mysqli_fetch_assoc($res)) {
    print_r($r);
}

$res = mysqli_query($conn, "SELECT * FROM categories");
echo "\nCategories:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo "- " . $r['id'] . ": " . $r['name'] . "\n";
}
?>

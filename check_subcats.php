<?php
include('MODEL/connect.php');
if (!$conn) { die("DB Connect fail"); }

$res = mysqli_query($conn, "SELECT * FROM products LIMIT 5");
echo "Sample products:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo "- " . $r['tensanpham'] . " | Cat: " . $r['category'] . " | Subcat: " . $r['subcategory_id'] . "\n";
}

$res = mysqli_query($conn, "SELECT * FROM subcategories LIMIT 5");
echo "\nSample subcategories:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo "- ID: " . $r['id'] . " | Name: " . $r['name'] . " | Parent: " . $r['parent_id'] . "\n";
}
?>

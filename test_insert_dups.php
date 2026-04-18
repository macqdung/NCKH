<?php
include('MODEL/connect.php');
include('MODEL/modeladmin.php');
$admin = new data_admin();

echo "Adding test product...\n";
$admin->insert_book("Test Book XYZ", "John Doe", "Pub", "12345", "Desc", "img.jpg", 10, 1000, 2, null);

$res = mysqli_query($conn, "SELECT id_sanpham, tensanpham, category FROM products WHERE tensanpham = 'Test Book XYZ'");
echo "Inserted rows:\n";
while($r = mysqli_fetch_assoc($res)) {
    echo "- " . $r['id_sanpham'] . " | " . $r['tensanpham'] . " | " . $r['category'] . "\n";
}

// See what get_all_books returns
$all = $admin->get_all_books();
echo "Total rows from get_all_books: " . count($all) . "\n";
foreach($all as $b) {
    if ($b['tensanpham'] == 'Test Book XYZ') {
        echo "In get_all_books: " . $b['tensanpham'] . " | Cat: " . $b['category_name'] . "\n";
    }
}
?>

<?php
include('MODEL/connect.php');
include('MODEL/modelcategory.php');

$cat_model = new data_category();
$cats = $cat_model->select_all_categories();

echo "Home Page Display (mqd1.php) logic test:\n";

foreach ($cats as $cat) {
    echo "\n=== CATEGORY: " . $cat['name'] . " (ID: " . $cat['id'] . ") ===\n";
    $products = $cat_model->select_products_by_category($cat['id']);
    if (empty($products)) {
        echo "  [No products found]\n";
    } else {
        foreach ($products as $p) {
            echo "  - " . $p['tensanpham'] . " (cat: " . $p['category'] . ", subcat: " . $p['subcategory_id'] . ")\n";
        }
    }
}
?>

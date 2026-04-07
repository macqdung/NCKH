<?php
include('MODEL/modelcategory.php');
$category_model = new data_category();
$categories = $category_model->select_all_categories();

$output = [];
foreach ($categories as $cat) {
    $products = $category_model->select_products_by_category($cat['id']);
    $output[$cat['name']] = array_map(function($p) { return $p['tensanpham']; }, $products);
}
echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

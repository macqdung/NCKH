<?php
include('../MODEL/modelcategory.php');
$category_model = new data_category();
$categories = $category_model->select_all_categories();
?>

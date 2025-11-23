<?php
// Script to scan media folder and generate SQL insert statements for products
$mediaDir = __DIR__ . '/media';
$images = array_diff(scandir($mediaDir), ['.', '..']);

// Example categories mapping by file keywords or manual assignment
// Adjust these mappings based on your naming conventions or other criteria
$categoryMapping = [
    'dune' => 1,           // Science Fiction
    'hobbit' => 2,         // Fantasy
    'pride' => 3,          // Romance
    'sapiens' => 4,        // History
    'clean_code' => 5,     // Technology
    'power_of_habit' => 6  // Self-help
];

$sqlStatements = [];
foreach ($images as $image) {
    $baseName = strtolower(pathinfo($image, PATHINFO_FILENAME));
    $mappedCategory = 0;

    foreach ($categoryMapping as $keyword => $catId) {
        if (strpos($baseName, $keyword) !== false) {
            $mappedCategory = $catId;
            break;
        }
    }

    // If no mapping, assign category 0 or handle differently
    if ($mappedCategory == 0) {
        // Skip or assign a default category
        continue;
    }

    $productName = str_replace('_', ' ', ucwords(str_replace('-', ' ', $baseName)));
    $author = 'Unknown';
    $publisher = 'Unknown';
    $isbn = '';
    $description = 'Description for ' . $productName;

    $sql = sprintf(
        "INSERT INTO products (tensanpham, mota, hinhanh, soluong, dongia, category, author, publisher, isbn) VALUES ('%s', '%s', '%s', %d, %d, %d, '%s', '%s', '%s');",
        $productName,
        $description,
        $image,
        10,
        100000,
        $mappedCategory,
        $author,
        $publisher,
        $isbn
    );
    $sqlStatements[] = $sql;
}

// Output SQL statements to a file
file_put_contents('insert_products.sql', implode("\n", $sqlStatements));

echo "SQL insert statements generated in insert_products.sql\n";
?>

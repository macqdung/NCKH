<?php
$test_cases['TC_LOGIN_001'] = [
    'title' => 'Kiểm tra hệ thống không cho phép đăng nhập khi nhập sai mật khẩu và hiển thị thông báo lỗi.',
    'preconditions' => [
        'User is on the login page (/SANPHAMMOI/VIEW/dangnhap.php)',
        'Database has a user with username "testuser" and correct password hashed',
        'Session is not started'
    ],
    'test_steps' => [
        '1. Enter valid username "testuser"',
        '2. Enter incorrect password "wrongpass"',
        '3. Click "Đăng nhập" button'
    ],
    'expected_results' => [
        'System does not log in the user',
        'Error message "Tên đăng nhập hoặc mật khẩu không đúng." is displayed',
        'User remains on login page',
        'Session variables for user are not set'
    ],
    'post_conditions' => [
        'Database unchanged',
        'No user session created'
    ]
];
$test_cases['TC_CART_001'] = [
    'title' => 'Xác nhận sản phẩm được thêm thành công, số lượng trong giỏ hàng tăng, và hiển thị đúng trong trang giỏ hàng.',
    'preconditions' => [
        'User is logged in',
        'Product with ID 1 exists in database with stock > 0',
        'Cart is empty'
    ],
    'test_steps' => [
        '1. Navigate to product page',
        '2. Select product ID 1',
        '3. Enter quantity 1',
        '4. Click "Add to Cart" button'
    ],
    'expected_results' => [
        'Product is added to session cart',
        'Cart count increases by 1',
        'Success message "Thêm vào giỏ hàng thành công" is displayed',
        'Redirect to product page with message',
        'In cart page, product is listed with correct quantity and details'
    ],
    'post_conditions' => [
        'Session cart contains the product',
        'Database stock unchanged (only updated on order)'
    ]
];

$test_cases['TC_ORDER_001'] = [
    'title' => 'Kiểm tra toàn bộ luồng mua hàng hoàn tất, đơn hàng được lưu vào CSDL với trạng thái "Chờ xác nhận", và số lượng tồn kho giảm.',
    'preconditions' => [
        'User is logged in',
        'Cart has at least one product',
        'Product has sufficient stock',
        'Database tables muahangg and products exist'
    ],
    'test_steps' => [
        '1. Add product to cart',
        '2. Go to cart page',
        '3. Click "Buy" or "Buy Selected" button',
        '4. Confirm order (if any confirmation step)'
    ],
    'expected_results' => [
        'Order is inserted into muahangg table with status "chờ xác nhận"',
        'Product stock is reduced by ordered quantity',
        'Cart is cleared (for full buy) or selected items removed',
        'Redirect to order history page with success message',
        'Order appears in user\'s order history'
    ],
    'post_conditions' => [
        'muahangg table has new record with correct details',
        'products table stock updated',
        'Session cart updated accordingly'
    ]
];

$test_cases['TC_ADMIN_PRODUCT_001'] = [
    'title' => 'Kiểm tra Admin có thể thêm sản phẩm mới với đầy đủ thông tin vào CSDL, và sản phẩm hiển thị trên website.',
    'preconditions' => [
        'Admin is logged in (session admin set)',
        'Admin is on admin page',
        'Valid image file available for upload',
        'Database products table exists'
    ],
    'test_steps' => [
        '1. Go to admin page',
        '2. Fill product form: tensanpham, mota, soluong, dongia, category',
        '3. Upload image file',
        '4. Click "Add Product" button'
    ],
    'expected_results' => [
        'Product is inserted into products table',
        'Image is uploaded to media/ folder',
        'Success message "Sản phẩm đã được thêm thành công!" displayed',
        'Redirect to admin page',
        'Product appears in product list on website'
    ],
    'post_conditions' => [
        'products table has new record with all fields',
        'Image file exists in media/ directory',
        'Product visible on product pages'
    ]
];

// Function to run test cases (basic structure)
function run_test_case($test_case) {
    echo "Running: " . $test_case['title'] . "\n";
  
    echo "Preconditions:\n";
    foreach ($test_case['preconditions'] as $pre) {
        echo "- $pre\n";
    }
    echo "Steps:\n";
    foreach ($test_case['test_steps'] as $step) {
        echo "- $step\n";
    }
    echo "Expected:\n";
    foreach ($test_case['expected_results'] as $exp) {
        echo "- $exp\n";
    }
    echo "\n";
}

// Example usage
if (isset($_GET['run'])) {
    foreach ($test_cases as $id => $case) {
        run_test_case($case);
    }
}
?>

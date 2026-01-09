<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dangnhap.php');
    exit();
}
include_once('../MODEL/connect.php');
include_once('../MODEL/modeladmin.php');
$admin = new data_admin();

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_book'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publisher = trim($_POST['publisher']);
        $isbn = trim($_POST['isbn']);
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = trim($_POST['description']);
        $main_category_id = $_POST['main_category_id'];
        $subcategory_id = $_POST['subcategory_id'] ?: null;
        $image = $_FILES['image']['name'] ?? '';

        if (!empty($title) && !empty($author) && !empty($price)) {
            if ($admin->insert_book($title, $author, $publisher, $isbn, $description, $image, $stock, $price, $main_category_id, $subcategory_id)) {
                $message = '本が追加されました。';
            } else {
                $message = '本の追加に失敗しました。';
            }
        }
    } elseif (isset($_POST['update_book'])) {
        $id = $_POST['book_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publisher = trim($_POST['publisher']);
        $isbn = trim($_POST['isbn']);
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = trim($_POST['description']);
        $main_category_id = $_POST['main_category_id'];
        $subcategory_id = $_POST['subcategory_id'] ?: null;

        if ($admin->update_book($id, $title, $author, $publisher, $isbn, $price, $stock, $description, $main_category_id, $subcategory_id)) {
            $message = '本が更新されました。';
        } else {
            $message = '本の更新に失敗しました。';
        }
    } elseif (isset($_POST['delete_book'])) {
        $id = $_POST['book_id'];
        if ($admin->delete_book($id)) {
            $message = '本が削除されました。';
        } else {
            $message = '本の削除に失敗しました。';
        }
    }
}

// Get data
$books = $admin->get_all_books();
$categories = $admin->get_all_categories();
$subcategories = [];
foreach ($categories as $category) {
    $subcategories[$category['id']] = $admin->get_subcategories_by_category($category['id']);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>本管理 - 管理者パネル</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Arial', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: slideInLeft 1s ease-out;
        }
        .sidebar .nav-link {
            color: #333;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 5px 0;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.3);
            transform: translateX(10px);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.5);
            color: #333;
        }
        main {
            animation: fadeIn 1.5s ease-in;
        }
        .alert-success {
            background: linear-gradient(45deg, #56ab2f, #a8e6cf);
            border: none;
            color: #fff;
            animation: bounceIn 1s ease-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .alert-heading {
            color: #fff;
            font-weight: bold;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        h1.h2 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: textGlow 2s ease-in-out infinite alternate;
        }
        @keyframes textGlow {
            from { text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
            to { text-shadow: 2px 2px 4px rgba(255,255,255,0.8); }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes zoomIn {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h5 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                        <span>管理者メニュー</span>
                    </h5>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">
                                <i class="fas fa-tachometer-alt"></i> ダッシュボード
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_categories.php">
                                <i class="fas fa-tags"></i> カテゴリ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="admin_books.php">
                                <i class="fas fa-book"></i> 本管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_staff.php">
                                <i class="fas fa-users"></i> スタッフ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_orders.php">
                                <i class="fas fa-shopping-cart"></i> 注文管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_vouchers.php">
                                <i class="fas fa-ticket-alt"></i> クーポン管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_promotions.php">
                                <i class="fas fa-percent"></i> プロモーション管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_loyalty.php">
                                <i class="fas fa-star"></i> ロイヤリティ管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_reports.php">
                                <i class="fas fa-chart-bar"></i> レポート
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_returns.php">
                                <i class="fas fa-undo"></i> 返品管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="dangnhap.php">
                                <i class="fas fa-sign-out-alt"></i> ログアウト
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">本管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Add Book Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>新しい本を追加</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">タイトル *</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="author" class="form-label">著者 *</label>
                                        <input type="text" class="form-control" id="author" name="author" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="publisher" class="form-label">出版社</label>
                                        <input type="text" class="form-control" id="publisher" name="publisher">
                                    </div>
                                    <div class="mb-3">
                                        <label for="isbn" class="form-label">ISBN</label>
                                        <input type="text" class="form-control" id="isbn" name="isbn">
                                    </div>
                                    <div class="mb-3">
                                        <label for="price" class="form-label">価格 *</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">在庫</label>
                                        <input type="number" class="form-control" id="stock" name="stock" value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="main_category_id" class="form-label">メインカテゴリ *</label>
                                        <select class="form-control" id="main_category_id" name="main_category_id" required onchange="loadSubcategories()">
                                            <option value="">選択してください</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subcategory_id" class="form-label">サブカテゴリ</label>
                                        <select class="form-control" id="subcategory_id" name="subcategory_id">
                                            <option value="">選択してください</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="image" class="form-label">画像</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">説明</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            <button type="submit" name="add_book" class="btn btn-primary">本を追加</button>
                        </form>
                    </div>
                </div>

                <!-- Books List -->
                <div class="card">
                    <div class="card-header">
                        <h5>本一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>タイトル</th>
                                        <th>著者</th>
                                        <th>価格</th>
                                        <th>在庫</th>
                                        <th>カテゴリ</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($books as $book): ?>
                                        <tr>
                                            <td><?php echo $book['ID_sanpham']; ?></td>
                                            <td><?php echo htmlspecialchars($book['tensanpham']); ?></td>
                                            <td><?php echo htmlspecialchars($book['author'] ?? ''); ?></td>
                                            <td><?php echo number_format($book['dongia']); ?> 円</td>
                                            <td><?php echo $book['soluong']; ?></td>
                                            <td><?php echo htmlspecialchars($book['category_name'] ?? ''); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-2" onclick="editBook(<?php echo $book['ID_sanpham']; ?>)">
                                                    <i class="fas fa-edit"></i> 編集
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('この本を削除しますか？')">
                                                    <input type="hidden" name="book_id" value="<?php echo $book['ID_sanpham']; ?>">
                                                    <button type="submit" name="delete_book" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i> 削除
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">本編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="book_id" id="edit_book_id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">タイトル *</label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_author" class="form-label">著者 *</label>
                                    <input type="text" class="form-control" id="edit_author" name="author" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_publisher" class="form-label">出版社</label>
                                    <input type="text" class="form-control" id="edit_publisher" name="publisher">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_isbn" class="form-label">ISBN</label>
                                    <input type="text" class="form-control" id="edit_isbn" name="isbn">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_price" class="form-label">価格 *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_stock" class="form-label">在庫</label>
                                    <input type="number" class="form-control" id="edit_stock" name="stock">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_main_category_id" class="form-label">メインカテゴリ *</label>
                                    <select class="form-control" id="edit_main_category_id" name="main_category_id" required onchange="loadEditSubcategories()">
                                        <option value="">選択してください</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_subcategory_id" class="form-label">サブカテゴリ</label>
                                    <select class="form-control" id="edit_subcategory_id" name="subcategory_id">
                                        <option value="">選択してください</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">説明</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_book" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const subcategoriesData = <?php echo json_encode($subcategories); ?>;

        function loadSubcategories() {
            const mainCategoryId = document.getElementById('main_category_id').value;
            const subcategorySelect = document.getElementById('subcategory_id');
            subcategorySelect.innerHTML = '<option value="">選択してください</option>';

            if (mainCategoryId && subcategoriesData[mainCategoryId]) {
                subcategoriesData[mainCategoryId].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.name;
                    subcategorySelect.appendChild(option);
                });
            }
        }

        function loadEditSubcategories() {
            const mainCategoryId = document.getElementById('edit_main_category_id').value;
            const subcategorySelect = document.getElementById('edit_subcategory_id');
            subcategorySelect.innerHTML = '<option value="">選択してください</option>';

            if (mainCategoryId && subcategoriesData[mainCategoryId]) {
                subcategoriesData[mainCategoryId].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.name;
                    subcategorySelect.appendChild(option);
                });
            }
        }

        function editBook(bookId) {
            // In a real implementation, you'd fetch book data via AJAX
            // For now, just open the modal
            document.getElementById('edit_book_id').value = bookId;
            new bootstrap.Modal(document.getElementById('editBookModal')).show();
        }
    </script>
</body>
</html>

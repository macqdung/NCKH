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
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['category_name']);
        if (!empty($name)) {
            if ($admin->add_category($name)) {
                $message = 'カテゴリが追加されました。';
            } else {
                $message = 'カテゴリの追加に失敗しました。';
            }
        }
    } elseif (isset($_POST['add_subcategory'])) {
        $name = trim($_POST['subcategory_name']);
        $parent_id = $_POST['parent_category'];
        if (!empty($name) && !empty($parent_id)) {
            if ($admin->add_subcategory($name, $parent_id)) {
                $message = 'サブカテゴリが追加されました。';
            } else {
                $message = 'サブカテゴリの追加に失敗しました。';
            }
        }
    } elseif (isset($_POST['update_subcategory'])) {
        $id = $_POST['subcategory_id'];
        $name = trim($_POST['subcategory_name']);
        if (!empty($name)) {
            if ($admin->update_subcategory($id, $name)) {
                $message = 'サブカテゴリが更新されました。';
            } else {
                $message = 'サブカテゴリの更新に失敗しました。';
            }
        }
    } elseif (isset($_POST['delete_subcategory'])) {
        $id = $_POST['subcategory_id'];
        if ($admin->delete_subcategory($id)) {
            $message = 'サブカテゴリが削除されました。';
        } else {
            $message = 'サブカテゴリの削除に失敗しました。';
        }
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['category_id'];
        if ($admin->delete_category($id)) {
            $message = 'カテゴリが削除されました。';
        } else {
            $message = 'カテゴリの削除に失敗しました。';
        }
    }
}

// Get data
$categories = $admin->get_all_categories();
$subcategories = [];
foreach ($categories as $category) {
    $subcategories[$category['id']] = $admin->get_subcategories_by_category($category['id']);
}
?>

<!DOCTYPE html>
<html lang="ja">
<?php include('head.php'); ?>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include('head_nav.php'); ?>

            <!-- Main content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">カテゴリ管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="row">
                    <!-- Add Main Category -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>メインカテゴリ追加</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="category_name" class="form-label">カテゴリ名</label>
                                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                                    </div>
                                    <button type="submit" name="add_category" class="btn btn-primary">追加</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Add Subcategory -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>サブカテゴリ追加</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="parent_category" class="form-label">親カテゴリ</label>
                                        <select class="form-control" id="parent_category" name="parent_category" required>
                                            <option value="">選択してください</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="subcategory_name" class="form-label">サブカテゴリ名</label>
                                        <input type="text" class="form-control" id="subcategory_name" name="subcategory_name" required>
                                    </div>
                                    <button type="submit" name="add_subcategory" class="btn btn-primary">追加</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories List -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>カテゴリ一覧</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($categories as $category): ?>
                                    <div class="category-item mb-4 border p-3 rounded bg-white shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0 fw-bold text-dark">
                                                <i class="fas fa-folder-open text-warning me-2"></i>
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </h5>
                                            <form method="POST" onsubmit="return confirm('警告: このカテゴリ và tất cả các danh mục con của nó sẽ bị xóa?')">
                                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                                <button type="submit" name="delete_category" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Xóa danh mục này
                                                </button>
                                            </form>
                                        </div>
                                        <ul class="list-group">
                                            <?php if (isset($subcategories[$category['id']]) && count($subcategories[$category['id']]) > 0): ?>
                                                <?php foreach ($subcategories[$category['id']] as $subcategory): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <?php echo htmlspecialchars($subcategory['name']); ?>
                                                        <div>
                                                            <button class="btn btn-sm btn-outline-primary me-2" onclick="editSubcategory(<?php echo $subcategory['id']; ?>, '<?php echo htmlspecialchars($subcategory['name']); ?>')">
                                                                <i class="fas fa-edit"></i> 編集
                                                            </button>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('このサブカテゴリを削除しますか？')">
                                                                <input type="hidden" name="subcategory_id" value="<?php echo $subcategory['id']; ?>">
                                                                <button type="submit" name="delete_subcategory" class="btn btn-sm btn-outline-danger">
                                                                    <i class="fas fa-trash"></i> 削除
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <li class="list-group-item text-muted">サブカテゴリなし</li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit Subcategory Modal -->
    <div class="modal fade" id="editSubcategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">サブカテゴリ編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="subcategory_id" id="edit_subcategory_id">
                        <div class="mb-3">
                            <label for="edit_subcategory_name" class="form-label">サブカテゴリ名</label>
                            <input type="text" class="form-control" id="edit_subcategory_name" name="subcategory_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_subcategory" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editSubcategory(id, name) {
            document.getElementById('edit_subcategory_id').value = id;
            document.getElementById('edit_subcategory_name').value = name;
            new bootstrap.Modal(document.getElementById('editSubcategoryModal')).show();
        }
    </script>
</body>
</html>

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
    if (isset($_POST['add_voucher'])) {
        $code = trim($_POST['code']);
        $type = $_POST['type'];
        $value = $_POST['value'];
        $min_order = $_POST['min_order'];
        $max_uses = $_POST['max_uses'] ?: null;
        $expiry_date = $_POST['expiry_date'] ?: null;
        $applicable_to = $_POST['applicable_to'];
        $product_ids = $applicable_to == 'product' ? json_encode($_POST['product_ids'] ?? []) : null;

        if ($admin->insert_voucher($code, $type, $value, $min_order, $max_uses, $expiry_date, $applicable_to, $product_ids)) {
            $message = 'クーポンが追加されました。';
        } else {
            $message = 'クーポンの追加に失敗しました。';
        }
    } elseif (isset($_POST['update_voucher'])) {
        $id = $_POST['voucher_id'];
        $code = trim($_POST['code']);
        $type = $_POST['type'];
        $value = $_POST['value'];
        $min_order = $_POST['min_order'];
        $max_uses = $_POST['max_uses'] ?: null;
        $expiry_date = $_POST['expiry_date'] ?: null;
        $applicable_to = $_POST['applicable_to'];
        $product_ids = $applicable_to == 'product' ? json_encode($_POST['product_ids'] ?? []) : null;

        if ($admin->update_voucher($id, $code, $type, $value, $min_order, $max_uses, $expiry_date, $applicable_to, $product_ids)) {
            $message = 'クーポンが更新されました。';
        } else {
            $message = 'クーポンの更新に失敗しました。';
        }
    } elseif (isset($_POST['delete_voucher'])) {
        $id = $_POST['voucher_id'];
        if ($admin->delete_voucher($id)) {
            $message = 'クーポンが削除されました。';
        } else {
            $message = 'クーポンの削除に失敗しました。';
        }
    }
}

// Get data
$vouchers = $admin->get_all_vouchers();
$books = $admin->get_all_books();
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
                    <h1 class="h2">クーポン管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Add Voucher Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>新しいクーポンを追加</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="voucherForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">クーポンコード *</label>
                                        <input type="text" class="form-control" id="code" name="code" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="type" class="form-label">タイプ</label>
                                        <select class="form-control" id="type" name="type">
                                            <option value="percentage">パーセント</option>
                                            <option value="fixed">固定金額</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="value" class="form-label">値 *</label>
                                        <input type="number" step="0.01" class="form-control" id="value" name="value" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="min_order" class="form-label">最小注文金額</label>
                                        <input type="number" step="0.01" class="form-control" id="min_order" name="min_order" value="0">
                                    </div>
                                    <div class="mb-3">
                                        <label for="max_uses" class="form-label">最大使用回数</label>
                                        <input type="number" class="form-control" id="max_uses" name="max_uses" placeholder="無制限の場合は空欄">
                                    </div>
                                    <div class="mb-3">
                                        <label for="expiry_date" class="form-label">有効期限</label>
                                        <input type="date" class="form-control" id="expiry_date" name="expiry_date">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="applicable_to" class="form-label">適用対象</label>
                                <select class="form-control" id="applicable_to" name="applicable_to">
                                    <option value="all">すべての商品</option>
                                    <option value="product">特定の商品</option>
                                </select>
                            </div>
                            <div class="mb-3" id="product_selection" style="display: none;">
                                <label class="form-label">対象商品を選択</label>
                                <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($books as $book): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="product_ids[]" value="<?php echo $book['ID_sanpham']; ?>" id="product_<?php echo $book['ID_sanpham']; ?>">
                                            <label class="form-check-label" for="product_<?php echo $book['ID_sanpham']; ?>">
                                                <?php echo htmlspecialchars($book['tensanpham']); ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <button type="submit" name="add_voucher" class="btn btn-primary">クーポンを追加</button>
                        </form>
                    </div>
                </div>

                <!-- Vouchers List -->
                <div class="card">
                    <div class="card-header">
                        <h5>クーポン一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>コード</th>
                                        <th>タイプ</th>
                                        <th>値</th>
                                        <th>最小注文</th>
                                        <th>使用回数</th>
                                        <th>有効期限</th>
                                        <th>適用対象</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($vouchers as $voucher): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($voucher['code']); ?></td>
                                            <td><?php echo $voucher['type'] == 'percentage' ? 'パーセント' : '固定金額'; ?></td>
                                            <td><?php echo $voucher['type'] == 'percentage' ? $voucher['value'] . '%' : number_format($voucher['value']) . ' 円'; ?></td>
                                            <td><?php echo number_format($voucher['min_order']); ?> 円</td>
                                            <td><?php echo $voucher['uses_count'] ?? 0; ?>/<?php echo $voucher['max_uses'] ?? '無制限'; ?></td>
                                            <td><?php echo $voucher['expiry_date'] ? date('Y-m-d', strtotime($voucher['expiry_date'])) : 'なし'; ?></td>
                                            <td><?php echo $voucher['applicable_to'] == 'all' ? 'すべて' : '特定の商品'; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-2" onclick="editVoucher(<?php echo $voucher['id']; ?>)">
                                                    <i class="fas fa-edit"></i> 編集
                                                </button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('このクーポンを削除しますか？')">
                                                    <input type="hidden" name="voucher_id" value="<?php echo $voucher['id']; ?>">
                                                    <button type="submit" name="delete_voucher" class="btn btn-sm btn-outline-danger">
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

    <!-- Edit Voucher Modal -->
    <div class="modal fade" id="editVoucherModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">クーポン編集</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="voucher_id" id="edit_voucher_id">
                        <!-- Form fields similar to add form -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_code" class="form-label">クーポンコード *</label>
                                    <input type="text" class="form-control" id="edit_code" name="code" required>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_type" class="form-label">タイプ</label>
                                    <select class="form-control" id="edit_type" name="type">
                                        <option value="percentage">パーセント</option>
                                        <option value="fixed">固定金額</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="edit_value" class="form-label">値 *</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_value" name="value" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_min_order" class="form-label">最小注文金額</label>
                                    <input type="number" step="0.01" class="form-control" id="edit_min_order" name="min_order">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_max_uses" class="form-label">最大使用回数</label>
                                    <input type="number" class="form-control" id="edit_max_uses" name="max_uses">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_expiry_date" class="form-label">有効期限</label>
                                    <input type="date" class="form-control" id="edit_expiry_date" name="expiry_date">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_applicable_to" class="form-label">適用対象</label>
                            <select class="form-control" id="edit_applicable_to" name="applicable_to">
                                <option value="all">すべての商品</option>
                                <option value="product">特定の商品</option>
                            </select>
                        </div>
                        <div class="mb-3" id="edit_product_selection" style="display: none;">
                            <label class="form-label">対象商品を選択</label>
                            <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($books as $book): ?>
                                    <div class="form-check">
                                        <input class="form-check-input edit-product-checkbox" type="checkbox" name="product_ids[]" value="<?php echo $book['ID_sanpham']; ?>" id="edit_product_<?php echo $book['ID_sanpham']; ?>">
                                        <label class="form-check-label" for="edit_product_<?php echo $book['ID_sanpham']; ?>">
                                            <?php echo htmlspecialchars($book['tensanpham']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_voucher" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle product selection visibility
        document.getElementById('applicable_to').addEventListener('change', function() {
            document.getElementById('product_selection').style.display = this.value === 'product' ? 'block' : 'none';
        });

        document.getElementById('edit_applicable_to').addEventListener('change', function() {
            document.getElementById('edit_product_selection').style.display = this.value === 'product' ? 'block' : 'none';
        });

        function editVoucher(voucherId) {
            // In a real implementation, you'd fetch voucher data via AJAX
            // For now, just open the modal
            document.getElementById('edit_voucher_id').value = voucherId;
            new bootstrap.Modal(document.getElementById('editVoucherModal')).show();
        }
    </script>
</body>
</html>

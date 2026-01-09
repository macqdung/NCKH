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
    if (isset($_POST['update_return_status'])) {
        $return_id = $_POST['return_id'];
        $status = $_POST['status'];
        $notes = $_POST['notes'] ?? null;

        if ($admin->update_return_status($return_id, $status, $notes)) {
            $message = '返品ステータスが更新されました。';
        } else {
            $message = '返品ステータスの更新に失敗しました。';
        }
    }
}

// Get data
$returns = $admin->get_all_returns();
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
                    <h1 class="h2">返品管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Returns List -->
                <div class="card">
                    <div class="card-header">
                        <h5>返品リクエスト一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>注文ID</th>
                                        <th>ユーザー</th>
                                        <th>商品</th>
                                        <th>数量</th>
                                        <th>理由</th>
                                        <th>ステータス</th>
                                        <th>リクエスト日</th>
                                        <th>処理日</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($returns as $return): ?>
                                        <tr>
                                            <td><?php echo $return['id']; ?></td>
                                            <td><?php echo $return['order_id']; ?></td>
                                            <td><?php echo htmlspecialchars($return['tendangnhap']); ?></td>
                                            <td><?php echo htmlspecialchars($return['tensanpham']); ?></td>
                                            <td><?php echo $return['quantity']; ?></td>
                                            <td><?php echo htmlspecialchars($return['reason']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                    echo $return['status'] == 'pending' ? 'warning' :
                                                         ($return['status'] == 'approved' ? 'success' :
                                                          ($return['status'] == 'rejected' ? 'danger' : 'secondary'));
                                                ?>">
                                                    <?php
                                                    echo $return['status'] == 'pending' ? '保留中' :
                                                         ($return['status'] == 'approved' ? '承認済み' :
                                                          ($return['status'] == 'rejected' ? '拒否' : '不明'));
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('Y-m-d', strtotime($return['request_date'])); ?></td>
                                            <td><?php echo $return['processed_date'] ? date('Y-m-d', strtotime($return['processed_date'])) : '-'; ?></td>
                                            <td>
                                                <?php if ($return['status'] == 'pending'): ?>
                                                    <button class="btn btn-sm btn-outline-success me-2" onclick="updateReturnStatus(<?php echo $return['id']; ?>, 'approved')">
                                                        <i class="fas fa-check"></i> 承認
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger me-2" onclick="updateReturnStatus(<?php echo $return['id']; ?>, 'rejected')">
                                                        <i class="fas fa-times"></i> 拒否
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">処理済み</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Return Statistics -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6>保留中の返品</h6>
                            </div>
                            <div class="card-body text-center">
                                <h3 class="text-warning">
                                    <?php echo count(array_filter($returns, fn($r) => $r['status'] == 'pending')); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6>承認済みの返品</h6>
                            </div>
                            <div class="card-body text-center">
                                <h3 class="text-success">
                                    <?php echo count(array_filter($returns, fn($r) => $r['status'] == 'approved')); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6>拒否された返品</h6>
                            </div>
                            <div class="card-body text-center">
                                <h3 class="text-danger">
                                    <?php echo count(array_filter($returns, fn($r) => $r['status'] == 'rejected')); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">返品ステータス更新</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="return_id" id="return_id">
                        <input type="hidden" name="status" id="status">
                        <div class="mb-3">
                            <label for="notes" class="form-label">メモ（オプション）</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <p id="statusMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="update_return_status" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateReturnStatus(returnId, status) {
            document.getElementById('return_id').value = returnId;
            document.getElementById('status').value = status;
            document.getElementById('statusMessage').textContent = status === 'approved' ? 'この返品を承認しますか？ 在庫が復元されます。' : 'この返品を拒否しますか？';
            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }
    </script>
</body>
</html>

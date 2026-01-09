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
    if (isset($_POST['add_staff'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $sdt = trim($_POST['sdt']);
        $role = $_POST['role'];

        if (!empty($username) && !empty($password) && !empty($sdt)) {
            if ($admin->add_staff($username, $password, $sdt, $role)) {
                $message = 'スタッフが追加されました。';
            } else {
                $message = 'スタッフの追加に失敗しました。ユーザー名が重複している可能性があります。';
            }
        }
    } elseif (isset($_POST['update_role'])) {
        $id = $_POST['staff_id'];
        $role = $_POST['role'];
        if ($admin->update_staff_role($id, $role)) {
            $message = 'スタッフの役割が更新されました。';
        } else {
            $message = 'スタッフの役割の更新に失敗しました。';
        }
    } elseif (isset($_POST['delete_staff'])) {
        $id = $_POST['staff_id'];
        if ($admin->delete_staff($id)) {
            $message = 'スタッフが削除されました。';
        } else {
            $message = 'スタッフの削除に失敗しました。';
        }
    }
}

// Get data
$staffs = $admin->get_all_staff();
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
                    <h1 class="h2">スタッフ管理</h1>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Add Staff Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>新しいスタッフを追加</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">ユーザー名 *</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">パスワード *</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sdt" class="form-label">電話番号 *</label>
                                        <input type="text" class="form-control" id="sdt" name="sdt" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="role" class="form-label">役割</label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="nhanvien">スタッフ</option>
                                            <option value="admin">管理者</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="add_staff" class="btn btn-primary">スタッフを追加</button>
                        </form>
                    </div>
                </div>

                <!-- Staff List -->
                <div class="card">
                    <div class="card-header">
                        <h5>スタッフ一覧</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>ユーザー名</th>
                                        <th>役割</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($staffs as $staff): ?>
                                        <tr>
                                            <td><?php echo $staff['ID_user']; ?></td>
                                            <td><?php echo htmlspecialchars($staff['tendangnhap']); ?></td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="staff_id" value="<?php echo $staff['ID_user']; ?>">
                                                    <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                        <option value="nhanvien" <?php echo $staff['role'] == 'nhanvien' ? 'selected' : ''; ?>>スタッフ</option>
                                                        <option value="admin" <?php echo $staff['role'] == 'admin' ? 'selected' : ''; ?>>管理者</option>
                                                    </select>
                                                    <input type="hidden" name="update_role" value="1">
                                                </form>
                                            </td>
                                            <td>
                                                <?php if ($staff['role'] !== 'admin' || count(array_filter($staffs, fn($s) => $s['role'] === 'admin')) > 1): ?>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('このスタッフを削除しますか？')">
                                                        <input type="hidden" name="staff_id" value="<?php echo $staff['ID_user']; ?>">
                                                        <button type="submit" name="delete_staff" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i> 削除
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">削除不可</span>
                                                <?php endif; ?>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

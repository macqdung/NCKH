<?php
include_once '../MODEL/modeldangky.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tendangnhap = trim($_POST['tendangnhap']);
    $matkhau = $_POST['matkhau'];
    $nhapmatkhau = $_POST['nhapmatkhau'];
    $sdt = trim($_POST['sdt']);
    $email = trim($_POST['email']);

    $errors = [];

    // Basic validation
    if (empty($tendangnhap) || empty($matkhau) || empty($nhapmatkhau) || empty($sdt) || empty($email)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    }

    if ($matkhau !== $nhapmatkhau) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    }

    // Validate phone number length and digits
    if (!preg_match('/^\d{10}$/', $sdt)) {
        $errors[] = "Số điện thoại phải gồm 10 chữ số.";
    }

    $userModel = new data_user();

    // Check if username already exists
    if ($userModel->check_username_exists($tendangnhap)) {
        $errors[] = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
    }

    if (count($errors) === 0) {
        // Insert user
        $inserted = $userModel->insert_user($tendangnhap, $matkhau, $sdt, $email, 'khách hàng');
        if ($inserted) {
            $success = "Đăng ký thành công! Bạn có thể <a href='../VIEW/dangnhap.php'>đăng nhập</a> ngay bây giờ.";
        } else {
            $errors[] = "Đăng ký thất bại, vui lòng thử lại.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Đăng ký tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4>Đăng ký tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php
                    if (!empty($errors)) {
                        echo '<div class="alert alert-danger"><ul>';
                        foreach ($errors as $error) {
                            echo '<li>' . htmlspecialchars($error) . '</li>';
                        }
                        echo '</ul></div>';
                    }
                    ?>
                    <form name="formDangKy" method="post" action="">
                        <div class="mb-3">
                            <label for="tendangnhap" class="form-label">Tên đăng nhập</label>
                            <input type="text" class="form-control" name="tendangnhap" maxlength="35" required value="<?php echo isset($tendangnhap) ? htmlspecialchars($tendangnhap) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="matkhau" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" name="matkhau" required>
                        </div>

                        <div class="mb-3">
                            <label for="nhapmatkhau" class="form-label">Nhập lại mật khẩu</label>
                            <input type="password" class="form-control" name="nhapmatkhau" required>
                        </div>

                        <div class="mb-3">
                            <label for="sdt" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" name="sdt" maxlength="10" required value="<?php echo isset($sdt) ? htmlspecialchars($sdt) : ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        </div>

                        <button type="submit" class="btn btn-success w-100">Đăng ký</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <small>Đã có tài khoản? <a href="dangnhap.php">Đăng nhập</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

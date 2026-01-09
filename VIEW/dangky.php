<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
            $success = "Đăng ký thành công! Bạn có thể <a href='dangnhap.php'>đăng nhập</a> ngay bây giờ.";
        } else {
            $errors[] = "Đăng ký thất bại, vui lòng thử lại.";
        }
    }
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>新規登録 - 本屋さん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .auth-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .auth-header i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        .auth-body {
            padding: 40px 30px;
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
        }
        .strength-weak { color: #dc3545; }
        .strength-medium { color: #ffc107; }
        .strength-strong { color: #28a745; }
        .btn-register {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }
        .auth-links {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .auth-links a {
            color: #28a745;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .auth-links a:hover {
            color: #1e7e34;
        }
        .terms-checkbox {
            margin-bottom: 20px;
        }
        .terms-checkbox .form-check-input:checked {
            background-color: #28a745;
            border-color: #28a745;
        }
        .divider {
            margin: 15px 0;
            position: relative;
            text-align: center;
        }
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }
        .divider span {
            background: white;
            padding: 0 15px;
            color: #6c757d;
            font-size: 14px;
        }
        @media (max-width: 576px) {
            .auth-container {
                padding: 20px;
            }
            .auth-card {
                margin: 0;
            }
            .auth-header, .auth-body {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>

    <div class="noidung">
        <div class="luoi chieurongluoi">
            <div class="auth-container">
                <div class="auth-card">
                    <div class="auth-header">
                        <i class="fas fa-user-plus"></i>
                        <h3 class="mb-2">新規アカウント登録</h3>
                        <p class="mb-0 opacity-75">本屋さんで素敵なショッピングをお楽しみください</p>
                    </div>

                    <div class="auth-body">
                        <?php
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger"><ul>';
                            foreach ($errors as $error) {
                                echo '<li>' . htmlspecialchars($error) . '</li>';
                            }
                            echo '</ul></div>';
                        }
                        if (!empty($success)) {
                            echo '<div class="alert alert-success">' . $success . '</div>';
                        }
                        ?>
                        <form method="post" action="">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tendangnhap" name="tendangnhap" maxlength="35" placeholder="ユーザー名" required value="<?php echo isset($tendangnhap) ? htmlspecialchars($tendangnhap) : ''; ?>">
                                <label for="tendangnhap">
                                    <i class="fas fa-user me-2"></i>ユーザー名
                                </label>
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="パスワード" required oninput="checkPasswordStrength()">
                                <label for="matkhau">
                                    <i class="fas fa-lock me-2"></i>パスワード
                                </label>
                                <div id="password-strength" class="password-strength"></div>
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control" id="nhapmatkhau" name="nhapmatkhau" placeholder="パスワード確認" required>
                                <label for="nhapmatkhau">
                                    <i class="fas fa-lock me-2"></i>パスワード確認
                                </label>
                            </div>

                            <div class="form-floating">
                                <input type="text" class="form-control" id="sdt" name="sdt" maxlength="10" placeholder="電話番号" required value="<?php echo isset($sdt) ? htmlspecialchars($sdt) : ''; ?>">
                                <label for="sdt">
                                    <i class="fas fa-phone me-2"></i>電話番号
                                </label>
                            </div>

                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="メールアドレス" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                                <label for="email">
                                    <i class="fas fa-envelope me-2"></i>メールアドレス
                                </label>
                            </div>

                            <button type="submit" class="btn btn-register">
                                <i class="fas fa-user-plus me-2"></i>新規登録
                            </button>
                        </form>
                    </div>

                    <div class="auth-links">
                        <p class="mb-2">すでにアカウントをお持ちですか？</p>
                        <a href="dangnhap.php">
                            <i class="fas fa-sign-in-alt me-1"></i>ログイン
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function checkPasswordStrength() {
            const password = document.getElementById('matkhau').value;
            const strengthIndicator = document.getElementById('password-strength');

            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength++;
            else feedback.push('8文字以上');

            if (/[a-z]/.test(password)) strength++;
            else feedback.push('小文字');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('大文字');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('数字');

            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('記号');

            let strengthText = '';
            let strengthClass = '';

            switch(strength) {
                case 0:
                case 1:
                    strengthText = '弱いパスワード';
                    strengthClass = 'strength-weak';
                    break;
                case 2:
                case 3:
                    strengthText = '普通のパスワード';
                    strengthClass = 'strength-medium';
                    break;
                case 4:
                case 5:
                    strengthText = '強いパスワード';
                    strengthClass = 'strength-strong';
                    break;
            }

            strengthIndicator.innerHTML = `<span class="${strengthClass}">${strengthText}</span>`;
            if (feedback.length > 0 && password.length > 0) {
                strengthIndicator.innerHTML += ` - 推奨: ${feedback.join(', ')}`;
            }
        }

        // Password confirmation validation
        document.getElementById('nhapmatkhau').addEventListener('input', function() {
            const password = document.getElementById('matkhau').value;
            const confirmPassword = this.value;
            const submitBtn = document.querySelector('.btn-register');

            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('パスワードが一致しません');
                submitBtn.disabled = true;
            } else {
                this.setCustomValidity('');
                submitBtn.disabled = false;
            }
        });
    </script>
</body>
</html>

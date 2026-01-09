<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once '../MODEL/modelquenmk.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../VIEW/PHPMailer/src/Exception.php';
require '../VIEW/PHPMailer/src/PHPMailer.php';
require '../VIEW/PHPMailer/src/SMTP.php';

$userModel = new data_user_forgot();
$errors = [];
$success = '';
$step = 1; // Step 1: enter email, Step 2: enter code and new password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_code'])) {
        // Step 1: Send verification code to email
        $email = trim($_POST['email']);
        if (empty($email)) {
            $errors[] = "メールアドレスを入力してください。";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "メールアドレスが無効です。";
        } else {
            $user = $userModel->get_user_by_email($email);
            if (!$user) {
                $errors[] = "このメールアドレスは登録されていません。";
            } else {
                // Generate 6-digit code
                $code = random_int(100000, 999999);
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_code'] = $code;
                $_SESSION['code_time'] = time();

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your_email@gmail.com'; // TODO: replace with your email
                    $mail->Password = 'your_email_password'; // TODO: replace with your email password or app password
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('your_email@gmail.com', '本屋さん');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'パスワードリセット確認コード';
                    $mail->Body = "確認コード: <b>$code</b><br>このコードは10分間有効です。";

                    $mail->send();
                    $success = "確認コードをメールアドレスに送信しました。";
                    $step = 2;
                } catch (Exception $e) {
                    $errors[] = "メール送信に失敗しました: {$mail->ErrorInfo}";
                }
            }
        }
    } elseif (isset($_POST['verify_code'])) {
        // Step 2: Verify code and reset password
        $email = $_SESSION['reset_email'] ?? '';
        $code = trim($_POST['code']);
        $matkhaumoi = $_POST['matkhaumoi'];
        $nhaplaimatkhau = $_POST['nhaplaimatkhau'];

        if (empty($code) || empty($matkhaumoi) || empty($nhaplaimatkhau)) {
            $errors[] = "すべての項目を入力してください。";
        } elseif ($matkhaumoi !== $nhaplaimatkhau) {
            $errors[] = "新しいパスワードと確認パスワードが一致しません。";
        } elseif ($code != ($_SESSION['reset_code'] ?? '') || (time() - ($_SESSION['code_time'] ?? 0)) > 600) {
            $errors[] = "確認コードが無効または期限切れです。";
        } else {
            // Update password
            $updated = $userModel->update_password_by_email($email, $matkhaumoi);
            if ($updated) {
                unset($_SESSION['reset_email'], $_SESSION['reset_code'], $_SESSION['code_time']);
                $success = "パスワードが正常に更新されました！<a href='dangnhap.php'>ログイン</a>してください。";
                $step = 1;
            } else {
                $errors[] = "パスワード更新に失敗しました。再度お試しください。";
                $step = 2;
            }
        }
    }
}
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>パスワードリセット - 本屋さん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        .auth-container {
            min-height: 80vh;
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
            max-width: 450px;
            width: 100%;
        }
        .auth-header {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
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
            border-color: #ffc107;
            box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
        }
        .btn-send-code, .btn-reset {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-send-code:hover, .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
        }
        .auth-links {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .auth-links a {
            color: #ffc107;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .auth-links a:hover {
            color: #e0a800;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 10px;
            position: relative;
        }
        .step.active {
            background: #ffc107;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 30px;
            height: 2px;
            background: #e9ecef;
            margin-left: 10px;
        }
        .step:last-child::after {
            display: none;
        }
        .code-input {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
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
                        <i class="fas fa-key"></i>
                        <h3 class="mb-2">パスワードリセット</h3>
                        <p class="mb-0 opacity-75">安全にパスワードをリセットします</p>
                    </div>

                    <div class="auth-body">
                        <div class="step-indicator">
                            <div class="step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">1</div>
                            <div class="step <?= $step >= 2 ? 'active' : '' ?>">2</div>
                        </div>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">
                                <?= $success ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($step === 1): ?>
                            <form method="post" action="">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="メールアドレス" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                                    <label for="email">
                                        <i class="fas fa-envelope me-2"></i>メールアドレス
                                    </label>
                                </div>
                                <button type="submit" name="send_code" class="btn btn-send-code">
                                    <i class="fas fa-paper-plane me-2"></i>確認コードを送信
                                </button>
                            </form>
                        <?php elseif ($step === 2): ?>
                            <form method="post" action="">
                                <div class="form-floating">
                                    <input type="text" class="form-control code-input" id="code" name="code" maxlength="6" placeholder="000000" required>
                                    <label for="code">
                                        <i class="fas fa-hashtag me-2"></i>確認コード (6桁)
                                    </label>
                                </div>

                                <div class="form-floating">
                                    <input type="password" class="form-control" id="matkhaumoi" name="matkhaumoi" placeholder="新しいパスワード" required>
                                    <label for="matkhaumoi">
                                        <i class="fas fa-lock me-2"></i>新しいパスワード
                                    </label>
                                </div>

                                <div class="form-floating">
                                    <input type="password" class="form-control" id="nhaplaimatkhau" name="nhaplaimatkhau" placeholder="パスワード確認" required>
                                    <label for="nhaplaimatkhau">
                                        <i class="fas fa-lock me-2"></i>パスワード確認
                                    </label>
                                </div>

                                <button type="submit" name="verify_code" class="btn btn-reset">
                                    <i class="fas fa-check me-2"></i>確認 & パスワード更新
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="auth-links">
                        <a href="dangnhap.php">
                            <i class="fas fa-arrow-left me-1"></i>ログインに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

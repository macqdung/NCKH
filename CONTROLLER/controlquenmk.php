<?php
session_start();
include_once '../MODEL/modelquenmk.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../VIEW/PHPMailer/src/Exception.php';
require '../VIEW/PHPMailer/src/PHPMailer.php';
require '../VIEW/PHPMailer/src/SMTP.php';

$step = isset($_POST['step']) ? $_POST['step'] : 'enter_email';
$email = '';
$code = '';
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 'send_code') {
        $email = trim($_POST['email']);
        if (empty($email)) {
            $errors[] = "Vui lòng nhập email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        } else {
            $userModel = new data_user_forgot();
            $user = $userModel->get_user_by_email($email);
            if (!$user) {
                $errors[] = "Email không tồn tại trong hệ thống.";
            } else {
                // Generate 6-digit code
                $code = sprintf("%06d", mt_rand(1, 999999));
                $_SESSION['reset_code'] = $code;
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_expiry'] = time() + 300; // 5 minutes expiry

                // Send email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings - UPDATE THESE WITH YOUR SMTP CREDENTIALS
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // For Gmail
                    $mail->SMTPAuth = true;
                    $mail->Username = 'mdung0106@gmail.com'; // Your Gmail address
                    $mail->Password = 'qabq woju hbhf wtnq'; // Your Gmail app password (no spaces)
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Enable verbose debug output
                    $mail->SMTPDebug = 2;
                    $mail->Debugoutput = 'html';

                    $mail->setFrom('mdung0106@gmail.com', 'Bakery Shop');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Mã xác thực đặt lại mật khẩu';
                    $mail->Body = "Mã xác thực của bạn là: <b>$code</b>. Mã này có hiệu lực trong 5 phút.";

                    $mail->send();
                    $success = "Mã xác thực đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.";
                    $step = 'verify_code';
                } catch (Exception $e) {
                    $errors[] = "Gửi email thất bại: {$mail->ErrorInfo}. Vui lòng kiểm tra cấu hình SMTP.";
                }
            }
        }
    } elseif ($step === 'verify_code') {
        $entered_code = trim($_POST['code']);
        $matkhaumoi = $_POST['matkhaumoi'];
        $nhaplaimatkhau = $_POST['nhaplaimatkhau'];
        $email = $_SESSION['reset_email'] ?? '';

        if (empty($entered_code) || empty($matkhaumoi) || empty($nhaplaimatkhau)) {
            $errors[] = "Vui lòng điền đầy đủ thông tin.";
        }

        if ($matkhaumoi !== $nhaplaimatkhau) {
            $errors[] = "Mật khẩu mới và nhập lại không khớp.";
        }

        if (empty($errors)) {
            if (time() > $_SESSION['reset_expiry']) {
                $errors[] = "Mã xác thực đã hết hạn. Vui lòng yêu cầu gửi lại.";
            } elseif ($entered_code !== $_SESSION['reset_code']) {
                $errors[] = "Mã xác thực không đúng.";
            } else {
                global $conn;
                $hashed_password = password_hash($matkhaumoi, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET matkhau = ? WHERE email = ?");
                $stmt->bind_param("ss", $hashed_password, $email);
                if ($stmt->execute()) {
                    unset($_SESSION['reset_code'], $_SESSION['reset_email'], $_SESSION['reset_expiry']);
                    $success = "Cập nhật mật khẩu thành công! Bạn có thể <a href='dangnhap.php'>đăng nhập</a> ngay.";
                } else {
                    $errors[] = "Cập nhật mật khẩu thất bại.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lấy lại mật khẩu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-warning text-dark text-center">
          <h4>Lấy lại mật khẩu</h4>
        </div>
        <div class="card-body">
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <ul>
                <?php foreach ($errors as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
          <?php endif; ?>

          <?php if ($step === 'enter_email'): ?>
            <form method="post" action="">
              <input type="hidden" name="step" value="send_code">
              <div class="mb-3">
                <label for="email" class="form-label">Nhập email của bạn</label>
                <input type="email" class="form-control" name="email" required value="<?php echo htmlspecialchars($email); ?>">
              </div>
              <button type="submit" class="btn btn-warning w-100">Gửi mã xác thực</button>
            </form>
          <?php elseif ($step === 'verify_code'): ?>
            <form method="post" action="">
              <input type="hidden" name="step" value="verify_code">
              <div class="mb-3">
                <label for="code" class="form-label">Nhập mã xác thực (6 chữ số)</label>
                <input type="text" class="form-control" name="code" maxlength="6" required>
              </div>
              <div class="mb-3">
                <label for="matkhaumoi" class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control" name="matkhaumoi" required>
              </div>
              <div class="mb-3">
                <label for="nhaplaimatkhau" class="form-label">Nhập lại mật khẩu</label>
                <input type="password" class="form-control" name="nhaplaimatkhau" required>
              </div>
              <button type="submit" class="btn btn-warning w-100">Xác thực & Cập nhật mật khẩu</button>
            </form>
          <?php endif; ?>
        </div>
        <div class="card-footer text-center">
          <small>Quay về <a href="dangnhap.php">Đăng nhập</a></small>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ログイン - 本屋さん</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }
        .auth-links {
            text-align: center;
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .auth-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .auth-links a:hover {
            color: #0056b3;
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
                        <i class="fas fa-user-circle"></i>
                        <h3 class="mb-2">ログイン</h3>
                        <p class="mb-0 opacity-75">アカウントにログインしてショッピングをお楽しみください</p>
                    </div>

                    <div class="auth-body">
                        <form method="post" action="../CONTROLLER/controldangnhap.php">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="tendangnhap" name="tendangnhap" maxlength="35" placeholder="ユーザー名" required>
                                <label for="tendangnhap">
                                    <i class="fas fa-user me-2"></i>ユーザー名
                                </label>
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control" id="matkhau" name="matkhau" placeholder="パスワード" required>
                                <label for="matkhau">
                                    <i class="fas fa-lock me-2"></i>パスワード
                                </label>
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>ログイン
                            </button>
                        </form>

                        <div class="divider">
                            <span>または</span>
                        </div>

                        <div class="text-center">
                            <p class="text-muted mb-3">SNSアカウントでログイン</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-google me-1"></i>Google
                                </button>
                                <button class="btn btn-outline-primary btn-sm">
                                    <i class="fab fa-facebook me-1"></i>Facebook
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="auth-links">
                        <div class="row text-center">
                            <div class="col-6">
                                <a href="quenmk.php">
                                    <i class="fas fa-key me-1"></i>パスワードを忘れた
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="dangky.php">
                                    <i class="fas fa-user-plus me-1"></i>新規登録
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

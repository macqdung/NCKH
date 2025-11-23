<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../CONTROLLER/controlvouchers.php');
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>私のバウチャー - 本屋さん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        .voucher-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }
        .voucher-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .btn-claim {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-claim:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        .claimed-card {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .invalid-card {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            opacity: 0.8;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.7;
        }
        .section-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        @media (max-width: 768px) {
            .voucher-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>

    <div class="noidung">
        <div class="luoi chieurongluoi py-5">
            <div class="hang">
                <div class="cot cot-12">
                    <h1 class="text-center mb-4">私のバウチャー</h1>
                    <p class="text-center text-muted mb-5">特別な割引と特典をお楽しみください</p>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <div class="alert <?= strpos($message, '成功') !== false ? 'alert-success' : 'alert-danger' ?> text-center mb-4">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Available vouchers -->
            <div class="hang">
                <div class="cot cot-12">
                    <h2 class="section-title">利用可能なバウチャー</h2>
                    <?php if (!empty($available_vouchers)): ?>
                        <div class="row" id="availableVouchers">
                            <?php foreach ($available_vouchers as $voucher): ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="voucher-card">
                                        <div class="text-center">
                                            <i class="fas fa-ticket-alt voucher-icon text-success"></i>
                                        </div>
                                        <h5 class="card-title text-center mb-3">コード: <?= htmlspecialchars($voucher['code']) ?></h5>
                                        <div class="card-text">
                                            <p><strong>割引:</strong> <?= $voucher['type'] == 'percent' ? $voucher['value'] . '%' : number_format($voucher['value'], 0, ',', '.') . ' 円' ?></p>
                                            <p><strong>最低注文額:</strong> <?= number_format($voucher['min_order'], 0, ',', '.') ?> 円</p>
                                            <p><strong>有効期限:</strong> <?= $voucher['expiry_date'] ? date('Y/m/d', strtotime($voucher['expiry_date'])) : '無期限' ?></p>
                                            <p><strong>適用対象:</strong> <?= $voucher['applicable_to'] == 'order' ? '注文' : '特定商品' ?></p>
                                        </div>
                                        <form method="post" class="text-center">
                                            <input type="hidden" name="voucher_code" value="<?= htmlspecialchars($voucher['code']) ?>">
                                            <button type="submit" name="claim_voucher" class="btn btn-claim">バウチャーを受け取る</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt text-muted"></i>
                            <h4>利用可能なバウチャーがありません</h4>
                            <p>新しいバウチャーが利用可能になるまでお待ちください。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Claimed vouchers -->
            <div class="hang">
                <div class="cot cot-12">
                    <h2 class="section-title mt-5">受け取ったバウチャー</h2>
                    <?php if (!empty($claimed_vouchers)): ?>
                        <div class="row" id="claimedVouchers">
                            <?php foreach ($claimed_vouchers as $voucher):
                                $expiry_date = isset($voucher['expiry_date']) ? $voucher['expiry_date'] : '';
                                $is_expired = false;
                                if ($expiry_date && $expiry_date !== '0000-00-00' && $expiry_date !== '30/11/-0001') {
                                    $is_expired = (strtotime($expiry_date) < strtotime(date('Y-m-d')));
                                }
                                $is_out_of_uses = (isset($voucher['max_uses_total']) && $voucher['max_uses_total'] > 0 && isset($voucher['uses_count']) && $voucher['uses_count'] >= $voucher['max_uses_total']);
                                $invalid = $is_expired || $is_out_of_uses;
                            ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="voucher-card <?= $invalid ? 'invalid-card' : 'claimed-card' ?>">
                                        <div class="text-center">
                                            <i class="fas fa-ticket-alt voucher-icon <?= $invalid ? 'text-muted' : 'text-white' ?>"></i>
                                        </div>
                                        <h5 class="card-title text-center mb-3">コード: <?= htmlspecialchars($voucher['code']) ?></h5>
                                        <div class="card-text">
                                            <p><strong>割引:</strong> <?= $voucher['type'] == 'percent' ? $voucher['value'] . '%' : number_format($voucher['value'], 0, ',', '.') . ' 円' ?></p>
                                            <p><strong>最低注文額:</strong> <?= number_format($voucher['min_order'], 0, ',', '.') ?> 円</p>
                                            <p><strong>有効期限:</strong> <?= $voucher['expiry_date'] ? date('Y/m/d', strtotime($voucher['expiry_date'])) : '無期限' ?></p>
                                            <p><strong>受け取り日:</strong> <?= date('Y/m/d H:i', strtotime($voucher['claimed_at'])) ?></p>
                                            <p><strong>適用対象:</strong> <?= $voucher['applicable_to'] == 'order' ? '注文' : '特定商品' ?></p>
                                            <?php if ($invalid): ?>
                                                <p class="text-danger fw-bold">バウチャーが無効です</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt text-muted"></i>
                            <h4>受け取ったバウチャーがありません</h4>
                            <p>バウチャーを受け取って特典をお楽しみください。</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>
</body>
</html>

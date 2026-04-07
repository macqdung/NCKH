<?php
session_start();
include('../MODEL/modelgiohang.php');
include('../MODEL/modelmqd1.php');
include('../MODEL/modeldangnhap.php');
include('../CONTROLLER/controlreview.php');
include_once('../MODEL/modeladmin.php');
$data = new data_muahang();
$data_cart = new data_mqd1();
$userModel = new data_user_login();
$admin_model = new data_admin();

$cartItems = GioHang::getItems();

$message = '';
$show_message_id = null;

    if (isset($_POST['addtocart'])) {
        $id_sanpham = intval($_POST['id_sanpham']);
        $quantity = intval($_POST['txtmua']);
        if ($quantity > 0) {
            $product = $data_cart->select_sanpham_id($id_sanpham);
            if (!empty($product)) {
                $stock = $product[0]['soluong'];
                if ($quantity <= $stock) {
                    GioHang::addItem($id_sanpham, $quantity);
                    $message = "カートに追加されました";
                } else {
                    $message = "要求数量が在庫を超えています (残り $stock)";
                }
            } else {
                $message = "製品が存在しません";
            }
        } else {
            $message = "数量は0より大きくなければなりません";
        }
        $show_message_id = $id_sanpham;
    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>購入</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
      body { background-color: #ffffff; color: #000000; font-family: 'Helvetica Neue', Arial, sans-serif; }
    </style>
</head>
<body>
    <?php include('menu.php'); ?>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="text-center bg-light py-2">
            <h5>こんにちは <?= htmlspecialchars($_SESSION['user']) ?>! <a href="dangxuat.php" class="btn btn-outline-secondary btn-sm">ログアウト</a></h5>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['message']) && !empty($_GET['message'])): ?>
        <div class="alert alert-info" role="alert">
            <?= htmlspecialchars($_GET['message']) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>
    <div class="noidung">
        <div class="luoi chieurongluoi">
            <?php if (isset($_GET['mua']) && !empty($_GET['mua'])): ?>
                <?php
                $id_sanpham = intval($_GET['mua']);
                $select = $data->select_sanpham_id($id_sanpham);
                if (!empty($select)):
                    $se_sp = $select[0];
                ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <h2 class="text-center mb-4">製品詳細</h2>
                    </div>
                </div>
                <div class="hang">
                    <div class="cot cot-6 maytinhbang-cot-6 dienthoai-cot-12">
                        <img src="../media/<?= $se_sp['hinhanh'] ?? '1.jpg' ?>" class="kichthuocanh1" alt="<?= htmlspecialchars($se_sp['tensanpham']) ?>">
                    </div>
                    <div class="cot cot-6 maytinhbang-cot-6 dienthoai-cot-12">
                        <h3><?= htmlspecialchars($se_sp['tensanpham']) ?></h3>
                        <p><strong>説明:</strong> <?= htmlspecialchars($se_sp['mota']) ?></p>
                        <p><strong>在庫数:</strong> <?= $se_sp['soluong'] ?></p>
                        <p><strong>単価:</strong> <span id="original-price"><?= number_format($se_sp['dongia'], 0, ',', '.') ?></span> 円</p>
                        <p id="discounted-price-display" style="display: none;"><strong>割引後価格:</strong> <span id="discounted-dongia">0</span> 円</p>
                        <form method="post" action="thanhtoan.php" class="mt-3">
                            <input type="hidden" name="id_sanpham" value="<?= $se_sp['ID_sanpham'] ?>">
                            <input type="hidden" name="dongia" value="<?= $se_sp['dongia'] ?>">
                            <div class="mb-3">
                                <label for="txtmua" class="form-label">購入数量:</label>
                                <input type="number" id="txtmua" name="txtmua" class="form-control" min="1" max="<?= $se_sp['soluong'] ?>" value="1" required oninput="tinhTongTien(this, <?= $se_sp['dongia'] ?>)">
                            </div>
                            <div class="mb-3">
                                <label for="tongtien" class="form-label">合計金額:</label>
                                <input type="text" id="tongtien" name="tongtien" class="form-control" value="<?= number_format($se_sp['dongia'], 0, ',', '.') ?>" placeholder="合計金額" readonly>
                            </div>
                            <?php
                            // 常にログイン済みユーザーに対してクーポン選択とプレビューを表示する
                            if (isset($_SESSION['user'])):
                                $user = $userModel->get_user_by_username($_SESSION['user']);
                                $id_user = $user['ID_user'];
                                $claimed_vouchers = $admin_model->get_user_claimed_vouchers($id_user);
                                $product_id = $se_sp['ID_sanpham'];
                            ?>
                                <div class="mb-3">
                                    <label for="voucher_select" class="form-label">クーポンを選択 (オプション):</label>
                                    <select id="voucher_select" name="voucher_id" class="form-control" onchange="applyVoucherDiscount()">
                                        <option value="">クーポンを使用しない</option>
                                        <?php foreach ($claimed_vouchers as $voucher): ?>
                                            <?php
                                            $option_text = $voucher['code'] . ' - ';
                                            if ($voucher['type'] == 'percent') {
                                                $option_text .= $voucher['value'] . '% off';
                                            } else {
                                                $option_text .= number_format($voucher['value'], 0, ',', '.') . ' 円 off';
                                            }
                                            $current_date = date('Y-m-d');
                                            $expiry_date = isset($voucher['expiry_date']) ? $voucher['expiry_date'] : '';
                                            $is_expired = false;
                                            if ($expiry_date && $expiry_date !== '0000-00-00' && $expiry_date !== '30/11/-0001') {
                                                $expiry_date_fmt = date_create($expiry_date) ? date_format(date_create($expiry_date), 'Y-m-d') : $expiry_date;
                                                if ($current_date > $expiry_date_fmt) $is_expired = true;
                                            }
                                            if ($is_expired) continue;
                                            if ($voucher['max_uses'] !== null && $voucher['max_uses'] > 0 && ($voucher['uses_count'] ?? 0) >= $voucher['max_uses']) continue;

                                            $is_applicable = true;
                                            if ($voucher['applicable_to'] == 'product') {
                                                $product_ids = json_decode($voucher['product_ids'], true) ?: [];
                                                $is_applicable = in_array($product_id, $product_ids);
                                                if (!$is_applicable) continue;
                                            }
                                            ?>
                                            <option value="<?= $voucher['id'] ?>"
                                                    data-type="<?= htmlspecialchars($voucher['type']) ?>"
                                                    data-value="<?= $voucher['value'] ?>"
                                                    data-applicable-to="<?= htmlspecialchars($voucher['applicable_to']) ?>"
                                                    data-min-order="<?= $voucher['min_order'] ?>">
                                                <?= htmlspecialchars($option_text) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">取得済みで適用可能なクーポンのみが表示されます。注文時にシステムが有効性を確認します。</small>
                                </div>
                                <div id="discount-preview" class="mb-3" style="display: none;">
                                    <p><strong>割引後価格:</strong> <span id="discounted-price">0</span> 円</p>
                                    <p><strong>節約:</strong> <span id="savings">0</span> 円</p>
                                </div>
                            <?php endif; ?>
                        <div class="d-flex gap-2">
                            <?php if (isset($_SESSION['user'])): ?> 
                                <button type="submit" name="datmua" class="btn btn-success">注文する</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-success" onclick="alert('商品を購入するにはログインしてください。'); window.location.href='dangnhap.php?redirect=muahang.php?mua=<?= $se_sp['ID_sanpham'] ?>';">注文する</button>
                            <?php endif; ?>
                            <button type="submit" name="addtocart" class="btn btn-primary">カートに追加</button>
                        </div>
                        <?php if ($show_message_id == $se_sp['ID_sanpham'] && $message): ?>
                            <div class="mt-3 alert alert-<?= $message == 'Thành công' ? 'success' : 'danger' ?>" role="alert">
                                <?= $message ?>
                            </div>
                        <?php endif; ?>
                        <p class="mt-3"><a href="mqd1.php" class="btn btn-primary">製品リストに戻る</a></p>
                    </div>
                </div>

                <script>
                let originalDongia = <?= $se_sp['dongia'] ?>;
                let currentSoluong = 1;

                function tinhTongTien(input, dongia) {
                    currentSoluong = input.value || 1;
                    var tongtien = originalDongia * currentSoluong;
                    input.form.tongtien.value = tongtien.toLocaleString('vi-VN');
                    applyVoucherDiscount(); // Re-apply discount on quantity change
                }

                function applyVoucherDiscount() {
                    const select = document.getElementById('voucher_select');
                    const voucherId = select.value;
                    const tongtienInput = document.querySelector('input[name="tongtien"]');
                    const originalPriceSpan = document.getElementById('original-price');
                    const discountedPriceDisplay = document.getElementById('discounted-price-display');
                    const discountedDongiaSpan = document.getElementById('discounted-dongia');
                    const discountPreview = document.getElementById('discount-preview');
                    const savingsSpan = document.getElementById('savings');
                    const tongtienValue = parseFloat(tongtienInput.value.replace(/\./g, '')) || (originalDongia * currentSoluong);

                    if (!voucherId) {
                        // No voucher selected
                        tongtienInput.value = tongtienValue.toLocaleString('vi-VN');
                        discountPreview.style.display = 'none';
                        discountedPriceDisplay.style.display = 'none';
                        return;
                    }

                    const option = select.options[select.selectedIndex];
                    const type = option.dataset.type;
                    const value = parseFloat(option.dataset.value);
                    const applicableTo = option.dataset.applicableTo;
                    const minOrder = parseFloat(option.dataset.minOrder);

                    if (tongtienValue < minOrder) {
                        alert('注文金額がクーポン使用条件を満たしていません (最低 ' + minOrder.toLocaleString('vi-VN') + ' 円)');
                        select.value = '';
                        return;
                    }

                    let discountAmount = 0;
                    let discountedTotal = tongtienValue;

                    if (applicableTo === 'product') {
                        // Product discount: Apply to dongia, then multiply by quantity
                        let discountedDongia = originalDongia;
                        if (type === 'percent') {
                            discountedDongia *= (1 - value / 100);
                        } else {
                            discountedDongia -= value;
                        }
                        discountedDongia = Math.max(0, discountedDongia); // No negative price
                        discountedTotal = discountedDongia * currentSoluong;
                        discountAmount = originalDongia * currentSoluong - discountedTotal;
                        // Update display
                        discountedPriceDisplay.style.display = 'block';
                        discountedDongiaSpan.textContent = discountedDongia.toLocaleString('vi-VN');
                    } else if (applicableTo === 'order') {
                        // Order discount: Apply to total
                        if (type === 'percent') {
                            discountAmount = tongtienValue * (value / 100);
                        } else {
                            discountAmount = Math.min(value, tongtienValue); // Don't exceed total
                        }
                        discountedTotal = tongtienValue - discountAmount;
                        discountAmount = Math.max(0, discountAmount);
                    }

                    tongtienInput.value = discountedTotal.toLocaleString('vi-VN');
                    document.getElementById('discounted-price').textContent = discountedTotal.toLocaleString('vi-VN');
                    discountPreview.style.display = 'block';
                    savingsSpan.textContent = discountAmount.toLocaleString('vi-VN');
                    originalPriceSpan.style.textDecoration = applicableTo === 'product' ? 'line-through' : 'none';
                }

                // Initialize on load
                document.addEventListener('DOMContentLoaded', function() {
                    const txtmua = document.getElementById('txtmua');
                    if (txtmua) {
                        txtmua.addEventListener('input', function() {
                            tinhTongTien(this, originalDongia);
                        });
                        // Set initial total
                        tinhTongTien(txtmua, originalDongia);
                    }
                });
                </script>
                <?php else: ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <p>製品が見つかりません。</p>
                    </div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <p>製品を選択してください。</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once('../MODEL/modelgiohang.php');
include_once('../MODEL/modelmqd1.php'); // For product details

$cartItems = GioHang::getItems();
?>

<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ショッピングカート - 本屋さん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
        .cart-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .cart-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }
        .cart-card.selected {
            border-color: #007bff;
            background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .quantity-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .cart-summary {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 20px;
        }
        .btn-checkout {
            background: linear-gradient(45deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
        .btn-checkout:disabled {
            background: #6c757d;
            transform: none;
        }
        .empty-cart {
            text-align: center;
            padding: 80px 20px;
            color: #6c757d;
        }
        .empty-cart i {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .section-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .select-all-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .cart-card {
                padding: 15px;
            }
            .product-image {
                width: 60px;
                height: 60px;
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
                    <h1 class="text-center mb-4">ショッピングカート</h1>
                    <p class="text-center text-muted mb-5">選択した商品を確認して、購入手続きにお進みください</p>
                </div>
            </div>

            <?php if (isset($_GET['message'])): ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <div class="alert alert-info text-center mb-4">
                            <?= htmlspecialchars($_GET['message']) ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <div class="hang">
                    <div class="cot cot-12">
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart text-muted"></i>
                            <h3>カートは空です</h3>
                            <p>お気に入りの本をカートに追加してください。</p>
                            <a href="mqd1.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>ショッピングを続ける
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="hang">
                    <div class="cot cot-8">
                        <div class="select-all-section">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this.checked)" <?= count($cartItems) > 0 ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-bold" for="selectAll">
                                            すべて選択 (<?= count($cartItems) ?> 商品)
                                        </label>
                                    </div>
                                </div>
                                <button class="btn btn-outline-danger" onclick="deleteSelected()" id="deleteBtn" style="display: none;">
                                    <i class="fas fa-trash me-1"></i>選択した商品を削除
                                </button>
                            </div>
                        </div>

                        <div id="cartList">
                            <?php foreach ($cartItems as $productId => $item):
                                $dataModel = new data_mqd1();
                                $product = $dataModel->getProductById($productId);
                                if (!$product) continue;
                                $isSelected = isset($item['selected']) ? $item['selected'] : true;
                                $itemTotal = $product['dongia'] * $item['quantity'];
                            ?>
                                <div class="cart-card <?= $isSelected ? 'selected' : '' ?>" data-product-id="<?= $productId ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="form-check me-3">
                                            <input class="form-check-input cart-checkbox" type="checkbox" id="select<?= $productId ?>" <?= $isSelected ? 'checked' : '' ?> onchange="toggleItem(this, <?= $productId ?>)">
                                            <label class="form-check-label" for="select<?= $productId ?>"></label>
                                        </div>

                                        <img src="../media/<?= htmlspecialchars($product['hinhanh'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($product['tensanpham']) ?>" class="product-image">

                                        <div class="flex-grow-1">
                                            <h5 class="mb-1"><?= htmlspecialchars($product['tensanpham']) ?></h5>
                                            <p class="text-primary fw-bold mb-2"><?= number_format($product['dongia'], 0, ',', '.') ?> 円</p>

                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="quantity-controls">
                                                    <span class="me-2">数量:</span>
                                                    <div class="quantity-btn" onclick="changeQuantity(<?= $productId ?>, -1, <?= $product['dongia'] ?>)">-</div>
                                                    <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" max="<?= $product['soluong'] ?>" onchange="updateQuantity(<?= $productId ?>, this.value, <?= $product['dongia'] ?>)">
                                                    <div class="quantity-btn" onclick="changeQuantity(<?= $productId ?>, 1, <?= $product['dongia'] ?>)">+</div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-primary fs-5 subtotal"><?= number_format($itemTotal, 0, ',', '.') ?> 円</div>
                                                    <small class="text-muted">小計</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ms-3">
                                            <a href="../CONTROLLER/controlgiohang.php?action=remove&id_sanpham=<?= $productId ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('この商品を削除しますか？')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="cot cot-4">
                        <div class="cart-summary">
                            <h4 class="mb-4">注文概要</h4>
                            <div class="d-flex justify-content-between mb-3">
                                <span>選択商品数:</span>
                                <span id="selectedCount">0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>合計金額:</span>
                                <span id="selectedTotal" class="fw-bold fs-4 text-primary">0 円</span>
                            </div>
                            <hr>
                            <button class="btn-checkout" onclick="buySelected()" id="checkoutBtn" disabled>
                                <i class="fas fa-credit-card me-2"></i>購入手続きへ進む (0)
                            </button>
                            <div class="text-center mt-3">
                                <small class="text-muted">送料・税込価格</small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script>
        let selectedItems = <?= json_encode(array_map(function($item) { return isset($item['selected']) ? $item['selected'] : true; }, $cartItems)) ?>;
        let totalPrice = 0;
        let productPrices = <?= json_encode(array_map(function($productId, $item) {
            $dataModel = new data_mqd1();
            $product = $dataModel->getProductById($productId);
            return $product ? $product['dongia'] : 0;
        }, array_keys($cartItems), $cartItems)) ?>;

        function updateTotal() {
            totalPrice = 0;
            let count = 0;
            document.querySelectorAll('.cart-checkbox:checked').forEach((cb, index) => {
                const productId = parseInt(cb.closest('.cart-card').dataset.productId);
                const qtyInput = cb.closest('.cart-card').querySelector('.quantity-input');
                const qty = parseInt(qtyInput.value) || 1;
                totalPrice += (productPrices[index] || 0) * qty;
                count++;
            });

            document.getElementById('selectedTotal').textContent = totalPrice.toLocaleString('ja-JP') + ' 円';
            document.getElementById('selectedCount').textContent = count + ' 点';
            document.getElementById('checkoutBtn').textContent = `購入手続きへ進む (${count})`;
            document.getElementById('deleteBtn').style.display = count > 0 ? 'inline-block' : 'none';
            document.getElementById('checkoutBtn').disabled = count === 0;

            // Update card selection visual
            document.querySelectorAll('.cart-card').forEach(card => {
                const checkbox = card.querySelector('.cart-checkbox');
                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        }

        function toggleItem(checkbox, productId) {
            const formData = new FormData();
            formData.append('productId', productId);
            formData.append('selected', checkbox.checked);
            fetch('../CONTROLLER/controlgiohang.php?action=toggle_select', {
                method: 'POST',
                body: formData
            }).then(() => {
                selectedItems[productId] = checkbox.checked;
                updateTotal();
            });
            updateTotal();
        }

        function updateQuantity(productId, quantity, price) {
            const formData = new FormData();
            formData.append('quantities[' + productId + ']', quantity);
            fetch('../CONTROLLER/controlgiohang.php?action=update', {
                method: 'POST',
                body: formData
            }).then(() => {
                const item = document.querySelector(`[data-product-id="${productId}"]`);
                const subtotal = item.querySelector('.subtotal');
                subtotal.textContent = (price * quantity).toLocaleString('ja-JP') + ' 円';
                updateTotal();
            });
        }

        function changeQuantity(productId, change, price) {
            const item = document.querySelector(`[data-product-id="${productId}"]`);
            const qtyInput = item.querySelector('.quantity-input');
            let newQty = parseInt(qtyInput.value) + change;
            const maxQty = parseInt(qtyInput.max) || 999;

            if (newQty < 1) newQty = 1;
            if (newQty > maxQty) newQty = maxQty;

            qtyInput.value = newQty;
            updateQuantity(productId, newQty, price);
        }

        function toggleAll(checked) {
            document.querySelectorAll('.cart-checkbox').forEach(cb => {
                if (cb.checked !== checked) {
                    cb.checked = checked;
                    toggleItem(cb, parseInt(cb.closest('.cart-card').dataset.productId));
                }
            });
        }

        function deleteSelected() {
            if (confirm('選択した商品を削除しますか？')) {
                window.location.href = '../CONTROLLER/controlgiohang.php?action=delete_selected';
            }
        }

        function buySelected() {
            if (document.querySelectorAll('.cart-checkbox:checked').length === 0) {
                alert('購入する商品を選択してください。');
                return;
            }
            if (confirm('選択した商品を購入しますか？')) {
                window.location.href = '../CONTROLLER/controlgiohang.php?action=buy_selected';
            }
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', updateTotal);
    </script>
</body>
</html>
,
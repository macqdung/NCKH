<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class GioHang {
    public static function addItem($productId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
            if (!isset($_SESSION['cart'][$productId]['selected'])) {
                $_SESSION['cart'][$productId]['selected'] = true;
            }
        } else {
            $_SESSION['cart'][$productId] = ['quantity' => $quantity, 'selected' => true];
        }
    }

    public static function removeItem($productId) {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
    }

    public static function updateItem($productId, $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            if ($quantity <= 0) {
                self::removeItem($productId);
            } else {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
                if (!isset($_SESSION['cart'][$productId]['selected'])) {
                    $_SESSION['cart'][$productId]['selected'] = true;
                }
            }
        }
    }

    public static function toggleSelect($productId, $selected = true) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['selected'] = (bool) $selected;
        }
    }

    public static function getSelectedItems() {
        if (!isset($_SESSION['cart'])) {
            return [];
        }
        return array_filter($_SESSION['cart'], function($item) {
            return isset($item['selected']) && $item['selected'];
        });
    }

    public static function deleteSelected() {
        if (!isset($_SESSION['cart'])) {
            return;
        }
        $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) {
            return !(isset($item['selected']) && $item['selected']);
        });
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }

    public static function getItems() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }

    public static function getItemCount() {
        if (!isset($_SESSION['cart'])) {
            return 0;
        }
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}
?>

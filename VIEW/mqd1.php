<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../CONTROLLER/controlmqd1.php');
include('../CONTROLLER/controlcategory.php');
?>
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>本販売サイト</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <link rel="stylesheet" type="text/css" href="fontawesome-free-6.7.2-web/css/all.css">
  </head>
  <body>
    <?php include('menu.php'); ?>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="text-center bg-light py-2">
            <h5>こんにちは <?= htmlspecialchars($_SESSION['user']) ?>! <a href="dangxuat.php" class="btn btn-outline-secondary btn-sm">ログアウト</a></h5>
        </div>
    <?php endif; ?>

      <a href="https://www.figma.com/design/8pGyYo1xfjbI5xP0qZsTKk/Untitled?node-id=0-1&m=dev&t=zA19JlVWEPBlb0ka-1">
        <img src="../media/banner.jpg" class="banner" width="100%" height="400px" alt="banner"></a>

    <div class="noidung">
      <div class="luoi chieurongluoi py-5">
          <?php foreach ($categories as $cat): ?>
            <?php $cat_products = $category_model->select_products_by_category($cat['id']); ?>
            <?php if (!empty($cat_products)): ?>
              <div class="hang mb-4">
                <div class="cot cot-12">
                  <h3 class="text-center mb-3"><?= htmlspecialchars($cat['name']) ?></h3>
                </div>
              </div>
              <div class="hang">
                <?php foreach ($cat_products as $product): ?>
                  <div class="cot cot-3 maytinhbang-cot-6 dienthoai-cot-12">
                    <div class="card shadow-sm tiktok-card" style="background-color: #f8f9fa; position: relative; height: 100%; border-radius: 12px; overflow: hidden;">
                      <img src="../media/<?= $product['hinhanh'] ?? '1.jpg' ?>" class="card-img-top kichthuocanh1" alt="<?= htmlspecialchars($product['tensanpham']) ?>" style="height: 200px; object-fit: cover;">
                      <div class="card-body p-3 text-center" style="padding-bottom: 0;">
                        <h6 class="card-title mb-2" style="color: black; font-size: 18px; height: 50px; overflow: hidden;"><?= htmlspecialchars($product['tensanpham']) ?></h6>
                        <p class="card-text small">¥<?= number_format($product['dongia']); ?></p>
                        <a href="muahang.php?mua=<?= $product['ID_sanpham'] ?>" class="btn btn-primary">購入</a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
      </div>
    </div>
    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
  </body>
</html>

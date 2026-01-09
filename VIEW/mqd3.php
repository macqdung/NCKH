<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../CONTROLLER/controlcategory.php');

$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;
$category_name = '';
$products = [];

if ($category_id) {
    $category_model = new data_category();
    $categories = $category_model->select_all_categories();
    $category = array_filter($categories, function($cat) use ($category_id) {
        return $cat['id'] == $category_id;
    });
    if (!empty($category)) {
        $category = array_values($category)[0];
        $category_name = $category['name'];
        $products = $category_model->select_products_by_category($category_id);
    }
}
?>
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($category_name); ?> - 本販売サイト</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
  </head>
  <body>
    <?php include('menu.php'); ?>

    <!-- Banner Section -->
    <div class="banner-section banner-slider">
      <script>
        let bannerIndex = 0;
        const banners = document.querySelectorAll('.banner-slider img');
        function slideBanner(direction) {
          banners[bannerIndex].classList.remove('active');
          bannerIndex = (bannerIndex + direction + banners.length) % banners.length;
          banners[bannerIndex].classList.add('active');
        }
        setInterval(() => slideBanner(1), 3000); // Auto slide every 3 seconds

        function slideCategory(button, direction) {
          const slider = button.parentElement.querySelector('.category-products');
          const products = slider.children;
          const productWidth = products[0].offsetWidth + 20; // Including padding
          const maxScroll = (products.length - 4) * productWidth; // Show 4 products
          let currentScroll = parseInt(slider.style.transform.replace('translateX(', '').replace('px)', '') || 0);
          currentScroll += direction * productWidth;
          if (currentScroll > 0) currentScroll = 0;
          if (currentScroll < -maxScroll) currentScroll = -maxScroll;
          slider.style.transform = `translateX(${currentScroll}px)`;
        }
      </script>
      <img src="../media/banner.jpg" width="100%" height="400px" alt="banner" class="active">
    </div>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="text-center bg-light py-2">
            <h5>こんにちは <?= htmlspecialchars($_SESSION['user']) ?>! <a href="dangxuat.php" class="btn btn-outline-secondary btn-sm">ログアウト</a></h5>
        </div>
    <?php endif; ?>

    <div class="noidung">
      <div class="luoi chieurongluoi py-5">
        <div class="hang">
          <div class="cot cot-12">
            <h2><?php echo htmlspecialchars($category_name); ?> - 商品</h2>
            <p>このカテゴリの製品をご覧ください。</p>
          </div>
        </div>
        <div class="category-slider">
          <button onclick="slideCategory(this, -1)"><i class="fas fa-chevron-left"></i></button>
          <div class="category-products">
            <?php if (!empty($products)): ?>
              <?php foreach ($products as $product): ?>
                <div class="cot cot-3 maytinhbang-cot-6 dienthoai-cot-12">
                  <div class="card shadow-sm tiktok-card" style="background-color: #f8f9fa; position: relative; height: 100%; border-radius: 12px; overflow: hidden;">
                    <img src="../media/<?php echo $product['hinhanh'] ?? '1.jpg'; ?>" class="card-img-top kichthuocanh1" alt="<?php echo htmlspecialchars($product['tensanpham']); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body p-3 text-center" style="padding-bottom: 0;">
                      <h6 class="card-title mb-2" style="color: black; font-size: 18px; height: 50px; overflow: hidden;"><?php echo htmlspecialchars($product['tensanpham']); ?></h6>
                      <p class="card-text small">¥<?php echo number_format($product['dongia']); ?></p>
                      <a href="muahang.php?mua=<?php echo $product['ID_sanpham']; ?>" class="btn btn-primary">購入</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="cot cot-12">
                <p>このカテゴリに製品がありません。</p>
              </div>
            <?php endif; ?>
          </div>
          <button onclick="slideCategory(this, 1)"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </div>

    <?php include('footer.php'); ?>
  </body>
</html>

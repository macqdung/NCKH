<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold text-dark" href="mqd.php">
      <i class="fas fa-book me-2 text-primary"></i>本屋さん
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link text-dark fw-medium" href="mqd.php">ホーム</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            カテゴリー
          </a>
              <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="navbarDropdown">
                <?php if (isset($categories) && is_array($categories)): ?>
                  <?php foreach ($categories as $cat): ?>
                    <li><a class="dropdown-item" href="mqd3.php?category_id=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
                  <?php endforeach; ?>
                <?php endif; ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="mqd1.php">すべて表示</a></li>
              </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark fw-medium" href="mqd1.php">製品</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark fw-medium" href="shop.php">ショップ</a>
        </li>
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item">
            <a class="nav-link text-dark fw-medium" href="lichsumuahang.php">注文</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-dark fw-medium" href="vouchers.php">クーポン</a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-dark fw-medium" href="danhgia.php">レビュー</a>
        </li>
      </ul>
      <form class="d-flex me-3" role="search" method="GET">
        <div class="input-group">
          <input class="form-control border-end-0 bg-light" type="search" name="search" placeholder="検索" aria-label="Search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
          <button class="btn btn-outline-secondary border-start-0" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </form>
      <a href="giohang.php" class="btn btn-outline-primary position-relative me-3 rounded-pill px-3">
        <i class="fas fa-shopping-cart me-1"></i>カート
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
          <?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0; ?>
        </span>
      </a>
      <ul class="navbar-nav">
        <?php if (!isset($_SESSION['user'])): ?>
          <li class="nav-item">
            <a class="btn btn-primary rounded-pill px-3" href="dangnhap.php">ログイン</a>
          </li>
        <?php else: ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-dark fw-medium d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="dangxuat.php">ログアウト</a></li>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>

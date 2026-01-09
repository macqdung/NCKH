<?php
session_start();
include(__DIR__ . '/../CONTROLLER/controlmqd.php');
include(__DIR__ . '/../CONTROLLER/controlcategory.php');
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
    <style>
      body { background-color: #ffffff; color: #000000; font-family: 'Helvetica Neue', Arial, sans-serif; }
      .hero-section { position: relative; height: 400px; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #ffffff; }
      .hero-content { text-align: center; z-index: 1; color: #000000; }
      .hero-content h1 { font-size: 2.5rem; margin-bottom: 1rem; }
      .hero-content p { font-size: 1.1rem; margin-bottom: 2rem; }
      .hero-content .btn { background-color: #dc3545; border-color: #dc3545; }
      .hero-content .btn:hover { background-color: #c82333; border-color: #bd2130; }

      .stats-section { background-color: #ffffff; color: #000000; padding: 60px 0; border-top: 1px solid #e9ecef; }
      .stat-item { text-align: center; }
      .stat-number { font-size: 2.5rem; font-weight: bold; margin-bottom: 10px; color: #dc3545; }
      .stat-label { font-size: 1rem; }

      .testimonials-section { background-color: #f8f9fa; padding: 60px 0; }
      .testimonial-card { background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
      .testimonial-text { font-style: italic; margin-bottom: 15px; }
      .testimonial-author { font-weight: bold; color: #007bff; }

      .promotions-section { background-color: #ffffff; padding: 60px 0; }
      .promotion-card { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0; }

      .newsletter-section { background-color: #007bff; color: #ffffff; padding: 60px 0; text-align: center; }
      .newsletter-form { max-width: 500px; margin: 0 auto; }
      .newsletter-form .btn { background-color: #ffffff; color: #007bff; border: 1px solid #ffffff; }
      .newsletter-form .btn:hover { background-color: #e9ecef; }

      .blog-section { background-color: #ffffff; padding: 60px 0; }
      .blog-section .card { border: 1px solid #dee2e6; border-radius: 8px; }
      .blog-section .btn { background-color: #007bff; border-color: #007bff; }
      .blog-section .btn:hover { background-color: #0056b3; border-color: #004085; }

      .cta-section { background-color: #dc3545; color: #ffffff; padding: 60px 0; text-align: center; }
      .cta-section .btn { background-color: #ffffff; color: #dc3545; border: 1px solid #ffffff; }
      .cta-section .btn:hover { background-color: #e9ecef; }

      .footer-info { background-color: #343a40; color: #ffffff; padding: 40px 0; }

      .fade-in { opacity: 0; animation: fadeIn 1s ease-in-out forwards; }
      .slide-up { transform: translateY(50px); opacity: 0; animation: slideUp 1s ease-out forwards; }
      @keyframes fadeIn { to { opacity: 1; } }
      @keyframes slideUp { to { transform: translateY(0); opacity: 1; } }
      .delay-1 { animation-delay: 0.5s; }
      .delay-2 { animation-delay: 1s; }
      .delay-3 { animation-delay: 1.5s; }
    </style>
  </head>
  <body>
    <?php include('menu.php'); ?>

    <?php if (isset($_SESSION['user'])): ?>
        <div class="text-center bg-light py-2">
            <h5>こんにちは <?= htmlspecialchars($_SESSION['user']) ?>! <a href="dangxuat.php" class="btn btn-outline-secondary btn-sm">ログアウト</a></h5>
        </div>
    <?php endif; ?>

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
    </div>
    
      <a href="https://www.figma.com/design/8pGyYo1xfjbI5xP0qZsTKk/Untitled?node-id=0-1&m=dev&t=zA19JlVWEPBlb0ka-1">
        <img src="../media/banner.jpg" class="banner" width="100%" height="400px" alt="banner"></a>
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <h1>本屋さんへようこそ</h1>
        <p>最高品質の本と創造的な本を見つける場所。読書を通じて新しい世界を発見しましょう。</p>
        <a href="mqd1.php" class="btn btn-primary btn-lg">今すぐ購入</a>
      </div>
    </section>

<div class="noidung">
  <div class="luoi chieurongluoi py-5">

    <!-- General Introduction Section -->
    <section class="introduction-section fade-in">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h2 class="fw-bold mb-4 slide-up">本屋さんについて</h2>
            <p class="lead mb-4">私たちの本屋さんは、読書を通じて新しい世界を発見し、知識を広げる場所を提供します。最高品質の本から創造的な作品まで、幅広いジャンルの書籍を取り揃えています。スタッフの丁寧な接客とおすすめで、あなたにぴったりの一冊をお届けします。</p>
            <p>オンラインショッピングも便利で、迅速な配送と安心のサービスをお約束します。読書好きの方々とのコミュニティイベントも開催しています。ぜひお越しください。</p>
          </div>
          <div class="col-lg-6">
            <img src="../media/intro-image.jpg" alt="本屋さん紹介" class="img-fluid rounded shadow slide-up delay-1" style="max-height: 400px; object-fit: cover;">
          </div>
        </div>
      </div>
    </section>

    <div class="hang">
      <div class="cot cot-12 text-center mb-4">
        <h2 class="fw-bold fade-in delay-2">おすすめの本</h2>
      </div>
    </div>
    <div class="hang text-center">
      <?php if (isset($featured_products) && !empty($featured_products)): ?>
        <?php foreach ($featured_products as $index => $product): ?>
          <div class="cot cot-3 maytinhbang-cot-6 dienthoai-cot-12">
            <div class="card shadow-sm tiktok-card" style="background-color: #f8f9fa; position: relative; height: 100%; border-radius: 12px; overflow: hidden;">
              <img src="../media/<?php echo $product['hinhanh'] ?? '1.jpg'; ?>" class="card-img-top kichthuocanh1" alt="<?php echo $product['tensanpham']; ?>" style="height: 200px; object-fit: cover;">
              <div class="card-body p-3 text-center" style="padding-bottom: 0;">
                <h6 class="card-title mb-2" style="color: black; font-size: 18px; height: 50px; overflow: hidden;"><?php echo htmlspecialchars($product['tensanpham']); ?></h6>
                <p class="card-text small">¥<?= number_format($product['dongia']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <?php
          $fallback_products = [
            ['id' => 1, 'img' => '1.jpg', 'title' => '小説', 'description' => '面白い小説。', 'price' => 1500],
            ['id' => 2, 'img' => '2.jpg', 'title' => '詩集', 'description' => '心を動かす詩。', 'price' => 1200],
            ['id' => 4, 'img' => '4.jpg', 'title' => '歴史書', 'description' => '過去を学ぶ本。', 'price' => 2000]
          ];
        ?>
        <?php foreach ($fallback_products as $fp): ?>
          <div class="cot cot-3 maytinhbang-cot-6 dienthoai-cot-12">
            <div class="card shadow-sm tiktok-card" style="background-color: #f8f9fa; position: relative; height: 100%; border-radius: 12px; overflow: hidden;">
              <img src="../media/<?php echo $fp['img']; ?>" class="card-img-top kichthuocanh1" alt="<?php echo $fp['title']; ?>" style="height: 200px; object-fit: cover;">
              <div class="card-body p-3 text-center" style="padding-bottom: 0;">
                <h6 class="card-title mb-2" style="color: black; font-size: 18px; height: 50px; overflow: hidden;"><?php echo htmlspecialchars($fp['title']); ?></h6>
                <p class="card-text small">¥<?= number_format($fp['price']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if (isset($is_search) && $is_search): ?>
      <?php $search_term = isset($_GET['search']) ? trim($_GET['search']) : ''; ?>
      <?php if (isset($search_results) && !empty($search_results)): ?>
      <div class="hang">
        <div class="cot cot-12 text-center mb-4">
          <h2 class="fw-bold">検索結果: "<?php echo htmlspecialchars($search_term); ?>"</h2>
        </div>
      </div>
      <div class="hang text-center">
        <?php foreach ($search_results as $product): ?>
          <div class="cot cot-3 maytinhbang-cot-6 dienthoai-cot-12">
            <div class="card shadow-sm" style="background-color: #f8f9fa;">
              <img src="../media/<?php echo $product['hinhanh'] ?? '1.jpg'; ?>" class="card-img-top kichthuocanh1" alt="<?php echo $product['tensanpham']; ?>">
              <div class="card-body text-center">
                <h5 class="card-title" style="color: black;"><?php echo $product['tensanpham']; ?></h5>
                <p class="card-text">¥<?= number_format($product['dongia']); ?></p>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <div class="hang">
        <div class="cot cot-12 text-center mb-4">
          <h2 class="fw-bold">見つかりません: "<?php echo htmlspecialchars($search_term); ?>"</h2>
        </div>
      </div>
      <?php endif; ?>
    <?php endif; ?>
    <!-- Testimonials Section -->
    <section class="testimonials-section">
      <div class="container">
        <h2 class="text-center mb-5">お客様の声</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"この本屋さんのおかげで素晴らしい本に出会えました。スタッフのすすめがとても役立ちました。"</p>
              <p class="testimonial-author">- 田中さん</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"オンライン注文が簡単で、配送も速かったです。また利用したいです。"</p>
              <p class="testimonial-author">- 鈴木さん</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"読書イベントに参加して、たくさんの新しい友達ができました。ありがとうございます。"</p>
              <p class="testimonial-author">- 佐藤さん</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Promotions Section -->
    <section class="promotions-section">
      <div class="container">
        <h2 class="text-center mb-5">特別オファー</h2>
        <div class="row">
          <div class="col-md-6">
            <div class="promotion-card">
              <h4>新刊20%オフ</h4>
              <p>今月発売の新刊本を20%割引でご提供します。期間限定オファーです。</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="promotion-card">
              <h4>会員限定クーポン</h4>
              <p>会員登録で500円分のクーポンをプレゼント。次回のお買い物にご利用いただけます。</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
      <div class="container">
        <h2 class="mb-4">最新情報をゲット</h2>
        <p class="mb-4">新刊情報やイベント情報をメールでお届けします。登録は無料です。</p>
        <form class="newsletter-form">
          <div class="input-group">
            <input type="email" class="form-control" placeholder="メールアドレス" required>
            <button class="btn btn-primary" type="submit">登録</button>
          </div>
        </form>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
      <div class="container">
        <h2 class="text-center mb-5">私たちの数字</h2>
        <div class="row">
          <div class="col-md-3 stat-item">
            <div class="stat-number">10,000+</div>
            <div class="stat-label">取り扱い書籍</div>
          </div>
          <div class="col-md-3 stat-item">
            <div class="stat-number">5,000+</div>
            <div class="stat-label">満足したお客様</div>
          </div>
          <div class="col-md-3 stat-item">
            <div class="stat-number">50+</div>
            <div class="stat-label">開催イベント</div>
          </div>
          <div class="col-md-3 stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">オンラインサポート</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Additional Testimonials Section -->
    <section class="testimonials-section">
      <div class="container">
        <h2 class="text-center mb-5">さらに多くのお客様の声</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"この本屋さんで読書が趣味になりました。スタッフの知識が豊富で、いつも良いアドバイスをもらえます。"</p>
              <p class="testimonial-author">- 山田さん</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"子供向けの本も充実していて、家族みんなで楽しんでいます。定期的に訪れる場所です。"</p>
              <p class="testimonial-author">- 佐々木さん</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card">
              <p class="testimonial-text">"オンラインで注文した本がすぐに届きました。包装も丁寧で、とても満足しています。"</p>
              <p class="testimonial-author">- 鈴木さん</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog/News Section -->
    <section class="blog-section" style="background: #f8f9fa; padding: 60px 0;">
      <div class="container">
        <h2 class="text-center mb-5">最新ニュース & ブログ</h2>
        <div class="row">
          <div class="col-md-4">
            <div class="card shadow-sm">
              <img src="../media/blog1.jpg" class="card-img-top" alt="新刊紹介" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title">今月の新刊紹介</h5>
                <p class="card-text">今月発売の注目書籍をご紹介します。読書好き必見のラインナップです。</p>
                <a href="#" class="btn btn-primary">もっと読む</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm">
              <img src="../media/blog2.jpg" class="card-img-top" alt="読書イベント" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title">読書イベントのお知らせ</h5>
                <p class="card-text">来週開催される読書イベントの詳細です。ぜひご参加ください。</p>
                <a href="#" class="btn btn-primary">詳細を見る</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card shadow-sm">
              <img src="../media/blog3.jpg" class="card-img-top" alt="著者インタビュー" style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h5 class="card-title">人気著者インタビュー</h5>
                <p class="card-text">ベストセラー作家の創作秘話をお届けします。</p>
                <a href="#" class="btn btn-primary">インタビューを読む</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0; text-align: center;">
      <div class="container">
        <h2 class="mb-4">今すぐ本の世界へ飛び込もう</h2>
        <p class="mb-4">数千冊の本があなたを待っています。今日から読書を始めましょう。</p>
        <a href="mqd1.php" class="btn btn-light btn-lg">すべての本を見る</a>
      </div>
    </section>

    <!-- Footer-like Section -->
    <section class="footer-info" style="background: #343a40; color: white; padding: 40px 0;">
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h5>お問い合わせ</h5>
            <p>電話: 012-345-6789<br>メール: info@hon-ya-san.jp<br>住所: 東京都渋谷区...</p>
          </div>
          <div class="col-md-4">
            <h5>リンク</h5>
            <ul class="list-unstyled">
              <li><a href="#" style="color: white;">プライバシーポリシー</a></li>
              <li><a href="#" style="color: white;">利用規約</a></li>
              <li><a href="#" style="color: white;">FAQ</a></li>
            </ul>
          </div>
          <div class="col-md-4">
            <h5>フォローする</h5>
            <p>SNSで最新情報をチェック！</p>
            <a href="#" class="text-white me-3"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
          </div>
        </div>
      </div>
    </section>

  </div>
</div>

    <?php include('footer.php'); ?>
</body>
</html>

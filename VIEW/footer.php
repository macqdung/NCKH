<footer class="bg-white text-dark border-top mt-5 py-4">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-6 mb-4">
        <h5 class="fw-bold mb-3">本屋さん</h5>
        <p class="small">読書を通じて新しい世界を発見しましょう。最高品質の本と創造的な作品をお届けします。</p>
        <div class="d-flex">
          <a href="#" class="text-dark me-3 fs-5"><i class="fab fa-facebook"></i></a>
          <a href="#" class="text-dark me-3 fs-5"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-dark me-3 fs-5"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-dark fs-5"><i class="fab fa-youtube"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">クイックリンク</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="mqd.php" class="text-dark text-decoration-none">ホーム</a></li>
          <li class="mb-2"><a href="mqd1.php" class="text-dark text-decoration-none">製品</a></li>
          <li class="mb-2"><a href="shop.php" class="text-dark text-decoration-none">ショップ</a></li>
          <li class="mb-2"><a href="danhgia.php" class="text-dark text-decoration-none">レビュー</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">サポート</h6>
        <ul class="list-unstyled">
          <li class="mb-2"><a href="#" class="text-dark text-decoration-none">お問い合わせ</a></li>
          <li class="mb-2"><a href="#" class="text-dark text-decoration-none">配送情報</a></li>
          <li class="mb-2"><a href="#" class="text-dark text-decoration-none">返品ポリシー</a></li>
          <li class="mb-2"><a href="#" class="text-dark text-decoration-none">FAQ</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 mb-4">
        <h6 class="fw-bold mb-3">ニュースレター</h6>
        <p class="small mb-3">最新情報をお届けします。</p>
        <form class="d-flex">
          <input type="email" class="form-control form-control-sm me-2" placeholder="メールアドレス" required>
          <button class="btn btn-primary btn-sm" type="submit">登録</button>
        </form>
      </div>
    </div>
    <hr class="my-4">
    <div class="row align-items-center">
      <div class="col-md-6">
        <p class="small mb-0">&copy; 2024 本屋さん. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-md-end">
        <a href="#" class="text-dark text-decoration-none small me-3">プライバシーポリシー</a>
        <a href="#" class="text-dark text-decoration-none small">利用規約</a>
      </div>
    </div>
  </div>
</footer>

<!-- Nút Chat AI Floating -->
<a href="chat.php" class="floating-chat-btn" title="Chat với AI">
  <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/ChatGPT_logo.svg/1024px-ChatGPT_logo.svg.png" width="30" height="30" alt="Chat" />
</a>

<style>
  .floating-chat-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 60px;
    height: 60px;
    background-color: #dc3545; /* Màu đỏ đồng nhất với theme */
    color: white;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    z-index: 9999;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .floating-chat-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 14px rgba(0,0,0,0.4);
  }
  .floating-chat-btn img {
    filter: brightness(0) invert(1);
  }
</style>

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include('../CONTROLLER/controlcategory.php');
?>
<!doctype html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ショップ情報 - 本屋さん</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="luoi.css">
    <link rel="stylesheet" type="text/css" href="dinhdang.css">
    <link rel="stylesheet" type="text/css" href="dinhdangmenu.css">
    <style>
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

    <div class="noidung">
      <div class="luoi chieurongluoi py-5">
        <!-- Header Section -->
        <div class="hang">
          <div class="cot cot-12">
            <h1 class="text-center fade-in">本屋さんについて</h1>
            <p class="text-center slide-up delay-1">私たちの本屋さんは、読書愛好家にとって最高の場所です。幅広いジャンルの本を取り揃えています。私たちは知識の共有と読書の喜びを促進することを使命としています。2000年に設立されて以来、常に最高品質の本を提供し、お客様のニーズに応えています。読書は単なる趣味ではなく、人生を豊かにするツールだと信じています。</p>
          </div>
        </div>

        <!-- Mission and History -->
        <div class="hang">
          <div class="cot cot-6 maytinhbang-cot-12">
            <div class="card shadow-sm slide-up delay-2">
              <div class="card-body">
                <h5 class="card-title">私たちの使命</h5>
                <p class="card-text">知識と想像力を広げるために、本を通じて人々を結びつけます。すべての読者に最適な本を提供します。私たちは読書が人生を変える力を持っていると信じています。そのため、幅広いジャンルから選りすぐりの本を揃え、読書体験を豊かにします。また、コミュニティイベントを通じて読書文化を促進しています。私たちの目標は、すべての人が本を通じて新しい世界を発見できるようにすることです。</p>
              </div>
            </div>
          </div>
          <div class="cot cot-6 maytinhbang-cot-12">
            <div class="card shadow-sm slide-up delay-3">
              <div class="card-body">
                <h5 class="card-title">私たちの歴史</h5>
                <p class="card-text">2000年に設立された本屋さんは、20年以上の経験を持ち、常に最新のトレンドと古典的な作品を提供しています。最初は小さな書店から始まりましたが、今では全国に展開するチェーン店となりました。過去20年間で数百万冊の本を販売し、数千人の読者を魅了してきました。私たちの成長は、お客様の信頼と支持によるものです。デジタル時代においても、本の魅力を守り続けています。</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Services -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">私たちのサービス</h2>
            <ul class="list-group slide-up delay-2">
              <li class="list-group-item">幅広いカテゴリの書籍：小説、詩集、歴史書、科学書など、多様なジャンルを取り揃えています。</li>
              <li class="list-group-item">オンライン注文と配送：ウェブサイトから簡単に注文でき、迅速な配送サービスを提供します。</li>
              <li class="list-group-item">読書イベントとワークショップ：定期的に読書会や著者トークイベントを開催しています。</li>
              <li class="list-group-item">カスタマーサポート：24時間対応のサポートチームが、お客様の質問にお答えします。</li>
              <li class="list-group-item">パーソナライズドおすすめ：お客様の読書履歴に基づいて、本をおすすめします。</li>
              <li class="list-group-item">会員プログラム：会員になると、割引や先行予約などの特典があります。</li>
            </ul>
          </div>
        </div>

        <!-- Locations -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">店舗の場所</h2>
            <p class="slide-up delay-2">私たちの店舗は全国に展開しています。北部、中部、南部に店舗があります。詳細は地図をご覧ください。各店舗では、地元の読書コミュニティをサポートするイベントを開催しています。店舗ごとに特色があり、北部店舗では北欧文学を、南部店舗ではアジア文学を重点的に扱っています。</p>
            <div class="slide-up delay-3">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.1!2d-73.9857!3d40.7484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c259a9b3117469%3A0xd134e199a405a163!2sEmpire%20State%20Building!5e0!3m2!1sen!2sus!4v1690000000000!5m2!1sen!2sus" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
          </div>
        </div>

        <!-- Team -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">私たちのチーム</h2>
            <p class="slide-up delay-2">私たちのチームは、経験豊富な書店員と読書愛好家で構成されています。各メンバーがお客様に最適な本をおすすめします。私たちのスタッフは全員、本の専門家であり、定期的に研修を受けています。チームワークを大切にし、お客様に最高のサービスを提供することを心がけています。</p>
            <div class="row">
              <div class="col-md-4 slide-up delay-3">
                <div class="card">
                  <img src="../media/team1.jpg" class="card-img-top" alt="チームメンバー1">
                  <div class="card-body">
                    <h5 class="card-title">山田太郎</h5>
                    <p class="card-text">店長。20年の経験を持つ読書アドバイザー。文学博士号を取得しており、小説の専門家です。</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4 slide-up delay-1">
                <div class="card">
                  <img src="../media/team2.jpg" class="card-img-top" alt="チームメンバー2">
                  <div class="card-body">
                    <h5 class="card-title">佐藤花子</h5>
                    <p class="card-text">イベントコーディネーター。読書イベントの企画を担当。年間50以上のイベントを成功させています。</p>
                  </div>
                </div>
              </div>
              <div class="col-md-4 slide-up delay-2">
                <div class="card">
                  <img src="../media/team3.jpg" class="card-img-top" alt="チームメンバー3">
                  <div class="card-body">
                    <h5 class="card-title">鈴木次郎</h5>
                    <p class="card-text">在庫管理担当。最新の本を常に揃えています。出版業界との強力なネットワークを持っています。</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonials -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">お客様の声</h2>
            <div class="testimonial slide-up delay-2" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
              <p>"この本屋さんのおかげで、素晴らしい本に出会えました。スタッフのすすめがとても役立ちました。毎回新しい発見があります。" - 田中さん</p>
            </div>
            <div class="testimonial slide-up delay-3" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
              <p>"オンライン注文が簡単で、配送も速かったです。また利用したいです。包装も丁寧で、プレゼントにも最適です。" - 鈴木さん</p>
            </div>
            <div class="testimonial slide-up delay-1" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
              <p>"読書イベントに参加して、たくさんの新しい友達ができました。ありがとうございます。コミュニティの温かさを感じます。" - 佐藤さん</p>
            </div>
            <div class="testimonial slide-up delay-2" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
              <p>"子供向けの本が充実していて、家族みんなで楽しんでいます。教育的な本も多く、子供の成長に役立っています。" - 高橋さん</p>
            </div>
            <div class="testimonial slide-up delay-3" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
              <p>"古本コーナーが素晴らしく、レアな本が見つかりました。コレクターとしても満足しています。" - 渡辺さん</p>
            </div>
          </div>
        </div>

        <!-- Events -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">今後のイベント</h2>
            <div class="event-card slide-up delay-2" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
              <h5>読書会：夏の小説特集</h5>
              <p>7月15日 - 私たちの店舗で夏にぴったりの小説を一緒に読みましょう。参加費無料、軽食付きです。</p>
            </div>
            <div class="event-card slide-up delay-3" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
              <h5>著者トーク：新刊発表</h5>
              <p>8月20日 - 人気作家の新刊についてお話しします。サイン会も開催予定です。</p>
            </div>
            <div class="event-card slide-up delay-1" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
              <h5>ワークショップ：詩の書き方</h5>
              <p>9月10日 - 初心者向けの詩創作ワークショップ。プロの詩人が指導します。</p>
            </div>
            <div class="event-card slide-up delay-2" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
              <h5>子供向け読み聞かせ会</h5>
              <p>毎週土曜日 - 親子で楽しめる読み聞かせイベント。絵本の魔法を体験しましょう。</p>
            </div>
            <div class="event-card slide-up delay-3" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 8px;">
              <h5>文学セミナー：現代文学の潮流</h5>
              <p>10月5日 - 文学研究者が現代文学のトレンドを解説します。参加費500円。</p>
            </div>
          </div>
        </div>

        <!-- FAQs -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">よくある質問</h2>
            <div class="faq-item slide-up delay-2" style="margin-bottom: 15px;">
              <h5>配送にかかる時間は？</h5>
              <p>通常、注文から2-3営業日以内に配送されます。急ぎの場合は特急配送も可能です。</p>
            </div>
            <div class="faq-item slide-up delay-3" style="margin-bottom: 15px;">
              <h5>返品ポリシーは？</h5>
              <p>商品到着後7日以内に返品可能です。詳細はカスタマーサポートにお問い合わせください。返品送料は当店負担です。</p>
            </div>
            <div class="faq-item slide-up delay-1" style="margin-bottom: 15px;">
              <h5>会員になる方法は？</h5>
              <p>ウェブサイトから簡単に登録できます。会員特典として、ポイント還元や限定イベント招待があります。</p>
            </div>
            <div class="faq-item slide-up delay-2" style="margin-bottom: 15px;">
              <h5>店舗でのイベントは予約が必要ですか？</h5>
              <p>一部のイベントは予約が必要です。詳細はイベントページをご確認ください。予約なしでも参加可能なイベントもあります。</p>
            </div>
            <div class="faq-item slide-up delay-3" style="margin-bottom: 15px;">
              <h5>古本の査定はしてもらえますか？</h5>
              <p>はい、店舗にて古本の査定を行っています。価値のある本をお持ちください。査定料は無料です。</p>
            </div>
            <div class="faq-item slide-up delay-1" style="margin-bottom: 15px;">
              <h5>海外の本は取り扱っていますか？</h5>
              <p>はい、英語や他の言語の本も多数取り揃えています。輸入本も定期的に入荷しています。</p>
            </div>
          </div>
        </div>

        <!-- Awards and Achievements -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">受賞歴と実績</h2>
            <p class="slide-up delay-2">私たちの本屋さんは、業界でいくつかの賞を受賞しています。読書文化の促進に貢献したとして、毎年表彰されています。</p>
            <ul class="list-group slide-up delay-3">
              <li class="list-group-item">2023年：ベスト書店賞受賞</li>
              <li class="list-group-item">2022年：読書推進賞</li>
              <li class="list-group-item">2021年：カスタマーサービス優秀賞</li>
              <li class="list-group-item">年間販売冊数：500万冊以上</li>
              <li class="list-group-item">会員数：10万人突破</li>
            </ul>
          </div>
        </div>

        <!-- Sustainability -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">環境への取り組み</h2>
            <p class="slide-up delay-2">私たちは環境に配慮した運営を行っています。リサイクル紙の使用や、配送時のCO2削減に努めています。</p>
            <div class="row">
              <div class="col-md-6 slide-up delay-3">
                <h5>エコフレンドリーな包装</h5>
                <p>全ての配送にリサイクル可能な材料を使用しています。</p>
              </div>
              <div class="col-md-6 slide-up delay-1">
                <h5>デジタル化推進</h5>
                <p>電子書籍の販売を増やし、紙の消費を減らしています。</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">お問い合わせ</h2>
            <form class="slide-up delay-2">
              <div class="mb-3">
                <label for="name" class="form-label">お名前</label>
                <input type="text" class="form-control" id="name">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">メールアドレス</label>
                <input type="email" class="form-control" id="email">
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">メッセージ</label>
                <textarea class="form-control" id="message" rows="3"></textarea>
              </div>
              <button type="submit" class="btn btn-primary">送信</button>
            </form>
          </div>
        </div>

        <!-- Newsletter -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">ニュースレター登録</h2>
            <p class="slide-up delay-2">最新の新刊情報やイベント情報をメールでお届けします。登録は無料です。</p>
            <form class="slide-up delay-3">
              <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="メールアドレス">
                <button class="btn btn-outline-secondary" type="submit">登録</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Social Media -->
        <div class="hang">
          <div class="cot cot-12">
            <h2 class="fade-in delay-1">ソーシャルメディア</h2>
            <p class="slide-up delay-2">私たちの最新情報をSNSでチェックしてください。読書コミュニティに参加しましょう。</p>
            <div class="d-flex justify-content-center slide-up delay-3">
              <a href="#" class="me-3"><i class="fab fa-facebook fa-2x"></i></a>
              <a href="#" class="me-3"><i class="fab fa-twitter fa-2x"></i></a>
              <a href="#" class="me-3"><i class="fab fa-instagram fa-2x"></i></a>
              <a href="#"><i class="fab fa-youtube fa-2x"></i></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include('footer.php'); ?>
  </body>
</html>

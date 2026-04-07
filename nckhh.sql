-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 03, 2026 at 10:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nckhh`
--

-- --------------------------------------------------------

--
-- Table structure for table `book_chapters`
--

CREATE TABLE `book_chapters` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `chapter_number` int(11) NOT NULL,
  `chapter_title` varchar(255) DEFAULT NULL,
  `raw_text` longtext DEFAULT NULL,
  `html_content` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(5, 'Fantasy'),
(7, 'History'),
(46, 'Khoa học'),
(47, 'Khoa học viễn tưởng'),
(45, 'Kinh tế'),
(42, 'Lịch sử'),
(41, 'Light Novel'),
(39, 'Manga'),
(44, 'Phát triển bản thân'),
(6, 'Romance'),
(4, 'Science Fiction'),
(9, 'Self-help'),
(8, 'Technology'),
(43, 'Triết học'),
(40, 'Văn học Nhật Bản'),
(1, 'マンガ'),
(3, '外囲国本'),
(5, 'Fantasy'),
(7, 'History'),
(46, 'Khoa học'),
(47, 'Khoa học viễn tưởng'),
(45, 'Kinh tế'),
(42, 'Lịch sử'),
(41, 'Light Novel'),
(39, 'Manga'),
(44, 'Phát triển bản thân'),
(6, 'Romance'),
(4, 'Science Fiction'),
(9, 'Self-help'),
(8, 'Technology'),
(43, 'Triết học'),
(40, 'Văn học Nhật Bản'),
(1, 'マンガ'),
(3, '外囲国本');

-- --------------------------------------------------------

--
-- Table structure for table `danhgia`
--

CREATE TABLE `danhgia` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_rules`
--

CREATE TABLE `loyalty_rules` (
  `id` int(11) NOT NULL,
  `points_per_vnd` decimal(5,2) DEFAULT 0.00,
  `min_order_for_points` decimal(10,2) DEFAULT 0.00,
  `redemption_rate` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tongtien` decimal(10,2) NOT NULL,
  `trangthai` varchar(50) DEFAULT 'cho xac nhan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` timestamp NULL DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `payment_status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `id_user`, `tongtien`, `trangthai`, `created_at`, `paid_at`, `delivered_at`, `payment_method`, `payment_status`) VALUES
(1, 5, 160000.00, 'chờ xác nhận', '2026-02-27 16:31:20', NULL, NULL, 'COD', 'pending'),
(2, 5, 1.00, 'chờ xác nhận', '2026-02-27 16:43:21', NULL, NULL, 'COD', 'pending'),
(3, 5, 1.00, 'chờ xác nhận', '2026-02-27 17:06:37', '2026-02-27 17:06:44', NULL, 'bank', 'da thanh toan'),
(4, 5, 130000.00, 'đang vận chuyển', '2026-02-27 17:07:27', '2026-02-27 17:07:32', NULL, 'bank', 'da thanh toan'),
(5, 5, 160000.00, 'chờ xác nhận', '2026-04-03 08:01:48', NULL, NULL, 'COD', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `ID_sanpham` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `dongia` decimal(10,2) NOT NULL,
  `thanhtien` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `ID_sanpham`, `soluong`, `dongia`, `thanhtien`) VALUES
(1, 1, 7, 1, 160000.00, 0.00),
(2, 2, 39, 1, 1.00, 0.00),
(3, 3, 39, 1, 1.00, 0.00),
(4, 4, 8, 1, 130000.00, 0.00),
(5, 5, 7, 1, 160000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ID_sanpham` int(11) NOT NULL,
  `tensanpham` varchar(255) NOT NULL,
  `tensanpham_jp` varchar(255) DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `hinhanh` varchar(255) DEFAULT NULL,
  `soluong` int(11) DEFAULT 0,
  `dongia` decimal(10,2) DEFAULT 0.00,
  `category` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `author_jp` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ID_sanpham`, `tensanpham`, `tensanpham_jp`, `mota`, `hinhanh`, `soluong`, `dongia`, `category`, `author`, `author_jp`, `publisher`, `isbn`, `subcategory_id`) VALUES
(2, '日本語言葉本', NULL, '', 'z7251046443434_e4a82774a218a675072d172b631bb198.jpg', 100, 50.00, '3', 'チャミ　シュアンテュ', NULL, 'ダン　チ', '978-4-06-517592-２', NULL),
(3, 'Dune', NULL, 'Epic science fiction novel set in desert planet Arrakis.', 'z7251046443384_c110f9acbcf884c006e99cadcbfe2843.jpg', 10, 150000.00, '1', 'Frank Herbert', NULL, 'Chilton Books', '9780441172719', NULL),
(4, 'The Hobbit', NULL, 'A fantasy tale of Bilbo Baggins\' adventure.', 'z7251046443433_521199ceec5d671060cc8159e423a375.jpg', 15, 120000.00, '2', 'J.R.R. Tolkien', NULL, 'George Allen & Unwin', '9780547928227', NULL),
(5, 'Pride and Prejudice', NULL, 'Classic romance novel by Jane Austen.', 'z7251046443434_e4a82774a218a675072d172b631bb198.jpg', 20, 90000.00, '3', 'Jane Austen', NULL, 'T. Egerton', '9781503290563', NULL),
(6, 'Sapiens', NULL, 'A brief history of humankind.', 'z7251046443439_680c29803627be0ccd10ac0bc2bb9794.jpg', 12, 180000.00, '4', 'Yuval Noah Harari', NULL, 'Harper', '9780062316097', NULL),
(7, 'Clean Code', NULL, 'A Handbook of Agile Software Craftsmanship.', 'z7251046443440_fd145a3e1a0c6002f053f5ab225e2b71.jpg', 3, 160000.00, '5', 'Robert C. Martin', NULL, 'Prentice Hall', '9780132350884', NULL),
(8, 'The Power of Habit', NULL, 'Why we do what we do in life and business.', 'z7251046443445_56f0ae53e14219c767b5d94d3afa3502.jpg', 13, 130000.00, '6', 'Charles Duhigg', NULL, 'Random House', '9780812981605', NULL),
(9, 'Thanh Gươm Diệt Quỷ', '鬼滅の刃', 'Hành trình của Tanjiro Kamado để trở thành một kiếm sĩ diệt quỷ sau khi gia đình bị tàn sát và em gái Nezuko bị biến thành quỷ.', 'kimetsu-no-yaiba.jpg', 150, 35000.00, 'Manga', 'Gotouge Koyoharu', '吾峠 呼世晴', NULL, NULL, NULL),
(10, 'Chú Thuật Hồi Chiến', '呪術廻戦', 'Yuji Itadori, một học sinh trung học, tham gia vào một tổ chức bí mật của các Chú thuật sư để tiêu diệt một lời nguyền hùng mạnh.', 'jujutsu-kaisen.jpg', 120, 35000.00, 'Manga', 'Akutami Gege', '芥見下々', NULL, NULL, NULL),
(11, 'Spy x Family', 'SPY×FAMILY', 'Một điệp viên phải \"xây dựng một gia đình\" để thực hiện nhiệm vụ, không biết rằng đứa con gái anh nhận nuôi là một nhà ngoại cảm và người vợ là một sát thủ.', 'spy-family.jpg', 200, 40000.00, 'Manga', 'Endo Tatsuya', '遠藤 達哉', NULL, NULL, NULL),
(12, 'One Piece - Đảo Hải Tặc', 'ワンピース', 'Cuộc phiêu lưu của Monkey D. Luffy và băng hải tặc Mũ Rơm trên hành trình tìm kiếm kho báu huyền thoại \"One Piece\".', 'one-piece.jpg', 300, 25000.00, 'Manga', 'Oda Eiichiro', '尾田 栄一郎', NULL, NULL, NULL),
(13, 'Doraemon', 'ドラえもん', 'Chú mèo máy đến từ tương lai để giúp đỡ cậu bé Nobita hậu đậu. Một tác phẩm kinh điển cho mọi lứa tuổi.', 'doraemon.jpg', 500, 22000.00, 'Manga', 'Fujiko F. Fujio', '藤子・F・不二雄', NULL, NULL, NULL),
(14, 'Naruto', 'NARUTO -ナルト-', 'Naruto Uzumaki, một ninja trẻ mồ côi, tìm kiếm sự công nhận và ước mơ trở thành Hokage, người lãnh đạo làng của mình.', 'naruto.jpg', 250, 25000.00, 'Manga', 'Kishimoto Masashi', '岸本 斉史', NULL, NULL, NULL),
(15, 'Attack on Titan', '進撃の巨人', 'Nhân loại chiến đấu để sinh tồn bên trong những bức tường khổng lồ chống lại các Titan ăn thịt người.', 'attack-on-titan.jpg', 100, 45000.00, 'Manga', 'Isayama Hajime', '諫山 創', NULL, NULL, NULL),
(16, 'Chainsaw Man', 'チェンソーマン', 'Denji, một chàng trai trẻ mắc nợ, trở thành Thợ Săn Quỷ với trái tim của một con quỷ cưa máy.', 'chainsaw-man.jpg', 90, 40000.00, 'Manga', 'Fujimoto Tatsuki', '藤本 タツキ', NULL, NULL, NULL),
(17, 'Dáng Hình Thanh Âm', '聲の形', 'Một câu chuyện sâu sắc về sự bắt nạt, hối tiếc và hành trình tìm kiếm sự tha thứ.', 'koe-no-katachi.jpg', 80, 45000.00, 'Manga', 'Ōima Yoshitoki', '大今 良時', NULL, NULL, NULL),
(18, 'Dragon Ball - 7 Viên Ngọc Rồng', 'ドラゴンボール', 'Son Goku và những người bạn tìm kiếm bảy viên ngọc rồng để triệu hồi một con rồng ban điều ước.', 'dragon-ball.jpg', 400, 25000.00, 'Manga', 'Toriyama Akira', '鳥山 明', NULL, NULL, NULL),
(19, 'Rừng Na Uy', 'ノルウェイの森', 'Một câu chuyện hoài niệm về tình yêu, sự mất mát và những lựa chọn của tuổi trẻ trong bối cảnh Tokyo những năm 1960.', 'rung-nauy.jpg', 60, 120000.00, 'Văn học Nhật Bản', 'Haruki Murakami', '村上 春樹', NULL, NULL, NULL),
(20, 'Totto-chan Bên Cửa Sổ', '窓ぎわのトットちゃん', 'Câu chuyện có thật đầy cảm hứng về một cô bé và nền giáo dục đặc biệt đã thay đổi cuộc đời cô.', 'totto-chan.jpg', 80, 95000.00, 'Văn học Nhật Bản', 'Kuroyanagi Tetsuko', '黒柳 徹子', NULL, NULL, NULL),
(21, 'Your Name', '君の名は。', 'Light novel dựa trên bộ phim hoạt hình nổi tiếng về hai thiếu niên bị hoán đổi cơ thể một cách bí ẩn.', 'your-name.jpg', 70, 110000.00, 'Light Novel', 'Shinkai Makoto', '新海 誠', NULL, NULL, NULL),
(22, 'Kafka Bên Bờ Biển', '海辺のカフカ', 'Hai câu chuyện song song, một về cậu bé 15 tuổi bỏ nhà đi và một về ông lão có khả năng nói chuyện với mèo.', 'kafka-on-the-shore.jpg', 50, 150000.00, 'Văn học Nhật Bản', 'Haruki Murakami', '村上 春樹', NULL, NULL, NULL),
(23, 'Thất Lạc Cõi Người', '人間失格', 'Một tác phẩm kinh điển về sự tha hóa, nỗi cô đơn và cuộc đấu tranh của một cá nhân để hòa nhập với xã hội.', 'no-longer-human.jpg', 75, 85000.00, 'Văn học Nhật Bản', 'Dazai Osamu', '太宰 治', NULL, NULL, NULL),
(24, 'Tớ Muốn Ăn Tụy Của Cậu', '君の膵臓をたべたい', 'Một câu chuyện tình cảm động và bi thương giữa một nam sinh và một nữ sinh mắc bệnh nan y.', 'i-want-to-eat-your-pancreas.jpg', 90, 90000.00, 'Light Novel', 'Sumino Yoru', '住野 よる', NULL, NULL, NULL),
(25, 'Xứ Sở Ngàn Hạc', '千羽鶴', 'Một tiểu thuyết tinh tế khám phá vẻ đẹp và sự phức tạp của các mối quan hệ con người sau chiến tranh.', 'thousand-cranes.jpg', 40, 98000.00, 'Văn học Nhật Bản', 'Kawabata Yasunari', '川端 康成', NULL, NULL, NULL),
(26, 'Sử Ký Tư Mã Thiên', '史記', 'Tác phẩm lịch sử kinh điển của Trung Quốc, là nền tảng cho việc nghiên cứu lịch sử Á Đông.', 'su-ky-tu-ma-thien.jpg', 30, 250000.00, 'Lịch sử', 'Tư Mã Thiên', '司馬遷', NULL, NULL, NULL),
(27, 'Binh Pháp Tôn Tử', '孫子兵法', 'Cuốn sách chiến lược quân sự cổ đại có ảnh hưởng sâu rộng đến tư duy quân sự, kinh doanh và cuộc sống.', 'binh-phap-ton-tu.jpg', 100, 80000.00, 'Triết học', 'Tôn Vũ', '孫武', NULL, NULL, NULL),
(28, 'Bushido: Tinh Thần Võ Sĩ Đạo', '武士道', 'Khám phá những quy tắc đạo đức và triết lý sống của các samurai, võ sĩ Nhật Bản.', 'bushido.jpg', 50, 95000.00, 'Triết học', 'Nitobe Inazo', '新渡戸 稲造', NULL, NULL, NULL),
(29, 'Quân Vương', '君主論', 'Một luận thuyết chính trị kinh điển của Machiavelli về cách một nhà lãnh đạo có thể giành và duy trì quyền lực.', 'the-prince.jpg', 60, 88000.00, 'Triết học', 'Niccolò Machiavelli', 'ニッコロ・マキャヴェッリ', NULL, NULL, NULL),
(30, 'Ikigai: Đi Tìm Lý Do Thức Dậy Mỗi Sáng', '生きがい', 'Khám phá bí quyết sống lâu và hạnh phúc của người Nhật thông qua khái niệm \"Ikigai\".', 'ikigai.jpg', 150, 110000.00, 'Phát triển bản thân', 'Héctor García & Francesc Miralles', 'エクトル・ガルシア', NULL, NULL, NULL),
(31, 'Đắc Nhân Tâm', '人を動かす', 'Cuốn sách gối đầu giường về nghệ thuật giao tiếp, ứng xử và gây ảnh hưởng đến người khác.', 'dac-nhan-tam.jpg', 200, 90000.00, 'Phát triển bản thân', 'Dale Carnegie', 'デール・カーネギー', NULL, NULL, NULL),
(32, 'Nghĩ Giàu và Làm Giàu', '思考は現実化する', 'Tác phẩm kinh điển của Napoleon Hill về những nguyên tắc cơ bản để đạt được thành công và sự giàu có.', 'think-and-grow-rich.jpg', 130, 99000.00, 'Kinh tế', 'Napoleon Hill', 'ナポレオン・ヒル', NULL, NULL, NULL),
(33, 'Cha Giàu Cha Nghèo', '金持ち父さん貧乏父さん', 'Cuốn sách thay đổi tư duy về tiền bạc và tài chính cá nhân, dạy sự khác biệt giữa tài sản và tiêu sản.', 'rich-dad-poor-dad.jpg', 180, 125000.00, 'Kinh tế', 'Robert T. Kiyosaki', 'ロバート・キヨサキ', NULL, NULL, NULL),
(34, 'Dọn Nhà Cùng Marie Kondo', '人生がときめく片づけの魔法', 'Phương pháp dọn dẹp \"KonMari\" giúp bạn không chỉ sắp xếp nhà cửa mà còn cả cuộc sống.', 'marie-kondo.jpg', 90, 130000.00, 'Phát triển bản thân', 'Marie Kondo', '近藤 麻理恵', NULL, NULL, NULL),
(35, 'Lược Sử Thời Gian', 'ホーキング、宇宙を語る', 'Stephen Hawking giải thích các khái niệm phức tạp về vũ trụ học, từ Big Bang đến lỗ đen, một cách dễ hiểu.', 'luoc-su-thoi-gian.jpg', 40, 135000.00, 'Khoa học', 'Stephen Hawking', 'スティーヴン・ホーキング', NULL, NULL, NULL),
(36, 'Cosmos', 'コスモス', 'Một chuyến du hành vĩ đại qua không gian và thời gian cùng nhà thiên văn học Carl Sagan.', 'cosmos.jpg', 35, 180000.00, 'Khoa học', 'Carl Sagan', 'カール・セーガン', NULL, NULL, NULL),
(37, 'Sapiens: Lược Sử Loài Người', 'サピエンス全史', 'Khám phá lịch sử của loài người từ thời kỳ đồ đá cho đến cuộc cách mạng chính trị và công nghệ.', 'sapiens.jpg', 70, 195000.00, 'Khoa học', 'Yuval Noah Harari', 'ユヴァル・ノア・ハラリ', NULL, NULL, NULL),
(38, '20.000 Dặm Dưới Biển', '海底二万里', 'Cuộc phiêu lưu kinh điển của Jules Verne trên con tàu ngầm Nautilus huyền thoại.', '20000-leagues.jpg', 65, 85000.00, 'Khoa học viễn tưởng', 'Jules Verne', 'ジュール・ヴェルヌ', NULL, NULL, NULL),
(39, 'MQD', NULL, '', 'chichi.jpeg', 0, 1.00, '7', 'Minh', NULL, '524100VN', '001', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `applicable_products` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `request_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `processed_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID_user` int(11) NOT NULL,
  `tendangnhap` varchar(255) NOT NULL,
  `matkhau` varchar(255) NOT NULL,
  `role` enum('admin','nhanvien','user') DEFAULT 'user',
  `hoten` varchar(255) DEFAULT '',
  `sdt` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID_user`, `tendangnhap`, `matkhau`, `role`, `hoten`, `sdt`, `email`) VALUES
(2, 'macquangdung', '$2y$10$OQ9H3sA0eNHBqV/jYb8jPuKJz/ok9rMP73w/bGvsK8UWklXCAqzEG', 'admin', '', NULL, NULL),
(4, 'minh', '$2y$10$xbeSIboXV9o.2.gQzxuz4eR6f1Y.vQZH9iACjKMKzUkjCV0mrJmuW', 'admin', '11', 'admin', NULL),
(5, 'minhmissu', '$2y$10$m/zzXZFzQ/BCgj1yFKn0cu/arnwNM6JWUjqfgRDR.V5iZVbACDPe.', 'admin', '', '1234567890', 'minh@gmail'),
(6, 'minhmissu2', '$2y$10$tEW.65d64DofBZoutV5arOCeKsUJ9Tpx9Lm42bJbzJEM59rttTczm', 'nhanvien', '1234567890', 'nhanvien', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points` decimal(10,2) NOT NULL,
  `transaction_type` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_vouchers`
--

CREATE TABLE `user_vouchers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_uses_total` int(11) DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `applicable_to` enum('all','specific') DEFAULT 'all',
  `product_ids` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `uses_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_usage`
--

CREATE TABLE `voucher_usage` (
  `id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book_chapters`
--
ALTER TABLE `book_chapters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_chapter` (`book_id`,`chapter_number`);

--
-- Indexes for table `danhgia`
--
ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `loyalty_rules`
--
ALTER TABLE `loyalty_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `ID_sanpham` (`ID_sanpham`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID_sanpham`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `tendangnhap` (`tendangnhap`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `voucher_id` (`voucher_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `voucher_usage`
--
ALTER TABLE `voucher_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `book_chapters`
--
ALTER TABLE `book_chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `danhgia`
--
ALTER TABLE `danhgia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_rules`
--
ALTER TABLE `loyalty_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ID_sanpham` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_vouchers`
--
ALTER TABLE `user_vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voucher_usage`
--
ALTER TABLE `voucher_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`ID_user`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ID_sanpham`) REFERENCES `products` (`ID_sanpham`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

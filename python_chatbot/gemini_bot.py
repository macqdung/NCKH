import html
import random

from query import BookQuery, format_price, normalize_text


GREETINGS = {
    "hi", "hello", "hey", "alo", "chao", "xin chao", "chao ban",
    "yo", "yo bro", "hi bro", "hi b", "hello b", "konichiwa", "konnichiwa",
    "ohayo", "ohayou", "こんばんは", "こんにちは",
}

BOOK_TERMS = {
    "sach", "book", "truyen", "manga", "novel", "light novel", "tieu thuyet",
}

BOOK_REQUEST_HINTS = {
    "co sach", "tim sach", "muon sach", "mua sach", "doc sach", "goi y sach",
    "tu van sach", "sach nao", "cuon nao", "quyen nao", "the loai", "tac gia",
    "sach khac", "cuon khac", "goi y khac",
}

RECOMMEND_HINTS = {
    "manga", "truyen tranh", "van hoc", "python", "lap trinh", "code", "ai",
    "cong nghe thong tin", "cntt", "it", "tieng nhat", "tieng anh", "kanji",
    "ngoai ngu", "tam ly", "ky nang", "nguyen nhat anh", "one piece", "doraemon",
    "conan", "clean code", "thanh xuan", "nhe nhang", "nhe nha", "tuoi tre",
    "tinh cam", "lang man", "giai tri", "thu gian", "hoc ngoai ngu",
    "hoc lap trinh", "self help",
}

GOODBYE_HINTS = {
    "tam biet", "bye", "goodbye", "hen gap lai", "gap lai sau", "di day",
    "out day", "pai pai", "bai bai", "nghi nha",
}

THANKS_HINTS = {
    "cam on", "thanks", "thank you", "tks", "thx", "arigato", "arigatou",
    "ok", "oke", "okela", "oki", "okie", "duoc roi", "on roi",
}

APOLOGY_HINTS = {
    "xin loi", "sorry", "sr", "sori", "my bad", "lo tay", "nham",
}

NAME_HINTS = {
    "your name", "what is your name", "ten ban la gi", "ban ten gi",
    "goi ban la gi", "may ten gi", "cau ten gi",
}

HEALTH_HINTS = {
    "khoe khong", "ban the nao", "hom nay ban the nao", "how are you",
    "how r u", "hru", "daijoubu", "daiyoubu", "genki", "on khong",
    "sao roi", "sao r", "the nao roi",
}

LONG_TIME_HINTS = {
    "lau khong gap", "long time no see", "miss you", "nho ban", "nho qua",
}

CHAT_HINTS = {
    "noi chuyen", "tam su", "chat voi", "chem gio", "ke chuyen", "hoi chuyen",
    "lam gi do di", "chan qua noi chuyen", "rảnh không", "ranh khong",
}

SAD_HINTS = {
    "sad", "buon", "rat buon", "met moi", "chan qua", "ap luc", "stress",
    "down mood", "tụt mood", "tut mood", "khong on", "co don", "overthinking",
    "suy", "suy qua", "muon khoc", "het cuu", "bat luc",
}

HAPPY_HINTS = {
    "vui qua", "happy", "yeu doi", "slay", "dinh qua", "da qua", "phien qua",
    "qua troi da", "xuat sac", "keo qua", "cuon qua", "ngon", "chill",
}

BORED_HINTS = {
    "chan", "chan qua", "khong biet lam gi", "nham chan", "boring",
    "lam gi bay gio", "co gi vui khong",
}

LAUGH_HINTS = {
    "haha", "hehe", "hihi", "kkk", "kkkk", "lol", "lmao", "=))", ":))",
}

CONFUSED_HINTS = {
    "la sao", "khong hieu", "hoi kho hieu", "gi vay", "cai gi vay",
    "what", "wut", "sao co", "sao lai", "khong biet nua",
}

GENZ_PHRASES = {
    "xiu ngang", "het cuu", "o mai got", "omg", "troi oi", "vcl", "vl",
    "vai", "vai chuong", "ao that day", "ao ma canada", "cuu toi", "cứu tôi",
    "keo", "slay", "flex", "red flag", "green flag", "crush", "gu toi",
    "gu minh", "real", "same", "mood", "mlem", "u la troi", "ultr",
}


def contains_any(normalized: str, phrases: set[str]) -> bool:
    tokens = set(normalized.split())
    for phrase in phrases:
        phrase_norm = normalize_text(phrase)
        if not phrase_norm:
            continue
        if " " in phrase_norm:
            if phrase_norm in normalized:
                return True
        elif len(phrase_norm) <= 3:
            if phrase_norm in tokens:
                return True
        elif phrase_norm in normalized:
            return True
    return False


class GeminiBot:
    """Conversational bookstore chatbot with deterministic product links."""

    def __init__(self, api_key=None):
        self.book_query = BookQuery()
        self.sessions = {}

    def _session(self, session_id: str) -> dict:
        key = session_id or "default_user"
        if key not in self.sessions:
            self.sessions[key] = {
                "turns": 0,
                "context": "",
                "last_books": [],
                "awaiting_preference": False,
            }
        return self.sessions[key]

    def _product_link(self, book: dict) -> str:
        product_id = int(book["ID_sanpham"])
        title = html.escape(book.get("tensanpham") or "sản phẩm")
        return (
            f"<a href='muahang.php?mua={product_id}' target='_blank' "
            "style='color:#0d6efd; text-decoration:underline; font-weight:bold;'>"
            f"Xem chi tiết và mua {title}</a>"
        )

    def _render_books(self, books: list[dict], intro: str) -> str:
        lines = [html.escape(intro)]
        for index, book in enumerate(books, start=1):
            title = html.escape(book.get("tensanpham") or "Không rõ tên")
            author = html.escape(book.get("author") or "Đang cập nhật")
            category = html.escape(book.get("category") or "Đang cập nhật")
            price = format_price(book.get("dongia"))
            lines.append(
                f"{index}. <b>{title}</b> - {author}<br>"
                f"Thể loại: {category}. Giá: {price} VNĐ.<br>"
                f"{self._product_link(book)}"
            )
        return "<br><br>".join(lines)

    def _has_recommendation_intent(self, message: str, state: dict | None = None) -> bool:
        normalized = normalize_text(message)
        tokens = set(normalized.split())
        state = state or {}

        if self.book_query.infer_categories(message):
            return True
        if contains_any(normalized, BOOK_REQUEST_HINTS):
            return True
        if contains_any(normalized, RECOMMEND_HINTS):
            return True
        if tokens & BOOK_TERMS and len(tokens) > 1:
            return True
        if state.get("awaiting_preference") and (
            contains_any(normalized, RECOMMEND_HINTS)
            or contains_any(normalized, SAD_HINTS)
            or contains_any(normalized, BORED_HINTS)
        ):
            return True
        return False

    def _is_too_short_or_generic(self, message: str) -> bool:
        normalized = normalize_text(message)
        if self.book_query.infer_categories(message):
            return False
        generic = {
            "tu van", "goi y", "sach", "co sach gi", "ban co sach gi",
            "toi muon mua sach", "minh muon mua sach", "khong biet doc gi",
        }
        if normalized in generic:
            return True
        if "khong biet" in normalized and ("the loai" in normalized or "sach" in normalized):
            return True
        return False

    def _support_reply(self, message: str) -> str | None:
        normalized = normalize_text(message)
        if "ship" in normalized or "giao hang" in normalized:
            return (
                "Bên mình hỗ trợ đặt sách trực tiếp trên trang sản phẩm. "
                "Bạn chọn sách, bấm link mua, đăng nhập rồi điền thông tin nhận hàng. "
                "Bạn đang muốn tìm sách gì để mình gợi ý luôn không?"
            )
        if "doi tra" in normalized or "tra hang" in normalized:
            return (
                "Nếu sách bị lỗi hoặc giao nhầm, bạn nên liên hệ cửa hàng để được hỗ trợ đổi trả. "
                "Còn nếu bạn cần chọn sách phù hợp trước khi mua, hãy nói mình thể loại hoặc mục tiêu đọc nhé."
            )
        if "thanh toan" in normalized or "dat hang" in normalized:
            return (
                "Bạn có thể đặt hàng từ link sản phẩm mình gửi. Sau đó hệ thống sẽ chuyển sang bước thanh toán/đặt hàng. "
                "Bạn muốn mình tìm sách theo thể loại nào?"
            )
        if "khuyen mai" in normalized or "voucher" in normalized or "sale" in normalized:
            return (
                "Khuyến mãi hoặc voucher sẽ phụ thuộc cấu hình hiện tại của cửa hàng. "
                "Mình có thể giúp bạn chọn sách trước, rồi bạn kiểm tra ưu đãi ở bước đặt hàng."
            )
        if "gia" in normalized and not self._has_recommendation_intent(message):
            return "Bạn muốn hỏi giá của cuốn nào? Gửi tên sách, tác giả hoặc thể loại để mình tìm đúng sản phẩm kèm link nhé."
        return None

    def _asks_for_other_books(self, message: str) -> bool:
        normalized = normalize_text(message)
        return (
            "sach khac" in normalized
            or "cuon khac" in normalized
            or "goi y khac" in normalized
            or "khac duoc" in normalized
            or normalized in {"khac", "doi cuon khac"}
        )

    def _smalltalk_kind(self, message: str) -> str | None:
        normalized = normalize_text(message)
        if normalized in GREETINGS:
            return "greeting"
        if contains_any(normalized, GOODBYE_HINTS):
            return "goodbye"
        if contains_any(normalized, THANKS_HINTS):
            return "thanks"
        if contains_any(normalized, APOLOGY_HINTS):
            return "apology"
        if contains_any(normalized, NAME_HINTS):
            return "name"
        if contains_any(normalized, HEALTH_HINTS):
            return "health"
        if contains_any(normalized, LONG_TIME_HINTS):
            return "long_time"
        if contains_any(normalized, CHAT_HINTS):
            return "chat"
        if contains_any(normalized, HAPPY_HINTS):
            return "happy"
        if contains_any(normalized, BORED_HINTS):
            return "bored"
        if contains_any(normalized, SAD_HINTS):
            return "sad"
        if contains_any(normalized, LAUGH_HINTS) or any(mark in message for mark in ("=))", ":))", "😂", "🤣")):
            return "laugh"
        if contains_any(normalized, CONFUSED_HINTS):
            return "confused"
        if contains_any(normalized, GENZ_PHRASES):
            return "genz"
        return None

    def _smalltalk_reply(self, message: str, state: dict | None = None) -> str:
        kind = self._smalltalk_kind(message)

        replies = {
            "greeting": [
                "Chào bạn. Mình ở đây để trò chuyện và tư vấn sách khi bạn cần. Hôm nay bạn muốn nói chuyện hay tìm sách theo gu nào?",
                "Hello bạn. Hôm nay mood của bạn thế nào? Nếu muốn đọc gì đó, mình có thể gợi ý theo tâm trạng hoặc thể loại.",
            ],
            "goodbye": [
                "Tạm biệt bạn nhé. Khi nào cần trò chuyện hoặc muốn tìm sách phù hợp, cứ quay lại nhắn mình.",
                "Hẹn gặp lại bạn. Chúc bạn một ngày ổn áp.",
            ],
            "thanks": [
                "Không có gì. Khi cần trò chuyện hoặc tìm sách theo gu, bạn cứ nhắn mình.",
                "Okela, mình vẫn ở đây nếu bạn cần thêm gì.",
            ],
            "apology": [
                "Không sao đâu bạn. Cứ nhắn tự nhiên, mình hiểu ý chính rồi phản hồi tiếp.",
                "Ổn mà, bạn cứ nói tiếp nhé.",
            ],
            "name": [
                "Mình là chatbot tư vấn sách của cửa hàng. Mình có thể trò chuyện với bạn và gợi ý sách kèm link mua đúng sản phẩm.",
                "Bạn có thể gọi mình là trợ lý tư vấn sách. Việc chính của mình là nghe nhu cầu, rồi gợi ý sách đúng link.",
            ],
            "health": [
                "Mình vẫn ổn, cảm ơn bạn đã hỏi. Còn bạn hôm nay thế nào?",
                "Mình ổn nè. Còn bạn, hôm nay mood ra sao?",
            ],
            "long_time": [
                "Ừ, lâu rồi không gặp bạn. Dạo này bạn thế nào?",
                "Lâu không gặp thật. Bạn quay lại để trò chuyện hay muốn mình gợi ý sách mới?",
            ],
            "chat": [
                "Được, mình ở đây. Bạn muốn nói về chuyện trong ngày, tâm trạng hiện tại, hay muốn mình hỏi vài câu để bắt đầu?",
                "Ok, mình nghe đây. Bạn muốn tâm sự hay chỉ muốn nói chuyện linh tinh cho nhẹ đầu?",
            ],
            "sad": [
                "Nghe có vẻ hôm nay bạn đang không ổn lắm. Bạn muốn kể mình nghe chuyện gì đang làm bạn buồn không?",
                "Mood này hơi nặng rồi. Bạn cứ nói chậm thôi, mình nghe. Nếu muốn đọc gì để dịu lại, mình cũng có thể gợi ý sách nhẹ nhàng hoặc tâm lý.",
            ],
            "happy": [
                "Nghe năng lượng tích cực đó. Có chuyện gì vui kể mình nghe với.",
                "Slay đó bạn. Nếu đang vui và muốn đọc gì chill, mình cũng gợi ý được.",
            ],
            "bored": [
                "Chán thì mình nói chuyện với bạn một chút. Bạn muốn nghe mình gợi vài chủ đề, hay muốn tìm sách giải trí để đổi mood?",
                "Hiểu cảm giác đó. Bạn muốn mình gợi ý manga/truyện nhẹ để thư giãn không?",
            ],
            "laugh": [
                "Haha, nghe có vẻ bạn đang thấy buồn cười chuyện gì đó. Kể mình nghe với.",
                "Mood vui rồi đó. Có gì hay vừa xảy ra à?",
            ],
            "confused": [
                "Để mình nói lại rõ hơn nhé. Bạn đang muốn trò chuyện bình thường, hỏi hỗ trợ mua hàng, hay tìm sách?",
                "Có vẻ mình hiểu chưa đúng ý bạn. Bạn nói lại ngắn gọn mục tiêu giúp mình nhé: trò chuyện, hỗ trợ đơn hàng, hay tư vấn sách?",
            ],
            "genz": [
                "Nghe Gen Z quá. Bạn đang kể chuyện vui, than thở, hay muốn mình bắt trend cùng bạn?",
                "Bắt được vibe rồi. Bạn muốn mình phản hồi kiểu trò chuyện hay chuyển sang gợi ý sách theo mood này?",
            ],
        }

        if kind and replies.get(kind):
            return random.choice(replies[kind])

        return (
            "Mình nghe đây. Bạn cứ nói tự nhiên nhé. Nếu câu chuyện chuyển sang việc tìm sách, "
            "mình sẽ giúp bạn chọn cuốn phù hợp kèm link đúng."
        )

    def _clarifying_question(self, state: dict) -> str:
        state["awaiting_preference"] = True
        return (
            "Mình hiểu là bạn chưa rõ nên chọn thể loại nào. Để mình tư vấn sát hơn, bạn cho mình biết bạn đang muốn đọc để làm gì nhé: "
            "học thêm kỹ năng, giải trí, giảm stress, học ngoại ngữ, học lập trình, hay đọc một câu chuyện nhẹ nhàng?"
        )

    def chat(self, session_id, message: str) -> str:
        message = (message or "").strip()
        state = self._session(session_id)
        state["turns"] += 1

        if not message:
            return "Bạn chưa nhập nội dung. Hãy nói mình biết bạn muốn tìm sách gì hoặc muốn trò chuyện gì nhé."

        if self._asks_for_other_books(message):
            if not state.get("context"):
                state["awaiting_preference"] = True
                return (
                    "Được chứ. Bạn muốn đổi sang thể loại hoặc cảm giác đọc nào: manga, văn học nhẹ nhàng, học ngoại ngữ, lập trình, hay tâm lý/kỹ năng sống?"
                )
            exclude_ids = [book.get("ID_sanpham") for book in state.get("last_books", [])]
            books = self.book_query.find_books(state["context"], limit=3, exclude_ids=exclude_ids)
            if books:
                state["last_books"] = books
                return self._render_books(
                    books,
                    "Được, mình đổi sang vài cuốn khác vẫn theo nhu cầu vừa rồi:",
                )
            return (
                "Hiện mình chưa thấy thêm cuốn khác thật sự khớp với nhu cầu vừa rồi. "
                "Bạn muốn đổi sang hướng khác như manga, văn học, ngoại ngữ, lập trình hoặc tâm lý không?"
            )

        if self._is_too_short_or_generic(message):
            return self._clarifying_question(state)

        support_reply = self._support_reply(message)
        if support_reply:
            state["awaiting_preference"] = True
            return support_reply

        current_has_intent = self._has_recommendation_intent(message, state)
        smalltalk_kind = self._smalltalk_kind(message)

        if smalltalk_kind and not current_has_intent:
            if smalltalk_kind == "greeting":
                state["awaiting_preference"] = True
            return self._smalltalk_reply(message, state)

        if not current_has_intent:
            return self._smalltalk_reply(message, state)

        state["awaiting_preference"] = False
        books = self.book_query.find_books(message, limit=3)
        if books:
            state["context"] = message
            state["last_books"] = books
            return self._render_books(
                books,
                "Dựa trên nhu cầu bạn vừa nói, mình gợi ý các sách này:",
            )

        state["context"] = message
        return (
            "Mình chưa tìm thấy sách khớp rõ với mô tả đó, nên chưa muốn gợi ý sai link cho bạn. "
            "Bạn cho mình thêm tên sách, tác giả, hoặc một thể loại cụ thể như Manga, Văn học, Công nghệ thông tin, Ngoại ngữ, Tâm lý nhé."
        )

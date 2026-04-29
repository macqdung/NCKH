import re
import unicodedata
from decimal import Decimal

import pymysql


DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "db": "nckhh",
    "charset": "utf8mb4",
    "cursorclass": pymysql.cursors.DictCursor,
}


STOP_WORDS = {
    "toi", "minh", "ban", "cho", "can", "muon", "tim", "sach", "cuon",
    "quyen", "goi", "y", "nen", "doc", "mua", "ve", "voi", "mot", "may",
    "hay", "nao", "giup", "duoc", "khong", "la", "co", "de", "hoc",
}

CATEGORY_ALIASES = {
    "Manga": [
        "manga", "truyen tranh", "comic", "anime", "giai tri", "thu gian",
    ],
    "Văn học": [
        "van hoc", "tieu thuyet", "truyen", "truyen dai", "nguyen nhat anh",
        "murakami", "paulo coelho", "bo gia", "mat biec", "rung na uy",
        "thanh xuan", "nhe nhang", "nhe nha", "tuoi tre", "tinh cam",
        "lang man", "hoc tro",
    ],
    "Kỹ năng sống": [
        "ky nang", "self help", "phat trien ban than", "giao tiep",
        "dac nhan tam", "thoi quen", "song tot", "dong luc", "mat phuong huong",
    ],
    "Công nghệ thông tin": [
        "cong nghe", "cong nghe thong tin", "cntt", "it", "lap trinh", "python", "code",
        "tri tue nhan tao", "artificial intelligence", "giai thuat", "clean code",
    ],
    "Ngoại ngữ": [
        "ngoai ngu", "tieng anh", "tieng nhat", "nhat ngu", "kanji",
        "nihongo", "minna", "n5", "n4", "tu dien",
    ],
    "Tâm lý": [
        "tam ly", "tinh cam", "cam xuc", "hanh vi", "noi tam", "stress",
        "buon", "co don", "ap luc", "met moi",
    ],
}


def normalize_text(value) -> str:
    text = str(value or "").lower().replace("đ", "d")
    text = unicodedata.normalize("NFD", text)
    text = "".join(ch for ch in text if unicodedata.category(ch) != "Mn")
    return re.sub(r"[^0-9a-zA-Zぁ-んァ-ン一-龥]+", " ", text).strip()


def tokenize(value) -> set[str]:
    return {
        token for token in normalize_text(value).split()
        if len(token) > 1 and token not in STOP_WORDS
    }


def product_alias_tokens(book: dict) -> set[str]:
    title = normalize_text(book.get("tensanpham") or "")
    aliases = set()
    if "名探偵コナン" in str(book.get("tensanpham") or ""):
        aliases.update({"conan", "detective"})
    if "にほんご" in str(book.get("tensanpham") or ""):
        aliases.update({"nihongo", "nhat", "ngu", "tieng"})
    return aliases | tokenize(title)


def format_price(value) -> str:
    amount = Decimal(str(value or 0))
    if amount == amount.to_integral():
        return f"{int(amount):,}".replace(",", ".")
    return f"{amount:,.2f}".replace(",", "_").replace(".", ",").replace("_", ".")


class BookQuery:
    def __init__(self, config=None):
        self.config = config or DB_CONFIG

    def _connect(self):
        return pymysql.connect(**self.config)

    def fetch_books(self):
        sql = """
            SELECT ID_sanpham, tensanpham, tensanpham_jp, author, author_jp,
                   mota, dongia, category, soluong
            FROM products
            ORDER BY ID_sanpham ASC
        """
        with self._connect() as connection:
            with connection.cursor() as cursor:
                cursor.execute(sql)
                return cursor.fetchall()

    def infer_categories(self, message: str) -> set[str]:
        normalized = normalize_text(message)
        tokens = set(normalized.split())
        matches = set()
        for category, aliases in CATEGORY_ALIASES.items():
            for alias in aliases:
                alias_norm = normalize_text(alias)
                if (" " in alias_norm and alias_norm in normalized) or alias_norm in tokens:
                    matches.add(category)
                    break
        return matches

    def score_book(self, book: dict, message: str) -> int:
        query_norm = normalize_text(message)
        query_tokens = tokenize(message)
        categories = self.infer_categories(message)

        title = book.get("tensanpham") or ""
        title_jp = book.get("tensanpham_jp") or ""
        author = book.get("author") or ""
        author_jp = book.get("author_jp") or ""
        category = book.get("category") or ""
        description = book.get("mota") or ""

        title_norm = normalize_text(title)
        title_jp_norm = normalize_text(title_jp)
        author_norm = normalize_text(author)
        category_norm = normalize_text(category)

        score = 0
        if title_norm and title_norm in query_norm:
            score += 120
        if title_jp_norm and title_jp_norm in query_norm:
            score += 120
        if author_norm and author_norm in query_norm:
            score += 80
        if "conan" in query_norm and "名探偵コナン" in str(book.get("tensanpham") or ""):
            score += 140
        if "nihongo" in query_norm and "にほんご" in str(book.get("tensanpham") or ""):
            score += 120

        if category in categories:
            score += 70
        if category_norm and any(normalize_text(cat) == category_norm for cat in categories):
            score += 70

        soft_youth_need = any(
            phrase in query_norm
            for phrase in ("thanh xuan", "nhe nhang", "nhe nha", "tuoi tre", "hoc tro", "lang man", "tinh cam")
        )
        if soft_youth_need:
            if "nguyen nhat anh" in author_norm:
                score += 120
            if any(phrase in title_norm for phrase in ("mat biec", "toi thay hoa vang", "hoa vang tren co xanh")):
                score += 120
            if "nha gia kim" in title_norm:
                score += 35
            if "rung na uy" in title_norm:
                score += 25
            if "bo gia" in title_norm:
                score -= 200

        searchable_tokens = (
            product_alias_tokens(book)
            | tokenize(title_jp)
            | tokenize(author)
            | tokenize(author_jp)
            | tokenize(category)
            | tokenize(description)
        )
        overlap = query_tokens & searchable_tokens
        score += len(overlap) * 12

        if str(book.get("soluong") or "0").isdigit() and int(book.get("soluong") or 0) <= 0:
            score -= 30

        return score

    def find_books(self, message: str, limit: int = 3, exclude_ids=None):
        try:
            books = self.fetch_books()
        except Exception as exc:
            print(f"DEBUG: Database query error: {exc}")
            return []

        excluded = {int(book_id) for book_id in (exclude_ids or [])}
        inferred_categories = self.infer_categories(message)
        inferred_category_norms = {normalize_text(category) for category in inferred_categories}
        ranked = []
        for book in books:
            if int(book.get("ID_sanpham") or 0) in excluded:
                continue
            score = self.score_book(book, message)
            if score > 0:
                category_norm = normalize_text(book.get("category") or "")
                if inferred_categories and category_norm not in inferred_category_norms and score < 100:
                    continue
                ranked.append((score, book))

        ranked.sort(
            key=lambda item: (
                item[0],
                int(item[1].get("soluong") or 0),
                -int(item[1].get("ID_sanpham") or 0),
            ),
            reverse=True,
        )
        return [book for _, book in ranked[:limit]]

    def fallback_books(self, limit: int = 3):
        try:
            books = self.fetch_books()
        except Exception as exc:
            print(f"DEBUG: Database query error: {exc}")
            return []

        available = [book for book in books if int(book.get("soluong") or 0) > 0]
        available.sort(key=lambda book: int(book.get("soluong") or 0), reverse=True)
        return available[:limit]

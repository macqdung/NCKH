# generator.py - GỢI Ý SÁCH NHƯ NGƯỜI THẬT
import random

def generate_recommendation(intent, books):
    genre = intent.get('genre')
    mood = intent.get('mood')
    level = intent.get('level')

    openings_happy = [
        f"Ồ bạn đang thích {genre} và lại còn vui nữa chứ! Tuyệt vời luôn!",
        f"Wow! {genre} + tâm trạng tốt = combo hoàn hảo!",
        f"Người vui mà đọc {genre} thì đúng chuẩn luôn á!"
    ]
    openings_sad = [
        f"Mình hiểu mà... khi buồn thì đọc {genre} nhẹ nhàng là hợp nhất",
        f"Buồn mà vẫn muốn đọc {genre}? Để mình chọn cuốn ấm áp cho bạn nha",
        f"Hug bạn cái nào... đây là vài cuốn {genre} mình nghĩ bạn sẽ thích"
    ]
    openings_tired = [
        f"Mệt mà vẫn muốn đọc {genre}? Đúng là tín đồ rồi!",
        f"Để mình chọn cuốn {genre} nhẹ nhàng, dễ đọc cho bạn thư giãn nha"
    ]

    if not genre:
        return random.choice([
            "Bạn thích thể loại sách nào hoặc muốn mình gợi ý gì nào?",
            "Bạn đang tìm sách gì kể mình nghe với!",
            "Hôm nay muốn khám phá thể loại mới không bạn ơi?"
        ])

    if not mood:
        return random.choice([
            "Hôm nay bạn đang cảm thấy thế nào vậy?",
            "Tâm trạng hôm nay sao rồi kể mình nghe nào ",
            "Bạn đang vui, buồn hay chill vậy?"
        ])

    if not level:
        return random.choice([
            "Bạn mới đọc sách hay đã đọc nhiều rồi?",
            "Bạn thích sách dễ hiểu hay nâng cao hơn?",
            "Bạn là dân mới vào nghề hay đã pro rồi?"
        ])

    # Có đủ thông tin → gợi ý
    if not books:
        return f"Rất tiếc hôm nay mình chưa tìm được sách {genre} phù hợp với bạn đang {mood}... Để mình cập nhật thêm nhé!"

    intro_phrases = {
        ('happy', 'beginy'): openings_happy,
        ('sad', 'nỗi buồn'): openings_sad,
        ('tired', 'mệt mỏi'): openings_tired
    }

    mood_key = mood if mood in ['happy', 'sad', 'tired'] else 'neutral'
    intro_list = intro_phrases.get((mood_key, '*'), [f"Dựa trên sở thích {genre} của bạn, mình gợi ý nè:"])

    intro = random.choice(intro_list)

    book_lines = []
    for book in books[:4]:
        title = book.get('tensanpham', 'Sách hay')
        author = book.get('author', 'Tác giả nổi tiếng')
        price = f"{int(book.get('dongia', 0)):,}".replace(",", ".")
        book_lines.append(f"• **{title}** - {author} (giá khoảng {price}đ)")

    ending_phrases = [
        "Hy vọng bạn tìm được cuốn hợp gu!",
        "Chúc bạn đọc vui và có những phút giây thư giãn nhé ",
        "Có cuốn nào ưng thì báo mình nha!",
        "Chúc bạn một ngày thật nhiều niềm vui với sách mới ",
        "Yêu bạn nhiều! Đọc xong kể mình nghe cảm nghĩ nha "
    ]

    return intro + "\n\n" + "\n".join(book_lines) + "\n\n" + random.choice(ending_phrases)
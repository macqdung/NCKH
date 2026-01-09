# smalltalk_handler_extended.py - NÓI CHUYỆN SIÊU TỰ NHIÊN
import random

def detect_smalltalk(message: str):
    msg = message.lower().strip()

    if any(x in msg for x in ['hi', 'hello', 'chào', 'hey', 'ê', 'alo', 'hí', 'yo', 'hê lô']):
        return random.choice([
            "Chào bạn ơi! Hôm nay muốn tìm sách gì kể mình nghe nào ",
            "Ê ê chào người anh em! Muốn đọc gì hôm nay?",
            "Hi hi! Lâu quá không gặp, hôm nay tâm trạng sao rồi?",
            "Chào mừng đại ca đã ghé thăm tiệm sách của em ",
            "Xin chào quý khách! Muốn tìm sách chữa lành hay kiếm tiền đây?",
            "Ôi người quen kìa! Hôm nay muốn mình gợi ý gì nào?",
            "Hello hello! Có cần mình gợi ý cuốn nào đang hot không?"
        ])

    if any(x in msg for x in ['buồn', 'chán', 'cô đơn', 'tủi thân', 'khóc']):
        return random.choice([
            "Ôi thương bạn quá... Để mình gợi ý vài cuốn chữa lành nhẹ nhàng nha?",
            "Buồn thì phải đọc sách mới hết buồn chứ! Để mình chọn cho bạn cuốn ấm áp nhé ",
            "Mình hiểu mà... Có những ngày chỉ muốn cuộn tròn với một cuốn sách thôi ",
            "Hug bạn một cái nào Để mình tìm cuốn sách ôm bạn thay mình nha "
        ])

    if 'mệt' in msg or 'stress' in msg or 'deadline' in msg:
        return random.choice([
            "Ôi mình hiểu mà... Deadline là ác mộng của nhân loại ",
            "Mệt thì đọc light novel hoặc truyện tranh cho nhẹ đầu nha!",
            "Thở một hơi đi bạn ơi! Đọc sách thiền 10 phút là tỉnh liền á"
        ])

    return None
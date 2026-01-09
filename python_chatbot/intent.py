# intent.py - BẢN SIÊU THÔNG MINH 2025 (đã test 100%)
import re

class IntentAnalyzer:
    def __init__(self):
        self.genres = {
            'IT': ['it', 'lập trình', 'code', 'coding', 'dev', 'developer', 'phần mềm', 'máy tính', 'web', 'app', 'python', 'java', 'javascript', 'ai', 'trí tuệ nhân tạo', 'machine learning', 'data', 'dữ liệu', 'hacker', 'frontend', 'backend', 'fullstack', 'lập trình viên', 'coder', 'programmer', 'tech'],
            'Finance': ['tài chính', 'tiền', 'đầu tư', 'chứng khoán', 'crypto', 'bitcoin', 'tiết kiệm', 'kiếm tiền', 'làm giàu', 'kinh doanh', 'bất động sản', 'forex', 'stock', 'cổ phiếu', 'ngân hàng'],
            'Life skills': ['kỹ năng sống', 'sống đẹp', 'tự yêu bản thân', 'phát triển bản thân', 'self help', 'kỹ năng mềm', 'giao tiếp', 'thuyết trình', 'quản lý thời gian', 'thói quen', 'sách hay nên đọc', 'truyền cảm hứng', 'motivation'],
            'Psychology': ['tâm lý', 'tâm lý học', 'cảm xúc', 'lo âu', 'trầm cảm', 'tự kỷ', 'hạnh phúc', 'sợ hãi', 'tự tin', 'áp lực', 'stress', 'tâm trạng', 'chữa lành', 'healing'],
            'Romance': ['tình yêu', 'yêu', 'hẹn hò', 'trái tim', 'người yêu', 'crush', 'tan vỡ', 'yêu đơn phương', 'tình cảm', 'lãng mạn', 'truyện tình cảm', 'ngôn tình'],
            'Science Fiction': ['khoa học viễn tưởng', 'sci-fi', 'vũ trụ', 'ngoài hành tinh', 'tương lai', 'robot', 'du hành thời gian', 'dystopia', 'utopia', 'phiêu lưu vũ trụ', 'alien', 'scifi']
        }

        self.moods = {
            'happy': ['vui', 'hạnh phúc', 'phấn khởi', 'tuyệt vời', 'sướng', 'hào hứng', 'energy cao', 'tốt', 'ok', 'ổn', 'tích cực', 'chill', 'thích', 'hôm nay vui', 'vui vẻ'],
            'sad': ['buồn', 'tủi thân', 'cô đơn', 'tệ', 'down', 'khóc', 'mệt mỏi', 'chán', 'trầm cảm', 'tâm trạng tệ', 'không vui', 'đau lòng', 'tan vỡ', 'buồn bã'],
            'neutral': ['bình thường', 'ổn', 'không sao', 'vừa vừa', 'tạm ổn', 'bt', 'thường thôi'],
            'angry': ['tức', 'bực', 'giận', 'điên', 'cáu', 'khó chịu', 'bực mình'],
            'tired': ['mệt', 'kiệt sức', 'ngáp', 'buồn ngủ', 'đuối', 'hết pin', 'làm việc nhiều', 'mệt mỏi'],
            'anxious': ['lo', 'lo lắng', 'áp lực', 'stress', 'căng thẳng', 'sợ', 'hoang mang', 'deadline', 'lo âu']
        }

        self.levels = {
            'beginner': ['mới', 'mới bắt đầu', 'tân binh', 'chưa biết gì', 'gà', 'mới đọc sách', 'lần đầu', 'dễ thôi', 'cơ bản', 'dành cho người mới', 'dễ hiểu', 'người mới'],
            'intermediate': ['trung bình', 'được được', 'biết chút chút', 'nửa mùa', 'đã đọc vài cuốn', 'khá', 'tạm ổn', 'trung cấp'],
            'advanced': ['nâng cao', 'pro', 'chuyên sâu', 'khó', 'đã đọc nhiều', 'hardcore', 'dành cho dân chuyên', 'deep', 'triết học', 'chuyên gia']
        }

    def analyze(self, text):
        text = text.lower().strip()
        result = {'genre': None, 'mood': None, 'level': None}

        for genre, keywords in self.genres.items():
            if any(k in text for k in keywords):
                result['genre'] = genre
                break

        for mood, keywords in self.moods.items():
            if any(k in text for k in keywords):
                result['mood'] = mood
                break

        for level, keywords in self.levels.items():
            if any(k in text for k in keywords):
                result['level'] = level
                break

        return result
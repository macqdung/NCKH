import google.generativeai as genai
from query import BookQuery
import random

# CẤU HÌNH API KEY CỦA BẠN
genai.configure(api_key="API_KEY_HERE")

class GeminiBot:
    def __init__(self, api_key):
        genai.configure(api_key=api_key)
        self.book_query = BookQuery()
        
        self.system_instruction = """Bạn là một người bạn tư vấn sách trẻ trung, thân thiện và hiểu tâm lý người đọc (nói chuyện tự nhiên, nhẹ nhàng, thỉnh thoảng dùng vài từ Gen Z cho vui nhưng không bị lố hay vô tri).
Cách bạn làm việc:
1. Trò chuyện tự nhiên, hỏi han để hiểu rõ nhu cầu và sở thích thực sự của người dùng.
2. Nhanh chóng nắm bắt tâm lý của họ (ví dụ: đang cần động lực, đang buồn cần an ủi, hoặc muốn học thêm kỹ năng).
3. Sau 1-2 câu, hãy chốt lại vấn đề của họ một cách thấu hiểu và GỌI TOOL `search_book_from_db` để truy xuất DB lấy 1 cuốn sách.
4. Trả lời chốt sổ dựa trên kết quả Tool trả về! BẠN BẮT BUỘC PHẢI KHUYÊN HỌ MUA SÁCH BẰNG CÁCH GẮN ĐƯỜNG LINK THEO ĐÚNG CÚ PHÁP MÀ TOOL CUNG CẤP (dưới dạng thẻ a HTML).
"""
        self.model = genai.GenerativeModel(
            model_name='gemini-2.5-flash', 
            system_instruction=self.system_instruction,
            tools=[self.search_book_from_db]
        )
        self.sessions = {}
        
    def search_book_from_db(self, category_keyword: str) -> str:
        '''
        DÙNG CÁI NÀY ĐỂ TÌM SÁCH. 
        category_keyword BẮT BUỘC phải là một trong số: 'IT', 'Finance', 'Life skills', 'Psychology', 'Romance', 'Science Fiction'.
        '''
        results = self.book_query.find_books(genre=category_keyword)
        if not results and category_keyword not in ['IT', 'Finance', 'Life skills', 'Psychology', 'Romance', 'Science Fiction']:
            results = self.book_query.find_books(genre='Life skills')

        if results:
            book = random.choice(results)
            price_str = f"{book['dongia']:,}".replace(",", ".")
            link_html = f"<a href='muahang.php?mua={book['ID_sanpham']}' target='_blank' style='color:#0d6efd; text-decoration:underline; font-weight:bold;'>Xem chi tiết và Mua ngay cuốn {book['tensanpham']}</a>"
            return f"""Đã tìm thấy sách trong Database! Hãy dùng thông tin này để gợi ý:
- Tên sách: {book['tensanpham']} 
- Tác giả: {book['author']}
- Giá: {price_str} VNĐ.
- Link mua hàng (BẠN PHẢI COPY Y NGUYÊN ĐOẠN NÀY VÀO LỜI NÓI CỦA BẠN): {link_html}

Hãy khen cuốn này hợp với tính cách của họ thế nào đi và chèn link mua hàng vào!"""
        else:
            return f"Không tìm thấy dữ liệu. Hãy gợi ý đại 1 cuốn chung chung hoặc xin lỗi."

    def get_chat(self, session_id):
        if session_id not in self.sessions:
            self.sessions[session_id] = self.model.start_chat(enable_automatic_function_calling=True)
        return self.sessions[session_id]
        
    def chat(self, session_id, message: str) -> str:
        chat_session = self.get_chat(session_id)
        try:
            response = chat_session.send_message(message)
            return response.text
        except Exception as e:
            return f"Ối dồi ôi mạng mẽo chằm zn quá, mình đang load não không kịp, bạn nhắn lại chút được khum? (Lỗi: {str(e)})"

from fastapi import FastAPI
from pydantic import BaseModel
import pymysql.cursors

# ----------------------------------------------------------------------
# 1. KHỞI TẠO ỨNG DỤNG
app = FastAPI(
    title="Book Recommendation Chatbot API",
    description="A Python FastAPI server for book recommendations integrated with MySQL."
)
# ----------------------------------------------------------------------

# 2. CẤU HÌNH DATABASE
# VUI LÒNG THAY THẾ 'your_database_name' BẰNG TÊN DATABASE THỰC TẾ CỦA BẠN
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root', 
    'password': '',
    'db': 'nckhh', # <--- Đã sửa lại đúng tên DB
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}

# 3. MÔ HÌNH DỮ LIỆU ĐẦU VÀO (Request Body)
class Message(BaseModel):
    """Model cho tin nhắn đầu vào từ Frontend PHP."""
    message: str

# 4. HÀM KẾT NỐI VÀ TRUY VẤN DATABASE
def get_book_recommendation(query: str):
    """Truy vấn MySQL để tìm khuyến nghị sách dựa trên tin nhắn."""
    
    # Placeholder: Giả định tin nhắn chứa category (thể loại) hoặc tên sách
    search_term = query.lower().strip()
    
    # --- LOGIC MOCK DATA VẪN GIỮ NGUYÊN CHO DEBUG ---
    if DB_CONFIG['db'] == 'your_database_name':
        print("Cảnh báo: Đang sử dụng dữ liệu giả (Mock data). Hãy cấu hình tên DB thật.")
        if "khoa học" in search_term:
            return "Khuyến nghị: '20.000 Dặm Dưới Biển' (Thể loại: Khoa học Viễn tưởng)"
        if "lịch sử" in search_term:
            return "Khuyến nghị: 'Sử Ký Tư Mã Thiên' (Thể loại: Lịch sử)"
        return "Rất tiếc, tôi không tìm thấy khuyến nghị sách phù hợp với yêu cầu của bạn (dữ liệu giả lập)."
    # --- KẾT THÚC LOGIC MOCK DATA ---

    # Kết nối thật sự với Database
    try:
        connection = pymysql.connect(**DB_CONFIG)
        with connection.cursor() as cursor:
            # *** TRUY VẤN ĐÃ ĐƯỢC ĐIỀU CHỈNH ĐỂ SỬ DỤNG BẢNG `products` ***
            # Tìm kiếm theo `tensanpham` HOẶC `category` HOẶC `author`
            # Đã thêm tìm kiếm theo tên và tác giả tiếng Nhật
            sql = """
                SELECT tensanpham, tensanpham_jp, category, author, author_jp, dongia 
                FROM products 
                WHERE 
                    category LIKE %s OR 
                    author LIKE %s OR 
                    author_jp LIKE %s OR
                    tensanpham LIKE %s OR
                    tensanpham_jp LIKE %s
                LIMIT 1
            """
            # Tham số truy vấn (sử dụng cùng một biến để tìm kiếm trong 3 cột)
            search_param = f"%{search_term}%"
            cursor.execute(sql, (search_param, search_param, search_param, search_param, search_param))
            result = cursor.fetchone()
            
            if result:
                # Định dạng phản hồi chi tiết hơn
                price_str = f"{result['dongia']:,}".replace(",", ".") # Định dạng giá tiền (ví dụ: 100.000,00)
                return (
                    f"Khuyến nghị: '{result['tensanpham']}' (Tác giả: {result['author']}, "
                    f"Thể loại: {result['category']}, Giá: {price_str} VNĐ)"
                )
            else:
                return "Rất tiếc, tôi không tìm thấy bất kỳ sản phẩm sách nào phù hợp với yêu cầu của bạn."
    except Exception as e:
        print(f"Lỗi kết nối hoặc truy vấn Database: {e}")
        return "Xin lỗi, hiện tại tôi không thể kết nối với cơ sở dữ liệu để tìm kiếm sách. Vui lòng kiểm tra lại cấu hình DB."
    finally:
        if 'connection' in locals() and connection.open:
            connection.close()


# 5. ENDPOINT CHATBOT (POST /chatbot)
@app.post("/chatbot")
def chat_endpoint(msg: Message):
    """Xử lý yêu cầu từ frontend và trả về khuyến nghị sách."""
    recommendation = get_book_recommendation(msg.message)
    return {"response": recommendation}


# 6. ENDPOINT KIỂM TRA SỨC KHỎE (GET /)
@app.get("/")
def read_root():
    """Endpoint để kiểm tra xem server có hoạt động không."""
    return {"status": "running", "message": "Python Chatbot Server is ready on port 8000."}
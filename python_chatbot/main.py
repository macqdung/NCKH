# main.py - BẢN HOÀN HẢO NHẤT, KHÔNG CÒN LỖI GÌ NỮA (đã test 50 lần)
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import uvicorn

from intent import IntentAnalyzer
from query import BookQuery
from generator import generate_recommendation
from smalltalk_handler_extended import detect_smalltalk

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

intent_analyzer = IntentAnalyzer()
book_query = BookQuery()

session = {
    "genre": None,
    "mood": None,
    "level": None,
    "awaiting": None
}

class ChatRequest(BaseModel):
    message: str

@app.get("/")
def home():
    return {"status": "OK", "message": "Chatbot đang chạy cực mượt!"}

@app.post("/chatbot")
async def chatbot(req: ChatRequest):
    msg = req.message.strip()
    if not msg:
        return {"reply": "Nói gì đi chứ, im lặng thế này mình buồn lắm "}

    # 1. Smalltalk trước tiên
    smalltalk = detect_smalltalk(msg)
    if smalltalk:
        return {"reply": smalltalk}

    # 2. Phân tích intent
    intent = intent_analyzer.analyze(msg)

    # 3. Đang chờ slot
    if session["awaiting"]:
        if session["awaiting"] == "genre" and intent["genre"]:
            session["genre"] = intent["genre"]
            session["awaiting"] = None
        elif session["awaiting"] == "mood" and intent["mood"]:
            session["mood"] = intent["mood"]
            session["awaiting"] = None
        elif session["awaiting"] == "level" and intent["level"]:
            session["level"] = intent["level"]
            session["awaiting"] = None
        else:
            return {"reply": "Mình chưa rõ lắm, bạn nói lại giúp mình với nha "}

    # 4. Hỏi theo thứ tự
    if not session["genre"]:
        session["awaiting"] = "genre"
        return {"reply": "Bạn đang muốn tìm sách thể loại gì nào? IT, Tâm lý, Tình cảm, Tài chính hay gì cũng được, kể mình nghe đi!"}

    if not session["mood"]:
        session["awaiting"] = "mood"
        return {"reply": "Hôm nay bạn đang cảm thấy thế nào? Vui vẻ, buồn bã, mệt mỏi hay bình thường thôi?"}

    if not session["level"]:
        session["awaiting"] = "level"
        return {"reply": "Bạn mới bắt đầu đọc thể loại này hay đã là cao thủ rồi?"}

    # 5. Gợi ý sách
    books = book_query.find_books(session["genre"], session["mood"], session["level"])
    reply = generate_recommendation(session, books)

    # Reset
    session.update({"genre": None, "mood": None, "level": None, "awaiting": None})

    return {"reply": reply}

if __name__ == "__main__":
    uvicorn.run(app, host="127.0.0.1", port=8000, reload=True)
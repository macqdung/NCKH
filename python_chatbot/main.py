# main.py - BẢN GEMINI API CHUẨN GEN Z, ĐỌC TÍNH CÁCH VÀ QUERY MYSQL
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import uvicorn
from gemini_bot import GeminiBot

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# KEY GEMINI BẠN ĐÃ CUNG CẤP LÊN ĐÂY
API_KEY = "AIzaSyCbWp8_TDE8MgXBEuC3I_jdknP-KZXif38"
bot = GeminiBot(api_key=API_KEY)

class ChatRequest(BaseModel):
    message: str
    session_id: str = "default_user" # Dùng chung 1 session cho demo

@app.get("/")
def home():
    return {"status": "OK", "message": "Chatbot Gemini GenZ đang chạy!"}

@app.post("/chatbot")
async def chatbot(req: ChatRequest):
    msg = req.message.strip()
    if not msg:
        return {"reply": "Chưa gõ gì mà sao gửi được hay vậy bá dơ?"}

    # Đưa hết tất cả mọi câu chat cho Gemini xử lý
    # Gemini sẽ tự trò chuyện, phân tích tính cách, tự call tool Database khi cần!
    reply = bot.chat(req.session_id, msg)

    return {"reply": reply}

if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)
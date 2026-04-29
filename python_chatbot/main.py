import os
import sys

import uvicorn
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from gemini_bot import GeminiBot


app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

bot = GeminiBot()


class ChatRequest(BaseModel):
    message: str
    session_id: str = "default_user"


@app.get("/")
def home():
    return {"status": "OK", "message": "Book recommendation chatbot is running."}


@app.post("/chatbot")
async def chatbot(req: ChatRequest):
    msg = req.message.strip()
    if not msg:
        return {"reply": "Bạn chưa nhập nội dung. Hãy nói mình biết bạn muốn tìm sách gì nhé."}

    return {"reply": bot.chat(req.session_id, msg)}


if __name__ == "__main__":
    uvicorn.run("main:app", host="127.0.0.1", port=8000, reload=True)

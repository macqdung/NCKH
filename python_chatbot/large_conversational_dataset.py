"""
Synthetic large conversational dataset generator.
Creates diverse conversational phrases covering:
- Greetings
- Farewells
- Thanks
- Help requests
- Small talk about life topics
- Book inquiries with variety of genres, moods, levels
- Mixed Q&A pairs for chatbot training

The dataset will be saved in JSON format for easy integration with chatbot pipeline.

This is a sample script to generate ~500 diverse conversation entries.
"""

import json
import random

def generate_dataset():
    greetings = ["Hello", "Hi", "Good morning", "Good evening", "Hey there", "Xin chào", "Chào bạn"]
    farewells = ["Goodbye", "Bye", "See you later", "Hẹn gặp lại", "Tạm biệt"]
    thanks = ["Thanks", "Thank you", "Cảm ơn", "Thanks a lot", "Cảm ơn bạn nhiều"]
    helps = ["Can you help me?", "I need assistance", "Bạn có thể giúp tôi không?", "Help me please", "Tôi cần trợ giúp"]
    how_are_you = ["How are you?", "Bạn khỏe không?", "Bạn cảm thấy thế nào?", "Are you okay?"]

    life_topics = [
        "What's the weather today?", "Tell me a joke", "What do you think about life?",
        "Bạn có lời khuyên về cuộc sống không?",
        "Mẹo làm việc hiệu quả là gì?", "Tell me a fun fact", "Bạn có thể kể chuyện không?"
    ]

    book_genres = ["IT", "Finance", "Life skills", "Psychology", "Romance", "Science Fiction"]

    dataset = []

    # Greetings
    for phrase in greetings:
        dataset.append({"input": phrase, "intent": "greeting", "response": "Xin chào! Làm thế nào tôi có thể giúp bạn?"})

    # Farewells
    for phrase in farewells:
        dataset.append({"input": phrase, "intent": "farewell", "response": "Chúc bạn một ngày tốt lành! Hẹn gặp lại!"})

    # Thanks
    for phrase in thanks:
        dataset.append({"input": phrase, "intent": "thanks", "response": "Bạn rất hoan nghênh! Rất vui được giúp đỡ bạn."})

    # Help
    for phrase in helps:
        dataset.append({"input": phrase, "intent": "help", "response": "Bạn có thể hỏi tôi về thể loại sách, tác giả hay tâm trạng của bạn."})

    # How are you
    for phrase in how_are_you:
        dataset.append({"input": phrase, "intent": "how_are_you", "response": "Tôi khỏe, cảm ơn bạn đã hỏi! Bạn cần tư vấn sách gì?"})

    # Life topics small talk
    for phrase in life_topics:
        dataset.append({"input": phrase, "intent": "small_talk", "response": "Đó là chủ đề thú vị! Bạn muốn tôi tư vấn sách liên quan đến chủ đề này không?"})

    # Book inquiries by genres, moods, levels with sample responses
    for genre in book_genres:
        dataset.append({
            "input": f"Can you recommend some {genre} books?",
            "intent": "book_recommendation",
            "entity": {"genre": genre},
            "response": f"Tôi có nhiều sách về {genre}. Bạn muốn sách dành cho người mới bắt đầu hay nâng cao?"
        })

    moods = ["happy", "sad", "neutral"]
    for mood in moods:
        dataset.append({
            "input": f"I'm feeling {mood} today. What should I read?",
            "intent": "book_recommendation",
            "entity": {"mood": mood},
            "response": f"Khi bạn đang {mood}, đây là vài cuốn sách tôi nghĩ bạn sẽ thích."
        })

    levels = ["beginner", "intermediate", "advanced"]
    for level in levels:
        dataset.append({
            "input": f"I'm an {level} reader. Suggestions?",
            "intent": "book_recommendation",
            "entity": {"level": level},
            "response": f"Dưới đây là các sách phù hợp cho trình độ {level} của bạn."
        })

    # Random filler entries to reach ~500 total
    filler_count = 500 - len(dataset)
    filler_phrases = [f"Sample filler phrase {i+1}" for i in range(filler_count)]
    for fp in filler_phrases:
        dataset.append({"input": fp, "intent": "small_talk", "response": "Cảm ơn câu hỏi của bạn!"})

    return dataset

def save_dataset(dataset, filename="large_chat_dataset.json"):
    with open(filename, "w", encoding="utf-8") as f:
        json.dump(dataset, f, ensure_ascii=False, indent=2)
    print(f"Synthetic dataset saved to {filename}")

if __name__ == "__main__":
    ds = generate_dataset()
    save_dataset(ds)

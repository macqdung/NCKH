import json
import random
import os

class ConversationalDataHandler:
    def __init__(self, dataset_path='large_chat_dataset.json'):
        self.dataset_path = dataset_path
        self.data = []
        self.load_data()

    def load_data(self):
        if not os.path.exists(self.dataset_path):
            raise FileNotFoundError(f"Dataset file {self.dataset_path} not found.")
        with open(self.dataset_path, 'r', encoding='utf-8') as f:
            self.data = json.load(f)

    def find_response(self, user_input):
        user_input_lower = user_input.lower()
        candidates = []

        # Simple keyword matching in input phrases to find candidate responses
        for entry in self.data:
            input_text = entry.get('input', '').lower()
            if any(word in user_input_lower for word in input_text.split()):
                candidates.append(entry['response'])

        # Fallback to random if no candidate found
        if not candidates:
            candidates = [entry['response'] for entry in self.data]

        response = random.choice(candidates)
        return response

# For testing
if __name__ == "__main__":
    handler = ConversationalDataHandler()
    while True:
        inp = input("You: ")
        if inp.strip().lower() == 'exit':
            break
        print("Bot:", handler.find_response(inp))

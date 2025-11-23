import yaml
from pathlib import Path

OUT_DIR = Path("rasa_dataset_expanded")
OUT_DIR.mkdir(parents=True, exist_ok=True)

def create_domain_file():
    domain = {
        "version": "3.1",
        "intents": [
            "greet", "goodbye", "thanks", "ask_genre", "provide_genre",
            "ask_mood", "provide_mood", "ask_level", "provide_level",
            "book_query", "smalltalk_weather", "smalltalk_feeling"
        ],
        "entities": ["genre", "mood", "level"],
        "slots": {
            "genre": {"type": "text", "influence_conversation": True},
            "mood": {"type": "text", "influence_conversation": True},
            "level": {"type": "text", "influence_conversation": True}
        },
        "responses": {
            "utter_ask_genre": [{"text": "Bạn thích thể loại sách nào?"}],
            "utter_ask_mood": [{"text": "Bạn đang có tâm trạng như thế nào hôm nay?"}],
            "utter_ask_level": [{"text": "Bạn là người mới bắt đầu hay đã có kinh nghiệm đọc sách?"}],
            "utter_recommend_books": [{"text": "Dưới đây là vài cuốn mình đề xuất dựa trên sở thích của bạn."}],
            "utter_greet": [{"text": "Xin chào! Tôi có thể giúp gì cho bạn?"}],
            "utter_goodbye": [{"text": "Hẹn gặp lại!"}],
            "utter_thanks": [{"text": "Không có gì!"}]
        },
        "actions": [
            "utter_ask_genre", "utter_ask_mood", "utter_ask_level",
            "utter_recommend_books", "utter_greet", "utter_goodbye", "utter_thanks"
        ]
    }
    with (OUT_DIR / "domain.yml").open("w", encoding="utf-8") as f:
        yaml.dump(domain, f, allow_unicode=True)
    print("Created domain.yml")

def create_rules_file():
    rules = {
        "version": "3.1",
        "rules": [
            {
                "rule": "ask for genre when missing and user asks for recommendation",
                "condition": [{"slot": "genre", "type": "not_set"}],
                "steps": [{"intent": "book_query"}, {"action": "utter_ask_genre"}]
            },
            {
                "rule": "ask for mood when missing and user asks for recommendation",
                "condition": [{"slot": "mood", "type": "not_set"}],
                "steps": [{"intent": "book_query"}, {"action": "utter_ask_mood"}]
            },
            {
                "rule": "ask for level when missing and user asks for recommendation",
                "condition": [{"slot": "level", "type": "not_set"}],
                "steps": [{"intent": "book_query"}, {"action": "utter_ask_level"}]
            }
        ]
    }
    with (OUT_DIR / "rules.yml").open("w", encoding="utf-8") as f:
        yaml.dump(rules, f, allow_unicode=True)
    print("Created rules.yml")

def create_config_file():
    config = {
        "version": "3.1",
        "language": "en",
        "pipeline": [
            {"name": "WhitespaceTokenizer"},
            {"name": "RegexFeaturizer"},
            {"name": "LexicalSyntacticFeaturizer"},
            {"name": "CountVectorsFeaturizer"},
            {"name": "CountVectorsFeaturizer", "analyzer": "char_wb", "min_ngram": 1, "max_ngram": 4},
            {"name": "DIETClassifier", "epochs": 100},
            {"name": "EntitySynonymMapper"},
            {"name": "ResponseSelector", "epochs": 100},
            {"name": "FallbackClassifier", "threshold": 0.3, "ambiguity_threshold": 0.1}
        ],
        "policies": [
            {"name": "MemoizationPolicy"},
            {"name": "TEDPolicy", "max_history": 5, "epochs": 100},
            {"name": "RulePolicy"}
        ]
    }
    with (OUT_DIR / "config.yml").open("w", encoding="utf-8") as f:
        yaml.dump(config, f, allow_unicode=True)
    print("Created config.yml")

def create_endpoints_file():
    endpoints = {
        "action_endpoint": {
            "url": "http://localhost:5055/webhook"
        }
    }
    with (OUT_DIR / "endpoints.yml").open("w", encoding="utf-8") as f:
        yaml.dump(endpoints, f, allow_unicode=True)
    print("Created endpoints.yml")

def create_credentials_file():
    creds = {
        "rest": {}
    }
    with (OUT_DIR / "credentials.yml").open("w", encoding="utf-8") as f:
        yaml.dump(creds, f, allow_unicode=True)
    print("Created credentials.yml")

if __name__ == "__main__":
    create_domain_file()
    create_rules_file()
    create_config_file()
    create_endpoints_file()
    create_credentials_file()
    print("All Rasa config files generated in", OUT_DIR)

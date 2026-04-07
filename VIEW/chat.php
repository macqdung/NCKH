<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>チャットボット - 本のおすすめ</title>
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #f2f2f2;
        margin: 0;
        padding: 0;
      }
      .chat-container {
        width: 100%;
        max-width: 600px;
        margin: 40px auto;
        background: white;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        height: 80vh;
      }
      .chat-header {
        padding: 15px;
        background-color: #dc3545;
        color: white;
        font-weight: bold;
        font-size: 1.2em;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
      }
      .chat-messages {
        flex-grow: 1;
        padding: 15px;
        overflow-y: auto;
      }
      .chat-message {
        margin-bottom: 15px;
        line-height: 1.4;
      }
      .chat-message.user {
        text-align: right;
      }
      .chat-message .message-text {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 15px;
        max-width: 75%;
      }
      .chat-message.user .message-text {
        background-color: #dc3545;
        color: white;
      }
      .chat-message.bot .message-text {
        background-color: #e9ecef;
        color: #333;
      }
      .chat-input-area {
        padding: 10px;
        border-top: 1px solid #ddd;
        display: flex;
      }
      .chat-input-area input[type="text"] {
        flex-grow: 1;
        padding: 10px;
        font-size: 1em;
        border: 1px solid #ccc;
        border-radius: 20px;
        outline: none;
      }
      .chat-input-area button {
        background-color: #dc3545;
        border: none;
        color: white;
        padding: 10px 20px;
        margin-left: 10px;
        font-size: 1em;
        border-radius: 20px;
        cursor: pointer;
      }
      .chat-input-area button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    </style>
</head>
<body>
  <div class="chat-container">
    <div class="chat-header">
      本のおすすめチャットボット
    </div>
    <div id="chatMessages" class="chat-messages"></div>
    <form id="chatForm" class="chat-input-area">
      <input type="text" id="userInput" autocomplete="off" placeholder="質問を入力してください..." required />
      <button type="submit">送信</button>
    </form>
  </div>

  <script>
    const chatForm = document.getElementById('chatForm');
    const chatMessages = document.getElementById('chatMessages');
    const userInput = document.getElementById('userInput');

    function appendMessage(text, sender) {
      const msgDiv = document.createElement('div');
      msgDiv.classList.add('chat-message', sender);
      const msgText = document.createElement('span');
      msgText.classList.add('message-text');
      msgText.innerHTML = text;
      msgDiv.appendChild(msgText);
      chatMessages.appendChild(msgDiv);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    chatForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const message = userInput.value.trim();
      if (!message) return;

      appendMessage(message, 'user');
      userInput.value = '';
      userInput.disabled = true;
      chatForm.querySelector('button').disabled = true;

     try {
  const response = await fetch('../CONTROLLER/controlchatbot.php', {  // ← GỌI QUA PHP PROXY
    method: 'POST',
    headers: {
      'Content-Type': 'application/json; charset=utf-8'
    },
    body: JSON.stringify({ message: message })
  });

  if (!response.ok) {
    throw new Error('Server error: ' + response.status);
  }

  const data = await response.json();
  appendMessage(data.reply, 'bot');
} catch (err) {
  console.error(err);
  appendMessage('Lỗi kết nối. Đang thử lại...', 'bot');
}

      userInput.disabled = false;
      chatForm.querySelector('button').disabled = false;
      userInput.focus();
    });
  </script>
</body>
</html>

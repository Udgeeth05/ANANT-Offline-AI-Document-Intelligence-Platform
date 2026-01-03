<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offline AI Assistant (Ollama)</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            width: 800px;
            max-width: 95%;
            background: #020617;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.7);
        }

        h1 {
            text-align: center;
            color: #38bdf8;
        }

        textarea {
            width: 100%;
            height: 120px;
            padding: 10px;
            font-size: 16px;
            border-radius: 6px;
            border: none;
            resize: none;
            margin-top: 10px;
        }

        button {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 16px;
            background: #38bdf8;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #0ea5e9;
        }

        .response {
            margin-top: 20px;
            padding: 15px;
            background: #020617;
            border: 1px solid #1e293b;
            border-radius: 6px;
            white-space: pre-wrap;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>🧠 Offline AI Assistant</h1>
    <p>Powered by <b>llama3.2:latest</b> (Ollama)</p>

    <form method="POST" action="ask.php">
        <textarea name="question" placeholder="Ask anything..." required></textarea>
        <br>
        <button type="submit">Ask</button>
    </form>

    <?php if (isset($_GET['answer'])): ?>
        <div class="response">
            <?= nl2br(htmlspecialchars($_GET['answer'])) ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

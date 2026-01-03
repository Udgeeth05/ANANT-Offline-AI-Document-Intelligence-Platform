<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: /kol/auth/login.php");
    exit;
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/auth/db.php';

/* ================= STATE INITIALIZATION ================= */
$userId = $_SESSION['user_id'] ?? 0;
$fullName = 'User';
$q = trim($_POST['question'] ?? '');
$difficulty = $_POST['difficulty'] ?? 'professional'; 
$answer = '';
$hasResponse = false;
$fileNameDisplay = '';
$isDocumentMode = false;

// Fetch User Info
$stmt = $pdo->prepare("SELECT id, full_name FROM users WHERE email=?");
$stmt->execute([$_SESSION['user']]);
if ($u = $stmt->fetch()) {
    $_SESSION['user_id'] = $u['id'];
    $userId = $u['id'];
    $fullName = $u['full_name'] ?: explode('@', $_SESSION['user'])[0];
}

/* ================= LOAD SPECIFIC CHAT FROM HISTORY ================= */
if (isset($_GET['chat_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM chats WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['chat_id'], $userId]);
    $loadedChat = $stmt->fetch();
    
    if ($loadedChat) {
        $q = $loadedChat['question'];
        $answer = $loadedChat['answer'];
        $hasResponse = true;
        $fileNameDisplay = (strpos($loadedChat['title'], '.') !== false) ? $loadedChat['title'] : '';
    }
}

/* ================= AI PROCESSING LOGIC ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($q !== '' || isset($_FILES['attachment']))) {
    $hasResponse = true;
    $uploadedFileContext = '';

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $originalName = basename($_FILES['attachment']['name']);
        $fileNameDisplay = $originalName;
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/kol/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $storedFile = $uploadDir . $safeName;
        move_uploaded_file($_FILES['attachment']['tmp_name'], $storedFile);

        $content = '';
        if (in_array($ext, ['txt','md','csv','json'])) {
            $content = file_get_contents($storedFile);
            $isDocumentMode = true;
        } elseif ($ext === 'pdf') {
            $xpdfExe = 'C:\\xampp\\htdocs\\kol\\xpdf-tools-win-4.06\\bin64\\pdftotext.exe';
            if (file_exists($xpdfExe)) {
                $cmd = '"' . $xpdfExe . '" -layout -enc UTF-8 ' . escapeshellarg($storedFile) . ' -';
                $content = shell_exec($cmd);
                if (!empty(trim($content))) $isDocumentMode = true;
            }
        }
        if ($isDocumentMode && !empty(trim($content))) {
            $content = (strlen($content) > 12000) ? substr($content, 0, 12000) . "\n...[truncated]" : $content;
            $uploadedFileContext = "\n\n===== CONTEXT =====\n" . $content . "\n===== END CONTEXT =====\n";
        }
    }

    $roleMap = [
        'student' => "Explain simply with analogies.",
        'professional' => "Direct, corporate, and efficient response.",
        'expert' => "Complex analysis, technical depth, and rigorous logic."
    ];
    $systemMsg = $roleMap[$difficulty] ?? $roleMap['professional'];
    $prompt = $isDocumentMode ? "$systemMsg Context: $uploadedFileContext Query: $q" : "$systemMsg Query: $q";

    $data = ["model" => "llama3.2:latest", "prompt" => $prompt, "stream" => false];
    $ch = curl_init("http://localhost:11434/api/generate");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_TIMEOUT => 300
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($res, true);
    $answer = trim($json['response'] ?? 'Engine Error.');

    if ($userId && $answer) {
        $stmt = $pdo->prepare("INSERT INTO chats (user_id,title,question,answer) VALUES (?,?,?,?)");
        $stmt->execute([$userId, ($fileNameDisplay ?: substr($q, 0, 40)), $q, $answer]);
        $newId = $pdo->lastInsertId();
        header("Location: dashboard.php?chat_id=" . $newId);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; color: #111827; }
        
        /* Layout Handling for Collapsible Sidebar */
        #main-wrapper { 
            margin-left: 280px; 
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        body.sidebar-is-collapsed #main-wrapper { margin-left: 68px; }

        /* Ask Bar Reference Design */
        .ask-bar-container { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-radius: 9999px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); 
            transition: all 0.2s ease;
        }
        textarea:focus { outline: none !important; border: none !important; box-shadow: none !important; }

        #loader { display: none; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); z-index: 9999; }
        .bot-eye { animation: blink 2s infinite; }
        @keyframes blink { 0%, 100% { transform: scaleY(1); } 50% { transform: scaleY(0.1); } }
    </style>
</head>
<body>

    <div id="loader" class="fixed inset-0 flex flex-col items-center justify-center">
        <div class="flex gap-1.5 mb-6">
            <div class="w-3 h-9 bg-black bot-eye rounded-full"></div>
            <div class="w-3 h-9 bg-black bot-eye rounded-full"></div>
        </div>
    </div>

    <div class="flex min-h-screen">
        <?php include 'layout/sidebar.php'; ?>

        <div id="main-wrapper" class="flex-1 flex flex-col">
            <div class="sticky top-0 z-40"><?php include 'layout/header.php'; ?></div>

            <main class="p-8 max-w-4xl mx-auto w-full flex-1">
                
                <div class="flex items-center justify-between mb-16 pb-6 border-b border-gray-100 uppercase tracking-widest text-[10px] font-bold">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-400">Active Mode:</span>
                        <span id="activeModeLabel" class="text-blue-600"><?= htmlspecialchars($difficulty) ?></span>
                    </div>
                </div>

                <?php if ($hasResponse): ?>
                    <div class="space-y-12">
                        <div class="flex flex-col gap-2">
                            <h2 class="text-2xl font-semibold text-gray-900 leading-tight"><?= htmlspecialchars($q) ?></h2>
                            <?php if ($fileNameDisplay): ?>
                                <div class="flex items-center gap-2 text-[10px] font-bold text-blue-600 bg-blue-50 w-fit px-2.5 py-1 rounded border border-blue-100">
                                    <i data-lucide="file-text" class="w-3 h-3"></i> <?= strtoupper(htmlspecialchars($fileNameDisplay)) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="bg-white p-10 rounded-[2.5rem] border border-gray-200 relative group">
                            <div id="aiResponse" class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap font-medium">
                                <?= nl2br(htmlspecialchars($answer)) ?>
                            </div>
                            <button onclick="copyContent()" class="absolute top-6 right-6 text-gray-300 hover:text-black transition opacity-0 group-hover:opacity-100">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="py-32 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-white border border-gray-100 rounded-2xl flex items-center justify-center mb-8 shadow-sm">
                            <i data-lucide="cpu" class="w-8 h-8 text-black"></i>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-20 sticky bottom-10">
                    <form method="POST" enctype="multipart/form-data" id="aiForm" onsubmit="startProcessing()">
                        <input type="hidden" name="difficulty" id="diffInput" value="<?= $difficulty ?>">

                        <div id="fileStage" class="hidden mb-4">
                            <div class="inline-flex items-center gap-3 bg-black text-white px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-xl">
                                <span id="stagedName" class="max-w-[180px] truncate">...</span>
                                <button type="button" onclick="clearStaged()" class="ml-2 hover:text-red-400 transition pl-3 border-l border-gray-800"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                        </div>

                        <div class="ask-bar-container p-2 flex items-center gap-3 px-6 h-16">
                            <label class="text-gray-400 hover:text-black cursor-pointer transition">
                                <i data-lucide="paperclip" class="w-6 h-6"></i>
                                <input type="file" name="attachment" id="filePicker" class="hidden">
                            </label>
                            
                            <textarea name="question" id="qBox" required 
                                      class="flex-1 bg-transparent border-none focus:ring-0 text-gray-800 placeholder:text-gray-400 py-2 resize-none text-base font-medium h-full flex items-center" 
                                      placeholder="Send command..." rows="1" style="line-height: 2.5rem;"></textarea>

                            <button class="bg-black hover:bg-gray-800 text-white p-2.5 rounded-full transition-all active:scale-90 flex items-center justify-center">
                                <i data-lucide="arrow-up" class="w-6 h-6"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const qBox = document.getElementById('qBox');
        const aiForm = document.getElementById('aiForm');
        const filePicker = document.getElementById('filePicker');
        const fileStage = document.getElementById('fileStage');
        const stagedName = document.getElementById('stagedName');
        const sideSelect = document.getElementById('difficultyLevel');
        const diffInput = document.getElementById('diffInput');
        const modeLabel = document.getElementById('activeModeLabel');

        // Enter to Search
        qBox.addEventListener('keydown', function(e) { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); if(this.value.trim() !== "") aiForm.submit(); } });
        
        // File Mgmt
        filePicker.addEventListener('change', function() { if (this.files[0]) { stagedName.textContent = this.files[0].name; fileStage.classList.remove('hidden'); fileStage.classList.add('flex'); } });
        function clearStaged() { filePicker.value = ""; fileStage.classList.add('hidden'); }
        
        // Loader
        function startProcessing() { document.getElementById('loader').style.display = 'flex'; }
        
        // Mode Sync
        if(sideSelect) { sideSelect.addEventListener('change', (e) => { diffInput.value = e.target.value; modeLabel.textContent = e.target.value; }); }
        
        async function copyContent() { const text = document.getElementById('aiResponse').innerText; await navigator.clipboard.writeText(text); }
    </script>
</body>
</html>
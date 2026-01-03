<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/auth/db.php';

$userId = $_SESSION['user_id'] ?? 0; 
$chatHistory = [];

if ($userId > 0) {
    try {
        // Fetch history to display in the sidebar
        $stmt = $pdo->prepare("SELECT id, title FROM chats WHERE user_id = ? ORDER BY created_at DESC LIMIT 15");
        $stmt->execute([$userId]);
        $chatHistory = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Sidebar history error: " . $e->getMessage());
    }
}
?>

<style>
    :root {
        --gem-bg: #f0f4f9;
        --gem-sidebar: #f8fafd;
        --gem-accent: #1a73e8;
        --gem-hover: #e8eaed;
        --gem-text: #1f1f1f;
        --gem-text-sec: #444746;
        --sidebar-width: 280px;
        --sidebar-collapsed: 68px;
    }

    .sidebar-container {
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--gem-sidebar);
        display: flex;
        flex-direction: column;
        padding: 12px;
        border-right: 1px solid #e3e3e3;
        position: fixed;
        left: 0; top: 0; z-index: 50;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .sidebar-container.collapsed {
        width: var(--sidebar-collapsed);
    }

    .menu-toggle {
        width: 44px; height: 44px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%; cursor: pointer;
        margin-bottom: 20px; color: var(--gem-text-sec);
    }
    .menu-toggle:hover { background: var(--gem-hover); }

    .btn-new-chat {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 16px; background: #dde3ea; border-radius: 100px;
        color: var(--gem-text-sec); font-weight: 500; margin-bottom: 24px;
        cursor: pointer; transition: all 0.2s; width: max-content;
        min-width: 44px;
    }
    .collapsed .btn-new-chat { padding: 12px; width: 44px; }
    .collapsed .btn-new-chat span { display: none; }

    .history-label { 
        font-size: 14px; font-weight: 600; padding: 0 16px 8px; 
        color: var(--gem-text); white-space: nowrap; 
    }
    .collapsed .history-label { display: none; }

    .history-list { flex: 1; overflow-y: auto; overflow-x: hidden; }
    
    .history-item {
        display: flex; align-items: center; gap: 12px;
        padding: 10px 16px; border-radius: 100px;
        color: var(--gem-text-sec); font-size: 14px;
        cursor: pointer; margin-bottom: 2px;
        transition: background 0.2s;
        text-decoration: none; /* For link styling */
    }
    .history-item span { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 180px; }
    .collapsed .history-item span { display: none; }
    .history-item:hover { background: var(--gem-hover); color: var(--gem-text); }

    /* Configuration Section remains as per your previous requirement */
    .settings-group { padding: 12px; border-top: 1px solid #e3e3e3; }
    .collapsed .settings-group { display: none; }
</style>

<div class="sidebar-container" id="gemSidebar">
    <div class="menu-toggle" onclick="toggleSidebar()">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </div>

    <a href="dashboard.php" class="btn-new-chat" title="New Chat" style="text-decoration: none;">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        <span>New Chat</span>
    </a>

    <div class="history-label">Recent</div>
    <div class="history-list">
        <?php if (!empty($chatHistory)): ?>
            <?php foreach($chatHistory as $chat): ?>
                <a href="dashboard.php?chat_id=<?php echo $chat['id']; ?>" class="history-item" title="<?php echo htmlspecialchars($chat['title']); ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <span><?php echo htmlspecialchars($chat['title']); ?></span>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="px-4 py-2 text-xs text-gray-400 italic collapsed-hide">No recent chats</div>
        <?php endif; ?>
    </div>

    <div class="settings-group">
        <div class="text-[10px] font-bold uppercase text-gray-500 mb-2 tracking-wider">Configuration</div>
        <select class="w-full p-2 text-sm border border-gray-200 rounded-lg bg-white outline-none focus:border-blue-500" id="difficultyLevel">
            <option value="student">Student Mode</option>
            <option value="professional" selected>Professional</option>
            <option value="expert">Expert Analyst</option>
        </select>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('gemSidebar');
    sidebar.classList.toggle('collapsed');
}
</script>
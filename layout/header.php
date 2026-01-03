<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userEmail = $_SESSION['user'] ?? 'user@example.com';
$userName  = explode('@', $userEmail)[0];
$initial   = strtoupper(substr($userName, 0, 1));
?>

<header class="w-full bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="flex items-center justify-between px-6 py-4">

        <!-- LEFT : BRAND -->
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 
                        flex items-center justify-center text-white font-bold text-lg">
                A
            </div>
            <div>
                <h1 class="text-lg font-semibold text-gray-800">ANANT</h1>
                <p class="text-xs text-gray-500">Offline Document Intelligence</p>
            </div>
        </div>

        <!-- CENTER : REALTIME INTERNET STATUS -->
        <div id="netStatus"
             class="flex items-center gap-2 px-4 py-2 rounded-full
                    text-sm font-medium bg-gray-100 text-gray-700">
            <span id="netDot"
                  class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            <span id="netText">Checking...</span>
        </div>

        <!-- RIGHT : USER MENU -->
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex flex-col text-right">
                <span class="text-sm font-medium text-gray-700">
                    <?= htmlspecialchars($userName) ?>
                </span>
                <span class="text-xs text-gray-500">Signed in</span>
            </div>

            <div class="relative">
                <button onclick="toggleUserMenu()"
                        class="w-10 h-10 rounded-full bg-gray-200
                               flex items-center justify-center
                               font-semibold text-gray-700 hover:bg-gray-300">
                    <?= $initial ?>
                </button>

                <!-- DROPDOWN -->
                <div id="userMenu"
                     class="hidden absolute right-0 mt-2 w-48 bg-white
                            rounded-lg shadow-lg border border-gray-200">
                    <a href="/kol/dashboard.php"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Dashboard
                    </a>
                    <a href="/kol/auth/logout.php"
                       class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        Logout
                    </a>
                </div>
            </div>
        </div>

    </div>
</header>

<script>
/* USER DROPDOWN */
function toggleUserMenu() {
    document.getElementById('userMenu').classList.toggle('hidden');
}

/* REALTIME INTERNET STATUS */
function updateNetworkStatus() {
    const dot  = document.getElementById('netDot');
    const text = document.getElementById('netText');
    const box  = document.getElementById('netStatus');

    if (navigator.onLine) {
        dot.className = 'w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse';
        text.textContent = 'Online';
        box.className =
            'flex items-center gap-2 px-4 py-2 rounded-full ' +
            'text-sm font-medium bg-green-50 text-green-700';
    } else {
        dot.className = 'w-2.5 h-2.5 rounded-full bg-red-500';
        text.textContent = 'Offline';
        box.className =
            'flex items-center gap-2 px-4 py-2 rounded-full ' +
            'text-sm font-medium bg-red-50 text-red-700';
    }
}

/* INITIAL CHECK */
updateNetworkStatus();

/* REALTIME EVENTS */
window.addEventListener('online', updateNetworkStatus);
window.addEventListener('offline', updateNetworkStatus);

/* CLOSE DROPDOWN ON OUTSIDE CLICK */
document.addEventListener('click', function (e) {
    const menu = document.getElementById('userMenu');
    if (!e.target.closest('button') && !e.target.closest('#userMenu')) {
        menu.classList.add('hidden');
    }
});
</script>

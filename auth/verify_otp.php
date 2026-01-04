<?php
session_start();

if (!isset($_SESSION['otp'], $_SESSION['otp_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $enteredOtp = trim($_POST['otp']);

    if (
        $enteredOtp == $_SESSION['otp'] &&
        (time() - $_SESSION['otp_time']) <= 300
    ) {
        // ✅ Final login success
        $_SESSION['user'] = $_SESSION['otp_email'];

        unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_time']);

        header("Location: /kol/dashboard.php");
        exit;
    } else {
        $error = "Invalid or expired OTP";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>OTP Verification</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-900">

<div class="bg-gray-800 p-8 rounded-xl w-96 text-white">
    <h2 class="text-2xl font-bold mb-4 text-center">Verify OTP</h2>

    <form method="POST" class="space-y-4">
        <input type="text" name="otp"
               placeholder="Enter 6-digit OTP"
               class="w-full px-4 py-3 rounded bg-gray-700 border border-gray-600"
               required>

        <button class="w-full bg-blue-600 hover:bg-blue-700 py-3 rounded font-bold">
            Verify
        </button>
    </form>

    <?php if (!empty($error)): ?>
        <p class="text-red-400 text-center mt-4"><?= $error ?></p>
    <?php endif; ?>
</div>

</body>
</html>

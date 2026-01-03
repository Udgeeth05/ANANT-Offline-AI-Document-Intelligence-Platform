<?php
require_once 'db.php';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            // Securely hash the password
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_pass);
            
            if ($stmt->execute()) {
                $success = "Account created! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Something went wrong. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | ANANT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" />
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="card w-96 bg-base-100/80 shadow-xl backdrop-blur-md">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-4">Create Account</h2>
            
            <?php if($error): ?>
                <div class="alert alert-error py-2 text-sm mb-4"><span><?php echo $error; ?></span></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert alert-success py-2 text-sm mb-4"><span><?php echo $success; ?></span></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-control mb-2">
                    <label class="label"><span class="label-text">Full Name</span></label>
                    <input type="text" name="full_name" placeholder="John Doe" class="input input-bordered w-full" required />
                </div>

                <div class="form-control mb-2">
                    <label class="label"><span class="label-text">Email</span></label>
                    <input type="email" name="email" placeholder="you@example.com" class="input input-bordered w-full" required />
                </div>
                
                <div class="form-control mb-2">
                    <label class="label"><span class="label-text">Password</span></label>
                    <input type="password" name="password" id="pass1" placeholder="••••••••" class="input input-bordered w-full" required />
                </div>

                <div class="form-control mb-6">
                    <label class="label"><span class="label-text">Confirm Password</span></label>
                    <input type="password" name="confirm_password" id="pass2" placeholder="••••••••" class="input input-bordered w-full" required />
                </div>
                
                <div class="card-actions">
                    <button type="submit" class="btn btn-primary w-full">Sign Up</button>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-sm">Already have an account? <a href="login.php" class="link link-primary">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
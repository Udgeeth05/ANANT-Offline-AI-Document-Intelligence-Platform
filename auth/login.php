<?php
session_start();

/* ✅ include db.php (CORRECT & SAFE) */
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/auth/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /*
      🔐 TEMP LOGIN LOGIC
      (Replace this later with real DB validation)
    */
    if ($email !== '' && $password !== '') {
        $_SESSION['user'] = $email;
        header("Location: /kol/dashboard.php");
        exit();
    }

}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <!-- Tailwind CSS + daisyUI CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center">

    <div class="card w-96 bg-base-100/80 shadow-xl backdrop-blur-md">
        <div class="card-body">
            <h2 class="card-title text-2xl font-bold text-center mb-6">Welcome Back</h2>
            
            <form method="post">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" name="email" placeholder="you@example.com"
                           class="input input-bordered w-full" required />
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Password</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               placeholder="••••••••"
                               class="input input-bordered w-full pr-10" required />
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor"
                                 class="h-5 w-5 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7
                                         -1.274 4.057-5.064 7-9.542 7
                                         -4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between mb-6">
                    <label class="cursor-pointer label">
                        <input type="checkbox" class="checkbox checkbox-primary" />
                        <span class="label-text ml-2">Remember me</span>
                    </label>
                    <a href="#" class="link link-primary text-sm">Forgot password?</a>
                </div>
                
                <div class="card-actions">
                    <button type="submit" class="btn btn-primary w-full">Login</button>
                </div>
                
                <div class="text-center mt-6">
                    <p class="text-sm">
                        Don't have an account?
                        <a href="#" class="link link-primary">Sign up</a>
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.innerHTML = `
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19
                           c-4.478 0-8.268-2.943-9.542-7
                           a9.97 9.97 0 011.563-3.029" />`;
            } else {
                passwordField.type = 'password';
                eyeIcon.innerHTML = `
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5
                           c4.478 0 8.268 2.943 9.542 7
                           -1.274 4.057-5.064 7-9.542 7
                           -4.477 0-8.268-2.943-9.542-7z" />`;
            }
        }
    </script>

</body>
</html>

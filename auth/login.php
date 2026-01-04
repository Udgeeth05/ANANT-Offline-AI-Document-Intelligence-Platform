<?php
session_start();

/* DB */
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/auth/db.php';

/* PHPMailer */
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/libs/PHPMailer/src/PHPMailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/libs/PHPMailer/src/SMTP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/kol/libs/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email !== '' && $password !== '') {

        // 🔐 Generate OTP
        $otp = rand(100000, 999999);

        $_SESSION['otp']       = $otp;
        $_SESSION['otp_email'] = $email;
        $_SESSION['otp_time']  = time();

        // ✉️ Send OTP
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'star14kid@gmail.com';
            $mail->Password   = 'tmzhgngghypapllr';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('star14kid@gmail.com', 'ANANT Security');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Your ANANT Login OTP';
            $mail->Body = "
                <h2>ANANT Secure Login</h2>
                <p>Your One-Time Password:</p>
                <h1>$otp</h1>
                <p>Valid for 5 minutes.</p>
            ";

            $mail->send();

            header("Location: verify_otp.php");
            exit();

        } catch (Exception $e) {
            $error = "OTP delivery failed.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANANT | Secure Gateway</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@latest/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }

        /* Cinematic Background Slider */
        .bg-slider {
            position: fixed;
            inset: 0;
            z-index: -1;
            background-color: #000;
        }

        .bg-slide {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 2s ease-in-out, transform 10s linear;
            transform: scale(1.1);
        }

        .bg-slide.active {
            opacity: 0.6;
            transform: scale(1);
        }

        /* Glassmorphism Panel */
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.8);
        }

        /* Pro Tool Styling */
        .pro-tool-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 4px;
        }

        .input-pro {
            background: rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transition: all 0.3s ease;
        }

        .input-pro:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            background: rgba(0, 0, 0, 0.4) !important;
        }

        .system-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.4; }
            100% { opacity: 1; }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Cinematic Background Slider -->
    <div class="bg-slider" id="slider">
        <!-- Images: Ancient Monuments & Valleys -->
        <div class="bg-slide active" style="background-image: url('https://images.unsplash.com/photo-1548013146-72479768bbaa?auto=format&fit=crop&q=80&w=2000')"></div>
        <div class="bg-slide" style="background-image: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&q=80&w=2000')"></div>
        <div class="bg-slide" style="background-image: url('https://images.unsplash.com/photo-1518709268805-4e9042af9f23?auto=format&fit=crop&q=80&w=2000')"></div>
    </div>

    <!-- Layout Container -->
    <div class="flex flex-col md:flex-row gap-8 items-center max-w-5xl w-full">
        
        <!-- Left Side: System Information (Pro Tooling) -->
        <div class="hidden lg:flex flex-col gap-6 w-1/2 p-8 animate-in fade-in slide-in-from-left-8 duration-700">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white flex items-center justify-center rounded-2xl shadow-xl">
                    <i data-lucide="zap" class="text-black w-6 h-6"></i>
                </div>
                <h1 class="text-4xl font-black text-white tracking-tighter italic">ANANT</h1>
            </div>
            
            <p class="text-xl text-white/70 leading-relaxed font-light">
                Securely interface with the next generation of <span class="text-white font-semibold">Local Neural Processing</span>.
            </p>

            <!-- Diagnostic Panel -->
            <div class="grid grid-cols-2 gap-4 mt-8">
                <div class="glass-card p-4 rounded-2xl">
                    <div class="pro-tool-label">System Status</div>
                    <div class="flex items-center gap-2 text-white font-bold text-sm uppercase italic">
                        <span class="system-dot"></span> Online
                    </div>
                </div>
                <div class="glass-card p-4 rounded-2xl">
                    <div class="pro-tool-label">Node Location</div>
                    <div class="text-white font-bold text-sm uppercase italic">Encrypted-Local</div>
                </div>
                <div class="glass-card p-4 rounded-2xl">
                    <div class="pro-tool-label">Auth Layer</div>
                    <div class="text-white font-bold text-sm uppercase italic">RSA-4096</div>
                </div>
                <div class="glass-card p-4 rounded-2xl">
                    <div class="pro-tool-label">AI Engine</div>
                    <div class="text-blue-400 font-bold text-sm uppercase italic">Anant-Core v4.0</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Card -->
        <div class="card w-full max-w-md glass-card rounded-[2.5rem] animate-in fade-in zoom-in duration-500">
            <div class="card-body p-10">
                <div class="flex flex-col items-center mb-8">
                    <h2 class="text-3xl font-black text-white tracking-tight mb-2 uppercase">Access Portal</h2>
                    <div class="h-1 w-12 bg-blue-500 rounded-full"></div>
                </div>
                
                <form method="post" class="space-y-6">
                    <div class="form-control">
                        <label class="label">
                            <span class="pro-tool-label">Identity Hash (Email)</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40"></i>
                            <input type="email" name="email" placeholder="user@anant.node"
                                   class="input input-pro w-full pl-12 rounded-2xl h-14 font-semibold" required />
                        </div>
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="pro-tool-label">Access Token (Password)</span>
                        </label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40"></i>
                            <input type="password" name="password" id="password"
                                   placeholder="••••••••"
                                   class="input input-pro w-full pl-12 pr-12 rounded-2xl h-14 font-semibold" required />
                            <button type="button" onclick="togglePassword()"
                                     class="absolute inset-y-0 right-0 pr-4 flex items-center text-white/40 hover:text-white transition-colors">
                                <i id="eye-icon" data-lucide="eye" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider">
                        <label class="flex items-center gap-2 cursor-pointer text-white/60 hover:text-white transition">
                            <input type="checkbox" class="checkbox checkbox-xs border-white/20 checkbox-primary rounded-md" />
                            <span>Persist Session</span>
                        </label>
                        <a href="#" class="text-blue-400 hover:text-blue-300 transition underline underline-offset-4">Lost Token?</a>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="btn bg-white border-none hover:bg-gray-200 text-black w-full rounded-2xl h-14 font-black uppercase tracking-widest text-sm shadow-2xl shadow-blue-500/20">
                            Initialize Login
                        </button>
                    </div>
                    
                    <div class="text-center mt-6">
                        <p class="text-[10px] uppercase font-bold text-white/40 tracking-widest leading-loose">
                            New Intelligence node? 
                            <a href="#" class="text-white hover:text-blue-400 transition ml-1 underline underline-offset-4">Register Station</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Password Toggle
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const icon = document.querySelector('#eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordField.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        // Cinematic Slider Logic
        const slides = document.querySelectorAll('.bg-slide');
        let currentSlide = 0;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(nextSlide, 8000);
    </script>

</body>
</html>
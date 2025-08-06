<?php
session_start();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                // Basic login validation
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                
                // Simple demo authentication (replace with database logic)
                if ($email === 'admin@barebloom.com' && $password === 'password123') {
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_email'] = $email;
                    $response['success'] = true;
                    $response['message'] = 'Login successful!';
                } else {
                    $response['message'] = 'Invalid email or password!';
                }
                break;
                
            case 'signup':
                // Basic signup validation
                $firstName = htmlspecialchars($_POST['firstName']);
                $lastName = htmlspecialchars($_POST['lastName']);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirmPassword'];
                
                if ($password !== $confirmPassword) {
                    $response['message'] = 'Passwords do not match!';
                } elseif (strlen($password) < 8) {
                    $response['message'] = 'Password must be at least 8 characters!';
                } else {
                    // In a real app, save to database
                    $response['success'] = true;
                    $response['message'] = 'Account created successfully!';
                }
                break;
                
            case 'forgot_password':
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    // In a real app, send email
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['verification_code'] = '1234'; // Demo code
                    $response['success'] = true;
                    $response['message'] = 'Password reset email sent!';
                } else {
                    $response['message'] = 'Please enter a valid email address!';
                }
                break;
                
            case 'verify_code':
                $code = $_POST['code'];
                
                if (isset($_SESSION['verification_code']) && $code === $_SESSION['verification_code']) {
                    $_SESSION['code_verified'] = true;
                    $response['success'] = true;
                    $response['message'] = 'Code verified successfully!';
                } else {
                    $response['message'] = 'Invalid verification code!';
                }
                break;
                
            case 'set_password':
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirmPassword'];
                
                if (!isset($_SESSION['code_verified']) || !$_SESSION['code_verified']) {
                    $response['message'] = 'Verification required!';
                } elseif ($password !== $confirmPassword) {
                    $response['message'] = 'Passwords do not match!';
                } elseif (strlen($password) < 8) {
                    $response['message'] = 'Password must be at least 8 characters!';
                } else {
                    // In a real app, update password in database
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['verification_code']);
                    unset($_SESSION['code_verified']);
                    $response['success'] = true;
                    $response['message'] = 'Password updated successfully!';
                }
                break;
        }
    }
    
    // Return JSON response for AJAX calls
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];

// If logged in, redirect to dashboard (you can create this page)
if ($isLoggedIn && !isset($_GET['logout'])) {
    // Uncomment to redirect to dashboard
    // header('Location: dashboard.php');
    // exit;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bare Bloom - Authentication</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #5d1a1a;
            --success-color: #84fab0;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-bg-signup: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            --card-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .auth-container {
            min-height: 100vh;
        }

        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            border: none;
            transition: all 0.5s ease;
            position: relative;
        }

        .page {
            display: none;
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.5s ease;
        }

        .page.active {
            display: block;
            opacity: 1;
            transform: translateX(0);
        }

        .return-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            color: #666;
        }

        .return-btn:hover {
            background: #e9ecef;
            color: #333;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .logo-section {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .logo-icon {
            width: 50px;
            height: 65px;
            margin-right: 15px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg {
            width: 100%;
            height: 100%;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 300;
            color: var(--secondary-color);
            font-style: italic;
            margin: 0;
        }

        .auth-title {
            font-size: 48px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .auth-subtitle {
            color: #666;
            margin-bottom: 2.5rem;
        }

        .form-control {
            padding: 15px 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
            z-index: 10;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary-custom {
            background: var(--primary-color);
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }

        .btn-primary-custom:hover {
            background: #0056b3;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,123,255,0.2);
            color: white;
        }

        .auth-link {
            color: #666;
        }

        .auth-link a {
            color: var(--primary-color);
            text-decoration: none;
            cursor: pointer;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            color: #dc3545;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #dc3545;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 30px 0;
            color: #666;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e1e5e9;
        }

        .divider span {
            background: white;
            padding: 0 20px;
            font-size: 14px;
        }

        .social-btn {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: inherit;
        }

        .social-btn:hover {
            border-color: #9ca3af;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: inherit;
            text-decoration: none;
        }

        .image-section {
            background: var(--gradient-bg);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 600px;
            transition: background 0.5s ease;
        }

        .image-section.signup-bg {
            background: var(--gradient-bg-signup);
        }

        .code-input {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 0.5em;
            padding: 20px;
            border: 2px solid #d1d5db;
            border-radius: 12px;
            background: #f8f9fa;
        }

        .code-input:focus {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
        }

        .password-strength {
            margin-top: 10px;
        }

        .strength-bar {
            height: 4px;
            background: #e1e5e9;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }

        .strength-text {
            font-size: 12px;
            font-weight: 500;
        }

        .requirements {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }

        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .requirement i {
            margin-right: 8px;
            font-size: 10px;
        }

        .requirement.met {
            color: #28a745;
        }

        .requirement.unmet {
            color: #dc3545;
        }

        .timer {
            color: #dc3545;
            font-weight: 600;
            font-size: 18px;
        }

        .resend-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        .resend-link:hover {
            text-decoration: underline;
            color: var(--primary-color);
        }

        .phone-mockup {
            position: relative;
            z-index: 2;
            transition: transform 0.5s ease;
        }

        .login-phone { transform: rotate(-5deg); }
        .signup-phone { transform: rotate(5deg); }
        .forgot-phone { transform: rotate(0deg); }
        .verify-phone { transform: rotate(-3deg); }
        .password-phone { transform: rotate(2deg); }

        .phone {
            width: 200px;
            height: 400px;
            background: white;
            border-radius: 25px;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            position: relative;
        }

        .phone-screen {
            width: 100%;
            height: 100%;
            background: #f8f9fa;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            position: relative;
        }

        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.1;
            background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px);
            background-size: 50px 50px;
        }

        .illustration {
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .illustration.active {
            display: block;
            opacity: 1;
        }

        .alert {
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.3; }
        }

        @media (max-width: 768px) {
            .image-section {
                display: none !important;
            }
            
            .auth-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid auth-container d-flex align-items-center justify-content-center">
        <div class="row auth-card w-100" style="max-width: 1000px;">
            <!-- Form Section -->
            <div class="col-lg-6 p-5">
                <?php if ($isLoggedIn): ?>
                <!-- Dashboard/Welcome Page -->
                <div class="text-center">
                    <div class="logo-section justify-content-center">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    <h1 class="auth-title">Welcome!</h1>
                    <p class="auth-subtitle">You are successfully logged in as <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    <a href="?logout=1" class="btn btn-primary-custom">Logout</a>
                </div>
                <?php else: ?>
                
                <!-- Login Page -->
                <div class="page active" id="loginPage">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    
                    <h1 class="auth-title">Login</h1>
                    <p class="auth-subtitle">Login to access your account</p>
                    <p class="text-muted small">Demo: Use <strong>admin@barebloom.com</strong> / <strong>password123</strong></p>
                    
                    <div id="loginMessage"></div>
                    
                    <form id="loginForm">
                        <div class="mb-4">
                            <label for="loginEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" placeholder="" required>
                        </div>
                        
                        <div class="mb-4 position-relative">
                            <label for="loginPassword" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" placeholder="" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label text-muted" for="remember">
                                    Remember me
                                </label>
                            </div>
                            <a class="forgot-password" onclick="showPage('forgotPage')">Forgot Password</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom mb-3">Login</button>
                        
                        <div class="text-center auth-link mb-4">
                            Don't Have an account? <a onclick="showPage('signupPage')">Sign up</a>
                        </div>
                        
                        <div class="divider">
                            <span>Or Login with</span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877f2">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.52 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Sign Up Page -->
                <div class="page" id="signupPage">
                    <button class="return-btn" onclick="showPage('loginPage')">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    
                    <h1 class="auth-title">Sign up</h1>
                    <p class="auth-subtitle">Fill up the form to create new account and starting adventure.</p>
                    
                    <div id="signupMessage"></div>
                    
                    <form id="signupForm">
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label for="firstName" class="form-label fw-semibold">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="" required>
                            </div>
                            <div class="col-6">
                                <label for="lastName" class="form-label fw-semibold">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="signupEmail" class="form-label fw-semibold">Email Address</label>
                            <input type="email" class="form-control" id="signupEmail" name="email" placeholder="" required>
                        </div>
                        
                        <div class="mb-3 position-relative">
                            <label for="signupPassword" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="signupPassword" name="password" placeholder="" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('signupPassword')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        
                        <div class="mb-4 position-relative">
                            <label for="confirmPassword" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agreeTerms" name="agreeTerms" required>
                            <label class="form-check-label text-muted" for="agreeTerms">
                                I agree to the <a href="#" class="text-primary">Terms and Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom mb-3">Create Account</button>
                        
                        <div class="text-center auth-link mb-4">
                            Already Have an account? <a onclick="showPage('loginPage')">Login</a>
                        </div>
                        
                        <div class="divider">
                            <span>Or Sign up with</span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877f2">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.52 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Forgot Password Page -->
                <div class="page" id="forgotPage">
                    <button class="return-btn" onclick="showPage('loginPage')">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    
                    <h1 class="auth-title">Forgot your password?</h1>
                    <p class="auth-subtitle">Enter below registered email id to get your email sent for forgot password.</p>
                    
                    <div id="forgotMessage"></div>
                    
                    <form id="forgotPasswordForm">
                        <div class="mb-4">
                            <label for="forgotEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="forgotEmail" name="email" placeholder="" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom mb-4">Submit</button>
                        
                        <div class="divider">
                            <span>Or Login with</span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#1877f2">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24">
                                        <path fill="#4285f4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34a853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#fbbc05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#ea4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="social-btn w-100">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.52 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Verify Code Page -->
                <div class="page" id="verifyPage">
                    <button class="return-btn" onclick="showPage('forgotPage')">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    
                    <h1 class="auth-title">Verify Code</h1>
                    <p class="auth-subtitle">An authentication code has been sent to your email.</p>
                    <p class="text-muted small">Demo code: <strong>1234</strong></p>
                    
                    <div id="verifyMessage"></div>
                    
                    <form id="verifyCodeForm">
                        <div class="mb-4">
                            <label for="verificationCode" class="form-label fw-semibold">Enter Code</label>
                            <input type="text" class="form-control code-input" id="verificationCode" name="code" placeholder="0000" maxlength="4" required>
                        </div>
                        
                        <div class="text-center mb-4">
                            <span class="text-muted">Didn't receive code? </span>
                            <a class="resend-link" id="resendCode">Resend Code</a>
                            <div class="mt-2">
                                <span class="timer" id="timer">02:00</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom mb-4">Verify</button>
                    </form>
                </div>

                <!-- Set Password Page -->
                <div class="page" id="setPasswordPage">
                    <button class="return-btn" onclick="showPage('verifyPage')">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    
                    <div class="logo-section">
                        <div class="logo-icon">
                            <svg viewBox="0 0 100 130" xmlns="http://www.w3.org/2000/svg">
                                <path d="M50 10 C30 30, 15 50, 15 70 C15 90, 30 110, 50 110 C70 110, 85 90, 85 70 C85 50, 70 30, 50 10 Z" 
                                      fill="#5d1a1a"/>
                                <g fill="white">
                                    <path d="M35 45 C35 40, 38 35, 42 35 C46 35, 49 40, 49 45 C49 50, 46 55, 42 55 C38 55, 35 50, 35 45"/>
                                    <path d="M42 30 C38 32, 35 35, 35 40 C35 38, 37 36, 40 35 C42 34, 44 33, 42 30"/>
                                    <path d="M48 32 C50 30, 52 32, 54 35 C52 33, 50 34, 48 35 C47 36, 46 38, 48 32"/>
                                    <path d="M42 55 C42 58, 43 62, 45 65 C46 67, 47 68, 48 70"/>
                                    <path d="M42 55 C42 58, 41 62, 39 65 C38 67, 37 68, 36 70"/>
                                    <path d="M25 60 C27 58, 30 60, 32 63 C30 61, 27 62, 25 60"/>
                                    <path d="M68 65 C70 63, 72 65, 74 68 C72 66, 70 67, 68 65"/>
                                </g>
                            </svg>
                        </div>
                        <h2 class="logo-text">Bare Bloom</h2>
                    </div>
                    
                    <h1 class="auth-title">Set a password</h1>
                    <p class="auth-subtitle">Your previous password has been reseted. Please set a new password for your account.</p>
                    
                    <div id="setPasswordMessage"></div>
                    
                    <form id="setPasswordForm">
                        <div class="mb-3 position-relative">
                            <label for="newPassword" class="form-label fw-semibold">Create Password</label>
                            <input type="password" class="form-control" id="newPassword" name="password" placeholder="" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthBar"></div>
                                </div>
                                <div class="strength-text" id="strengthText">Password strength</div>
                            </div>
                            <div class="requirements">
                                <div class="requirement unmet" id="lengthReq">
                                    <i class="bi bi-x-circle"></i>
                                    <span>At least 8 characters</span>
                                </div>
                                <div class="requirement unmet" id="uppercaseReq">
                                    <i class="bi bi-x-circle"></i>
                                    <span>One uppercase letter</span>
                                </div>
                                <div class="requirement unmet" id="numberReq">
                                    <i class="bi bi-x-circle"></i>
                                    <span>One number</span>
                                </div>
                                <div class="requirement unmet" id="specialReq">
                                    <i class="bi bi-x-circle"></i>
                                    <span>One special character</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4 position-relative">
                            <label for="confirmNewPassword" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmNewPassword" name="confirmPassword" placeholder="" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmNewPassword')">
                                <i class="bi bi-eye"></i>
                            </button>
                            <div class="mt-2">
                                <small class="text-muted" id="matchMessage"></small>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom mb-4">Set Password</button>
                    </form>
                </div>
                
                <?php endif; ?>
            </div>
            
            <!-- Image Section -->
            <div class="col-lg-6 image-section d-none d-lg-flex" id="imageSection">
                <div class="background-pattern"></div>
                
                <!-- Login Illustration -->
                <div class="illustration active" id="loginIllustration">
                    <div class="phone-mockup login-phone">
                        <div class="phone">
                            <div class="phone-screen">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">✓</div>
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">🔒</div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Signup Illustration -->
                <div class="illustration" id="signupIllustration">
                    <div class="phone-mockup signup-phone">
                        <div class="phone">
                            <div class="phone-screen">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">👤</div>
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">+</div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <div style="width: 8px; height: 8px; background: #ec4899; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #8b5cf6; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forgot Password Illustration -->
                <div class="illustration" id="forgotIllustration">
                    <div class="phone-mockup forgot-phone">
                        <div class="phone">
                            <div class="phone-screen">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">?</div>
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">📧</div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <div style="width: 8px; height: 8px; background: #f59e0b; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #8fd3f4; border-radius: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Verify Code Illustration -->
                <div class="illustration" id="verifyIllustration">
                    <div class="phone-mockup verify-phone">
                        <div class="phone">
                            <div class="phone-screen">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">🔐</div>
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px;">1234</div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <div style="width: 8px; height: 8px; background: #8b5cf6; border-radius: 50%; animation: blink 1s infinite;"></div>
                                    <div style="width: 8px; height: 8px; background: #3b82f6; border-radius: 50%; animation: blink 1s infinite 0.2s;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%; animation: blink 1s infinite 0.4s;"></div>
                                    <div style="width: 8px; height: 8px; background: #8fd3f4; border-radius: 50%; animation: blink 1s infinite 0.6s;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Set Password Illustration -->
                <div class="illustration" id="setPasswordIllustration">
                    <div class="phone-mockup password-phone">
                        <div class="phone">
                            <div class="phone-screen">
                                <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; font-weight: bold;">🔑</div>
                                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 20px;">✨</div>
                                <div style="display: flex; gap: 8px; margin-top: 10px;">
                                    <div style="width: 8px; height: 8px; background: #10b981; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #059669; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #84fab0; border-radius: 50%;"></div>
                                    <div style="width: 8px; height: 8px; background: #8fd3f4; border-radius: 50%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5.3 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        let timeLeft = 120;
        let timerInterval;

        function showMessage(elementId, message, isSuccess = false) {
            const messageElement = document.getElementById(elementId);
            if (messageElement) {
                messageElement.innerHTML = `<div class="alert alert-${isSuccess ? 'success' : 'danger'}">${message}</div>`;
                setTimeout(() => {
                    messageElement.innerHTML = '';
                }, 5000);
            }
        }

        function showPage(pageId) {
            // Hide all pages
            document.querySelectorAll('.page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show target page
            setTimeout(() => {
                document.getElementById(pageId).classList.add('active');
            }, 100);

            // Update image section background and illustration
            const imageSection = document.getElementById('imageSection');
            const illustrations = document.querySelectorAll('.illustration');
            
            // Hide all illustrations
            illustrations.forEach(ill => ill.classList.remove('active'));
            
            // Update background and show relevant illustration
            switch(pageId) {
                case 'signupPage':
                    imageSection.classList.add('signup-bg');
                    document.getElementById('signupIllustration')?.classList.add('active');
                    break;
                case 'forgotPage':
                    imageSection.classList.remove('signup-bg');
                    document.getElementById('forgotIllustration')?.classList.add('active');
                    break;
                case 'verifyPage':
                    imageSection.classList.remove('signup-bg');
                    document.getElementById('verifyIllustration')?.classList.add('active');
                    startTimer();
                    break;
                case 'setPasswordPage':
                    imageSection.classList.remove('signup-bg');
                    document.getElementById('setPasswordIllustration')?.classList.add('active');
                    break;
                default: // loginPage
                    imageSection.classList.remove('signup-bg');
                    document.getElementById('loginIllustration')?.classList.add('active');
                    break;
            }
        }

        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.nextElementSibling.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye';
            }
        }

        function startTimer() {
            timeLeft = 120;
            const timerElement = document.getElementById('timer');
            const resendLink = document.getElementById('resendCode');
            
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    resendLink.style.pointerEvents = 'auto';
                    resendLink.style.opacity = '1';
                    timerElement.textContent = 'Code expired';
                    timerElement.style.color = '#dc3545';
                } else {
                    timeLeft--;
                }
            }, 1000);
        }

        function checkPasswordStrength(password) {
            let score = 0;
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                number: /\d/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            updateRequirement('lengthReq', requirements.length);
            updateRequirement('uppercaseReq', requirements.uppercase);
            updateRequirement('numberReq', requirements.number);
            updateRequirement('specialReq', requirements.special);

            Object.values(requirements).forEach(met => met && score++);

            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            strengthBar.className = 'strength-fill';
            
            if (score === 0) {
                strengthText.textContent = 'Password strength';
                strengthText.style.color = '#666';
            } else if (score === 1) {
                strengthBar.classList.add('strength-weak');
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#dc3545';
            } else if (score === 2) {
                strengthBar.classList.add('strength-fair');
                strengthText.textContent = 'Fair';
                strengthText.style.color = '#ffc107';
            } else if (score === 3) {
                strengthBar.classList.add('strength-good');
                strengthText.textContent = 'Good';
                strengthText.style.color = '#17a2b8';
            } else if (score === 4) {
                strengthBar.classList.add('strength-strong');
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#28a745';
            }

            return score;
        }

        function updateRequirement(elementId, met) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            const icon = element.querySelector('i');
            
            if (met) {
                element.className = 'requirement met';
                icon.className = 'bi bi-check-circle';
            } else {
                element.className = 'requirement unmet';
                icon.className = 'bi bi-x-circle';
            }
        }

        function checkPasswordMatch() {
            const password = document.getElementById('newPassword')?.value;
            const confirmPassword = document.getElementById('confirmNewPassword')?.value;
            const matchMessage = document.getElementById('matchMessage');

            if (!matchMessage || confirmPassword === '') {
                if (matchMessage) matchMessage.textContent = '';
                return;
            }

            if (password === confirmPassword) {
                matchMessage.textContent = 'Passwords match ✓';
                matchMessage.style.color = '#28a745';
            } else {
                matchMessage.textContent = 'Passwords do not match';
                matchMessage.style.color = '#dc3545';
            }
        }

        function handleFormSubmit(formId, action, messageId, nextPage = null) {
            const form = document.getElementById(formId);
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitBtn = this.querySelector('.btn-primary-custom');
                const formData = new FormData(this);
                formData.append('action', action);
                formData.append('ajax', '1');
                
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...';
                submitBtn.disabled = true;
                
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Success!';
                        submitBtn.style.background = '#28a745';
                        showMessage(messageId, data.message, true);
                        
                        setTimeout(() => {
                            if (action === 'login') {
                                window.location.reload();
                            } else if (nextPage) {
                                showPage(nextPage);
                            } else if (action === 'signup') {
                                showPage('loginPage');
                            } else if (action === 'set_password') {
                                showPage('loginPage');
                            }
                            
                            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text');
                            submitBtn.style.background = '#007bff';
                            submitBtn.disabled = false;
                        }, 2000);
                    } else {
                        submitBtn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error';
                        submitBtn.style.background = '#dc3545';
                        showMessage(messageId, data.message, false);
                        
                        setTimeout(() => {
                            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text');
                            submitBtn.style.background = '#007bff';
                            submitBtn.disabled = false;
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage(messageId, 'An error occurred. Please try again.', false);
                    
                    submitBtn.innerHTML = submitBtn.getAttribute('data-original-text');
                    submitBtn.style.background = '#007bff';
                    submitBtn.disabled = false;
                });
            });
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Store original button texts
            document.querySelectorAll('.btn-primary-custom').forEach(btn => {
                btn.setAttribute('data-original-text', btn.textContent);
            });

            // Handle all forms
            handleFormSubmit('loginForm', 'login', 'loginMessage');
            handleFormSubmit('signupForm', 'signup', 'signupMessage');
            handleFormSubmit('forgotPasswordForm', 'forgot_password', 'forgotMessage', 'verifyPage');
            handleFormSubmit('verifyCodeForm', 'verify_code', 'verifyMessage', 'setPasswordPage');
            handleFormSubmit('setPasswordForm', 'set_password', 'setPasswordMessage');

            // Code input formatting
            const codeInput = document.getElementById('verificationCode');
            if (codeInput) {
                codeInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/\D/g, '');
                });
            }

            // Resend code
            const resendLink = document.getElementById('resendCode');
            if (resendLink) {
                resendLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (timeLeft <= 0) {
                        timeLeft = 120;
                        const timerElement = document.getElementById('timer');
                        timerElement.style.color = '#dc3545';
                        this.style.pointerEvents = 'none';
                        this.style.opacity = '0.5';
                        startTimer();
                        showMessage('verifyMessage', 'Verification code resent!', true);
                    }
                });
            }

            // Password strength checker for new password
            const newPasswordInput = document.getElementById('newPassword');
            if (newPasswordInput) {
                newPasswordInput.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
            }

            const confirmNewPasswordInput = document.getElementById('confirmNewPassword');
            if (confirmNewPasswordInput) {
                confirmNewPasswordInput.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
</body>
</html>
<?php
/**
 * Authentication Page - Sign In & Sign Up
 * Modern UI with glassmorphism effects and Google OAuth integration
 */

require_once '../config/database.php';
require_once '../config/email-service.php';
require_once '../config/asset-config.php';

// Handle form submissions
$mode = $_GET['mode'] ?? 'signin';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'signin':
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                
                if (!empty($email) && !empty($password)) {
                    try {
                        $pdo = DatabaseConfig::getConnection();
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
                        $stmt->execute([$email]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($user && password_verify($password, $user['password'])) {
                            // Check if email is verified
                            if (!$user['email_verified']) {
                                $error = 'Please verify your email address before signing in. Check your email for the verification link.';
                                break;
                            }
                            
                            session_start();
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['user_email'] = $user['email'];
                            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                            $_SESSION['user_type'] = $user['user_type'];
                            
                            // Update last login
                            $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                            $stmt->execute([$user['id']]);
                            
                            // Handle remember me
                            if (isset($_POST['remember'])) {
                                $token = bin2hex(random_bytes(32));
                                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                                $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                                $stmt->execute([$token, $user['id']]);
                            }
                            
                            // Redirect with success parameter to show welcome message
                            header('Location: index.php?login=success');
                            exit;
                        } else {
                            $error = 'Invalid email or password';
                        }
                    } catch (Exception $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                } else {
                    $error = 'Email and password are required';
                }
                break;
                
            case 'signup':
                $firstName = $_POST['first_name'] ?? '';
                $lastName = $_POST['last_name'] ?? '';
                $email = $_POST['email'] ?? '';
                $password = $_POST['password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                // Validation
                if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
                    $error = 'All fields are required';
                } elseif ($password !== $confirmPassword) {
                    $error = 'Passwords do not match';
                } elseif (strlen($password) < 8) {
                    $error = 'Password must be at least 8 characters long';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Invalid email address';
                } else {
                    try {
                        $pdo = DatabaseConfig::getConnection();
                        
                        // Check if email already exists
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                        $stmt->execute([$email]);
                        if ($stmt->fetch()) {
                            $error = 'Email already registered';
                        } else {
                            // Generate unique username from email
                            $baseUsername = strtolower(explode('@', $email)[0]);
                            $username = $baseUsername;
                            $counter = 1;
                            
                            // Check if username exists and make it unique
                            while (true) {
                                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                                $stmt->execute([$username]);
                                if (!$stmt->fetch()) {
                                    break; // Username is unique
                                }
                                $username = $baseUsername . $counter;
                                $counter++;
                            }
                            
                            // Generate email verification token
                            $verificationToken = bin2hex(random_bytes(32));
                            
                            // Create user (ALWAYS with email_verified = 0 to force verification)
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, password_hash, password, user_type, is_active, email_verified, created_at) VALUES (?, ?, ?, ?, ?, ?, 'buyer', 1, 0, NOW())");
                            $stmt->execute([$username, $firstName, $lastName, $email, $hashedPassword, $hashedPassword]);
                            
                            $userId = $pdo->lastInsertId();
                            
                            // Store verification token
                            $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                            $stmt->execute([$verificationToken, $userId]);
                            
                            // Send verification email using EmailService
                            $emailService = new EmailService();
                            $emailResult = $emailService->sendVerificationEmail($email, $firstName, $verificationToken);
                            
                            if ($emailResult['success']) {
                                if ($emailResult['method'] === 'file') {
                                    // Development mode - provide direct link
                                    $success = '<div class="bg-green-500/20 border border-green-500/30 p-4 rounded-lg">
                                        <div class="flex items-center space-x-2 mb-3">
                                            <i class="ri-check-circle-line text-green-400"></i>
                                            <span class="text-white font-semibold">Account created successfully!</span>
                                        </div>
                                        <div class="bg-blue-500/20 border border-blue-500/30 p-3 rounded-lg mb-3">
                                            <p class="text-white/90 text-sm mb-2">
                                                <strong>🔧 Development Mode:</strong> Email saved to file instead of being sent.
                                            </p>
                                            <p class="text-white/90 text-sm mb-3">
                                                Click the button below to verify your account:
                                            </p>
                                            <a href="' . htmlspecialchars($emailResult['verification_link']) . '" 
                                               class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">
                                                ✅ Verify Account Now
                                            </a>
                                            <p class="text-white/70 text-xs mt-2">
                                                Email saved to: ' . htmlspecialchars(basename($emailResult['file_path'])) . '
                                            </p>
                                        </div>
                                        <p class="text-white/80 text-sm">
                                            You must verify your email before signing in.
                                        </p>
                                    </div>';
                                } else {
                                    // Production mode - email sent
                                    $success = '<div class="bg-green-500/20 border border-green-500/30 p-4 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <i class="ri-check-circle-line text-green-400"></i>
                                            <span class="text-white">Account created successfully! Please check your email and click the verification link before signing in.</span>
                                        </div>
                                    </div>';
                                }
                                $mode = 'signin'; // Switch to signin mode
                            } else {
                                $error = 'Account created but failed to send verification email: ' . ($emailResult['error'] ?? 'Unknown error');
                            }
                        }
                    } catch (Exception $e) {
                        $error = 'Database error: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Handle email verification
if (isset($_GET['action']) && $_GET['action'] === 'verify') {
    $token = $_GET['token'] ?? '';
    
    if (!empty($token)) {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            // Find user with this verification token
            $stmt = $pdo->prepare("SELECT id, email, first_name FROM users WHERE remember_token = ? AND email_verified = 0");
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Verify the user
                $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, remember_token = NULL WHERE id = ?");
                $stmt->execute([$user['id']]);
                
                // Create success message for email verification
                $success = '
                                <div class="flex items-center space-x-2">
                                    <i class="ri-check-circle-line text-green-400"></i>
                                    <span>Email verified successfully! Welcome to Orbix Market, ' . htmlspecialchars($user['first_name']) . '! You can now sign in to your account.</span>
                                </div>';
                $mode = 'signin';
            } else {
                $error = 'Invalid or expired verification link. Please try signing up again or contact support.';
                $mode = 'signup';
            }
        } catch (Exception $e) {
            $error = 'Verification failed: ' . $e->getMessage();
            $mode = 'signup';
        }
    } else {
        $error = 'Invalid verification link.';
        $mode = 'signup';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Orbix Market</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css" rel="stylesheet">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <link rel="stylesheet" href="<?php echo AssetConfig::getCssPath('auth.css'); ?>">
    <!-- <link rel="stylesheet" href="<?php echo AssetConfig::getCssPath('universal-fix.css'); ?>"> -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#FF5F1D',
                        secondary: '#1f2937'
                    },
                    borderRadius: {
                        'button': '8px'
                    },
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                        'pacifico': ['Pacifico', 'serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="font-inter min-h-screen gradient-bg flex items-center justify-center p-6">

    <!-- Back to Home - Top Right Corner -->
    <div class="fixed top-6 right-6 z-50">
        <a href="index.php" class="text-white/80 hover:text-white text-sm flex items-center space-x-2 transition-colors">
            <span>Back to Home</span>
            <i class="ri-arrow-right-line"></i>
        </a>
    </div>

    <!-- Background decorative elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="floating-animation absolute top-20 left-20 w-32 h-32 bg-white/10 rounded-full blur-xl"></div>
        <div class="floating-animation absolute top-40 right-32 w-24 h-24 bg-white/20 rounded-full blur-lg" style="animation-delay: -2s;"></div>
        <div class="floating-animation absolute bottom-32 left-40 w-40 h-40 bg-white/5 rounded-full blur-2xl" style="animation-delay: -4s;"></div>
        <div class="floating-animation absolute bottom-20 right-20 w-28 h-28 bg-white/15 rounded-full blur-xl" style="animation-delay: -6s;"></div>
    </div>

    <!-- Main Container -->
    <div class="w-full max-w-sm sm:max-w-md relative z-10">
        <!-- Logo -->
        <div class="text-center mb-4 sm:mb-6">
            <div class="flex items-center justify-center space-x-2 mb-2 sm:mb-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <span class="text-white font-bold text-lg sm:text-xl">O</span>
                </div>
                <span class="font-pacifico text-xl sm:text-2xl text-white">Orbix Market</span>
            </div>
            <p class="text-white/80 text-sm hidden sm:block">Welcome to the premium template marketplace</p>
        </div>

        <!-- Auth Form Container -->
        <div class="glass-card rounded-2xl p-4 sm:p-6 compact-form">
            <?php if (!empty($error)): ?>
            <!-- Error Message -->
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/30 rounded-xl text-white">
                <div class="flex items-center space-x-3">
                    <i class="ri-error-warning-line text-red-400"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <!-- Success Message -->
            <div class="mb-6 p-4 bg-green-500/20 border border-green-500/30 rounded-xl text-white">
                <?php echo $success; ?>
            </div>
            <?php endif; ?>

            <?php if ($mode === 'signin'): ?>
            <!-- Sign In Form -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-white text-center mb-2">Welcome Back</h2>
                <p class="text-white/70 text-center text-sm">Sign in to your account</p>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="signin">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-white/90 text-xs sm:text-sm font-medium mb-1 sm:mb-2">Email Address</label>
                        <div class="relative">
                            <i class="ri-mail-line absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60 text-sm"></i>
                            <input type="email" name="email" required 
                                   class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Enter your email">
                        </div>
                    </div>

                    <div>
                        <label class="block text-white/90 text-xs sm:text-sm font-medium mb-1 sm:mb-2">Password</label>
                        <div class="relative">
                            <i class="ri-lock-line absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60 text-sm"></i>
                            <input type="password" name="password" required 
                                   class="w-full pl-10 pr-10 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Enter your password">
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white/80 toggle-password">
                                <i class="ri-eye-line text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <label class="flex items-center text-white/80">
                            <input type="checkbox" name="remember" class="mr-2 rounded border-white/20 bg-white/10 w-3 h-3">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="text-white/80 hover:text-white transition-colors">Forgot password?</a>
                    </div>

                    <button type="submit" class="w-full bg-white text-primary py-3 rounded-xl font-semibold hover:bg-white/90 transition-all btn-hover text-sm sm:text-base">
                        Sign In
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-4 sm:my-6">
                <div class="flex-1 border-t border-white/20"></div>
                <span class="px-3 text-white/60 text-xs">or continue with</span>
                <div class="flex-1 border-t border-white/20"></div>
            </div>

            <!-- Social Login -->
            <div class="space-y-2">
                <!-- Google Sign In -->
                <button id="googleSignIn" class="w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white py-3 rounded-xl font-medium transition-all btn-hover flex items-center justify-center space-x-2 text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>

                <!-- GitHub Sign In -->
                <button class="w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white py-3 rounded-xl font-medium transition-all btn-hover flex items-center justify-center space-x-2 text-sm">
                    <i class="ri-github-fill"></i>
                    <span>Continue with GitHub</span>
                </button>
            </div>

            <!-- Switch to Sign Up -->
            <div class="text-center mt-6 pt-4 border-t border-white/20">
                <p class="text-white/80 text-sm">
                    Don't have an account? 
                    <a href="auth.php?mode=signup" class="text-white font-medium hover:underline transition-colors">Sign up</a>
                    here
                </p>
            </div>

            <?php else: ?>
            <!-- Sign Up Form -->
            <div class="mb-4">
                <h2 class="text-xl sm:text-2xl font-bold text-white text-center mb-1">Create Account</h2>
                <p class="text-white/70 text-center text-xs sm:text-sm">Join our premium marketplace</p>
            </div>
            
            <form method="POST">
                <input type="hidden" name="action" value="signup">
                
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-white/90 text-xs font-medium mb-1">First Name</label>
                            <input type="text" name="first_name" required 
                                   class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="First name">
                        </div>
                        <div>
                            <label class="block text-white/90 text-xs font-medium mb-1">Last Name</label>
                            <input type="text" name="last_name" required 
                                   class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Last name">
                        </div>
                    </div>

                    <div>
                        <label class="block text-white/90 text-xs font-medium mb-1">Email Address</label>
                        <div class="relative">
                            <i class="ri-mail-line absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60 text-sm"></i>
                            <input type="email" name="email" required 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Enter your email">
                        </div>
                    </div>

                    <div>
                        <label class="block text-white/90 text-xs font-medium mb-1">Password</label>
                        <div class="relative">
                            <i class="ri-lock-line absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60 text-sm"></i>
                            <input type="password" name="password" required 
                                   class="w-full pl-10 pr-10 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Create password">
                            <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/60 hover:text-white/80 toggle-password">
                                <i class="ri-eye-line text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-white/90 text-xs font-medium mb-1">Confirm Password</label>
                        <div class="relative">
                            <i class="ri-lock-line absolute left-3 top-1/2 transform -translate-y-1/2 text-white/60 text-sm"></i>
                            <input type="password" name="confirm_password" required 
                                   class="w-full pl-10 pr-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/60 focus:outline-none focus:border-white/40 input-focus text-sm"
                                   placeholder="Confirm password">
                        </div>
                    </div>

                    <div>
                        <label class="flex items-start text-white/80 text-xs leading-tight">
                            <input type="checkbox" name="terms" required class="mr-2 mt-0.5 rounded border-white/20 bg-white/10 w-3 h-3 flex-shrink-0">
                            <span>I agree to the <a href="#" class="text-white hover:underline">Terms</a> and <a href="#" class="text-white hover:underline">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-white text-primary py-2.5 rounded-xl font-semibold hover:bg-white/90 transition-all btn-hover text-sm">
                        Create Account
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="flex items-center my-3">
                <div class="flex-1 border-t border-white/20"></div>
                <span class="px-3 text-white/60 text-xs">or continue with</span>
                <div class="flex-1 border-t border-white/20"></div>
            </div>

            <!-- Social Login -->
            <div class="space-y-2">
                <!-- Google Sign In -->
                <button id="googleSignIn" class="w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white py-2.5 rounded-xl font-medium transition-all btn-hover flex items-center justify-center space-x-2 text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <span>Continue with Google</span>
                </button>

                <!-- GitHub Sign In -->
                <button class="w-full bg-white/10 hover:bg-white/20 border border-white/20 text-white py-2.5 rounded-xl font-medium transition-all btn-hover flex items-center justify-center space-x-2 text-sm">
                    <i class="ri-github-fill"></i>
                    <span>Continue with GitHub</span>
                </button>
            </div>

            <!-- Switch to Sign In -->
            <div class="text-center mt-4 pt-3 border-t border-white/20">
                <p class="text-white/80 text-xs">
                    Already have an account? 
                    <a href="auth.php?mode=signin" class="text-white font-medium hover:underline transition-colors">Sign in here</a>
                </p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ri-eye-line');
                    icon.classList.add('ri-eye-off-line');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ri-eye-off-line');
                    icon.classList.add('ri-eye-line');
                }
            });
        });

        // Initialize active tab style
        const signinTab = document.getElementById('signinTab');
        const signupTab = document.getElementById('signupTab');
        signinTab.classList.add('active', 'bg-white/20', 'text-white');
        signupTab.classList.add('text-white/60');

        // Google Sign In
        function handleCredentialResponse(response) {
            // Send the credential to your server
            fetch('../api/auth.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'google_signin',
                    credential: response.credential
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    alert('Login failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during login');
            });
        }

        // Initialize Google Sign In
        window.onload = function () {
            google.accounts.id.initialize({
                client_id: '1234567890-abcdefghijklmnopqrstuvwxyz123456.apps.googleusercontent.com', // Replace with your Google Client ID
                callback: handleCredentialResponse
            });

            // Custom Google Sign In button
            document.getElementById('googleSignIn').addEventListener('click', function() {
                google.accounts.id.prompt();
            });
        };

        // Form validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long!');
                return false;
            }
        });

        // Handle URL params for mode switching
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode');
        if (mode === 'signup') {
            // Show signup form
            document.getElementById('signupForm').style.display = 'block';
            document.getElementById('signinForm').style.display = 'none';
        }
    </script>
</body>
</html>
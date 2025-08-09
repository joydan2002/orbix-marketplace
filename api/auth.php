<?php
/**
 * Authentication Handler
 * Handles sign in, sign up, and Google OAuth authentication
 */

session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get JSON input for AJAX requests
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'signin':
            handleSignIn($pdo);
            break;
            
        case 'signup':
            handleSignUp($pdo);
            break;
            
        case 'google_signin':
            handleGoogleSignIn($pdo, $input['credential']);
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleSignIn($pdo) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }
    
    // Check user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid email or password');
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['user_type'] = $user['user_type'];
    
    // Update last login
    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);
    
    // Set remember me cookie if requested
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 days
        
        // Store token in database
        $stmt = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
        $stmt->execute([$token, $user['id']]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Login successful']);
}

function handleSignUp($pdo) {
    $firstName = $_POST['first_name'] ?? '';
    $lastName = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        throw new Exception('All fields are required');
    }
    
    if ($password !== $confirmPassword) {
        throw new Exception('Passwords do not match');
    }
    
    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email already registered');
    }
    
    // Create user
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, is_active, created_at) VALUES (?, ?, ?, ?, 'buyer', 1, NOW())");
    $stmt->execute([$firstName, $lastName, $email, $hashedPassword]);
    
    $userId = $pdo->lastInsertId();
    
    // Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
    $_SESSION['user_type'] = 'buyer';
    
    echo json_encode(['success' => true, 'message' => 'Account created successfully']);
}

function handleGoogleSignIn($pdo, $credential) {
    // Verify Google JWT token
    $payload = verifyGoogleToken($credential);
    
    if (!$payload) {
        throw new Exception('Invalid Google token');
    }
    
    $email = $payload['email'];
    $firstName = $payload['given_name'] ?? '';
    $lastName = $payload['family_name'] ?? '';
    $profileImage = $payload['picture'] ?? '';
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR google_id = ?");
    $stmt->execute([$email, $payload['sub']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE users SET google_id = ?, profile_image = ?, last_login = NOW() WHERE id = ?");
        $stmt->execute([$payload['sub'], $profileImage, $user['id']]);
        $userId = $user['id'];
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, google_id, profile_image, user_type, is_active, email_verified, created_at) VALUES (?, ?, ?, ?, ?, 'buyer', 1, 1, NOW())");
        $stmt->execute([$firstName, $lastName, $email, $payload['sub'], $profileImage]);
        $userId = $pdo->lastInsertId();
        $user = ['user_type' => 'buyer'];
    }
    
    // Create session
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
    $_SESSION['user_type'] = $user['user_type'];
    
    echo json_encode(['success' => true, 'message' => 'Google login successful']);
}

function handleLogout() {
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/');
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function verifyGoogleToken($token) {
    // Simple JWT decode (in production, use proper JWT library)
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return false;
    }
    
    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
    
    // In production, verify signature and issuer
    if (!$payload || !isset($payload['email'])) {
        return false;
    }
    
    return $payload;
}
?>
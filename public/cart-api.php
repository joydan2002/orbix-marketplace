<?php
/**
 * API Endpoints for Cart Operations
 * Handles AJAX requests for cart management with database
 */

require_once __DIR__ . '/../config/database.php';

// Start session for user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON response header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get the action from URL parameter
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'add':
            handleCartAdd($pdo);
            break;
            
        case 'remove':
            handleCartRemove($pdo);
            break;
            
        case 'get':
            handleCartGet($pdo);
            break;
            
        case 'clear':
            handleCartClear($pdo);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'error' => 'Invalid action'
            ]);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
}

// Handle cart add operation
function handleCartAdd($pdo) {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not authenticated',
            'redirect' => 'auth.php'
        ]);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $templateId = $_POST['template_id'] ?? 0;
    
    if (!$templateId) {
        echo json_encode([
            'success' => false,
            'error' => 'Template ID is required'
        ]);
        return;
    }
    
    // Check if template exists and get details - Fixed query to handle NULL seller_id
    $stmt = $pdo->prepare("
        SELECT 
            t.id, 
            t.title, 
            t.price, 
            t.preview_image, 
            COALESCE(u.first_name, 'Anonymous') as first_name, 
            COALESCE(u.last_name, 'Seller') as last_name
        FROM templates t 
        LEFT JOIN users u ON t.seller_id = u.id 
        WHERE t.id = ? AND t.status = 'approved'
    ");
    $stmt->execute([$templateId]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$template) {
        // Debug: Let's check what templates are actually available
        $stmt = $pdo->query("SELECT id, title, status FROM templates WHERE status = 'approved' LIMIT 5");
        $availableTemplates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $debugInfo = "Available templates: ";
        foreach ($availableTemplates as $t) {
            $debugInfo .= "ID:{$t['id']} ";
        }
        
        echo json_encode([
            'success' => false,
            'error' => 'Template not found',
            'debug' => $debugInfo,
            'requested_id' => $templateId
        ]);
        return;
    }
    
    // Check if item already in cart
    $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND template_id = ?");
    $stmt->execute([$userId, $templateId]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'error' => 'Template already in cart'
        ]);
        return;
    }
    
    // Add to cart
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, template_id, added_at) VALUES (?, ?, NOW())");
    $success = $stmt->execute([$userId, $templateId]);
    
    if ($success) {
        // Get updated cart count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode([
            'success' => true,
            'cart_count' => $cartCount,
            'template' => [
                'id' => $template['id'],
                'title' => $template['title'],
                'price' => $template['price'],
                'image' => $template['preview_image'],
                'seller' => $template['first_name'] . ' ' . $template['last_name']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to add template to cart'
        ]);
    }
}

// Handle cart remove operation
function handleCartRemove($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not authenticated'
        ]);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $templateId = $_POST['template_id'] ?? 0;
    
    if (!$templateId) {
        echo json_encode([
            'success' => false,
            'error' => 'Template ID is required'
        ]);
        return;
    }
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND template_id = ?");
    $success = $stmt->execute([$userId, $templateId]);
    
    if ($success) {
        // Get updated cart count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode([
            'success' => true,
            'cart_count' => $cartCount
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to remove template from cart'
        ]);
    }
}

// Handle get cart items
function handleCartGet($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not authenticated'
        ]);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("
        SELECT 
            c.template_id,
            t.title,
            t.price,
            t.preview_image,
            CONCAT(COALESCE(u.first_name, 'Anonymous'), ' ', COALESCE(u.last_name, 'Seller')) as seller_name,
            c.added_at
        FROM cart c
        JOIN templates t ON c.template_id = t.id
        LEFT JOIN users u ON t.seller_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total
    $total = array_sum(array_column($items, 'price'));
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'count' => count($items),
        'total' => $total
    ]);
}

// Handle clear cart
function handleCartClear($pdo) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'User not authenticated'
        ]);
        return;
    }
    
    $userId = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $success = $stmt->execute([$userId]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cart_count' => 0
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to clear cart'
        ]);
    }
}
?>
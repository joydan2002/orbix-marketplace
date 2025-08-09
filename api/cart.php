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
    $serviceId = $_POST['service_id'] ?? 0;
    
    // Check that exactly one of template_id or service_id is provided
    if (!$templateId && !$serviceId) {
        echo json_encode([
            'success' => false,
            'error' => 'Template ID or Service ID is required'
        ]);
        return;
    }
    
    if ($templateId && $serviceId) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot add both template and service in the same request'
        ]);
        return;
    }
    
    // Handle template addition
    if ($templateId) {
        // Check if template exists and get details
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
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            echo json_encode([
                'success' => false,
                'error' => 'Template not found'
            ]);
            return;
        }
        
        // Check if template already in cart
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND template_id = ?");
        $stmt->execute([$userId, $templateId]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'error' => 'Template already in cart'
            ]);
            return;
        }
        
        // Add template to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, template_id, added_at) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$userId, $templateId]);
        
        $itemType = 'template';
    }
    
    // Handle service addition
    if ($serviceId) {
        // Check if service exists and get details
        $stmt = $pdo->prepare("
            SELECT 
                s.id, 
                s.title, 
                s.price, 
                s.preview_image, 
                COALESCE(u.first_name, 'Anonymous') as first_name, 
                COALESCE(u.last_name, 'Seller') as last_name
            FROM services s 
            LEFT JOIN users u ON s.seller_id = u.id 
            WHERE s.id = ? AND s.status = 'approved'
        ");
        $stmt->execute([$serviceId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$item) {
            echo json_encode([
                'success' => false,
                'error' => 'Service not found'
            ]);
            return;
        }
        
        // Check if service already in cart
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND service_id = ?");
        $stmt->execute([$userId, $serviceId]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'error' => 'Service already in cart'
            ]);
            return;
        }
        
        // Add service to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, service_id, added_at) VALUES (?, ?, NOW())");
        $success = $stmt->execute([$userId, $serviceId]);
        
        $itemType = 'service';
    }
    
    if ($success) {
        // Get updated cart count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Prepare response data based on item type
        $responseItem = [
            'id' => $item['id'],
            'title' => $item['title'],
            'price' => $item['price'],
            'seller' => $item['first_name'] . ' ' . $item['last_name'],
            'type' => $itemType
        ];
        
        // Add type-specific image field
        if ($itemType === 'template') {
            $responseItem['image'] = $item['preview_image'];
        } else {
            $responseItem['image'] = $item['preview_image']; // services also use preview_image
        }
        
        echo json_encode([
            'success' => true,
            'cart_count' => $cartCount,
            'item' => $responseItem
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to add ' . $itemType . ' to cart'
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
    $serviceId = $_POST['service_id'] ?? 0;
    
    // Check that exactly one of template_id or service_id is provided
    if (!$templateId && !$serviceId) {
        echo json_encode([
            'success' => false,
            'error' => 'Template ID or Service ID is required'
        ]);
        return;
    }
    
    if ($templateId && $serviceId) {
        echo json_encode([
            'success' => false,
            'error' => 'Cannot remove both template and service in the same request'
        ]);
        return;
    }
    
    // Build query based on item type
    if ($templateId) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND template_id = ?");
        $success = $stmt->execute([$userId, $templateId]);
        $itemType = 'template';
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND service_id = ?");
        $success = $stmt->execute([$userId, $serviceId]);
        $itemType = 'service';
    }
    
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
            'error' => 'Failed to remove ' . $itemType . ' from cart'
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
    
    // Get both templates and services from cart
    $stmt = $pdo->prepare("
        SELECT 
            c.template_id,
            c.service_id,
            t.title as template_title,
            t.price as template_price,
            t.preview_image as template_image,
            s.title as service_title,
            s.price as service_price,
            s.preview_image as service_image,
            CONCAT(COALESCE(ut.first_name, us.first_name, 'Anonymous'), ' ', COALESCE(ut.last_name, us.last_name, 'Seller')) as seller_name,
            c.added_at,
            CASE 
                WHEN c.template_id IS NOT NULL THEN 'template'
                WHEN c.service_id IS NOT NULL THEN 'service'
            END as item_type
        FROM cart c
        LEFT JOIN templates t ON c.template_id = t.id
        LEFT JOIN services s ON c.service_id = s.id
        LEFT JOIN users ut ON t.seller_id = ut.id
        LEFT JOIN users us ON s.seller_id = us.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process the results to normalize the data
    $items = [];
    $total = 0;
    
    foreach ($cartItems as $cartItem) {
        if ($cartItem['item_type'] === 'template') {
            $item = [
                'id' => $cartItem['template_id'],
                'title' => $cartItem['template_title'],
                'price' => $cartItem['template_price'],
                'preview_image' => $cartItem['template_image'],
                'seller_name' => $cartItem['seller_name'],
                'added_at' => $cartItem['added_at'],
                'type' => 'template'
            ];
        } else {
            $item = [
                'id' => $cartItem['service_id'],
                'title' => $cartItem['service_title'],
                'price' => $cartItem['service_price'],
                'preview_image' => $cartItem['service_image'],
                'seller_name' => $cartItem['seller_name'],
                'added_at' => $cartItem['added_at'],
                'type' => 'service'
            ];
        }
        
        $items[] = $item;
        $total += $item['price'];
    }
    
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
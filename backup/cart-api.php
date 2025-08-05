<?php
/**
 * Cart API
 * Handle shopping cart operations
 */

require_once 'config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Get request action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    $pdo = DatabaseConfig::getConnection();
    
    switch ($action) {
        case 'get':
            // Get cart items for logged in user
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'items' => []]);
                break;
            }
            
            $stmt = $pdo->prepare("
                SELECT 
                    c.template_id,
                    t.title,
                    t.price,
                    t.preview_image,
                    CONCAT(u.first_name, ' ', u.last_name) as seller_name,
                    c.added_at
                FROM cart c
                JOIN templates t ON c.template_id = t.id
                JOIN users u ON t.seller_id = u.id
                WHERE c.user_id = ?
                ORDER BY c.added_at DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'items' => $items
            ]);
            break;
            
        case 'add':
            // Add item to cart
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([
                    'success' => false,
                    'error' => 'Not authenticated',
                    'redirect' => 'auth.php'
                ]);
                break;
            }
            
            $templateId = $_POST['template_id'] ?? 0;
            
            if (!$templateId) {
                throw new Exception('Template ID is required');
            }
            
            // Check if template exists and is approved
            $stmt = $pdo->prepare("SELECT id, title, price FROM templates WHERE id = ? AND status = 'approved'");
            $stmt->execute([$templateId]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$template) {
                throw new Exception('Template not found or not available');
            }
            
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND template_id = ?");
            $stmt->execute([$_SESSION['user_id'], $templateId]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item already in cart'
                ]);
                break;
            }
            
            // Add to cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, template_id) VALUES (?, ?)");
            $result = $stmt->execute([$_SESSION['user_id'], $templateId]);
            
            if (!$result) {
                throw new Exception('Failed to add item to cart');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Item added to cart'
            ]);
            break;
            
        case 'remove':
            // Remove item from cart
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $templateId = $_POST['template_id'] ?? 0;
            
            if (!$templateId) {
                throw new Exception('Template ID is required');
            }
            
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND template_id = ?");
            $result = $stmt->execute([$_SESSION['user_id'], $templateId]);
            
            if (!$result) {
                throw new Exception('Failed to remove item from cart');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
            break;
            
        case 'clear':
            // Clear all cart items
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $result = $stmt->execute([$_SESSION['user_id']]);
            
            if (!$result) {
                throw new Exception('Failed to clear cart');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Cart cleared'
            ]);
            break;
            
        case 'count':
            // Get cart item count
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => true, 'count' => 0]);
                break;
            }
            
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'count' => $result['count']
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
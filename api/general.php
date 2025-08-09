<?php
/**
 * API Endpoint for Orbix Market
 * Handles AJAX requests and returns JSON responses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/template-manager.php';
require_once __DIR__ . '/../config/cloudinary-config.php'; // Add Cloudinary support

// Start session for user authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $pdo = DatabaseConfig::getConnection();
    $action = $_GET['action'] ?? '';

    switch ($action) {
        case 'templates':
            getTemplates($pdo);
            break;
        
        case 'categories':
            getCategories($pdo);
            break;
            
        case 'services':
            getServices($pdo);
            break;
            
        case 'featured':
            getFeaturedTemplates($pdo);
            break;
            
        case 'stats':
            getStats($pdo);
            break;
            
        case 'domain-search':
            searchDomains($pdo);
            break;
            
        case 'domain-extensions':
            getDomainExtensions($pdo);
            break;
            
        case 'cart_add':
            handleCartAdd($pdo);
            break;
            
        case 'cart_remove':
            handleCartRemove($pdo);
            break;
            
        case 'cart_get':
            handleCartGet($pdo);
            break;
            
        case 'cart_clear':
            handleCartClear($pdo);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function getTemplates($pdo) {
    $categoryId = $_GET['category'] ?? null;
    $limit = intval($_GET['limit'] ?? 12);
    $offset = intval($_GET['offset'] ?? 0);
    $featured = $_GET['featured'] ?? null;
    
    $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug,
                   u.first_name, u.last_name, u.profile_image,
                   COALESCE(t.rating, 0) as rating,
                   COALESCE(t.reviews_count, 0) as reviews_count
            FROM templates t 
            LEFT JOIN categories c ON t.category_id = c.id 
            LEFT JOIN users u ON t.seller_id = u.id 
            WHERE t.status = 'approved' AND u.user_type = 'seller'"; // Add seller filter
    
    $params = [];
    
    if ($categoryId) {
        $sql .= " AND t.category_id = :category_id";
        $params['category_id'] = $categoryId;
    }
    
    if ($featured === '1') {
        $sql .= " AND t.is_featured = 1";
    }
    
    $sql .= " ORDER BY t.is_featured DESC, t.created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    foreach ($params as $key => $value) {
        $stmt->bindValue(':' . $key, $value);
    }
    
    $stmt->execute();
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process templates and optimize image URLs for Cloudinary
    foreach ($templates as &$template) {
        $template['tags'] = json_decode($template['tags'] ?? '[]', true);
        $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
        
        // Convert image URLs to optimized Cloudinary URLs
        $template['preview_image'] = getOptimizedImageUrl($template['preview_image'], 'thumb');
        if (!empty($template['profile_image'])) {
            $template['profile_image'] = getOptimizedImageUrl($template['profile_image'], 'avatar_small');
        }
    }
    
    echo json_encode(['success' => true, 'data' => $templates]);
}

function getFeaturedTemplates($pdo) {
    $limit = intval($_GET['limit'] ?? 4);
    
    $sql = "SELECT t.*, c.name as category_name,
                   u.first_name, u.last_name, u.profile_image
            FROM templates t 
            LEFT JOIN categories c ON t.category_id = c.id 
            LEFT JOIN users u ON t.seller_id = u.id 
            WHERE t.status = 'approved' AND t.is_featured = 1 AND u.user_type = 'seller'
            ORDER BY t.views_count DESC, t.created_at DESC 
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process templates and optimize image URLs for Cloudinary
    foreach ($templates as &$template) {
        $template['tags'] = json_decode($template['tags'] ?? '[]', true);
        $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
        
        // Convert image URLs to optimized Cloudinary URLs
        $template['preview_image'] = getOptimizedImageUrl($template['preview_image'], 'medium');
        if (!empty($template['profile_image'])) {
            $template['profile_image'] = getOptimizedImageUrl($template['profile_image'], 'avatar_small');
        }
    }
    
    echo json_encode(['success' => true, 'data' => $templates]);
}

function getCategories($pdo) {
    $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY name";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $categories]);
}

function getServices($pdo) {
    $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY id";
    $stmt = $pdo->query($sql);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $services]);
}

function getStats($pdo) {
    // Get template count from sellers only
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM templates t 
                         LEFT JOIN users u ON t.seller_id = u.id 
                         WHERE t.status = 'approved' AND u.user_type = 'seller'");
    $templateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get seller count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'seller' AND is_verified = 1");
    $sellerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total downloads from sellers' templates only
    $stmt = $pdo->query("SELECT SUM(t.downloads_count) as total FROM templates t 
                         LEFT JOIN users u ON t.seller_id = u.id 
                         WHERE t.status = 'approved' AND u.user_type = 'seller'");
    $totalDownloads = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $stats = [
        'templates' => number_format($templateCount),
        'sellers' => number_format($sellerCount),
        'downloads' => number_format($totalDownloads)
    ];
    
    echo json_encode(['success' => true, 'data' => $stats]);
}

function searchDomains($pdo) {
    $domainName = $_GET['domain'] ?? '';
    $extension = $_GET['extension'] ?? '.com';
    
    if (empty($domainName)) {
        echo json_encode(['success' => false, 'message' => 'Domain name is required']);
        return;
    }
    
    // Clean domain name (remove spaces, special chars)
    $domainName = strtolower(trim(preg_replace('/[^a-zA-Z0-9-]/', '', $domainName)));
    
    if (empty($domainName)) {
        echo json_encode(['success' => false, 'message' => 'Invalid domain name']);
        return;
    }
    
    // Check if specific domain+extension exists
    $sql = "SELECT * FROM domains WHERE name = :name AND extension = :extension";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $domainName);
    $stmt->bindValue(':extension', $extension);
    $stmt->execute();
    $exactMatch = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get all available extensions for this domain
    $sql = "SELECT de.extension, de.price, de.renewal_price,
                   CASE WHEN d.id IS NOT NULL THEN d.is_available ELSE TRUE END as is_available,
                   CASE WHEN d.id IS NOT NULL THEN d.is_premium ELSE FALSE END as is_premium,
                   CASE WHEN d.id IS NOT NULL THEN d.price ELSE de.price END as final_price
            FROM domain_extensions de
            LEFT JOIN domains d ON d.name = :name AND d.extension = de.extension
            WHERE de.is_active = 1
            ORDER BY de.sort_order";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':name', $domainName);
    $stmt->execute();
    $allExtensions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate suggestions if domain is taken
    $suggestions = [];
    if ($exactMatch && !$exactMatch['is_available']) {
        $suggestionWords = ['pro', 'hub', 'online', 'web', 'digital', 'app', 'site', 'world'];
        foreach ($suggestionWords as $word) {
            $suggestedName = $domainName . $word;
            $sql = "SELECT * FROM domains WHERE name = :name AND extension = :extension";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':name', $suggestedName);
            $stmt->bindValue(':extension', $extension);
            $stmt->execute();
            $suggestion = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$suggestion || $suggestion['is_available']) {
                $price = $suggestion ? $suggestion['price'] : 12.99;
                $suggestions[] = [
                    'name' => $suggestedName,
                    'extension' => $extension,
                    'price' => $price,
                    'is_available' => true,
                    'is_premium' => false
                ];
                if (count($suggestions) >= 4) break;
            }
        }
    }
    
    $response = [
        'success' => true,
        'domain_name' => $domainName,
        'searched_extension' => $extension,
        'exact_match' => $exactMatch,
        'all_extensions' => $allExtensions,
        'suggestions' => $suggestions
    ];
    
    echo json_encode($response);
}

function getDomainExtensions($pdo) {
    $sql = "SELECT * FROM domain_extensions WHERE is_active = 1 ORDER BY sort_order, extension";
    $stmt = $pdo->query($sql);
    $extensions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'data' => $extensions]);
}

// Handle cart add operation
function handleCartAdd($pdo) {
    // Check if user is logged in
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
    
    // Check if template exists
    $stmt = $pdo->prepare("SELECT id, title, price, preview_image FROM templates WHERE id = ? AND status = 'approved'");
    $stmt->execute([$templateId]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$template) {
        echo json_encode([
            'success' => false,
            'error' => 'Template not found'
        ]);
        return;
    }
    
    // Check if item already in cart
    $stmt = $pdo->prepare("SELECT id FROM cart_items WHERE user_id = ? AND template_id = ?");
    $stmt->execute([$userId, $templateId]);
    
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'error' => 'Template already in cart',
            'message' => 'This template is already in your cart!'
        ]);
        return;
    }
    
    // Add to cart
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, template_id, added_at) VALUES (?, ?, NOW())");
    $success = $stmt->execute([$userId, $templateId]);
    
    if ($success) {
        // Get updated cart count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Template added to cart successfully!',
            'cart_count' => $cartCount,
            'template' => $template
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
    
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND template_id = ?");
    $success = $stmt->execute([$userId, $templateId]);
    
    if ($success) {
        // Get updated cart count
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?");
        $stmt->execute([$userId]);
        $cartCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode([
            'success' => true,
            'message' => 'Template removed from cart',
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
            ci.template_id,
            t.title,
            t.price,
            t.preview_image,
            u.first_name,
            u.last_name,
            ci.added_at
        FROM cart_items ci
        JOIN templates t ON ci.template_id = t.id
        JOIN users u ON t.seller_id = u.id
        WHERE ci.user_id = ?
        ORDER BY ci.added_at DESC
    ");
    
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Optimize image URLs for cart items
    foreach ($items as &$item) {
        $item['preview_image'] = getOptimizedImageUrl($item['preview_image'], 'thumb');
        $item['seller_name'] = trim($item['first_name'] . ' ' . $item['last_name']);
    }
    
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
    
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
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
<?php
/**
 * API Endpoint for Orbix Market
 * Handles AJAX requests and returns JSON responses
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

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
            WHERE t.status = 'approved'";
    
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
    
    // Process tags from JSON
    foreach ($templates as &$template) {
        $template['tags'] = json_decode($template['tags'] ?? '[]', true);
        $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
    }
    
    echo json_encode(['success' => true, 'data' => $templates]);
}

function getFeaturedTemplates($pdo) {
    $limit = intval($_GET['limit'] ?? 4);
    
    $sql = "SELECT t.*, c.name as category_name,
                   u.first_name, u.last_name
            FROM templates t 
            LEFT JOIN categories c ON t.category_id = c.id 
            LEFT JOIN users u ON t.seller_id = u.id 
            WHERE t.status = 'approved' AND t.is_featured = 1
            ORDER BY t.views_count DESC, t.created_at DESC 
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($templates as &$template) {
        $template['tags'] = json_decode($template['tags'] ?? '[]', true);
        $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
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
    // Get template count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM templates WHERE status = 'approved'");
    $templateCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get seller count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'seller' AND is_verified = 1");
    $sellerCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total downloads (sum of all template downloads)
    $stmt = $pdo->query("SELECT SUM(downloads_count) as total FROM templates WHERE status = 'approved'");
    $totalDownloads = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $stats = [
        'templates' => number_format($templateCount),
        'sellers' => number_format($sellerCount),
        'downloads' => number_format($totalDownloads)
    ];
    
    echo json_encode(['success' => true, 'data' => $stats]);
}
?>
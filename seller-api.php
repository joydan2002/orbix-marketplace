<?php
/**
 * Enhanced Seller API - Professional E-commerce Backend
 * Handles all seller operations with advanced analytics and management
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/email-service.php';

// Enable CORS for API requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start session for authentication
session_start();

class SellerAPI {
    private $pdo;
    private $userId;
    
    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
        $this->userId = $_SESSION['user_id'] ?? null;
    }
    
    public function handleRequest() {
        if (!$this->userId) {
            return $this->jsonResponse(['error' => 'Authentication required'], 401);
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? $_GET['action'] ?? '';
        
        try {
            switch ($action) {
                case 'upgrade_to_seller':
                    return $this->upgradeToSeller();
                    
                case 'get_dashboard_stats':
                    return $this->getDashboardStats();
                    
                case 'get_analytics_data':
                    return $this->getAnalyticsData($input);
                    
                case 'upload_product':
                    return $this->uploadProduct($input);
                    
                case 'update_product':
                    return $this->updateProduct($input);
                    
                case 'delete_product':
                    return $this->deleteProduct($input);
                    
                case 'get_products':
                    return $this->getProducts($input);
                    
                case 'get_orders':
                    return $this->getOrders($input);
                    
                case 'get_earnings':
                    return $this->getEarnings($input);
                    
                case 'get_customers':
                    return $this->getCustomers($input);
                    
                case 'update_seller_profile':
                    return $this->updateSellerProfile($input);
                    
                case 'get_performance_metrics':
                    return $this->getPerformanceMetrics($input);
                    
                case 'generate_report':
                    return $this->generateReport($input);
                    
                default:
                    return $this->jsonResponse(['error' => 'Invalid action'], 400);
            }
        } catch (Exception $e) {
            error_log("Seller API Error: " . $e->getMessage());
            return $this->jsonResponse(['error' => 'Internal server error'], 500);
        }
    }
    
    private function upgradeToSeller() {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET user_type = 'seller', updated_at = NOW() WHERE id = ?");
            $result = $stmt->execute([$this->userId]);
            
            if ($result) {
                // Update session
                $_SESSION['user_type'] = 'seller';
                
                // Send welcome email
                $this->sendSellerWelcomeEmail();
                
                // Log the upgrade
                $this->logActivity('account_upgraded', 'User upgraded to seller account');
                
                return $this->jsonResponse([
                    'success' => true,
                    'message' => 'Successfully upgraded to seller account'
                ]);
            } else {
                return $this->jsonResponse(['error' => 'Failed to upgrade account'], 500);
            }
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
    
    private function getDashboardStats() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    COUNT(DISTINCT t.id) as total_templates,
                    COUNT(DISTINCT s.id) as total_services,
                    COUNT(DISTINCT CASE WHEN t.status = 'approved' THEN t.id END) as approved_templates,
                    COUNT(DISTINCT CASE WHEN t.status = 'pending' THEN t.id END) as pending_templates,
                    COUNT(DISTINCT CASE WHEN s.status = 'active' THEN s.id END) as active_services,
                    COALESCE(SUM(t.downloads_count), 0) as total_downloads,
                    COALESCE(SUM(s.orders_count), 0) as total_orders,
                    COALESCE(AVG(t.rating), 0) as avg_template_rating,
                    COALESCE(AVG(s.rating), 0) as avg_service_rating,
                    COALESCE(SUM(t.views_count), 0) as template_views,
                    COALESCE(SUM(s.views_count), 0) as service_views,
                    COALESCE(SUM(t.price * t.downloads_count * 0.7), 0) as template_earnings,
                    COALESCE(SUM(s.price * s.orders_count * 0.7), 0) as service_earnings
                FROM users u
                LEFT JOIN templates t ON u.id = t.seller_id
                LEFT JOIN services s ON u.id = s.seller_id
                WHERE u.id = ?
                GROUP BY u.id
            ");
            
            $stmt->execute([$this->userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$stats) {
                $stats = [
                    'total_templates' => 0, 'total_services' => 0, 'approved_templates' => 0,
                    'pending_templates' => 0, 'active_services' => 0, 'total_downloads' => 0,
                    'total_orders' => 0, 'avg_template_rating' => 0, 'avg_service_rating' => 0,
                    'template_views' => 0, 'service_views' => 0, 'template_earnings' => 0,
                    'service_earnings' => 0
                ];
            }
            
            // Calculate derived metrics
            $stats['total_products'] = $stats['total_templates'] + $stats['total_services'];
            $stats['total_sales'] = $stats['total_downloads'] + $stats['total_orders'];
            $stats['total_views'] = $stats['template_views'] + $stats['service_views'];
            $stats['total_earnings'] = $stats['template_earnings'] + $stats['service_earnings'];
            $stats['avg_rating'] = ($stats['avg_template_rating'] + $stats['avg_service_rating']) / 2;
            $stats['conversion_rate'] = $stats['total_views'] > 0 ? ($stats['total_sales'] / $stats['total_views']) * 100 : 0;
            
            // Get recent trends (30 days comparison)
            $trends = $this->getTrendData();
            $stats['trends'] = $trends;
            
            return $this->jsonResponse(['success' => true, 'data' => $stats]);
            
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => 'Failed to fetch dashboard stats'], 500);
        }
    }
    
    private function getAnalyticsData($params) {
        $period = $params['period'] ?? '30d';
        $type = $params['type'] ?? 'all';
        
        try {
            // Revenue over time
            $revenueData = $this->getRevenueAnalytics($period);
            
            // Top performing products
            $topProducts = $this->getTopProducts($period);
            
            // Traffic sources
            $trafficSources = $this->getTrafficSources($period);
            
            // Geographic data
            $geoData = $this->getGeographicData($period);
            
            // Conversion funnel
            $conversionFunnel = $this->getConversionFunnel($period);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'revenue' => $revenueData,
                    'top_products' => $topProducts,
                    'traffic_sources' => $trafficSources,
                    'geographic' => $geoData,
                    'conversion_funnel' => $conversionFunnel
                ]
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => 'Failed to fetch analytics data'], 500);
        }
    }
    
    private function uploadProduct($data) {
        $productType = $data['type'] ?? '';
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';
        $price = floatval($data['price'] ?? 0);
        $category = $data['category'] ?? '';
        $tags = $data['tags'] ?? [];
        $files = $data['files'] ?? [];
        
        if (empty($title) || empty($description) || $price <= 0) {
            return $this->jsonResponse(['error' => 'Missing required fields'], 400);
        }
        
        try {
            $this->pdo->beginTransaction();
            
            if ($productType === 'template') {
                $stmt = $this->pdo->prepare("
                    INSERT INTO templates (seller_id, title, description, price, category, tags, 
                                         preview_image, demo_url, download_url, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                ");
                $stmt->execute([
                    $this->userId, $title, $description, $price, $category,
                    json_encode($tags), $files['preview'] ?? '', $files['demo'] ?? '', 
                    $files['download'] ?? ''
                ]);
            } else if ($productType === 'service') {
                $stmt = $this->pdo->prepare("
                    INSERT INTO services (seller_id, title, description, price, category, tags,
                                        image, delivery_time, revisions, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
                ");
                $stmt->execute([
                    $this->userId, $title, $description, $price, $category,
                    json_encode($tags), $files['image'] ?? '', 
                    $data['delivery_time'] ?? 7, $data['revisions'] ?? 3
                ]);
            }
            
            $productId = $this->pdo->lastInsertId();
            $this->pdo->commit();
            
            // Log activity
            $this->logActivity('product_created', "Created new {$productType}: {$title}");
            
            return $this->jsonResponse([
                'success' => true,
                'message' => ucfirst($productType) . ' uploaded successfully',
                'product_id' => $productId
            ]);
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return $this->jsonResponse(['error' => 'Failed to upload product'], 500);
        }
    }
    
    private function getProducts($params) {
        $page = intval($params['page'] ?? 1);
        $limit = intval($params['limit'] ?? 20);
        $search = $params['search'] ?? '';
        $status = $params['status'] ?? '';
        $type = $params['type'] ?? '';
        
        $offset = ($page - 1) * $limit;
        
        try {
            $whereConditions = ['seller_id = ?'];
            $whereParams = [$this->userId];
            
            if (!empty($search)) {
                $whereConditions[] = 'title LIKE ?';
                $whereParams[] = "%{$search}%";
            }
            
            if (!empty($status)) {
                $whereConditions[] = 'status = ?';
                $whereParams[] = $status;
            }
            
            $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
            
            // Get templates and services separately then combine
            $products = [];
            
            if ($type === '' || $type === 'template') {
                $stmt = $this->pdo->prepare("
                    SELECT 'template' as type, id, title, description, price, category, 
                           preview_image as image, downloads_count as sales, views_count as views,
                           rating, status, created_at, updated_at
                    FROM templates {$whereClause}
                    ORDER BY created_at DESC
                    LIMIT {$limit} OFFSET {$offset}
                ");
                $stmt->execute($whereParams);
                $products = array_merge($products, $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            
            if ($type === '' || $type === 'service') {
                $stmt = $this->pdo->prepare("
                    SELECT 'service' as type, id, title, description, price, category,
                           image, orders_count as sales, views_count as views,
                           rating, status, created_at, updated_at
                    FROM services {$whereClause}
                    ORDER BY created_at DESC
                    LIMIT {$limit} OFFSET {$offset}
                ");
                $stmt->execute($whereParams);
                $products = array_merge($products, $stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            
            // Sort by created_at desc
            usort($products, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            return $this->jsonResponse([
                'success' => true,
                'data' => array_slice($products, 0, $limit),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => count($products)
                ]
            ]);
            
        } catch (Exception $e) {
            return $this->jsonResponse(['error' => 'Failed to fetch products'], 500);
        }
    }
    
    private function getRevenueAnalytics($period) {
        // Implementation for revenue analytics over time
        $days = $this->periodToDays($period);
        
        $stmt = $this->pdo->prepare("
            SELECT 
                DATE(created_at) as date,
                SUM(CASE WHEN 'templates' THEN price * downloads_count * 0.7 ELSE 0 END) as template_revenue,
                SUM(CASE WHEN 'services' THEN price * orders_count * 0.7 ELSE 0 END) as service_revenue
            FROM (
                SELECT created_at, price, downloads_count, 0 as orders_count FROM templates 
                WHERE seller_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                UNION ALL
                SELECT created_at, price, 0 as downloads_count, orders_count FROM services 
                WHERE seller_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ) combined
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        $stmt->execute([$this->userId, $days, $this->userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTrendData() {
        // Get 30-day trend comparison
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) 
                         THEN (price * downloads_count * 0.7) ELSE 0 END) as current_month_earnings,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) 
                              AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
                         THEN (price * downloads_count * 0.7) ELSE 0 END) as previous_month_earnings,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as current_month_products,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) 
                               AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as previous_month_products
                FROM (
                    SELECT created_at, price, downloads_count FROM templates WHERE seller_id = ?
                    UNION ALL
                    SELECT created_at, price, orders_count as downloads_count FROM services WHERE seller_id = ?
                ) combined
            ");
            
            $stmt->execute([$this->userId, $this->userId]);
            $trends = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate percentage changes
            $earningsChange = 0;
            $productsChange = 0;
            
            if ($trends['previous_month_earnings'] > 0) {
                $earningsChange = (($trends['current_month_earnings'] - $trends['previous_month_earnings']) 
                                 / $trends['previous_month_earnings']) * 100;
            }
            
            if ($trends['previous_month_products'] > 0) {
                $productsChange = (($trends['current_month_products'] - $trends['previous_month_products']) 
                                 / $trends['previous_month_products']) * 100;
            }
            
            return [
                'earnings_change' => round($earningsChange, 1),
                'products_change' => round($productsChange, 1)
            ];
            
        } catch (Exception $e) {
            return ['earnings_change' => 0, 'products_change' => 0];
        }
    }
    
    private function periodToDays($period) {
        switch ($period) {
            case '7d': return 7;
            case '30d': return 30;
            case '90d': return 90;
            case '1y': return 365;
            default: return 30;
        }
    }
    
    private function logActivity($action, $description) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO seller_activity_log (seller_id, action, description, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$this->userId, $action, $description]);
        } catch (Exception $e) {
            // Log error but don't fail the main operation
            error_log("Failed to log seller activity: " . $e->getMessage());
        }
    }
    
    private function sendSellerWelcomeEmail() {
        // Implementation for sending welcome email to new sellers
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$this->userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Send welcome email using EmailService
                // This would integrate with the existing email service
            }
        } catch (Exception $e) {
            error_log("Failed to send seller welcome email: " . $e->getMessage());
        }
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}

// Initialize and handle the request
$api = new SellerAPI();
$api->handleRequest();
?>
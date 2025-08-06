<?php
/**
 * Seller Data Loader
 * Loads all data needed for seller dashboard sections
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-config.php'; // Add Cloudinary support

// Check if user is logged in as seller
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$seller_id = $_SESSION['user_id'];

try {
    $pdo = DatabaseConfig::getConnection();
    
    // Get seller profile
    $stmt = $pdo->prepare("
        SELECT u.*, sp.business_name, sp.description, sp.location, sp.phone, 
               sp.business_type, sp.total_sales, sp.total_orders, sp.response_time, 
               sp.account_balance
        FROM users u 
        LEFT JOIN seller_profiles sp ON u.id = sp.user_id 
        WHERE u.id = ?
    ");
    $stmt->execute([$seller_id]);
    $sellerProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get templates
    $stmt = $pdo->prepare("
        SELECT * FROM templates 
        WHERE seller_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get services - FIX: Use 'services' table instead of 'seller_services'
    $stmt = $pdo->prepare("
        SELECT * FROM services 
        WHERE seller_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get template orders
    $stmt = $pdo->prepare("
        SELECT o.*, oi.template_id, t.title as template_title, u.first_name, u.last_name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN templates t ON oi.template_id = t.id
        JOIN users u ON o.user_id = u.id
        WHERE t.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $templateOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get service orders - FIX: Use 'services' table
    $stmt = $pdo->prepare("
        SELECT so.*, s.title as service_title, u.first_name, u.last_name
        FROM service_orders so
        JOIN services s ON so.service_id = s.id
        JOIN users u ON so.buyer_id = u.id
        WHERE so.seller_id = ?
        ORDER BY so.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $serviceOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get template reviews
    $stmt = $pdo->prepare("
        SELECT r.*, t.title as template_title, u.first_name, u.last_name
        FROM reviews r
        JOIN templates t ON r.template_id = t.id
        JOIN users u ON r.user_id = u.id
        WHERE t.seller_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $templateReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get service reviews - FIX: Use 'services' table  
    $stmt = $pdo->prepare("
        SELECT sr.*, s.title as service_title, u.first_name, u.last_name
        FROM service_reviews sr
        JOIN services s ON sr.service_id = s.id
        JOIN users u ON sr.user_id = u.id
        WHERE s.seller_id = ?
        ORDER BY sr.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $serviceReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get messages
    $stmt = $pdo->prepare("
        SELECT m.*, u.first_name, u.last_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $sellerMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get earnings
    $stmt = $pdo->prepare("
        SELECT * FROM seller_earnings 
        WHERE seller_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get analytics
    $stmt = $pdo->prepare("
        SELECT * FROM seller_analytics 
        WHERE seller_id = ? 
        ORDER BY date DESC
        LIMIT 30
    ");
    $stmt->execute([$seller_id]);
    $analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get withdrawal requests
    $stmt = $pdo->prepare("
        SELECT * FROM withdrawal_requests 
        WHERE seller_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate stats
    $totalTemplates = count($templates);
    $totalServices = count($services);
    $totalOrders = count($templateOrders) + count($serviceOrders);
    $allReviews = array_merge($templateReviews, $serviceReviews);
    $totalReviews = count($allReviews);
    
    // Calculate earnings stats
    $totalEarnings = array_sum(array_column($earnings, 'net_amount'));
    $availableBalance = array_sum(array_column(array_filter($earnings, function($e) { 
        return $e['status'] === 'available'; 
    }), 'net_amount'));
    
    // Calculate review stats
    $reviewStats = [
        'total' => $totalReviews,
        'average_rating' => $totalReviews > 0 ? array_sum(array_column($allReviews, 'rating')) / $totalReviews : 0,
        'five_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 5; })),
        'four_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 4; })),
        'three_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 3; })),
        'two_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 2; })),
        'one_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 1; }))
    ];
    
    // Template stats
    $templateStats = [
        'active' => count(array_filter($templates, function($t) { return $t['status'] === 'approved'; })),
        'pending' => count(array_filter($templates, function($t) { return $t['status'] === 'pending'; })),
        'draft' => count(array_filter($templates, function($t) { return $t['status'] === 'draft'; }))
    ];
    
    // Service stats - FIX: Use correct status values from 'services' table
    $serviceStats = [
        'total' => count($services),
        'active' => count(array_filter($services, function($s) { return $s['status'] === 'approved'; })),
        'pending' => count(array_filter($services, function($s) { return $s['status'] === 'pending'; })),
        'draft' => count(array_filter($services, function($s) { return $s['status'] === 'draft'; })),
        'total_orders' => array_sum(array_column($services, 'orders_count')),
        'total_revenue' => array_sum(array_map(function($s) { return $s['price'] * $s['orders_count']; }, $services))
    ];
    
    // Recent activity
    $recentActivity = [];
    foreach ($templateOrders as $order) {
        $recentActivity[] = [
            'type' => 'order',
            'title' => 'New Template Order',
            'description' => $order['template_title'],
            'amount' => $order['total_amount'],
            'date' => $order['created_at']
        ];
    }
    foreach ($serviceOrders as $order) {
        $recentActivity[] = [
            'type' => 'order', 
            'title' => 'New Service Order',
            'description' => $order['service_title'],
            'amount' => $order['total_amount'],
            'date' => $order['created_at']
        ];
    }
    foreach ($allReviews as $review) {
        $recentActivity[] = [
            'type' => 'review',
            'title' => 'New Review',
            'description' => ($review['template_title'] ?? $review['service_title']) . ' - ' . $review['rating'] . ' stars',
            'amount' => null,
            'date' => $review['created_at']
        ];
    }
    
    // Sort recent activity by date
    usort($recentActivity, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    // Monthly earnings for chart
    $monthlyEarnings = [];
    foreach ($analytics as $analytic) {
        $month = date('Y-m', strtotime($analytic['date']));
        if (!isset($monthlyEarnings[$month])) {
            $monthlyEarnings[$month] = 0;
        }
        $monthlyEarnings[$month] += $analytic['revenue'];
    }
    
    // Count unread messages
    $unreadMessages = count(array_filter($sellerMessages, function($m) { 
        return !$m['is_read']; 
    }));
    
} catch (Exception $e) {
    error_log("Seller data loader error: " . $e->getMessage());
    // Set default values if database fails
    $sellerProfile = ['first_name' => 'Seller', 'last_name' => ''];
    $templates = [];
    $services = [];
    $templateOrders = [];
    $serviceOrders = [];
    $allReviews = [];
    $sellerMessages = [];
    $earnings = [];
    $analytics = [];
    $withdrawals = [];
    $totalEarnings = 0;
    $availableBalance = 0;
    $totalOrders = 0;
    $totalReviews = 0;
    $totalTemplates = 0;
    $totalServices = 0;
    $reviewStats = ['total' => 0, 'average_rating' => 0, 'five_star' => 0, 'four_star' => 0, 'three_star' => 0, 'two_star' => 0, 'one_star' => 0];
    $templateStats = ['active' => 0, 'pending' => 0, 'draft' => 0];
    $serviceStats = ['active' => 0, 'pending' => 0, 'draft' => 0];
    $recentActivity = [];
    $monthlyEarnings = [];
    $unreadMessages = 0;
}
?>
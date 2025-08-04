<?php
/**
 * Seller Dashboard Data Initialization
 * Fetches all real data from database for seller sections
 */

// Check if user is logged in and is a seller
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'seller') {
    header('Location: auth.php');
    exit();
}

require_once '../config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $seller_id = $_SESSION['user_id'];
    
    // Get seller profile data
    $stmt = $pdo->prepare("
        SELECT sp.*, u.first_name, u.last_name, u.email, u.profile_image, u.created_at as user_created_at
        FROM seller_profiles sp 
        LEFT JOIN users u ON sp.user_id = u.id 
        WHERE sp.user_id = ?
    ");
    $stmt->execute([$seller_id]);
    $sellerProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // If no seller profile exists, create a basic one
    if (!$sellerProfile) {
        $stmt = $pdo->prepare("
            INSERT INTO seller_profiles (user_id, business_name, total_sales, total_orders, account_balance, created_at) 
            VALUES (?, '', 0.00, 0, 0.00, NOW())
        ");
        $stmt->execute([$seller_id]);
        
        // Re-fetch the profile
        $stmt = $pdo->prepare("
            SELECT sp.*, u.first_name, u.last_name, u.email, u.profile_image, u.created_at as user_created_at
            FROM seller_profiles sp 
            LEFT JOIN users u ON sp.user_id = u.id 
            WHERE sp.user_id = ?
        ");
        $stmt->execute([$seller_id]);
        $sellerProfile = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get seller templates
    $stmt = $pdo->prepare("
        SELECT t.*, c.name as category_name
        FROM templates t
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.seller_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $sellerTemplates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get seller services from seller_services table
    $stmt = $pdo->prepare("
        SELECT ss.*, sc.name as category_name
        FROM seller_services ss
        LEFT JOIN service_categories sc ON ss.category_id = sc.id
        WHERE ss.seller_id = ?
        ORDER BY ss.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $sellerServices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get template orders
    $stmt = $pdo->prepare("
        SELECT o.*, oi.template_id, t.title as template_title, u.first_name, u.last_name, u.email as buyer_email
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN templates t ON oi.template_id = t.id
        JOIN users u ON o.user_id = u.id
        WHERE t.seller_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $templateOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get service orders
    $stmt = $pdo->prepare("
        SELECT so.*, ss.title as service_title, u.first_name, u.last_name, u.email as buyer_email
        FROM service_orders so
        JOIN seller_services ss ON so.service_id = ss.id
        JOIN users u ON so.buyer_id = u.id
        WHERE so.seller_id = ?
        ORDER BY so.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $serviceOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Combine all orders
    $allOrders = array_merge($templateOrders, $serviceOrders);
    usort($allOrders, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
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
    
    // Get service reviews
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
    
    // Combine all reviews
    $allReviews = array_merge($templateReviews, $serviceReviews);
    usort($allReviews, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Get messages
    $stmt = $pdo->prepare("
        SELECT m.*, u.first_name, u.last_name, u.profile_image
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE m.receiver_id = ?
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $sellerMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get earnings
    $stmt = $pdo->prepare("
        SELECT se.*, t.title as template_title
        FROM seller_earnings se
        LEFT JOIN templates t ON se.template_id = t.id
        WHERE se.seller_id = ?
        ORDER BY se.created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $sellerEarnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get analytics data
    $stmt = $pdo->prepare("
        SELECT * FROM seller_analytics
        WHERE seller_id = ?
        ORDER BY date DESC
        LIMIT 30
    ");
    $stmt->execute([$seller_id]);
    $analyticsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get withdrawal requests
    $stmt = $pdo->prepare("
        SELECT * FROM withdrawal_requests
        WHERE seller_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$seller_id]);
    $withdrawalRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate statistics
    $totalTemplates = count($sellerTemplates);
    $totalServices = count($sellerServices);
    $totalOrders = count($allOrders);
    $totalReviews = count($allReviews);
    $totalMessages = count($sellerMessages);
    $unreadMessages = count(array_filter($sellerMessages, function($msg) { return !$msg['is_read']; }));
    
    // Template stats
    $templateStats = [
        'total' => $totalTemplates,
        'active' => count(array_filter($sellerTemplates, function($t) { return $t['status'] === 'approved'; })),
        'pending' => count(array_filter($sellerTemplates, function($t) { return $t['status'] === 'pending'; })),
        'total_downloads' => array_sum(array_column($sellerTemplates, 'downloads_count')),
        'total_revenue' => array_sum(array_column($templateOrders, 'total_amount'))
    ];
    
    // Service stats
    $serviceStats = [
        'total' => $totalServices,
        'active' => count(array_filter($sellerServices, function($s) { return $s['status'] === 'active'; })),
        'pending' => count(array_filter($sellerServices, function($s) { return $s['status'] === 'pending'; })),
        'total_orders' => count($serviceOrders),
        'total_revenue' => array_sum(array_column($serviceOrders, 'total_amount'))
    ];
    
    // Order stats
    $orderStats = [
        'total' => $totalOrders,
        'pending' => count(array_filter($allOrders, function($o) { return $o['status'] === 'pending'; })),
        'completed' => count(array_filter($allOrders, function($o) { return $o['status'] === 'completed'; })),
        'cancelled' => count(array_filter($allOrders, function($o) { return $o['status'] === 'cancelled'; }))
    ];
    
    // Review stats
    $reviewStats = [
        'total' => $totalReviews,
        'average_rating' => $totalReviews > 0 ? array_sum(array_column($allReviews, 'rating')) / $totalReviews : 0,
        'five_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 5; })),
        'four_star' => count(array_filter($allReviews, function($r) { return $r['rating'] == 4; }))
    ];
    
    // Earnings stats
    $totalEarnings = array_sum(array_column($sellerEarnings, 'net_amount'));
    $availableBalance = array_sum(array_column(array_filter($sellerEarnings, function($e) { return $e['status'] === 'available'; }), 'net_amount'));
    $pendingEarnings = array_sum(array_column(array_filter($sellerEarnings, function($e) { return $e['status'] === 'pending'; }), 'net_amount'));
    
    // Monthly earnings for chart
    $monthlyEarnings = [];
    for ($i = 6; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthlyEarnings[$month] = 0;
    }
    
    foreach ($sellerEarnings as $earning) {
        $month = date('Y-m', strtotime($earning['created_at']));
        if (isset($monthlyEarnings[$month])) {
            $monthlyEarnings[$month] += $earning['net_amount'];
        }
    }
    
    // Recent activity (last 10 actions)
    $recentActivity = [];
    
    // Add recent orders
    foreach (array_slice($allOrders, 0, 5) as $order) {
        $recentActivity[] = [
            'type' => 'order',
            'title' => isset($order['template_title']) ? $order['template_title'] : $order['service_title'],
            'description' => 'New order received',
            'amount' => $order['total_amount'],
            'date' => $order['created_at'],
            'status' => $order['status']
        ];
    }
    
    // Add recent reviews
    foreach (array_slice($allReviews, 0, 3) as $review) {
        $recentActivity[] = [
            'type' => 'review',
            'title' => isset($review['template_title']) ? $review['template_title'] : $review['service_title'],
            'description' => $review['rating'] . ' star review received',
            'amount' => null,
            'date' => $review['created_at'],
            'status' => 'completed'
        ];
    }
    
    // Sort recent activity by date
    usort($recentActivity, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    $recentActivity = array_slice($recentActivity, 0, 10);
    
    // Get categories for filters
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name");
    $stmt->execute();
    $templateCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("SELECT * FROM service_categories ORDER BY name");
    $stmt->execute();
    $serviceCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Seller init error: " . $e->getMessage());
    
    // Set default values if database error occurs
    $sellerProfile = [
        'user_id' => $seller_id,
        'business_name' => 'My Business',
        'description' => '',
        'total_sales' => 0,
        'total_orders' => 0,
        'account_balance' => 0,
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? ''
    ];
    
    $sellerTemplates = [];
    $sellerServices = [];
    $allOrders = [];
    $allReviews = [];
    $sellerMessages = [];
    $sellerEarnings = [];
    $analyticsData = [];
    $withdrawalRequests = [];
    $templateCategories = [];
    $serviceCategories = [];
    $recentActivity = [];
    $monthlyEarnings = [];
    
    $templateStats = ['total' => 0, 'active' => 0, 'pending' => 0, 'total_downloads' => 0, 'total_revenue' => 0];
    $serviceStats = ['total' => 0, 'active' => 0, 'pending' => 0, 'total_orders' => 0, 'total_revenue' => 0];
    $orderStats = ['total' => 0, 'pending' => 0, 'completed' => 0, 'cancelled' => 0];
    $reviewStats = ['total' => 0, 'average_rating' => 0, 'five_star' => 0, 'four_star' => 0];
    
    $totalEarnings = 0;
    $availableBalance = 0;
    $pendingEarnings = 0;
    $unreadMessages = 0;
}
?>
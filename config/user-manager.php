<?php
/**
 * User Manager Class
 * Handles all user-related operations and statistics for both buyers and sellers
 */

class UserManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get comprehensive user statistics based on user type
     */
    public function getUserStats($userId, $userType = null) {
        try {
            // Get basic user info if userType not provided
            if (!$userType) {
                $stmt = $this->pdo->prepare("SELECT user_type FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $userType = $user['user_type'] ?? 'buyer';
            }
            
            // Initialize default stats
            $stats = [
                'total_templates' => 0,
                'total_services' => 0,
                'total_orders' => 0,
                'total_downloads' => 0,
                'avg_rating' => 0,
                'total_reviews' => 0,
                'total_favorites' => 0,
                'total_earnings' => 0,
                'total_views' => 0
            ];
            
            if ($userType === 'seller') {
                return $this->getSellerStats($userId);
            } else {
                return $this->getBuyerStats($userId);
            }
            
        } catch (Exception $e) {
            error_log("User stats error: " . $e->getMessage());
            return $this->getDefaultStats();
        }
    }
    
    /**
     * Get seller statistics with comprehensive data
     */
    private function getSellerStats($sellerId) {
        try {
            // Single optimized query for all seller stats
            $stmt = $this->pdo->prepare("
                SELECT 
                    -- Templates stats
                    COUNT(DISTINCT t.id) as total_templates,
                    COUNT(DISTINCT CASE WHEN t.status = 'approved' THEN t.id END) as approved_templates,
                    COALESCE(SUM(DISTINCT t.downloads_count), 0) as template_downloads,
                    COALESCE(SUM(DISTINCT t.views_count), 0) as template_views,
                    
                    -- Services stats  
                    COUNT(DISTINCT s.id) as total_services,
                    COUNT(DISTINCT CASE WHEN s.status = 'active' THEN s.id END) as active_services,
                    COALESCE(SUM(DISTINCT s.orders_count), 0) as service_orders,
                    COALESCE(SUM(DISTINCT s.views_count), 0) as service_views,
                    
                    -- Orders stats
                    COUNT(DISTINCT o.id) as total_orders,
                    
                    -- Reviews stats
                    COUNT(DISTINCT tr.id) as template_reviews,
                    COUNT(DISTINCT sr.id) as service_reviews,
                    COALESCE(AVG(tr.rating), 0) as template_rating,
                    COALESCE(AVG(sr.rating), 0) as service_rating,
                    
                    -- Favorites stats
                    COUNT(DISTINCT tf.id) as template_favorites,
                    COUNT(DISTINCT sf.id) as service_favorites
                    
                FROM users u
                LEFT JOIN templates t ON u.id = t.seller_id
                LEFT JOIN services s ON u.id = s.seller_id
                LEFT JOIN orders o ON o.user_id = u.id
                LEFT JOIN reviews tr ON tr.template_id = t.id
                LEFT JOIN reviews sr ON sr.service_id = s.id  
                LEFT JOIN favorites tf ON tf.template_id = t.id
                LEFT JOIN favorites sf ON sf.service_id = s.id
                WHERE u.id = ?
                GROUP BY u.id
            ");
            
            $stmt->execute([$sellerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Calculate combined stats
                $stats = [
                    'total_templates' => intval($result['total_templates'] ?? 0),
                    'total_services' => intval($result['total_services'] ?? 0),
                    'total_orders' => intval($result['total_orders'] ?? 0),
                    'total_downloads' => intval($result['template_downloads'] ?? 0),
                    'total_reviews' => intval($result['template_reviews'] ?? 0) + intval($result['service_reviews'] ?? 0),
                    'total_favorites' => intval($result['template_favorites'] ?? 0) + intval($result['service_favorites'] ?? 0),
                    'total_views' => intval($result['template_views'] ?? 0) + intval($result['service_views'] ?? 0),
                    'avg_rating' => 0,
                    'total_earnings' => 0
                ];
                
                // Calculate average rating
                $templateRating = floatval($result['template_rating'] ?? 0);
                $serviceRating = floatval($result['service_rating'] ?? 0);
                $templateReviews = intval($result['template_reviews'] ?? 0);
                $serviceReviews = intval($result['service_reviews'] ?? 0);
                
                if ($templateReviews > 0 && $serviceReviews > 0) {
                    $stats['avg_rating'] = ($templateRating + $serviceRating) / 2;
                } elseif ($templateReviews > 0) {
                    $stats['avg_rating'] = $templateRating;
                } elseif ($serviceReviews > 0) {
                    $stats['avg_rating'] = $serviceRating;
                }
                
                return $stats;
            }
            
        } catch (Exception $e) {
            error_log("Seller stats error: " . $e->getMessage());
        }
        
        return $this->getDefaultStats();
    }
    
    /**
     * Get buyer statistics
     */
    private function getBuyerStats($buyerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    -- Orders stats
                    COUNT(DISTINCT o.id) as total_orders,
                    
                    -- Downloads from purchased templates
                    COALESCE(SUM(oi.quantity), 0) as total_downloads,
                    
                    -- Reviews given
                    COUNT(DISTINCT r.id) as total_reviews,
                    COALESCE(AVG(r.rating), 0) as avg_rating_given,
                    
                    -- Favorites
                    COUNT(DISTINCT f.id) as total_favorites
                    
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                LEFT JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN reviews r ON r.user_id = u.id
                LEFT JOIN favorites f ON f.user_id = u.id
                WHERE u.id = ?
                GROUP BY u.id
            ");
            
            $stmt->execute([$buyerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'total_templates' => 0, // Buyers don't create templates
                    'total_services' => 0,  // Buyers don't create services
                    'total_orders' => intval($result['total_orders'] ?? 0),
                    'total_downloads' => intval($result['total_downloads'] ?? 0),
                    'avg_rating' => floatval($result['avg_rating_given'] ?? 0),
                    'total_reviews' => intval($result['total_reviews'] ?? 0),
                    'total_favorites' => intval($result['total_favorites'] ?? 0),
                    'total_earnings' => 0,  // Buyers don't earn
                    'total_views' => 0      // Not tracked for buyers
                ];
            }
            
        } catch (Exception $e) {
            error_log("Buyer stats error: " . $e->getMessage());
        }
        
        return $this->getDefaultStats();
    }
    
    /**
     * Get user's recent activity
     */
    public function getRecentActivity($userId, $userType, $limit = 5) {
        try {
            if ($userType === 'seller') {
                return $this->getSellerActivity($userId, $limit);
            } else {
                return $this->getBuyerActivity($userId, $limit);
            }
        } catch (Exception $e) {
            error_log("Recent activity error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get seller's recent activity
     */
    private function getSellerActivity($sellerId, $limit) {
        $stmt = $this->pdo->prepare("
            SELECT 'order' as type, o.order_number as title, o.total_amount as amount, 
                   o.status, o.created_at, CONCAT(u.first_name, ' ', u.last_name) as customer_name
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN templates t ON oi.template_id = t.id
            JOIN users u ON o.user_id = u.id
            WHERE t.seller_id = ?
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$sellerId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get buyer's recent activity
     */
    private function getBuyerActivity($buyerId, $limit) {
        $stmt = $this->pdo->prepare("
            SELECT 'purchase' as type, t.title, o.total_amount as amount,
                   o.status, o.created_at, o.order_number
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN templates t ON oi.template_id = t.id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$buyerId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get default stats structure
     */
    private function getDefaultStats() {
        return [
            'total_templates' => 0,
            'total_services' => 0,
            'total_orders' => 0,
            'total_downloads' => 0,
            'avg_rating' => 0,
            'total_reviews' => 0,
            'total_favorites' => 0,
            'total_earnings' => 0,
            'total_views' => 0
        ];
    }
    
    /**
     * Safe number formatting helper
     */
    public static function safeNumberFormat($value, $decimals = 0) {
        return number_format(floatval($value ?? 0), $decimals);
    }
}

<?php
/**
 * Seller Manager Class
 * Handles all seller-related operations and business logic
 */

class SellerManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Get seller statistics
     */
    public function getSellerStats($sellerId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    -- Templates stats
                    COUNT(DISTINCT t.id) as total_templates,
                    COUNT(DISTINCT CASE WHEN t.status = 'approved' THEN t.id END) as approved_templates,
                    COUNT(DISTINCT CASE WHEN t.status = 'pending' THEN t.id END) as pending_templates,
                    COALESCE(SUM(t.downloads_count), 0) as template_downloads,
                    COALESCE(AVG(t.rating), 0) as template_rating,
                    COALESCE(SUM(t.views_count), 0) as template_views,
                    
                    -- Services stats
                    COUNT(DISTINCT s.id) as total_services,
                    COUNT(DISTINCT CASE WHEN s.status = 'active' THEN s.id END) as active_services,
                    COALESCE(SUM(s.orders_count), 0) as service_orders,
                    COALESCE(AVG(s.rating), 0) as service_rating,
                    COALESCE(SUM(s.views_count), 0) as service_views,
                    
                    -- Financial stats (70% commission)
                    COALESCE(SUM(t.price * t.downloads_count * 0.7), 0) as template_earnings,
                    COALESCE(SUM(s.price * s.orders_count * 0.7), 0) as service_earnings
                FROM users u
                LEFT JOIN templates t ON u.id = t.seller_id
                LEFT JOIN services s ON u.id = s.seller_id
                WHERE u.id = ?
                GROUP BY u.id
            ");
            $stmt->execute([$sellerId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($stats) {
                // Calculate totals
                $stats['total_products'] = $stats['total_templates'] + $stats['total_services'];
                $stats['total_earnings'] = $stats['template_earnings'] + $stats['service_earnings'];
                $stats['total_orders'] = $stats['template_downloads'] + $stats['service_orders'];
                $stats['avg_rating'] = ($stats['template_rating'] + $stats['service_rating']) / 2;
            }
            
            return $stats ?: [
                'total_templates' => 0, 'approved_templates' => 0, 'pending_templates' => 0,
                'total_services' => 0, 'active_services' => 0,
                'total_products' => 0, 'total_earnings' => 0, 'total_orders' => 0,
                'template_downloads' => 0, 'service_orders' => 0, 'avg_rating' => 0,
                'template_views' => 0, 'service_views' => 0
            ];
        } catch (Exception $e) {
            error_log("Seller stats error: " . $e->getMessage());
            return [
                'total_templates' => 0, 'approved_templates' => 0, 'pending_templates' => 0,
                'total_services' => 0, 'active_services' => 0,
                'total_products' => 0, 'total_earnings' => 0, 'total_orders' => 0,
                'template_downloads' => 0, 'service_orders' => 0, 'avg_rating' => 0,
                'template_views' => 0, 'service_views' => 0
            ];
        }
    }
    
    /**
     * Get monthly earnings for charts
     */
    public function getMonthlyEarnings($sellerId, $months = 12) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    SUM(amount * 0.7) as earnings,
                    COUNT(*) as orders
                FROM (
                    SELECT created_at, price as amount FROM templates WHERE seller_id = ? AND status = 'approved'
                    UNION ALL
                    SELECT created_at, price as amount FROM services WHERE seller_id = ? AND status = 'active'
                ) combined
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
                GROUP BY month
                ORDER BY month ASC
            ");
            $stmt->execute([$sellerId, $sellerId, $months]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Monthly earnings error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent orders/downloads
     */
    public function getRecentOrders($sellerId, $limit = 10) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    'template' as type,
                    t.title as product_name,
                    t.price,
                    t.downloads_count as quantity,
                    t.created_at,
                    'Download' as status
                FROM templates t
                WHERE t.seller_id = ? AND t.status = 'approved' AND t.downloads_count > 0
                UNION ALL
                SELECT 
                    'service' as type,
                    s.title as product_name,
                    s.price,
                    s.orders_count as quantity,
                    s.created_at,
                    'Completed' as status
                FROM services s
                WHERE s.seller_id = ? AND s.status = 'active' AND s.orders_count > 0
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$sellerId, $sellerId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Recent orders error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get seller products with pagination
     */
    public function getSellerProducts($sellerId, $type = 'all', $page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        $products = [];
        
        try {
            if ($type === 'templates' || $type === 'all') {
                $stmt = $this->pdo->prepare("
                    SELECT id, title, slug, description, price, preview_image, demo_url, 
                           technology, category, status, downloads_count, views_count, 
                           rating, created_at, updated_at, 'template' as type
                    FROM templates 
                    WHERE seller_id = ?
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$sellerId, $limit, $offset]);
                $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $products = array_merge($products, $templates);
            }
            
            if ($type === 'services' || $type === 'all') {
                $stmt = $this->pdo->prepare("
                    SELECT id, title, slug, description, price, preview_image, demo_url,
                           category, delivery_time, status, orders_count, views_count,
                           rating, created_at, updated_at, 'service' as type
                    FROM services 
                    WHERE seller_id = ?
                    ORDER BY created_at DESC 
                    LIMIT ? OFFSET ?
                ");
                $stmt->execute([$sellerId, $limit, $offset]);
                $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $products = array_merge($products, $services);
            }
            
            return $products;
        } catch (Exception $e) {
            error_log("Seller products error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Upgrade user to seller
     */
    public function upgradeToSeller($userId) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET user_type = 'seller' WHERE id = ?");
            return $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Upgrade to seller error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get top sellers
     */
    public function getTopSellers($limit = 8) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT 
                    u.id, u.first_name, u.last_name, u.profile_image,
                    COUNT(DISTINCT t.id) + COUNT(DISTINCT s.id) as total_products,
                    COALESCE(SUM(t.downloads_count), 0) + COALESCE(SUM(s.orders_count), 0) as total_sales,
                    (COALESCE(AVG(t.rating), 0) + COALESCE(AVG(s.rating), 0)) / 2 as avg_rating,
                    COALESCE(SUM(t.price * t.downloads_count * 0.7), 0) + COALESCE(SUM(s.price * s.orders_count * 0.7), 0) as total_earnings
                FROM users u
                LEFT JOIN templates t ON u.id = t.seller_id AND t.status = 'approved'
                LEFT JOIN services s ON u.id = s.seller_id AND s.status = 'active'
                WHERE u.user_type IN ('seller', 'admin')
                GROUP BY u.id
                HAVING total_products > 0
                ORDER BY total_earnings DESC, total_sales DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Top sellers error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new product (template or service)
     */
    public function createProduct($sellerId, $data, $type = 'template') {
        try {
            $table = $type === 'service' ? 'services' : 'templates';
            
            // Common fields
            $fields = [
                'seller_id', 'title', 'slug', 'description', 'price', 
                'category', 'preview_image', 'status', 'created_at'
            ];
            
            $values = [
                $sellerId,
                $data['title'],
                $this->generateSlug($data['title']),
                $data['description'],
                $data['price'],
                $data['category'],
                $data['preview_image'] ?? null,
                'pending',
                date('Y-m-d H:i:s')
            ];
            
            // Type-specific fields
            if ($type === 'template') {
                $fields[] = 'technology';
                $fields[] = 'demo_url';
                $values[] = $data['technology'] ?? null;
                $values[] = $data['demo_url'] ?? null;
            } else {
                $fields[] = 'delivery_time';
                $values[] = $data['delivery_time'] ?? 7;
            }
            
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            $sql = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            error_log("Create product error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generate unique slug from title
     */
    private function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Check for uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug exists in templates or services
     */
    private function slugExists($slug) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM (
                    SELECT slug FROM templates WHERE slug = ?
                    UNION
                    SELECT slug FROM services WHERE slug = ?
                ) combined
            ");
            $stmt->execute([$slug, $slug]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Update seller profile
     */
    public function updateSellerProfile($sellerId, $data) {
        try {
            $fields = [];
            $values = [];
            
            if (isset($data['first_name'])) {
                $fields[] = 'first_name = ?';
                $values[] = $data['first_name'];
            }
            
            if (isset($data['last_name'])) {
                $fields[] = 'last_name = ?';
                $values[] = $data['last_name'];
            }
            
            if (isset($data['bio'])) {
                $fields[] = 'bio = ?';
                $values[] = $data['bio'];
            }
            
            if (isset($data['profile_image'])) {
                $fields[] = 'profile_image = ?';
                $values[] = $data['profile_image'];
            }
            
            if (empty($fields)) {
                return false;
            }
            
            $values[] = $sellerId;
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            error_log("Update seller profile error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a product (template or service) if owned by seller
     */
    public function deleteProduct($sellerId, $type, $productId) {
        try {
            $table = $type === 'service' ? 'services' : 'templates';
            $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = ? AND seller_id = ?");
            return $stmt->execute([$productId, $sellerId]);
        } catch (Exception $e) {
            error_log("Delete product error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Duplicate a product (template or service) for the seller
     */
    public function duplicateProduct($sellerId, $type, $productId) {
        try {
            $table = $type === 'service' ? 'services' : 'templates';
            // Fetch original product
            $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = ? AND seller_id = ?");
            $stmt->execute([$productId, $sellerId]);
            $orig = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$orig) {
                return false;
            }
            // Prepare data for new product
            $data = [
                'title' => 'Copy of ' . $orig['title'],
                'description' => $orig['description'],
                'price' => $orig['price'],
                'category' => $orig['category_id'] ?? $orig['category'],
                'preview_image' => $orig['preview_image'] ?? null,
            ];
            if ($type === 'template') {
                $data['technology'] = $orig['technology'];
                $data['demo_url'] = $orig['demo_url'];
            } else {
                $data['delivery_time'] = $orig['delivery_time'] ?? null;
            }
            // Create new product as pending
            return $this->createProduct($sellerId, $data, $type);
        } catch (Exception $e) {
            error_log("Duplicate product error: " . $e->getMessage());
            return false;
        }
    }
}
?>
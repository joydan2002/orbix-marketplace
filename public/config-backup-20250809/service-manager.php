<?php
/**
 * Service Manager Class
 * Handles all service-related database operations for the marketplace
 */

class ServiceManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get services with filters, sorting, and pagination
     */
    public function getServices($filters = [], $sort = "popular", $limit = 12, $offset = 0) {
        $whereConditions = ["s.status = 'approved'", "u.user_type = 'seller'"]; // Only show services from sellers
        $params = [];
        
        // Apply filters
        if (!empty($filters["category"]) && $filters["category"] !== "all") {
            $whereConditions[] = "sc.slug = ?";
            $params[] = $filters["category"];
        }
        
        if (!empty($filters["search"])) {
            $whereConditions[] = "(s.title LIKE ? OR s.description LIKE ?)";
            $searchTerm = "%" . $filters["search"] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters["min_price"])) {
            $whereConditions[] = "s.price >= ?";
            $params[] = $filters["min_price"];
        }
        
        if (!empty($filters["max_price"])) {
            $whereConditions[] = "s.price <= ?";
            $params[] = $filters["max_price"];
        }
        
        if (!empty($filters["delivery_time"])) {
            $deliveryConditions = [];
            foreach ($filters["delivery_time"] as $delivery) {
                $deliveryConditions[] = "s.delivery_time = ?";
                $params[] = $delivery;
            }
            $whereConditions[] = "(" . implode(" OR ", $deliveryConditions) . ")";
        }
        
        if (!empty($filters["technology"])) {
            $techConditions = [];
            foreach ($filters["technology"] as $tech) {
                $techConditions[] = "s.technology LIKE ?";
                $params[] = "%" . $tech . "%";
            }
            $whereConditions[] = "(" . implode(" OR ", $techConditions) . ")";
        }
        
        if (!empty($filters["min_rating"])) {
            $whereConditions[] = "s.rating >= ?";
            $params[] = $filters["min_rating"];
        }
        
        if (!empty($filters["featured"])) {
            $whereConditions[] = "s.is_featured = 1";
        }
        
        // Build WHERE clause
        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        // Build ORDER BY clause
        switch ($sort) {
            case "newest":
                $orderBy = "ORDER BY s.created_at DESC";
                break;
            case "price-low":
                $orderBy = "ORDER BY s.price ASC";
                break;
            case "price-high":
                $orderBy = "ORDER BY s.price DESC";
                break;
            case "rating":
                $orderBy = "ORDER BY s.rating DESC, s.reviews_count DESC";
                break;
            case "delivery":
                $orderBy = "ORDER BY CASE 
                    WHEN s.delivery_time LIKE '%hour%' THEN 1
                    WHEN s.delivery_time LIKE '%day%' THEN CAST(SUBSTRING_INDEX(s.delivery_time, ' ', 1) AS UNSIGNED) + 1
                    ELSE 999
                END ASC";
                break;
            default:
                $orderBy = "ORDER BY s.is_featured DESC, s.orders_count DESC, s.rating DESC";
                break;
        }
        
        $sql = "
            SELECT 
                s.*,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Anonymous Seller') as seller_name,
                COALESCE(u.profile_image, 'https://via.placeholder.com/150x150/FF5F1F/FFFFFF?text=S') as profile_image,
                sc.name as category_name,
                sc.slug as category_slug,
                s.rating as avg_rating,
                s.reviews_count as review_count,
                COALESCE(s.orders_count, 0) as orders_count
            FROM services s
            LEFT JOIN users u ON s.seller_id = u.id
            LEFT JOIN service_categories sc ON s.category_id = sc.id
            {$whereClause}
            {$orderBy}
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get total count of services matching filters
     */
    public function getTotalCount($filters = []) {
        $whereConditions = ["s.status = 'approved'", "u.user_type = 'seller'"]; // Only show services from sellers
        $params = [];
        
        // Apply same filters as getServices
        if (!empty($filters["category"]) && $filters["category"] !== "all") {
            $whereConditions[] = "sc.slug = ?";
            $params[] = $filters["category"];
        }
        
        if (!empty($filters["search"])) {
            $whereConditions[] = "(s.title LIKE ? OR s.description LIKE ?)";
            $searchTerm = "%" . $filters["search"] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters["min_price"])) {
            $whereConditions[] = "s.price >= ?";
            $params[] = $filters["min_price"];
        }
        
        if (!empty($filters["max_price"])) {
            $whereConditions[] = "s.price <= ?";
            $params[] = $filters["max_price"];
        }
        
        if (!empty($filters["delivery_time"])) {
            $deliveryConditions = [];
            foreach ($filters["delivery_time"] as $delivery) {
                $deliveryConditions[] = "s.delivery_time = ?";
                $params[] = $delivery;
            }
            $whereConditions[] = "(" . implode(" OR ", $deliveryConditions) . ")";
        }
        
        if (!empty($filters["technology"])) {
            $techConditions = [];
            foreach ($filters["technology"] as $tech) {
                $techConditions[] = "s.technology LIKE ?";
                $params[] = "%" . $tech . "%";
            }
            $whereConditions[] = "(" . implode(" OR ", $techConditions) . ")";
        }
        
        if (!empty($filters["min_rating"])) {
            $whereConditions[] = "s.rating >= ?";
            $params[] = $filters["min_rating"];
        }
        
        if (!empty($filters["featured"])) {
            $whereConditions[] = "s.is_featured = 1";
        }
        
        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $sql = "
            SELECT COUNT(*) as total
            FROM services s
            LEFT JOIN users u ON s.seller_id = u.id
            LEFT JOIN service_categories sc ON s.category_id = sc.id
            {$whereClause}
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result["total"]);
    }
    
    /**
     * Get service categories with service counts
     */
    public function getCategories() {
        $sql = "
            SELECT 
                sc.*,
                COUNT(s.id) as service_count
            FROM service_categories sc
            LEFT JOIN services s ON sc.id = s.category_id AND s.status = 'approved'
            LEFT JOIN users u ON s.seller_id = u.id
            WHERE sc.is_active = 1 AND (s.id IS NULL OR u.user_type = 'seller')
            GROUP BY sc.id
            ORDER BY sc.sort_order ASC
        ";
        
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add "All Services" category at the beginning
        $totalCount = $this->getTotalCount();
        array_unshift($categories, [
            "name" => "All Services",
            "slug" => "all",
            "service_count" => $totalCount
        ]);
        
        return $categories;
    }
    
    /**
     * Get technology filters with counts
     */
    public function getTechnologyFilters() {
        $sql = "
            SELECT 
                s.technology, 
                COUNT(*) as count
            FROM services s
            LEFT JOIN users u ON s.seller_id = u.id
            WHERE s.status = 'approved' AND u.user_type = 'seller' AND s.technology IS NOT NULL AND s.technology != ''
            GROUP BY s.technology
            ORDER BY count DESC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get delivery time filters with counts
     */
    public function getDeliveryTimeFilters() {
        $sql = "
            SELECT 
                s.delivery_time, 
                COUNT(*) as count
            FROM services s
            LEFT JOIN users u ON s.seller_id = u.id
            WHERE s.status = 'approved' AND u.user_type = 'seller' AND s.delivery_time IS NOT NULL AND s.delivery_time != ''
            GROUP BY s.delivery_time
            ORDER BY 
                CASE 
                    WHEN s.delivery_time LIKE '%hour%' THEN 1
                    WHEN s.delivery_time LIKE '%day%' THEN CAST(SUBSTRING_INDEX(s.delivery_time, ' ', 1) AS UNSIGNED) + 1
                    ELSE 999
                END ASC
        ";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get single service by ID
     */
    public function getServiceById($id) {
        $sql = "
            SELECT 
                s.*,
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Anonymous Seller') as seller_name,
                COALESCE(u.profile_image, 'https://via.placeholder.com/150x150/FF5F1F/FFFFFF?text=S') as profile_image,
                sc.name as category_name,
                sc.slug as category_slug,
                s.rating as avg_rating,
                s.reviews_count as review_count,
                COALESCE(s.orders_count, 0) as orders_count
            FROM services s
            LEFT JOIN users u ON s.seller_id = u.id
            LEFT JOIN service_categories sc ON s.category_id = sc.id
            WHERE s.id = ? AND s.status = 'approved'
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Increment view count for a service
     */
    public function incrementViewCount($serviceId) {
        $sql = "UPDATE services SET views_count = views_count + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serviceId]);
    }
}
?>
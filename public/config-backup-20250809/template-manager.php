<?php
/**
 * Template Manager Class
 * Handles all template-related database operations
 */

require_once __DIR__ . '/cloudinary-config.php'; // Import để sử dụng getOptimizedImageUrl function

class TemplateManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all templates with optional filtering and sorting
     */
    public function getTemplates($filters = [], $sort = 'popular', $limit = null, $offset = 0) {
        $whereConditions = ["t.status = 'approved'", "u.user_type = 'seller'"]; // Only show templates from sellers
        $params = [];
        
        // Apply filters
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $whereConditions[] = "c.slug = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(t.title LIKE ? OR t.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['min_price'])) {
            $whereConditions[] = "t.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $whereConditions[] = "t.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['technology'])) {
            $technologies = is_array($filters['technology']) ? $filters['technology'] : [$filters['technology']];
            $techPlaceholders = [];
            foreach ($technologies as $tech) {
                $techPlaceholders[] = "?";
                $params[] = $tech;
            }
            $whereConditions[] = "t.technology IN (" . implode(',', $techPlaceholders) . ")";
        }
        
        if (!empty($filters['min_rating'])) {
            $whereConditions[] = "t.rating >= ?";
            $params[] = $filters['min_rating'];
        }
        
        if (!empty($filters['featured'])) {
            $whereConditions[] = "t.is_featured = 1";
        }
        
        // Build WHERE clause
        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        // Build ORDER BY clause
        switch ($sort) {
            case 'price-low':
                $orderBy = "ORDER BY t.price ASC";
                break;
            case 'price-high':
                $orderBy = "ORDER BY t.price DESC";
                break;
            case 'rating':
                $orderBy = "ORDER BY avg_rating DESC, review_count DESC";
                break;
            case 'newest':
                $orderBy = "ORDER BY t.created_at DESC";
                break;
            default:
                $orderBy = "ORDER BY t.downloads_count DESC, t.views_count DESC";
                break;
        }
        
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug,
                       COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Anonymous Seller') as seller_name,
                       COALESCE(u.profile_image, 'https://via.placeholder.com/150x150/FF5F1F/FFFFFF?text=T') as profile_image,
                       COALESCE(AVG(r.rating), t.rating, 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM templates t
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON t.seller_id = u.id
                LEFT JOIN reviews r ON t.id = r.template_id
                {$whereClause}
                GROUP BY t.id
                {$orderBy}";
        
        // Apply limit and offset
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process templates data
        foreach ($templates as &$template) {
            $template['tags'] = !empty($template['tags']) ? json_decode($template['tags'], true) : [];
            $template['tags'] = is_array($template['tags']) ? $template['tags'] : [];
            $template['avg_rating'] = round((float)$template['avg_rating'], 1);
            
            // Generate optimized image URLs for JavaScript
            if (!empty($template['preview_image'])) {
                $template['preview_image_url'] = getOptimizedImageUrl($template['preview_image'], 'thumb');
            } else {
                // Use fallback image
                $fallbackImage = $this->generateFallbackImage($template['category_slug']);
                $template['preview_image'] = $fallbackImage;
                $template['preview_image_url'] = $fallbackImage;
            }
            
            if (!empty($template['profile_image'])) {
                $template['seller_avatar_url'] = getOptimizedImageUrl($template['profile_image'], 'avatar_small');
            } else {
                $template['seller_avatar_url'] = 'assets/images/default-avatar.png';
            }
        }
        
        return $templates;
    }
    
    /**
     * Get template categories with counts
     */
    public function getCategories() {
        $sql = "SELECT c.*, COUNT(t.id) as template_count
                FROM categories c
                LEFT JOIN templates t ON c.id = t.category_id AND t.status = 'approved'
                LEFT JOIN users u ON t.seller_id = u.id AND u.user_type = 'seller'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.name";
        
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add "all" category
        $totalCount = array_sum(array_column($categories, 'template_count'));
        array_unshift($categories, [
            'id' => 0,
            'name' => 'All Templates',
            'slug' => 'all',
            'template_count' => $totalCount
        ]);
        
        return $categories;
    }
    
    /**
     * Get template by ID
     */
    public function getTemplateById($id) {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug,
                       u.first_name, u.last_name, u.profile_image,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM templates t
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON t.seller_id = u.id
                LEFT JOIN reviews r ON t.id = r.template_id
                WHERE t.id = :id AND t.status = 'approved'
                GROUP BY t.id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($template) {
            $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
            $template['tags'] = !empty($template['tags']) ? json_decode($template['tags'], true) : [];
            $template['tags'] = is_array($template['tags']) ? $template['tags'] : [];
            $template['avg_rating'] = round((float)$template['avg_rating'], 1);
            
            // Increment view count
            $this->incrementViewCount($id);
        }
        
        return $template;
    }
    
    /**
     * Get template reviews
     */
    public function getTemplateReviews($templateId, $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, u.first_name, u.last_name, u.profile_image
                FROM reviews r
                JOIN users u ON r.user_id = u.id
                WHERE r.template_id = :template_id
                ORDER BY r.created_at DESC
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':template_id', $templateId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get technology filter options with counts
     */
    public function getTechnologyFilters() {
        $sql = "SELECT t.technology, COUNT(*) as count
                FROM templates t
                LEFT JOIN users u ON t.seller_id = u.id
                WHERE t.status = 'approved' AND t.technology IS NOT NULL AND u.user_type = 'seller'
                GROUP BY t.technology
                ORDER BY count DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get featured templates
     */
    public function getFeaturedTemplates($limit = 6) {
        return $this->getTemplates(['featured' => true], 'popular', $limit);
    }
    
    /**
     * Increment template view count
     */
    private function incrementViewCount($templateId) {
        $sql = "UPDATE templates SET views_count = views_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $templateId, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    /**
     * Check if image exists
     */
    private function imageExists($imageUrl) {
        // If it's a Cloudinary image ID (starts with 'orbix_'), consider it valid
        if (strpos($imageUrl, 'orbix_') === 0) {
            return true;
        }
        
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return true; // Assume external URLs are valid
        }
        return file_exists($_SERVER['DOCUMENT_ROOT'] . $imageUrl);
    }
    
    /**
     * Generate fallback image URL
     */
    private function generateFallbackImage($category) {
        // Use the actual default images that exist
        $fallbackImages = [
            'business' => '/orbix/assets/images/default-template.jpg',
            'ecommerce' => '/orbix/assets/images/default-template.jpg',
            'e-commerce' => '/orbix/assets/images/default-template.jpg',
            'portfolio' => '/orbix/assets/images/default-template.jpg',
            'landing' => '/orbix/assets/images/default-template.jpg',
            'admin' => '/orbix/assets/images/default-template.jpg',
            'blog' => '/orbix/assets/images/default-template.jpg',
            'education' => '/orbix/assets/images/default-template.jpg'
        ];
        
        return $fallbackImages[$category] ?? '/orbix/assets/images/default-template.jpg';
    }
    
    /**
     * Get total template count with filters
     */
    public function getTotalCount($filters = []) {
        $whereConditions = ["t.status = 'approved'", "u.user_type = 'seller'"]; // Only count templates from sellers
        $params = [];
        
        // Apply same filters as getTemplates
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $whereConditions[] = "c.slug = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $whereConditions[] = "(t.title LIKE ? OR t.description LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['min_price'])) {
            $whereConditions[] = "t.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $whereConditions[] = "t.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['technology'])) {
            $technologies = is_array($filters['technology']) ? $filters['technology'] : [$filters['technology']];
            $techPlaceholders = [];
            foreach ($technologies as $tech) {
                $techPlaceholders[] = "?";
                $params[] = $tech;
            }
            $whereConditions[] = "t.technology IN (" . implode(',', $techPlaceholders) . ")";
        }
        
        if (!empty($filters['min_rating'])) {
            $whereConditions[] = "t.rating >= ?";
            $params[] = $filters['min_rating'];
        }
        
        if (!empty($filters['featured'])) {
            $whereConditions[] = "t.is_featured = 1";
        }
        
        $whereClause = count($whereConditions) > 0 ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $sql = "SELECT COUNT(DISTINCT t.id) as total
                FROM templates t
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON t.seller_id = u.id
                {$whereClause}";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
}
?>
<?php
/**
 * Template Manager Class
 * Handles all template-related database operations
 */

class TemplateManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all templates with optional filtering and sorting
     */
    public function getTemplates($filters = [], $sort = 'popular', $limit = null, $offset = 0) {
        $sql = "SELECT t.*, c.name as category_name, c.slug as category_slug,
                       u.first_name, u.last_name, u.profile_image,
                       COALESCE(AVG(r.rating), 0) as avg_rating,
                       COUNT(r.id) as review_count
                FROM templates t
                LEFT JOIN categories c ON t.category_id = c.id
                LEFT JOIN users u ON t.seller_id = u.id
                LEFT JOIN reviews r ON t.id = r.template_id
                WHERE t.status = 'approved'";
        
        $params = [];
        
        // Apply filters
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND c.slug = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE :search OR t.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND t.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND t.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['technology'])) {
            $technologies = is_array($filters['technology']) ? $filters['technology'] : [$filters['technology']];
            $techPlaceholders = [];
            foreach ($technologies as $i => $tech) {
                $techPlaceholders[] = ":tech_$i";
                $params["tech_$i"] = $tech;
            }
            $sql .= " AND t.technology IN (" . implode(',', $techPlaceholders) . ")";
        }
        
        if (!empty($filters['min_rating'])) {
            $sql .= " AND t.rating >= :min_rating";
            $params['min_rating'] = $filters['min_rating'];
        }
        
        if (!empty($filters['featured'])) {
            $sql .= " AND t.is_featured = 1";
        }
        
        $sql .= " GROUP BY t.id";
        
        // Apply sorting
        switch ($sort) {
            case 'price-low':
                $sql .= " ORDER BY t.price ASC";
                break;
            case 'price-high':
                $sql .= " ORDER BY t.price DESC";
                break;
            case 'rating':
                $sql .= " ORDER BY avg_rating DESC, review_count DESC";
                break;
            case 'newest':
                $sql .= " ORDER BY t.created_at DESC";
                break;
            case 'popular':
            default:
                $sql .= " ORDER BY t.downloads_count DESC, t.views_count DESC";
                break;
        }
        
        // Apply limit and offset
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            if ($key === 'limit' || $key === 'offset') {
                $stmt->bindValue(":$key", (int)$value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        $stmt->execute();
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process templates data
        foreach ($templates as &$template) {
            $template['seller_name'] = trim($template['first_name'] . ' ' . $template['last_name']);
            $template['tags'] = json_decode($template['tags'], true) ?: [];
            $template['avg_rating'] = round((float)$template['avg_rating'], 1);
            
            // Generate fallback image if needed
            if (empty($template['preview_image']) || !$this->imageExists($template['preview_image'])) {
                $template['preview_image'] = $this->generateFallbackImage($template['category_slug']);
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
            $template['tags'] = json_decode($template['tags'], true) ?: [];
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
        $sql = "SELECT technology, COUNT(*) as count
                FROM templates 
                WHERE status = 'approved' AND technology IS NOT NULL
                GROUP BY technology
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
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return true; // Assume external URLs are valid
        }
        return file_exists($_SERVER['DOCUMENT_ROOT'] . $imageUrl);
    }
    
    /**
     * Generate fallback image URL
     */
    private function generateFallbackImage($category) {
        $fallbackImages = [
            'business' => '/orbix/assets/images/fallbacks/business-fallback.svg',
            'e-commerce' => '/orbix/assets/images/fallbacks/ecommerce-fallback.svg',
            'portfolio' => '/orbix/assets/images/fallbacks/portfolio-fallback.svg',
            'landing' => '/orbix/assets/images/fallbacks/landing-fallback.svg',
            'admin' => '/orbix/assets/images/fallbacks/admin-fallback.svg'
        ];
        
        return $fallbackImages[$category] ?? '/orbix/assets/images/fallbacks/default-fallback.svg';
    }
    
    /**
     * Get total template count with filters
     */
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(DISTINCT t.id) as total
                FROM templates t
                LEFT JOIN categories c ON t.category_id = c.id
                WHERE t.status = 'approved'";
        
        $params = [];
        
        // Apply same filters as getTemplates
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND c.slug = :category";
            $params['category'] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (t.title LIKE :search OR t.description LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND t.price >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND t.price <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['technology'])) {
            $technologies = is_array($filters['technology']) ? $filters['technology'] : [$filters['technology']];
            $techPlaceholders = [];
            foreach ($technologies as $i => $tech) {
                $techPlaceholders[] = ":tech_$i";
                $params["tech_$i"] = $tech;
            }
            $sql .= " AND t.technology IN (" . implode(',', $techPlaceholders) . ")";
        }
        
        if (!empty($filters['min_rating'])) {
            $sql .= " AND t.rating >= :min_rating";
            $params['min_rating'] = $filters['min_rating'];
        }
        
        if (!empty($filters['featured'])) {
            $sql .= " AND t.is_featured = 1";
        }
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }
}
?>
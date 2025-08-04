<?php
/**
 * Seller API
 * Handle seller-related operations
 */

require_once '../config/database.php';
require_once '../config/seller-manager.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $pdo = DatabaseConfig::getConnection();
    $sellerManager = new SellerManager($pdo);
    
    switch ($action) {
        case 'upgrade_to_seller':
            // Upgrade user account to seller
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            // Check if already a seller
            $stmt = $pdo->prepare("SELECT user_type FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            if ($user['user_type'] === 'seller') {
                echo json_encode([
                    'success' => true,
                    'message' => 'Already a seller account'
                ]);
                break;
            }
            
            // Update user type to seller
            $stmt = $pdo->prepare("UPDATE users SET user_type = 'seller' WHERE id = ?");
            $result = $stmt->execute([$_SESSION['user_id']]);
            
            if (!$result) {
                throw new Exception('Failed to upgrade account');
            }
            
            // Update session with new user type
            $_SESSION['user_type'] = 'seller';
            
            echo json_encode([
                'success' => true,
                'message' => 'Successfully upgraded to seller account'
            ]);
            break;
            
        case 'get_seller_stats':
            // Get comprehensive seller statistics using SellerManager
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $stats = $sellerManager->getSellerStats($_SESSION['user_id']);
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        case 'get_monthly_earnings':
            // Get monthly earnings for charts
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $months = intval($input['months'] ?? 12);
            $monthlyData = $sellerManager->getMonthlyEarnings($_SESSION['user_id'], $months);
            
            echo json_encode([
                'success' => true,
                'monthly_earnings' => $monthlyData
            ]);
            break;
            
        case 'get_recent_orders':
            // Get recent orders/downloads
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $limit = intval($input['limit'] ?? 10);
            $orders = $sellerManager->getRecentOrders($_SESSION['user_id'], $limit);
            
            echo json_encode([
                'success' => true,
                'orders' => $orders
            ]);
            break;
            
        case 'get_seller_products':
            // Get seller's products (templates and services)
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $page = intval($input['page'] ?? 1);
            $limit = intval($input['limit'] ?? 20);
            $type = $input['type'] ?? 'all'; // 'all', 'templates', 'services'
            
            $products = $sellerManager->getSellerProducts($_SESSION['user_id'], $type, $page, $limit);
            
            echo json_encode([
                'success' => true,
                'products' => $products,
                'pagination' => [
                    'current_page' => $page,
                    'items_per_page' => $limit
                ]
            ]);
            break;
            
        case 'create_product':
            // Create new product (template or service)
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $productType = $input['product_type'] ?? 'template';
            $productData = $input['product_data'] ?? [];
            
            // Validate required fields
            $requiredFields = ['title', 'description', 'price', 'category'];
            foreach ($requiredFields as $field) {
                if (empty($productData[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }
            
            $result = $sellerManager->createProduct($_SESSION['user_id'], $productData, $productType);
            
            if (!$result) {
                throw new Exception('Failed to create product');
            }
            
            echo json_encode([
                'success' => true,
                'message' => ucfirst($productType) . ' created successfully and is pending approval'
            ]);
            break;

        case 'add_product':
            // Handle form submission from add product modal
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $productType = $_POST['product_type'] ?? 'template';
            
            // Validate required fields
            $requiredFields = ['title', 'description', 'price', 'category'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("Field '$field' is required");
                }
            }
            
            // Map category slug to category_id
            $categoryMap = [
                'web-design' => 1,      // Business
                'mobile-app' => 2,      // Mobile Apps
                'ui-ux' => 3,           // Portfolio
                'graphics' => 4,        // Landing Page
                'development' => 5,     // Admin Dashboard
                'marketing' => 9,       // E-commerce
                'business' => 12,       // Blog
                'other' => 13           // SaaS
            ];
            
            $categorySlug = $_POST['category'];
            $categoryId = $categoryMap[$categorySlug] ?? 1; // Default to Business
            
            // Handle file uploads
            $previewImagePath = null;
            $productFilePath = null;
            
            // Upload preview image
            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === 0) {
                $uploadDir = '../uploads/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $file = $_FILES['preview_image'];
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (in_array($file['type'], $allowedTypes) && $file['size'] <= 5 * 1024 * 1024) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'preview_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $previewImagePath = 'uploads/products/' . $filename;
                    }
                }
            }
            
            // Upload product files (for templates)
            if ($productType === 'template' && isset($_FILES['product_files']) && $_FILES['product_files']['error'] === 0) {
                $file = $_FILES['product_files'];
                $allowedTypes = ['application/zip', 'application/x-zip-compressed', 'application/x-rar-compressed'];
                
                if (in_array($file['type'], $allowedTypes) && $file['size'] <= 100 * 1024 * 1024) {
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'product_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
                    $filepath = $uploadDir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $productFilePath = 'uploads/products/' . $filename;
                    }
                }
            }
            
            // Add type-specific fields
            if ($productType === 'template') {
                $features = isset($_POST['features']) ? implode(',', array_filter($_POST['features'])) : '';
                
                // Convert tags to JSON format
                $tags = '';
                if (!empty($_POST['tags'])) {
                    $tagArray = array_map('trim', explode(',', $_POST['tags']));
                    $tagArray = array_filter($tagArray); // Remove empty tags
                    $tags = json_encode($tagArray);
                }
                
                // Create slug from title
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                
                // Insert into templates table
                $stmt = $pdo->prepare("
                    INSERT INTO templates (seller_id, title, slug, description, price, category_id, technology, 
                                         preview_image, download_file, demo_url, tags, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $result = $stmt->execute([
                    $_SESSION['user_id'],
                    $_POST['title'],
                    $slug,
                    $_POST['description'],
                    floatval($_POST['price']),
                    $categoryId,
                    $_POST['technology'] ?? '',
                    $previewImagePath,
                    $productFilePath,
                    $_POST['demo_url'] ?? null,
                    $tags,
                    'pending'
                ]);
                
            } else { // service
                $features = isset($_POST['features']) ? implode(',', array_filter($_POST['features'])) : '';
                $deliveryTime = intval($_POST['delivery_time'] ?? 7);
                
                // Convert tags to JSON format
                $tags = '';
                if (!empty($_POST['tags'])) {
                    $tagArray = array_map('trim', explode(',', $_POST['tags']));
                    $tagArray = array_filter($tagArray); // Remove empty tags
                    $tags = json_encode($tagArray);
                }
                
                // Create slug from title  
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                
                // Insert into services table
                $stmt = $pdo->prepare("
                    INSERT INTO services (seller_id, title, slug, description, price, category_id, 
                                        delivery_time, preview_image, demo_url, tags, 
                                        features, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                
                $result = $stmt->execute([
                    $_SESSION['user_id'],
                    $_POST['title'],
                    $slug,
                    $_POST['description'],
                    floatval($_POST['price']),
                    $categoryId,
                    $deliveryTime,
                    $previewImagePath,
                    $_POST['demo_url'] ?? null,
                    $tags,
                    $features,
                    'pending'
                ]);
            }
            
            if (!$result) {
                throw new Exception('Failed to create product');
            }
            
            echo json_encode([
                'success' => true,
                'message' => ucfirst($productType) . ' submitted successfully and is pending approval',
                'redirect' => '/seller-channel.php'
            ]);
            break;
            
        case 'update_seller_profile':
            // Update seller profile information
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $profileData = $input['profile_data'] ?? [];
            
            $result = $sellerManager->updateSellerProfile($_SESSION['user_id'], $profileData);
            
            if (!$result) {
                throw new Exception('Failed to update profile');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully'
            ]);
            break;
            
        case 'get_top_sellers':
            // Get top performing sellers (public data)
            $limit = intval($input['limit'] ?? 8);
            $topSellers = $sellerManager->getTopSellers($limit);
            
            echo json_encode([
                'success' => true,
                'top_sellers' => $topSellers
            ]);
            break;
            
        case 'get_product':
            // Get individual product data
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            $productId = intval($_GET['id'] ?? $input['id'] ?? 0);
            $type = $_GET['type'] ?? $input['type'] ?? 'template';
            
            if (!$productId) {
                throw new Exception('Product ID is required');
            }
            
            $table = $type === 'service' ? 'services' : 'templates';
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? AND seller_id = ?");
            $stmt->execute([$productId, $_SESSION['user_id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            // Add type to product data
            $product['type'] = $type;
            
            echo json_encode([
                'success' => true,
                'product' => $product
            ]);
            break;
            
        case 'upload_product_image':
            // Handle product image upload
            if (!isset($_SESSION['user_id']) || !isset($_FILES['image'])) {
                throw new Exception('Invalid upload request');
            }
            
            $uploadDir = '../uploads/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
            }
            
            if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
                throw new Exception('File size too large. Maximum 5MB allowed.');
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'product_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new Exception('Failed to upload file');
            }
            
            echo json_encode([
                'success' => true,
                'filename' => $filename,
                'url' => '/uploads/products/' . $filename
            ]);
            break;
            
        case 'delete_product':
            // Delete a seller's product
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            $type = $input['type'] ?? '';
            $id = intval($input['id'] ?? 0);
            $result = $sellerManager->deleteProduct($_SESSION['user_id'], $type, $id);
            if (!$result) {
                throw new Exception('Failed to delete product');
            }
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            break;

        case 'duplicate_product':
            // Duplicate a seller's product
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            $type = $input['type'] ?? '';
            $id = intval($input['id'] ?? 0);
            $result = $sellerManager->duplicateProduct($_SESSION['user_id'], $type, $id);
            if (!$result) {
                throw new Exception('Failed to duplicate product');
            }
            echo json_encode(['success' => true, 'message' => 'Product duplicated successfully']);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
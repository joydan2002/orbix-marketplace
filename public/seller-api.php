<?php
/**
 * Seller API
 * Handle seller-related operations
 */

require_once '../config/database.php';
require_once '../config/seller-manager.php';
require_once '../config/cloudinary-service.php'; // Add Cloudinary support

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
    
    // Initialize Cloudinary service
    $cloudinary = new CloudinaryService();
    
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
            
            $productType = $_POST['type'] ?? 'template'; // Fix: get type from form
            
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
            
            // For services, use different category mapping to service_categories table
            if ($productType === 'service') {
                $categoryMap = [
                    'web-design' => 7,      // Design & Development
                    'mobile-app' => 7,      // Design & Development
                    'ui-ux' => 7,           // Design & Development
                    'graphics' => 7,        // Design & Development
                    'development' => 7,     // Design & Development
                    'marketing' => 8,       // Digital Marketing
                    'business' => 10,       // Business Services
                    'other' => 10           // Business Services
                ];
            }
            
            $categorySlug = $_POST['category'];
            $categoryId = $categoryMap[$categorySlug] ?? 1; // Default to Business
            
            // Handle file uploads with Cloudinary
            $previewImageUrl = null;
            $productFileUrl = null;
            
            // Upload preview image to Cloudinary
            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $folder = $productType === 'template' ? 'templates' : 'services';
                    $uploadResult = $cloudinary->uploadImage($_FILES['preview_image'], $folder);
                    
                    if ($uploadResult['success']) {
                        $previewImageUrl = $uploadResult['public_id'];
                        error_log("✅ Preview image uploaded to Cloudinary: " . $previewImageUrl);
                    } else {
                        error_log("❌ Preview image upload failed: " . $uploadResult['error']);
                        throw new Exception('Failed to upload preview image: ' . $uploadResult['error']);
                    }
                } catch (Exception $e) {
                    error_log("❌ Cloudinary upload exception: " . $e->getMessage());
                    throw new Exception('Failed to upload preview image: ' . $e->getMessage());
                }
            }
            
            // Upload product files to Cloudinary (for templates)
            if ($productType === 'template' && isset($_FILES['product_files']) && $_FILES['product_files']['error'] === UPLOAD_ERR_OK) {
                try {
                    // For ZIP files, we'll upload as raw files using uploadImage method
                    $uploadResult = $cloudinary->uploadImage($_FILES['product_files'], null, 'templates/files');
                    
                    if ($uploadResult['success']) {
                        $productFileUrl = $uploadResult['public_id'];
                        error_log("✅ Product file uploaded to Cloudinary: " . $productFileUrl);
                    } else {
                        error_log("❌ Product file upload failed: " . $uploadResult['error']);
                        throw new Exception('Failed to upload product files: ' . $uploadResult['error']);
                    }
                } catch (Exception $e) {
                    error_log("❌ Cloudinary file upload exception: " . $e->getMessage());
                    throw new Exception('Failed to upload product files: ' . $e->getMessage());
                }
            }
            
            // Add type-specific fields
            if ($productType === 'template') {
                $features = isset($_POST['features']) ? implode(',', array_filter($_POST['features'])) : '';
                
                // Convert tags to JSON format
                $tags = null; // Default to NULL for JSON column
                if (!empty($_POST['tags'])) {
                    $tagArray = array_map('trim', explode(',', $_POST['tags']));
                    $tagArray = array_filter($tagArray); // Remove empty tags
                    if (!empty($tagArray)) {
                        $tags = json_encode($tagArray);
                        error_log('📝 Tags processed: ' . $tags);
                    } else {
                        error_log('📝 Tags array empty after filtering, setting to NULL');
                    }
                } else {
                    error_log('📝 No tags provided, setting to NULL');
                }
                
                // Create slug from title
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                
                // Insert into templates table with Cloudinary URLs
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
                    $previewImageUrl, // Cloudinary public_id
                    $productFileUrl,  // Cloudinary public_id
                    $_POST['demo_url'] ?? null,
                    $tags,
                    'pending'
                ]);
                
            } else { // service
                $features = isset($_POST['features']) ? implode(',', array_filter($_POST['features'])) : '';
                $deliveryTime = intval($_POST['delivery_time'] ?? 7);
                
                // Convert tags to JSON format
                $tags = null; // Default to NULL for JSON column
                if (!empty($_POST['tags'])) {
                    $tagArray = array_map('trim', explode(',', $_POST['tags']));
                    $tagArray = array_filter($tagArray); // Remove empty tags
                    if (!empty($tagArray)) {
                        $tags = json_encode($tagArray);
                        error_log('📝 Tags processed: ' . $tags);
                    } else {
                        error_log('📝 Tags array empty after filtering, setting to NULL');
                    }
                } else {
                    error_log('📝 No tags provided, setting to NULL');
                }
                
                // Create slug from title  
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['title'])));
                
                // Insert into services table with Cloudinary URLs
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
                    $previewImageUrl, // Cloudinary public_id
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
                throw new Exception('Not authenticated - Please log in to delete products');
            }
            
            $type = $input['type'] ?? '';
            $id = intval($input['id'] ?? 0);
            
            // Super detailed logging for debugging
            error_log("🗑️ DELETE REQUEST START ==================");
            error_log("User ID: " . $_SESSION['user_id']);
            error_log("Raw input: " . file_get_contents('php://input'));
            error_log("Parsed input: " . json_encode($input));
            error_log("Type: '$type'");
            error_log("ID: $id");
            error_log("==========================================");
            
            if (!$type) {
                throw new Exception('Product type is required (template or service)');
            }
            
            if (!$id) {
                throw new Exception('Product ID is required');
            }
            
            // Check if product exists and belongs to this seller
            $table = $type === 'service' ? 'services' : 'templates';
            error_log("Using table: $table");
            
            $checkStmt = $pdo->prepare("SELECT id, title FROM {$table} WHERE id = ? AND seller_id = ?");
            $checkStmt->execute([$id, $_SESSION['user_id']]);
            $product = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            error_log("Product check result: " . json_encode($product));
            
            if (!$product) {
                // Let's see what products actually exist for this user
                $debugStmt = $pdo->prepare("SELECT id, title, 'template' as type FROM templates WHERE seller_id = ? 
                                          UNION ALL 
                                          SELECT id, title, 'service' as type FROM services WHERE seller_id = ?");
                $debugStmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                $userProducts = $debugStmt->fetchAll(PDO::FETCH_ASSOC);
                error_log("User's products: " . json_encode($userProducts));
                
                throw new Exception("Product not found or you don't have permission to delete it");
            }
            
            // Delete the product
            $deleteStmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ? AND seller_id = ?");
            $result = $deleteStmt->execute([$id, $_SESSION['user_id']]);
            
            error_log("Delete result: " . ($result ? 'SUCCESS' : 'FAILED'));
            
            if (!$result) {
                throw new Exception('Failed to delete product');
            }
            
            echo json_encode([
                'success' => true,
                'message' => ucfirst($type) . ' "' . $product['title'] . '" deleted successfully'
            ]);
            break;
            
        case 'get_image_url':
            // Get Cloudinary URL for an image
            $imagePublicId = $_GET['image'] ?? '';
            $size = $_GET['size'] ?? 'medium';
            
            if (!$imagePublicId) {
                throw new Exception('Image public ID is required');
            }
            
            try {
                // Define size transformations
                $transformations = [
                    'small' => 'c_fill,w_150,h_150,q_auto',
                    'medium' => 'c_fill,w_300,h_200,q_auto',
                    'large' => 'c_fill,w_600,h_400,q_auto',
                    'full' => 'q_auto'
                ];
                
                $transformation = $transformations[$size] ?? $transformations['medium'];
                
                // Get optimized image URL from Cloudinary
                $imageUrl = $cloudinary->getOptimizedUrl($imagePublicId, $transformation);
                
                if (empty($imageUrl)) {
                    throw new Exception('Failed to generate image URL');
                }
                
                echo json_encode([
                    'success' => true,
                    'url' => $imageUrl,
                    'public_id' => $imagePublicId
                ]);
            } catch (Exception $e) {
                // If Cloudinary fails, try to fallback to default image
                $fallbackUrl = $size === 'small' ? '/assets/images/default-template.jpg' : 
                              ($size === 'large' ? '/assets/images/default-template.jpg' : 
                               '/assets/images/default-template.jpg');
                
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'fallback_url' => $fallbackUrl
                ]);
            }
            break;
            
        case 'update_product':
            // Update product
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Not authenticated');
            }
            
            // Handle both JSON input and FormData
            $productType = $input['type'] ?? $_POST['product_type'] ?? 'template';
            $productId = intval($input['id'] ?? $_POST['product_id'] ?? 0);
            $productData = $input['product_data'] ?? $_POST;
            
            error_log("🔧 Update product called with:");
            error_log("Product Type: " . $productType);
            error_log("Product ID: " . $productId);
            error_log("POST data: " . json_encode($_POST));
            error_log("Input data: " . json_encode($input));
            
            if (!$productId) {
                throw new Exception('Product ID is required');
            }
            
            // Handle file uploads if present
            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                try {
                    $folder = $productType === 'template' ? 'templates' : 'services';
                    $uploadResult = $cloudinary->uploadImage($_FILES['preview_image'], $folder);
                    
                    if ($uploadResult['success']) {
                        $productData['preview_image'] = $uploadResult['public_id'];
                        error_log("✅ Preview image updated to Cloudinary: " . $uploadResult['public_id']);
                    } else {
                        error_log("❌ Preview image upload failed: " . $uploadResult['error']);
                        throw new Exception('Failed to upload preview image: ' . $uploadResult['error']);
                    }
                } catch (Exception $e) {
                    error_log("❌ Cloudinary upload exception: " . $e->getMessage());
                    throw new Exception('Failed to upload preview image: ' . $e->getMessage());
                }
            }
            
            // Handle product files upload for templates
            if ($productType === 'template' && isset($_FILES['product_files']) && $_FILES['product_files']['error'] === UPLOAD_ERR_OK) {
                try {
                    $uploadResult = $cloudinary->uploadImage($_FILES['product_files'], null, 'templates/files');
                    
                    if ($uploadResult['success']) {
                        $productData['download_file'] = $uploadResult['public_id'];
                        error_log("✅ Product file updated to Cloudinary: " . $uploadResult['public_id']);
                    } else {
                        error_log("❌ Product file upload failed: " . $uploadResult['error']);
                        throw new Exception('Failed to upload product files: ' . $uploadResult['error']);
                    }
                } catch (Exception $e) {
                    error_log("❌ Cloudinary file upload exception: " . $e->getMessage());
                    throw new Exception('Failed to upload product files: ' . $e->getMessage());
                }
            }
            
            // Process tags if present
            if (isset($productData['tags']) && !empty($productData['tags'])) {
                $tagArray = array_map('trim', explode(',', $productData['tags']));
                $tagArray = array_filter($tagArray); // Remove empty tags
                if (!empty($tagArray)) {
                    $productData['tags'] = json_encode($tagArray);
                    error_log('📝 Tags processed: ' . $productData['tags']);
                } else {
                    $productData['tags'] = null;
                }
            }
            
            // Process features if present (for services and templates)
            if (isset($productData['features']) && is_array($productData['features'])) {
                $productData['features'] = implode(',', array_filter($productData['features']));
                error_log('📝 Features processed: ' . $productData['features']);
            }
            
            $result = $sellerManager->updateProduct($_SESSION['user_id'], $productType, $productId, $productData);
            
            if (!$result) {
                throw new Exception('Failed to update product');
            }
            
            echo json_encode([
                'success' => true,
                'message' => ucfirst($productType) . ' updated successfully'
            ]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    error_log("Seller API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
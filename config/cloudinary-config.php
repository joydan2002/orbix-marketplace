<?php
/**
 * Cloudinary Configuration for Orbix
 * Updated with actual credentials
 */

// ✅ ACTUAL CLOUDINARY CREDENTIALS
define('CLOUDINARY_CLOUD_NAME', 'dpmwj7f9j');
define('CLOUDINARY_API_KEY', '413853847468875');
define('CLOUDINARY_API_SECRET', 'BvDJ1bFElcEFLGxZHdbau9BVFJQ');

// Upload Presets (Create these in Cloudinary Dashboard)
define('CLOUDINARY_PRODUCT_PRESET', 'orbix_products'); 
define('CLOUDINARY_AVATAR_PRESET', 'orbix_avatars'); 
define('CLOUDINARY_VIDEO_PRESET', 'orbix_videos'); 

// Cloudinary URLs
define('CLOUDINARY_UPLOAD_URL', 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/image/upload');
define('CLOUDINARY_VIDEO_UPLOAD_URL', 'https://api.cloudinary.com/v1_1/' . CLOUDINARY_CLOUD_NAME . '/video/upload');
define('CLOUDINARY_DELIVERY_URL', 'https://res.cloudinary.com/' . CLOUDINARY_CLOUD_NAME . '/image/upload/');
define('CLOUDINARY_VIDEO_DELIVERY_URL', 'https://res.cloudinary.com/' . CLOUDINARY_CLOUD_NAME . '/video/upload/');

// Default Image Transformations
define('CLOUDINARY_PRODUCT_THUMB', 'w_300,h_200,c_fill,q_auto,f_auto');
define('CLOUDINARY_PRODUCT_LARGE', 'w_800,h_600,c_fill,q_auto,f_auto');
define('CLOUDINARY_AVATAR_SMALL', 'w_50,h_50,c_fill,q_auto,f_auto,r_max');
define('CLOUDINARY_AVATAR_MEDIUM', 'w_100,h_100,c_fill,q_auto,f_auto,r_max');
define('CLOUDINARY_AVATAR_LARGE', 'w_200,h_200,c_fill,q_auto,f_auto,r_max');

// Folder Structure
define('CLOUDINARY_FOLDERS', [
    'products' => 'orbix/products',
    'avatars' => 'orbix/avatars', 
    'templates' => 'orbix/templates',
    'services' => 'orbix/services',
    'videos' => 'orbix/videos',
    'temp' => 'orbix/temp'
]);

// File Size Limits (in bytes)
define('MAX_IMAGE_SIZE', 10 * 1024 * 1024); // 10MB
define('MAX_VIDEO_SIZE', 100 * 1024 * 1024); // 100MB

// Allowed file types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'mov', 'avi', 'webm']);

/**
 * Detect environment for folder structure
 */
function isProductionEnvironment() {
    // For CLI, assume development
    if (php_sapi_name() === 'cli') {
        return false;
    }
    
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return !in_array($host, ['localhost', '127.0.0.1', 'orbix.local']);
}

/**
 * Get environment-specific settings
 */
function getCloudinaryEnvironment() {
    $is_dev = !isProductionEnvironment();
    
    return [
        'is_development' => $is_dev,
        'folder_prefix' => $is_dev ? 'dev/' : 'prod/',
        'quality' => $is_dev ? 'auto:low' : 'auto:good'
    ];
}

/**
 * Build Cloudinary URL with transformations
 */
function buildCloudinaryUrl($public_id, $transformation = '', $resource_type = 'image') {
    $base_url = ($resource_type === 'video') ? CLOUDINARY_VIDEO_DELIVERY_URL : CLOUDINARY_DELIVERY_URL;
    
    if (!empty($transformation)) {
        return $base_url . $transformation . '/' . $public_id;
    }
    
    return $base_url . $public_id;
}

/**
 * Get optimized image URL
 */
function getOptimizedImageUrl($image_path, $size = 'medium') {
    if (empty($image_path)) {
        // Return default image based on size
        $defaults = [
            'thumb' => '/orbix/public/assets/images/default-template.jpg',
            'medium' => '/orbix/public/assets/images/default-template.jpg', 
            'avatar_small' => '/orbix/public/assets/images/default-avatar.png',
            'avatar_medium' => '/orbix/public/assets/images/default-avatar.png',
            'avatar_large' => '/orbix/public/assets/images/default-avatar.png'
        ];
        return $defaults[$size] ?? '/orbix/public/assets/images/default-template.jpg';
    }
    
    // If it's already a full URL (external or Cloudinary), return as-is
    if (filter_var($image_path, FILTER_VALIDATE_URL)) {
        return $image_path;
    }
    
    // If it's a local path (starts with / or contains /orbix/assets), return as-is
    if (strpos($image_path, '/') === 0 || strpos($image_path, '/orbix/assets') !== false || strpos($image_path, 'fallback') !== false) {
        return $image_path;
    }
    
    // Handle Cloudinary public IDs (only for actual uploaded images)
    $public_id = $image_path;
    
    // All images are uploaded to orbix/products/ folder regardless of prefix
    if (strpos($public_id, 'orbix/') === false && strpos($public_id, 'orbix_') === 0) {
        $public_id = 'orbix/products/' . $public_id;
    }
    
    $transformations = [
        'thumb' => CLOUDINARY_PRODUCT_THUMB,
        'medium' => CLOUDINARY_PRODUCT_LARGE,
        'avatar_small' => CLOUDINARY_AVATAR_SMALL,
        'avatar_medium' => CLOUDINARY_AVATAR_MEDIUM,
        'avatar_large' => CLOUDINARY_AVATAR_LARGE
    ];
    
    $transform = $transformations[$size] ?? CLOUDINARY_PRODUCT_THUMB;
    
    try {
        return buildCloudinaryUrl($public_id, $transform);
    } catch (Exception $e) {
        // Return default image if Cloudinary URL building fails
        $defaults = [
            'thumb' => '/orbix/public/assets/images/default-template.jpg',
            'medium' => '/orbix/public/assets/images/default-template.jpg', 
            'avatar_small' => '/orbix/public/assets/images/default-avatar.png',
            'avatar_medium' => '/orbix/public/assets/images/default-avatar.png',
            'avatar_large' => '/orbix/public/assets/images/default-avatar.png'
        ];
        return $defaults[$size] ?? '/orbix/public/assets/images/default-template.jpg';
    }
}
?>
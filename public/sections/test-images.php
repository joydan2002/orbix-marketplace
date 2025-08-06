<?php
/**
 * Test Image Display in seller-products
 * Debug why images are not showing
 */

require_once '../../config/database.php';
require_once '../../config/cloudinary-config.php';

// Simulate session for testing
session_start();
$_SESSION['user_id'] = 44; // Seller ID 44 has both templates and services
$_SESSION['user_type'] = 'seller';

// Test the actual data loading logic
require_once 'seller-data-loader.php';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Image Test</title></head><body>\n";
echo "<h1>🔍 Testing Image Display</h1>\n";

echo "<h2>📋 Raw Template Data:</h2>\n";
foreach ($templates as $template) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>\n";
    echo "<h3>{$template['title']}</h3>\n";
    echo "<p><strong>ID:</strong> {$template['id']}</p>\n";
    echo "<p><strong>Preview Image DB Value:</strong> '{$template['preview_image']}'</p>\n";
    
    $hasImage = !empty($template['preview_image']);
    echo "<p><strong>Has Image:</strong> " . ($hasImage ? 'YES' : 'NO') . "</p>\n";
    
    if ($hasImage) {
        $optimizedUrl = getOptimizedImageUrl($template['preview_image'], 'thumb');
        echo "<p><strong>Generated URL:</strong> {$optimizedUrl}</p>\n";
        echo "<p><strong>Test Image:</strong><br>\n";
        echo "<img src='{$optimizedUrl}' alt='{$template['title']}' style='max-width: 200px; border: 1px solid red;' onload=\"console.log('Image loaded: {$optimizedUrl}')\" onerror=\"console.log('Image failed: {$optimizedUrl}'); this.style.border='3px solid red';\"></p>\n";
    } else {
        echo "<p><strong>No Image - Will Show Icon</strong></p>\n";
    }
    echo "</div>\n";
}

echo "<h2>🛠️ Services Data:</h2>\n";
foreach ($services as $service) {
    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>\n";
    echo "<h3>{$service['title']}</h3>\n";
    echo "<p><strong>ID:</strong> {$service['id']}</p>\n";
    echo "<p><strong>Preview Image DB Value:</strong> '{$service['preview_image']}'</p>\n";
    
    $hasImage = !empty($service['preview_image']);
    echo "<p><strong>Has Image:</strong> " . ($hasImage ? 'YES' : 'NO') . "</p>\n";
    
    if ($hasImage) {
        $optimizedUrl = getOptimizedImageUrl($service['preview_image'], 'thumb');
        echo "<p><strong>Generated URL:</strong> {$optimizedUrl}</p>\n";
        echo "<p><strong>Test Image:</strong><br>\n";
        echo "<img src='{$optimizedUrl}' alt='{$service['title']}' style='max-width: 200px; border: 1px solid blue;' onload=\"console.log('Image loaded: {$optimizedUrl}')\" onerror=\"console.log('Image failed: {$optimizedUrl}'); this.style.border='3px solid red';\"></p>\n";
    } else {
        echo "<p><strong>No Image - Will Show Icon</strong></p>\n";
    }
    echo "</div>\n";
}

echo "</body></html>\n";
?>
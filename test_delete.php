<?php
// Test deleteProduct function from seller-manager.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['user_id'] = 44; // Your actual user ID

require_once 'config/database.php';
require_once 'config/seller-manager.php';

try {
    $pdo = DatabaseConfig::getConnection();
    $sellerManager = new SellerManager($pdo);
    
    echo "🔍 Testing SellerManager->deleteProduct() for User ID 44...\n\n";
    
    // Get a template to test with (we'll get a fresh one since we deleted one earlier)
    $stmt = $pdo->prepare('SELECT id, title, seller_id FROM templates WHERE seller_id = ? ORDER BY id ASC LIMIT 1');
    $stmt->execute([44]);
    $template = $stmt->fetch();
    
    if ($template) {
        $testId = $template['id'];
        echo "📋 Testing with Template ID: $testId (Title: {$template['title']})\n\n";
        
        echo "🗑️ Calling sellerManager->deleteProduct($testId, 'template', 44)...\n";
        
        // Call the actual function that's failing
        $result = $sellerManager->deleteProduct(44, 'template', $testId);
        
        echo "📊 Function returned: " . ($result ? 'TRUE (success)' : 'FALSE (failed)') . "\n";
        
        // Check if product still exists
        $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM templates WHERE id = ?');
        $checkStmt->execute([$testId]);
        $count = $checkStmt->fetch()['count'];
        echo "🔍 Product still exists in database: " . ($count > 0 ? 'YES' : 'NO') . "\n";
        
        if (!$result) {
            echo "\n❌ Function returned FALSE - this is why you're getting the error!\n";
            echo "Check the error logs to see what happened inside the function.\n";
        } else {
            echo "\n✅ Function worked correctly!\n";
        }
        
    } else {
        echo "❌ No templates found for User ID 44 (maybe we deleted them all?)\n";
        
        // Let's check services instead
        $stmt = $pdo->prepare('SELECT id, title, seller_id FROM services WHERE seller_id = ? ORDER BY id ASC LIMIT 1');
        $stmt->execute([44]);
        $service = $stmt->fetch();
        
        if ($service) {
            $testId = $service['id'];
            echo "📋 Testing with Service ID: $testId (Title: {$service['title']})\n\n";
            
            echo "🗑️ Calling sellerManager->deleteProduct($testId, 'service', 44)...\n";
            
            $result = $sellerManager->deleteProduct(44, 'service', $testId);
            
            echo "📊 Function returned: " . ($result ? 'TRUE (success)' : 'FALSE (failed)') . "\n";
            
            // Check if product still exists
            $checkStmt = $pdo->prepare('SELECT COUNT(*) as count FROM services WHERE id = ?');
            $checkStmt->execute([$testId]);
            $count = $checkStmt->fetch()['count'];
            echo "🔍 Product still exists in database: " . ($count > 0 ? 'YES' : 'NO') . "\n";
        } else {
            echo "❌ No services found either!\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
<?php
// Simple direct test without including files
session_start();
$_SESSION['user_id'] = 44;

require_once 'config/database.php';
require_once 'config/seller-manager.php';

echo "🔍 Final Test - Simulating exact frontend request...\n\n";

$pdo = DatabaseConfig::getConnection();
$sellerManager = new SellerManager($pdo);

// Find a product to test with
$stmt = $pdo->prepare('SELECT id, title FROM templates WHERE seller_id = ? LIMIT 1');
$stmt->execute([44]);
$product = $stmt->fetch();

if (!$product) {
    $stmt = $pdo->prepare('SELECT id, title FROM services WHERE seller_id = ? LIMIT 1');
    $stmt->execute([44]);
    $product = $stmt->fetch();
    $type = 'service';
} else {
    $type = 'template';
}

if (!$product) {
    echo "❌ No products found for user 44\n";
    exit;
}

echo "📋 Testing with: {$product['title']} (ID: {$product['id']}, Type: $type)\n\n";

// Test the exact same flow as seller-api.php
try {
    echo "Step 1: Authentication check\n";
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated - Please log in to delete products');
    }
    echo "✅ User authenticated: {$_SESSION['user_id']}\n\n";

    echo "Step 2: Input validation\n";
    $input_type = $type;
    $input_id = intval($product['id']);
    
    if (!$input_type) {
        throw new Exception('Product type is required (template or service)');
    }
    
    if (!$input_id) {
        throw new Exception('Product ID is required');
    }
    echo "✅ Input valid: Type='$input_type', ID=$input_id\n\n";

    echo "Step 3: Check product exists and belongs to seller\n";
    $table = $input_type === 'service' ? 'services' : 'templates';
    $checkStmt = $pdo->prepare("SELECT id, title FROM {$table} WHERE id = ? AND seller_id = ?");
    $checkStmt->execute([$input_id, $_SESSION['user_id']]);
    $found_product = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$found_product) {
        throw new Exception('Product not found or you do not have permission to delete it');
    }
    echo "✅ Product found: ID {$found_product['id']}, Title: {$found_product['title']}\n\n";

    echo "Step 4: Call deleteProduct function\n";
    echo "Calling: sellerManager->deleteProduct({$_SESSION['user_id']}, '$input_type', $input_id)\n";
    $result = $sellerManager->deleteProduct($_SESSION['user_id'], $input_type, $input_id);
    echo "Result: " . ($result ? 'TRUE' : 'FALSE') . "\n\n";
    
    if (!$result) {
        throw new Exception('Failed to delete product - Database operation failed');
    }
    
    echo "Step 5: Verify deletion\n";
    $verifyStmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} WHERE id = ?");
    $verifyStmt->execute([$input_id]);
    $count = $verifyStmt->fetch()['count'];
    echo "Product still exists: " . ($count > 0 ? 'YES (ERROR!)' : 'NO (SUCCESS!)') . "\n\n";
    
    echo "🎉 SUCCESS: Product deleted successfully!\n";
    $response = ['success' => true, 'message' => 'Product deleted successfully'];
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    $response = ['success' => false, 'error' => $e->getMessage()];
}

echo "\n📊 Final API Response:\n";
echo json_encode($response) . "\n";
?>
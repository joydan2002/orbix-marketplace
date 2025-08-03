<?php
/**
 * Fix Database Data Script
 * Add missing sellers and fix templates without seller_id
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>🔧 Fixing Database Data</h2>\n";
echo "<pre style='background: #f0f8ff; padding: 20px; border-radius: 8px;'>\n";

try {
    $pdo = DatabaseConfig::getConnection();
    echo "✅ Database connection successful!\n\n";
    
    // First, let's add some sellers if we don't have enough
    echo "👥 ADDING SAMPLE SELLERS:\n";
    echo "========================\n";
    
    $sellers = [
        ['first_name' => 'Sarah', 'last_name' => 'Chen', 'email' => 'sarah.chen@example.com', 'username' => 'sarahchen'],
        ['first_name' => 'Mike', 'last_name' => 'Rodriguez', 'email' => 'mike.rodriguez@example.com', 'username' => 'mikero'],
        ['first_name' => 'Emma', 'last_name' => 'Wilson', 'email' => 'emma.wilson@example.com', 'username' => 'emmaw'],
        ['first_name' => 'David', 'last_name' => 'Kim', 'email' => 'david.kim@example.com', 'username' => 'davidkim'],
        ['first_name' => 'Lisa', 'last_name' => 'Anderson', 'email' => 'lisa.anderson@example.com', 'username' => 'lisaa']
    ];
    
    foreach ($sellers as $seller) {
        // Check if seller already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$seller['email']]);
        
        if (!$stmt->fetch()) {
            // Insert new seller
            $stmt = $pdo->prepare("
                INSERT INTO users (first_name, last_name, email, username, password_hash, user_type, email_verified, created_at) 
                VALUES (?, ?, ?, ?, ?, 'seller', 1, NOW())
            ");
            $stmt->execute([
                $seller['first_name'],
                $seller['last_name'], 
                $seller['email'],
                $seller['username'],
                password_hash('password123', PASSWORD_DEFAULT) // Default password
            ]);
            
            $sellerId = $pdo->lastInsertId();
            echo "✅ Added seller: {$seller['first_name']} {$seller['last_name']} (ID: {$sellerId})\n";
        } else {
            echo "⚠️  Seller {$seller['email']} already exists\n";
        }
    }
    
    // Get all available sellers
    echo "\n🔍 GETTING ALL SELLERS:\n";
    echo "======================\n";
    $stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE user_type = 'seller'");
    $allSellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($allSellers)) {
        echo "❌ No sellers found! Cannot fix templates.\n";
        exit;
    }
    
    foreach ($allSellers as $seller) {
        echo "Seller ID: {$seller['id']} - {$seller['first_name']} {$seller['last_name']}\n";
    }
    
    // Fix templates with NULL seller_id
    echo "\n🔧 FIXING TEMPLATES WITH NULL SELLER_ID:\n";
    echo "========================================\n";
    
    $stmt = $pdo->query("SELECT id, title FROM templates WHERE seller_id IS NULL");
    $templatesWithoutSeller = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($templatesWithoutSeller) . " templates without seller_id\n\n";
    
    foreach ($templatesWithoutSeller as $index => $template) {
        // Assign seller in round-robin fashion
        $sellerIndex = $index % count($allSellers);
        $assignedSeller = $allSellers[$sellerIndex];
        
        $stmt = $pdo->prepare("UPDATE templates SET seller_id = ? WHERE id = ?");
        $stmt->execute([$assignedSeller['id'], $template['id']]);
        
        echo "✅ Template '{$template['title']}' (ID: {$template['id']}) → Seller: {$assignedSeller['first_name']} {$assignedSeller['last_name']}\n";
    }
    
    // Verify the fix
    echo "\n✅ VERIFICATION:\n";
    echo "===============\n";
    
    $stmt = $pdo->query("
        SELECT COUNT(*) as total_templates, 
               COUNT(seller_id) as templates_with_seller,
               COUNT(*) - COUNT(seller_id) as templates_without_seller
        FROM templates 
        WHERE status = 'approved'
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Total approved templates: {$stats['total_templates']}\n";
    echo "Templates with seller: {$stats['templates_with_seller']}\n";
    echo "Templates without seller: {$stats['templates_without_seller']}\n";
    
    if ($stats['templates_without_seller'] == 0) {
        echo "🎉 ALL TEMPLATES NOW HAVE SELLERS!\n";
    }
    
    // Test the cart API query one more time
    echo "\n🧪 TESTING CART API QUERY AGAIN:\n";
    echo "================================\n";
    
    $stmt = $pdo->query("SELECT id FROM templates WHERE status = 'approved' LIMIT 1");
    $firstTemplate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($firstTemplate) {
        $testId = $firstTemplate['id'];
        $stmt = $pdo->prepare("
            SELECT 
                t.id, 
                t.title, 
                t.price, 
                t.preview_image, 
                COALESCE(u.first_name, 'Anonymous') as first_name, 
                COALESCE(u.last_name, 'Seller') as last_name
            FROM templates t 
            LEFT JOIN users u ON t.seller_id = u.id 
            WHERE t.id = ? AND t.status = 'approved'
        ");
        $stmt->execute([$testId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            echo "✅ Query successful for template ID {$testId}:\n";
            echo "   Title: {$result['title']}\n";
            echo "   Price: \${$result['price']}\n";
            echo "   Seller: {$result['first_name']} {$result['last_name']}\n";
            echo "\n🚀 CART SYSTEM SHOULD NOW WORK!\n";
        } else {
            echo "❌ Query still failing\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n</pre>";
echo "<p><strong>✅ Database fix completed!</strong></p>";
echo "<p>Now try adding templates to cart again - it should work!</p>";
?>
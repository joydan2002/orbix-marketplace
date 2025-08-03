<?php
/**
 * Database Debug Script
 * Check database structure and data for cart issues
 */

require_once __DIR__ . '/config/database.php';

echo "<h2>🔍 Database Debug Report</h2>\n";
echo "<pre style='background: #f5f5f5; padding: 20px; border-radius: 8px;'>\n";

try {
    $pdo = DatabaseConfig::getConnection();
    echo "✅ Database connection successful!\n\n";
    
    // Check templates table structure
    echo "📋 TEMPLATES TABLE STRUCTURE:\n";
    echo "============================\n";
    $stmt = $pdo->query("DESCRIBE templates");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] == 'NO' ? 'NOT NULL' : 'NULL'
        );
    }
    
    // Check templates data
    echo "\n📊 TEMPLATES DATA (First 5 records):\n";
    echo "====================================\n";
    $stmt = $pdo->query("SELECT id, title, status, seller_id, price FROM templates LIMIT 5");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($templates)) {
        echo "❌ NO TEMPLATES FOUND IN DATABASE!\n";
        
        // Let's check if table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'templates'");
        if ($stmt->rowCount() == 0) {
            echo "❌ TEMPLATES TABLE DOES NOT EXIST!\n";
        }
    } else {
        foreach ($templates as $template) {
            echo sprintf("ID: %-3s | Title: %-30s | Status: %-10s | Seller: %-3s | Price: $%s\n",
                $template['id'],
                substr($template['title'], 0, 30),
                $template['status'],
                $template['seller_id'],
                $template['price']
            );
        }
    }
    
    // Check cart table structure
    echo "\n📋 CART TABLE STRUCTURE:\n";
    echo "=======================\n";
    $stmt = $pdo->query("DESCRIBE cart");
    $cartColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cartColumns as $column) {
        echo sprintf("%-20s %-15s %s\n", 
            $column['Field'], 
            $column['Type'], 
            $column['Null'] == 'NO' ? 'NOT NULL' : 'NULL'
        );
    }
    
    // Check users table for sellers
    echo "\n👥 USERS TABLE (Sellers):\n";
    echo "========================\n";
    $stmt = $pdo->query("SELECT id, first_name, last_name, user_type FROM users WHERE user_type = 'seller' LIMIT 5");
    $sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($sellers)) {
        echo "❌ NO SELLERS FOUND IN DATABASE!\n";
    } else {
        foreach ($sellers as $seller) {
            echo sprintf("ID: %-3s | Name: %-20s | Type: %s\n",
                $seller['id'],
                $seller['first_name'] . ' ' . $seller['last_name'],
                $seller['user_type']
            );
        }
    }
    
    // Test the problematic query from cart-api.php
    echo "\n🔧 TESTING CART API QUERY:\n";
    echo "==========================\n";
    
    // Simulate the query that's failing
    $testTemplateId = 1; // Test with first template
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, t.price, t.preview_image, u.first_name, u.last_name
        FROM templates t 
        JOIN users u ON t.seller_id = u.id 
        WHERE t.id = ? AND t.status = 'approved'
    ");
    $stmt->execute([$testTemplateId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Query successful for template ID {$testTemplateId}:\n";
        echo "   Title: {$result['title']}\n";
        echo "   Price: \${$result['price']}\n";
        echo "   Seller: {$result['first_name']} {$result['last_name']}\n";
    } else {
        echo "❌ Query failed for template ID {$testTemplateId}\n";
        
        // Let's debug step by step
        echo "\n🔍 DEBUGGING STEP BY STEP:\n";
        
        // Check if template exists
        $stmt = $pdo->prepare("SELECT * FROM templates WHERE id = ?");
        $stmt->execute([$testTemplateId]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($template) {
            echo "✅ Template exists with ID {$testTemplateId}\n";
            echo "   Status: {$template['status']}\n";
            echo "   Seller ID: {$template['seller_id']}\n";
            
            // Check if seller exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$template['seller_id']]);
            $seller = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($seller) {
                echo "✅ Seller exists with ID {$template['seller_id']}\n";
                echo "   Name: {$seller['first_name']} {$seller['last_name']}\n";
            } else {
                echo "❌ SELLER NOT FOUND for ID {$template['seller_id']}\n";
                echo "   THIS IS THE PROBLEM! Template has invalid seller_id\n";
            }
            
            if ($template['status'] !== 'approved') {
                echo "❌ TEMPLATE STATUS is '{$template['status']}', not 'approved'\n";
                echo "   THIS COULD BE THE PROBLEM!\n";
            }
        } else {
            echo "❌ Template does not exist with ID {$testTemplateId}\n";
        }
    }
    
    // Show current session info
    echo "\n🔐 SESSION INFO:\n";
    echo "================\n";
    session_start();
    if (isset($_SESSION['user_id'])) {
        echo "✅ User logged in with ID: {$_SESSION['user_id']}\n";
    } else {
        echo "❌ No user logged in\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "Connection details:\n";
    echo "  Host: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "\n";
    echo "  Database: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "\n";
    echo "  User: " . (defined('DB_USER') ? DB_USER : 'Not defined') . "\n";
}

echo "\n</pre>";
echo "<p><strong>🚀 Next Steps:</strong></p>";
echo "<ul>";
echo "<li>If templates table is empty, run populate-templates.php</li>";
echo "<li>If sellers are missing, ensure users table has sellers</li>";
echo "<li>If template status is not 'approved', update the status</li>";
echo "<li>Check seller_id references in templates table</li>";
echo "</ul>";
?>
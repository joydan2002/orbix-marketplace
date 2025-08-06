<?php
/**
 * Fix Services Seller ID
 * Update all services with NULL seller_id to seller_id = 44
 */

require_once '../../config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔧 FIXING SERVICES SELLER_ID...\n";
    echo "==============================\n\n";
    
    // Check current state
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM services WHERE seller_id IS NULL");
    $nullCount = $stmt->fetch()['count'];
    echo "📊 Services with NULL seller_id: $nullCount\n";
    
    // Update all NULL seller_id services to seller_id = 44
    $stmt = $pdo->prepare("UPDATE services SET seller_id = ? WHERE seller_id IS NULL OR seller_id = 26");
    $result = $stmt->execute([44]);
    
    if ($result) {
        $affectedRows = $stmt->rowCount();
        echo "✅ Updated $affectedRows services to seller_id = 44\n";
        
        // Verify the update
        $stmt = $pdo->query("SELECT seller_id, COUNT(*) as count FROM services GROUP BY seller_id");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\n📋 SERVICES AFTER UPDATE:\n";
        foreach ($results as $row) {
            echo "   Seller ID: {$row['seller_id']} - Services: {$row['count']}\n";
        }
        
        echo "\n🎉 SERVICES SELLER_ID FIXED!\n";
        echo "Now seller 44 should see all services in dashboard.\n\n";
        
        echo "🔄 NEXT STEP: Refresh seller dashboard:\n";
        echo "   http://localhost/orbix/public/seller-channel.php\n";
        
    } else {
        echo "❌ Failed to update services\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
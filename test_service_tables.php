<?php
// Check service tables in database
require_once 'config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "🔍 Checking service tables in database...\n\n";
    
    // Check what service tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE '%service%'");
    $serviceTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Service tables found:\n";
    foreach ($serviceTables as $table) {
        echo "  - $table\n";
    }
    echo "\n";
    
    // Check services table structure if exists
    if (in_array('services', $serviceTables)) {
        echo "🔍 Structure of 'services' table:\n";
        $stmt = $pdo->query("DESCRIBE services");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        
        // Check if user 44 has services in this table
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM services WHERE seller_id = ?");
        $stmt->execute([44]);
        $count = $stmt->fetch()['count'];
        echo "  📊 User 44 has $count services in 'services' table\n\n";
    }
    
    // Check seller_services table structure if exists  
    if (in_array('seller_services', $serviceTables)) {
        echo "🔍 Structure of 'seller_services' table:\n";
        $stmt = $pdo->query("DESCRIBE seller_services");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        
        // Check if user 44 has services in this table
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM seller_services WHERE seller_id = ?");
        $stmt->execute([44]);
        $count = $stmt->fetch()['count'];
        echo "  📊 User 44 has $count services in 'seller_services' table\n\n";
    }
    
    // Show actual service data for user 44
    echo "📋 Actual service data for user 44:\n";
    
    foreach ($serviceTables as $table) {
        echo "\n--- From $table ---\n";
        try {
            $stmt = $pdo->prepare("SELECT id, title, status FROM $table WHERE seller_id = ? LIMIT 5");
            $stmt->execute([44]);
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($services)) {
                echo "  No services found\n";
            } else {
                foreach ($services as $service) {
                    echo "  ID: {$service['id']}, Title: {$service['title']}, Status: {$service['status']}\n";
                }
            }
        } catch (Exception $e) {
            echo "  Error: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}
?>
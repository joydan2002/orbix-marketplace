<?php
// Debug file for seller-channel.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...<br>";

try {
    echo "1. Checking database connection...<br>";
    require_once '../config/database.php';
    echo "Database config loaded successfully<br>";
    
    echo "2. Checking service-manager...<br>";
    require_once '../config/service-manager.php';
    echo "Service manager loaded successfully<br>";
    
    echo "3. Checking seller-manager...<br>";
    require_once '../config/seller-manager.php';
    echo "Seller manager loaded successfully<br>";
    
    echo "4. Testing database query...<br>";
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE user_type = 'seller'");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "Found " . $result['count'] . " sellers in database<br>";
    
    echo "5. All checks passed!<br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . "<br>";
    echo "Line: " . $e->getLine() . "<br>";
}
?>
<?php
/**
 * Database Connection Test for Railway Deployment
 */

echo "=== Railway Environment Detection ===\n";
echo "RAILWAY_ENVIRONMENT: " . ($_ENV['RAILWAY_ENVIRONMENT'] ?? getenv('RAILWAY_ENVIRONMENT') ?? 'NOT SET') . "\n";
echo "Socket file exists: " . (file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock') ? 'YES (Local)' : 'NO (Railway)') . "\n";

// Test environment variables
echo "\n=== Environment Variables Test ===\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'NOT SET') . "\n";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'NOT SET') . "\n";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'NOT SET') . "\n";
echo "DB_PASSWORD: " . (($_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')) ? 'SET' : 'NOT SET') . "\n";
echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 'NOT SET') . "\n";
echo "ENVIRONMENT: " . ($_ENV['ENVIRONMENT'] ?? getenv('ENVIRONMENT') ?? 'NOT SET') . "\n";

echo "\n=== Railway MySQL Variables Test ===\n";
echo "MYSQLHOST: " . ($_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'NOT SET') . "\n";
echo "MYSQLDATABASE: " . ($_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'NOT SET') . "\n";
echo "MYSQLUSER: " . ($_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'NOT SET') . "\n";
echo "MYSQLPASSWORD: " . (($_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD')) ? 'SET' : 'NOT SET') . "\n";
echo "MYSQLPORT: " . ($_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? 'NOT SET') . "\n";

// Test direct PDO connection with external Railway connection
echo "\n=== Direct Railway Connection Test ===\n";
$railwayHost = 'interchange.proxy.rlwy.net';
$railwayPort = '32514';
$railwayUser = 'root';
$railwayPass = 'lvegkEtkrgHuUXRXtcdNmgWxNnOioLXc';
$railwayDb = 'railway';

echo "Using Railway external connection:\n";
echo "Host: $railwayHost\n";
echo "Port: $railwayPort\n";
echo "Database: $railwayDb\n";
echo "User: $railwayUser\n";

try {
    $dsn = "mysql:host=$railwayHost;port=$railwayPort;dbname=$railwayDb;charset=utf8mb4";
    echo "DSN: $dsn\n";
    
    $pdo = new PDO($dsn, $railwayUser, $railwayPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ Direct Railway connection successful!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = '$railwayDb'");
    $result = $stmt->fetch();
    echo "✅ Found {$result['table_count']} tables in Railway database\n";
    
} catch (Exception $e) {
    echo "❌ Direct Railway connection failed: " . $e->getMessage() . "\n";
}

// Test database connection using our config
echo "\n=== Database Config Test ===\n";
require_once __DIR__ . '/config/database.php';

try {
    $pdo = DatabaseConfig::getConnection();
    echo "✅ Database connection successful!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE()");
    $result = $stmt->fetch();
    echo "✅ Found {$result['table_count']} tables in database\n";
    
    // Test specific table
    $stmt = $pdo->query("SELECT COUNT(*) as users FROM users LIMIT 1");
    $result = $stmt->fetch();
    echo "✅ Users table accessible, found {$result['users']} users\n";
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "❌ Error details: " . $e->getFile() . " line " . $e->getLine() . "\n";
}

echo "\n=== PHP Environment ===\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PDO Available: " . (extension_loaded('pdo') ? 'YES' : 'NO') . "\n";
echo "PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'YES' : 'NO') . "\n";
?>

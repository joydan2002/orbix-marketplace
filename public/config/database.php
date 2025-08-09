<?php
/**
 * Database Configuration
 * Handles database connections and app configuration
 * Now supports both local development and production deployment
 */

// Load Railway environment config if on Railway
if (isset($_ENV['RAILWAY_ENVIRONMENT']) || getenv('RAILWAY_ENVIRONMENT') || 
    !file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock')) {
    require_once __DIR__ . '/railway-env.php';
}

// Load production config if available
if (file_exists(__DIR__ . '/production-config.php')) {
    require_once __DIR__ . '/production-config.php';
}

if (!class_exists('DatabaseConfig')) {
    class DatabaseConfig {
        /**
         * Get database configuration based on environment
         */
        /**
         * Get database configuration based on environment
         */
    private static function getDbConfig() {
        // Check Railway provided variables first
        $railwayHost = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST');
        $railwayDb = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE');
        $railwayUser = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER');
        $railwayPass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD');
        $railwayPort = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT');
        
        if ($railwayHost && $railwayDb && $railwayUser) {
            // Railway production environment
            return [
                'host' => $railwayHost,
                'dbname' => $railwayDb,
                'username' => $railwayUser,
                'password' => $railwayPass ?? '',
                'port' => $railwayPort ?? '3306',
                'charset' => 'utf8mb4'
            ];
        }
        
        // Check custom environment variables (backup)
        $envHost = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
        $envName = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
        $envUser = $_ENV['DB_USER'] ?? getenv('DB_USER');
        $envPass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
        $envPort = $_ENV['DB_PORT'] ?? getenv('DB_PORT');
        
        if ($envHost && $envName && $envUser) {
            // Custom environment variables
            return [
                'host' => $envHost,
                'dbname' => $envName,
                'username' => $envUser,
                'password' => $envPass ?? '',
                'port' => $envPort ?? '3306',
                'charset' => 'utf8mb4'
            ];
        }
        
        // Check if we're in production using legacy config
        if (class_exists('ProductionConfig') && ProductionConfig::isProduction()) {
            return ProductionConfig::getDatabaseConfig();
        }
        
        // Local development configuration
        return [
            'host' => 'localhost',
            'dbname' => 'orbix_market',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'socket' => '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock'
        ];
    }
    
    // App Configuration
    const APP_NAME = 'Orbix Market';
    const APP_VERSION = '2.0.0';
    
    public static function getAppUrl() {
        if (class_exists('ProductionConfig') && ProductionConfig::isProduction()) {
            return $_ENV['APP_URL'] ?? getenv('APP_URL') ?? 'https://your-app.railway.app';
        }
        return 'http://localhost/orbix';
    }
    
    private static $connection = null;
    
    /**
     * Get database connection
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $config = self::getDbConfig();
                
                // Build DSN based on environment
                if (isset($config['socket']) && file_exists($config['socket'])) {
                    // Local development with socket
                    $dsn = "mysql:unix_socket={$config['socket']};dbname={$config['dbname']};charset={$config['charset']}";
                } else {
                    // Production or standard connection
                    $port = isset($config['port']) ? ";port={$config['port']}" : '';
                    $dsn = "mysql:host={$config['host']}{$port};dbname={$config['dbname']};charset={$config['charset']}";
                }
                
                self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die("Database connection failed. Please check your configuration.");
            }
        }
        return self::$connection;
    }
    
    /**
     * Get app configuration
     */
    public static function getAppConfig() {
        return [
            'name' => self::APP_NAME,
            'url' => self::getAppUrl(),
            'version' => self::APP_VERSION
        ];
    }
    
    /**
     * Test database connection
     */
    public static function testConnection() {
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->query("SELECT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
}
?>
<?php
/**
 * Database Configuration
 * Handles database connections and app configuration
 */

class DatabaseConfig {
    // Database Configuration
    const DB_HOST = 'localhost';
    const DB_NAME = 'orbix_market';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // App Configuration
    const APP_NAME = 'Orbix Market';
    const APP_URL = 'http://localhost/orbix';
    const APP_VERSION = '2.0.0';
    
    private static $connection = null;
    
    /**
     * Get database connection
     */
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . self::DB_HOST . ";dbname=" . self::DB_NAME . ";charset=" . self::DB_CHARSET;
                self::$connection = new PDO($dsn, self::DB_USER, self::DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                throw new Exception("Database connection failed");
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
            'url' => self::APP_URL,
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
?>
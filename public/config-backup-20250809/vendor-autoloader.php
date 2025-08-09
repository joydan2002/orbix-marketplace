<?php
/**
 * Smart Vendor Autoloader
 * Automatically detects vendor directory location for different environments
 */

class VendorAutoloader {
    
    private static $vendorPath = null;
    
    /**
     * Get the correct vendor path based on current environment
     */
    public static function getVendorPath() {
        if (self::$vendorPath !== null) {
            return self::$vendorPath;
        }
        
        // Possible vendor locations in order of preference
        $possiblePaths = [
            __DIR__ . '/../../vendor',           // From public/config/ to root/vendor/
            __DIR__ . '/../vendor',              // From config/ to root/vendor/
            __DIR__ . '/vendor',                 // Same directory
            dirname(dirname(__DIR__)) . '/vendor', // Railway: from /app/public/config to /app/vendor
            '/app/vendor',                       // Railway absolute path
        ];
        
        foreach ($possiblePaths as $path) {
            if (is_dir($path) && file_exists($path . '/autoload.php')) {
                self::$vendorPath = $path;
                return $path;
            }
        }
        
        // Fallback - try composer autoload
        $autoloadPaths = [
            __DIR__ . '/../../vendor/autoload.php',
            __DIR__ . '/../vendor/autoload.php',
            __DIR__ . '/vendor/autoload.php',
            '/app/vendor/autoload.php',
        ];
        
        foreach ($autoloadPaths as $autoloadPath) {
            if (file_exists($autoloadPath)) {
                self::$vendorPath = dirname($autoloadPath);
                return dirname($autoloadPath);
            }
        }
        
        throw new Exception('Vendor directory not found');
    }
    
    /**
     * Require PHPMailer classes
     */
    public static function requirePHPMailer() {
        $vendorPath = self::getVendorPath();
        
        $requiredFiles = [
            '/phpmailer/src/PHPMailer.php',      // Direct phpmailer installation
            '/phpmailer/src/SMTP.php',
            '/phpmailer/src/Exception.php'
        ];
        
        foreach ($requiredFiles as $file) {
            $fullPath = $vendorPath . $file;
            if (file_exists($fullPath)) {
                require_once $fullPath;
            } else {
                // Try composer structure
                $composerPath = $vendorPath . '/phpmailer/phpmailer/src' . basename($file);
                if (file_exists($composerPath)) {
                    require_once $composerPath;
                } else {
                    throw new Exception("PHPMailer file not found: $file (tried $fullPath and $composerPath)");
                }
            }
        }
    }
    
    /**
     * Initialize composer autoloader if available
     */
    public static function initComposer() {
        try {
            $vendorPath = self::getVendorPath();
            $autoloadPath = $vendorPath . '/autoload.php';
            
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
                return true;
            }
        } catch (Exception $e) {
            // Autoloader not available, continue without it
        }
        
        return false;
    }
}
?>

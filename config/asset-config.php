<?php
/**
 * Assets URL Configuration
 * Automatically detects environment and provides correct asset paths
 */

class AssetConfig {
    
    /**
     * Get the base path for assets based on current environment
     */
    public static function getAssetPath($asset = '') {
        // Check if we're running from public directory (Railway deployment)
        $isFromPublic = (basename(dirname($_SERVER['SCRIPT_NAME'])) === 'public' || 
                        $_SERVER['SCRIPT_NAME'] === '/index.php' ||
                        strpos($_SERVER['SCRIPT_NAME'], '/public/') === false);
        
        // If running from public directory (Railway), use relative path
        if ($isFromPublic) {
            $basePath = 'assets/';
        } else {
            // If running from root directory (local), use public/assets/
            $basePath = 'public/assets/';
        }
        
        return $basePath . $asset;
    }
    
    /**
     * Get CSS file path
     */
    public static function getCssPath($filename) {
        return self::getAssetPath('css/' . $filename);
    }
    
    /**
     * Get JS file path
     */
    public static function getJsPath($filename) {
        return self::getAssetPath('js/' . $filename);
    }
    
    /**
     * Get image file path
     */
    public static function getImagePath($filename) {
        return self::getAssetPath('images/' . $filename);
    }
    
    /**
     * Get default image path with fallback
     */
    public static function getDefaultImage($type = 'template') {
        $defaultImages = [
            'template' => 'default-template.jpg',
            'service' => 'default-service.jpg',
            'avatar' => 'default-avatar.png'
        ];
        
        $filename = $defaultImages[$type] ?? $defaultImages['template'];
        return self::getImagePath($filename);
    }
}
?>

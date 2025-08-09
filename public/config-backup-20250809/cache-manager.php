<?php
/**
 * Cache Manager
 * Handles caching for improved performance
 */

class CacheManager {
    private static $cacheDir = null;
    
    /**
     * Initialize cache directory
     */
    private static function initCacheDir() {
        if (self::$cacheDir === null) {
            self::$cacheDir = __DIR__ . '/../cache/';
            if (!is_dir(self::$cacheDir)) {
                mkdir(self::$cacheDir, 0755, true);
            }
        }
        return self::$cacheDir;
    }
    
    /**
     * Get cached data
     */
    public static function getCache($key, $maxAge = 300) {
        $cacheFile = self::initCacheDir() . md5($key) . '.cache';
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        $cacheData = json_decode(file_get_contents($cacheFile), true);
        
        if (!$cacheData || (time() - $cacheData['timestamp']) > $maxAge) {
            unlink($cacheFile);
            return null;
        }
        
        return $cacheData;
    }
    
    /**
     * Set cache data
     */
    public static function setCache($key, $data, $metadata = []) {
        $cacheFile = self::initCacheDir() . md5($key) . '.cache';
        
        $cacheData = [
            'timestamp' => time(),
            'data' => $data,
            'metadata' => $metadata
        ];
        
        file_put_contents($cacheFile, json_encode($cacheData));
    }
    
    /**
     * Clear cache
     */
    public static function clearCache($key = null) {
        $cacheDir = self::initCacheDir();
        
        if ($key !== null) {
            $cacheFile = $cacheDir . md5($key) . '.cache';
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        } else {
            // Clear all cache files
            $files = glob($cacheDir . '*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
}
?>
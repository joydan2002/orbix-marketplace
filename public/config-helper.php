<?php
/**
 * Config Path Helper for Railway Deployment
 * Provides consistent path resolution for config files
 */

/**
 * Get config file path with fallback for different deployment environments
 */
function getConfigPath($filename) {
    // Possible config locations in order of preference
    $possiblePaths = [
        __DIR__ . '/../config/' . $filename,           // Local development
        __DIR__ . '/../../config/' . $filename,       // From includes/ or api/ subdirs  
        '/app/config/' . $filename,                    // Railway absolute path
        dirname(dirname(__DIR__)) . '/config/' . $filename, // Alternative relative
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            return $path;
        }
    }
    
    // If nothing found, return the most likely path and let it error
    return __DIR__ . '/../config/' . $filename;
}

/**
 * Safely require config file with path resolution
 */
function requireConfig($filename) {
    $path = getConfigPath($filename);
    require_once $path;
}
?>

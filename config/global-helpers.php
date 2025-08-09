<?php
/**
 * Global Utility Functions for the Orbix Marketplace
 * Provides safe formatting and common utility functions
 */

if (!function_exists('safeNumberFormat')) {
    /**
     * Safely format numbers with null handling
     */
    function safeNumberFormat($value, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',') {
        return number_format(floatval($value ?? 0), $decimals, $decimal_separator, $thousands_separator);
    }
}

if (!function_exists('safeCurrencyFormat')) {
    /**
     * Safely format currency values
     */
    function safeCurrencyFormat($value, $currency = '$', $decimals = 2) {
        return $currency . safeNumberFormat($value, $decimals);
    }
}

if (!function_exists('safeRatingFormat')) {
    /**
     * Safely format rating values with 1 decimal
     */
    function safeRatingFormat($value) {
        $rating = floatval($value ?? 0);
        return number_format($rating, 1);
    }
}

if (!function_exists('safePercentFormat')) {
    /**
     * Safely format percentage values
     */
    function safePercentFormat($value, $decimals = 1) {
        return safeNumberFormat($value, $decimals) . '%';
    }
}

if (!function_exists('escapeOutput')) {
    /**
     * Safely escape output for HTML
     */
    function escapeOutput($value, $default = '') {
        return htmlspecialchars($value ?? $default, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatDate')) {
    /**
     * Safely format dates
     */
    function formatDate($date, $format = 'M j, Y') {
        if (!$date) return '';
        try {
            return date($format, strtotime($date));
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('safeArrayValue')) {
    /**
     * Safely get array value with default
     */
    function safeArrayValue($array, $key, $default = null) {
        return $array[$key] ?? $default;
    }
}

if (!function_exists('generateStars')) {
    /**
     * Generate star rating HTML
     */
    function generateStars($rating, $maxStars = 5, $size = 'text-sm') {
        $rating = floatval($rating ?? 0);
        $stars = '';
        
        for ($i = 1; $i <= $maxStars; $i++) {
            $class = $i <= floor($rating) ? 'ri-star-fill' : 'ri-star-line';
            $stars .= "<i class=\"{$class} {$size}\"></i>";
        }
        
        return $stars;
    }
}

if (!function_exists('truncateText')) {
    /**
     * Safely truncate text with ellipsis
     */
    function truncateText($text, $length = 100, $suffix = '...') {
        $text = strip_tags($text ?? '');
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('validateRequired')) {
    /**
     * Check if required array keys exist and are not empty
     */
    function validateRequired($array, $keys) {
        foreach ($keys as $key) {
            if (!isset($array[$key]) || empty($array[$key])) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('sanitizeInput')) {
    /**
     * Sanitize user input
     */
    function sanitizeInput($input) {
        return trim(htmlspecialchars($input ?? '', ENT_QUOTES, 'UTF-8'));
    }
}

if (!function_exists('isValidEmail')) {
    /**
     * Validate email address
     */
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('generateUniqueId')) {
    /**
     * Generate unique ID for various purposes
     */
    function generateUniqueId($prefix = '') {
        return $prefix . uniqid() . bin2hex(random_bytes(4));
    }
}

if (!function_exists('debugLog')) {
    /**
     * Safe debug logging
     */
    function debugLog($message, $context = []) {
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message);
        }
        
        $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
        if (!empty($context)) {
            $logMessage .= ' | Context: ' . json_encode($context);
        }
        
        error_log($logMessage);
    }
}

if (!function_exists('isProduction')) {
    /**
     * Check if running in production environment
     */
    function isProduction() {
        return isset($_ENV['RAILWAY_ENVIRONMENT']) || 
               (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false);
    }
}

if (!function_exists('getAssetUrl')) {
    /**
     * Get proper asset URL based on environment
     */
    function getAssetUrl($path) {
        $baseUrl = isProduction() ? 'https://orbix-marketplace-production.up.railway.app' : '';
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

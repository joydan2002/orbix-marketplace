<?php
/**
 * Global Bootstrap File
 * Loads essential utilities and helpers for the entire application
 */

// Load global helpers first
require_once __DIR__ . '/global-helpers.php';

// Set error reporting based on environment
if (isProduction()) {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Set timezone
date_default_timezone_set('UTC');

// Set UTF-8 encoding
mb_internal_encoding('UTF-8');

// Start output buffering
if (!ob_get_level()) {
    ob_start();
}

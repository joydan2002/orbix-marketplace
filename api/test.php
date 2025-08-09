<?php
/**
 * API Test Endpoint
 * Simple test to verify API routing is working
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

echo json_encode([
    'success' => true,
    'message' => 'API routing is working!',
    'timestamp' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['HTTP_HOST'] ?? 'unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown'
]);
?>

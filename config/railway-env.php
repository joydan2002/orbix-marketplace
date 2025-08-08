<?php
/**
 * Railway Production Database Config
 * Direct connection using external Railway MySQL
 */

// Railway MySQL External Connection
$_ENV['RAILWAY_DB_HOST'] = 'interchange.proxy.rlwy.net';
$_ENV['RAILWAY_DB_PORT'] = '32514';
$_ENV['RAILWAY_DB_USER'] = 'root';
$_ENV['RAILWAY_DB_PASSWORD'] = 'lvegkEtkrgHuUXRXtcdNmgWxNnOioLXc';
$_ENV['RAILWAY_DB_NAME'] = 'railway';

// Set standard environment variables
$_ENV['DB_HOST'] = $_ENV['RAILWAY_DB_HOST'];
$_ENV['DB_PORT'] = $_ENV['RAILWAY_DB_PORT'];
$_ENV['DB_USER'] = $_ENV['RAILWAY_DB_USER'];
$_ENV['DB_PASSWORD'] = $_ENV['RAILWAY_DB_PASSWORD'];
$_ENV['DB_NAME'] = $_ENV['RAILWAY_DB_NAME'];
$_ENV['ENVIRONMENT'] = 'production';

// Also set MySQL variables for compatibility
$_ENV['MYSQLHOST'] = $_ENV['RAILWAY_DB_HOST'];
$_ENV['MYSQLPORT'] = $_ENV['RAILWAY_DB_PORT'];
$_ENV['MYSQLUSER'] = $_ENV['RAILWAY_DB_USER'];
$_ENV['MYSQLPASSWORD'] = $_ENV['RAILWAY_DB_PASSWORD'];
$_ENV['MYSQLDATABASE'] = $_ENV['RAILWAY_DB_NAME'];
?>

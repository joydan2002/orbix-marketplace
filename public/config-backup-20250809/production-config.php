<?php
/**
 * Production Environment Configuration
 * For deployment on Railway/Render/other platforms
 */

// Production database configuration
class ProductionConfig {
    public static function getDatabaseConfig() {
        return [
            'host' => $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost',
            'dbname' => $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'orbix_market',
            'username' => $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? '',
            'charset' => 'utf8mb4'
        ];
    }
    
    public static function getCloudinaryConfig() {
        return [
            'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME'),
            'api_key' => $_ENV['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY'),
            'api_secret' => $_ENV['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET'),
            'upload_preset' => $_ENV['CLOUDINARY_UPLOAD_PRESET'] ?? getenv('CLOUDINARY_UPLOAD_PRESET') ?? 'orbix_uploads'
        ];
    }
    
    public static function getEmailConfig() {
        return [
            'smtp_host' => $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST') ?? 'smtp.gmail.com',
            'smtp_port' => $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT') ?? 587,
            'smtp_username' => $_ENV['SMTP_USERNAME'] ?? getenv('SMTP_USERNAME'),
            'smtp_password' => $_ENV['SMTP_PASSWORD'] ?? getenv('SMTP_PASSWORD'),
            'from_email' => $_ENV['FROM_EMAIL'] ?? getenv('FROM_EMAIL') ?? 'noreply@orbix.com',
            'from_name' => $_ENV['FROM_NAME'] ?? getenv('FROM_NAME') ?? 'Orbix Market'
        ];
    }
    
    public static function isProduction() {
        return ($_ENV['ENVIRONMENT'] ?? getenv('ENVIRONMENT')) === 'production';
    }
}

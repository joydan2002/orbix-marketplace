<?php
/**
 * Application Configuration
 * Centralized configuration for Orbix Market
 */

class AppConfig {
    // Email Configuration
    const EMAIL_VERIFICATION_REQUIRED = true; // Bắt buộc xác thực email
    const EMAIL_FROM_ADDRESS = 'noreply@orbixmarket.com';
    const EMAIL_FROM_NAME = 'Orbix Market';
    
    // Development Email Settings (for localhost)
    const DEV_EMAIL_METHOD = 'file'; // 'file', 'smtp', or 'mailhog'
    const DEV_EMAIL_LOG_PATH = '../logs/emails/';
    
    // SMTP Settings (for production or development SMTP)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = ''; // Your email
    const SMTP_PASSWORD = ''; // Your app password
    const SMTP_ENCRYPTION = 'tls';
    
    // Application Settings
    const APP_NAME = 'Orbix Market';
    const APP_URL = 'http://localhost/orbix/public/';
    const VERIFICATION_TOKEN_EXPIRY = 24; // hours
    
    /**
     * Get email configuration based on environment
     */
    public static function getEmailConfig() {
        $isDevelopment = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
                         strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);
        
        return [
            'is_development' => $isDevelopment,
            'verification_required' => self::EMAIL_VERIFICATION_REQUIRED,
            'method' => $isDevelopment ? self::DEV_EMAIL_METHOD : 'smtp',
            'from_email' => self::EMAIL_FROM_ADDRESS,
            'from_name' => self::EMAIL_FROM_NAME,
            'log_path' => self::DEV_EMAIL_LOG_PATH
        ];
    }
    
    /**
     * Get verification link
     */
    public static function getVerificationLink($token) {
        return self::APP_URL . "auth.php?action=verify&token=" . $token;
    }
}
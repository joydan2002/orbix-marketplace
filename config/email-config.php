<?php
/**
 * Email Configuration Settings
 */

class EmailConfig {
    // Development settings - TẮT việc lưu vào file để gửi email thật
    const DEV_EMAIL_TO_FILE = false; // Đổi thành false để gửi email thật
    const DEV_EMAIL_LOG_PATH = '../logs/emails/'; // Path to save email files
    
    // SMTP Settings - Cấu hình Gmail SMTP (MIỄN PHÍ)
    const SMTP_HOST = 'smtp.gmail.com';
    const SMTP_PORT = 587;
    const SMTP_USERNAME = 'changntbc00132@fpt.edu.vn'; // THAY ĐỔI: Email Gmail của bạn
    const SMTP_PASSWORD = 'oisd qdzu yxjw uybq'; // THAY ĐỔI: App Password của Gmail
    const SMTP_FROM_EMAIL = 'changntbc00132@fpt.edu.vn'; // THAY ĐỔI: Email Gmail của bạn
    const SMTP_FROM_NAME = 'Orbix Market';
    
    // General settings
    const ALWAYS_REQUIRE_VERIFICATION = true; // Force email verification even in development
    
    /**
     * Check if we're in development environment
     */
    public static function isDevelopment() {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return strpos($host, 'localhost') !== false || 
               strpos($host, '127.0.0.1') !== false ||
               strpos($host, '.local') !== false;
    }
    
    /**
     * Get base URL for the application
     */
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        
        // Remove /public from path if present
        $path = str_replace('/public', '', $path);
        
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Get email log directory path
     */
    public static function getEmailLogPath() {
        return __DIR__ . '/' . self::DEV_EMAIL_LOG_PATH;
    }
    
    // Getter methods for SMTP configuration
    public static function getSmtpHost() {
        return self::SMTP_HOST;
    }
    
    public static function getSmtpPort() {
        return self::SMTP_PORT;
    }
    
    public static function getSmtpUsername() {
        return self::SMTP_USERNAME;
    }
    
    public static function getSmtpPassword() {
        return self::SMTP_PASSWORD;
    }
    
    public static function getSmtpSecure() {
        return 'tls';
    }
    
    public static function getFromEmail() {
        return self::SMTP_FROM_EMAIL;
    }
    
    public static function getFromName() {
        return self::SMTP_FROM_NAME;
    }
    
    public static function getReplyToEmail() {
        return self::SMTP_FROM_EMAIL;
    }
}
?>
<?php
/**
 * Email Service for handling all email operations
 */

require_once 'email-config.php';
require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';
require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    /**
     * Send verification email to user
     */
    public function sendVerificationEmail($email, $firstName, $verificationToken) {
        $baseUrl = EmailConfig::getBaseUrl();
        $verificationLink = $baseUrl . '/auth.php?action=verify&token=' . $verificationToken;
        
        $subject = 'Verify Your Orbix Market Account';
        $htmlBody = $this->getVerificationEmailTemplate($firstName, $verificationLink);
        $textBody = strip_tags($htmlBody);
        
        // Kiểm tra cấu hình: nếu DEV_EMAIL_TO_FILE = false thì gửi email thật
        if (EmailConfig::isDevelopment() && EmailConfig::DEV_EMAIL_TO_FILE) {
            // Development: Save to file
            $result = $this->saveEmailToFile($email, $subject, $htmlBody);
            $result['verification_link'] = $verificationLink;
            $result['method'] = 'file';
            return $result;
        } else {
            // Production OR Development với DEV_EMAIL_TO_FILE = false: Send real email
            return $this->sendSMTPEmail($email, $subject, $htmlBody, $textBody);
        }
    }
    
    /**
     * Save email to file (for development)
     */
    private function saveEmailToFile($to, $subject, $body) {
        try {
            // Create logs/emails directory if it doesn't exist
            $logDir = __DIR__ . '/../logs/emails';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            
            // Create filename with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "email_{$timestamp}_{$to}.html";
            $filepath = $logDir . '/' . $filename;
            
            // Create email content
            $emailContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>{$subject}</title>
</head>
<body>
    <div style='background: #f4f4f4; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px;'>
            <h2 style='color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;'>Development Email Log</h2>
            <p><strong>To:</strong> {$to}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>
            <hr style='margin: 20px 0;'>
            <div>
                {$body}
            </div>
        </div>
    </div>
</body>
</html>";
            
            // Save to file
            if (file_put_contents($filepath, $emailContent)) {
                return [
                    'success' => true,
                    'message' => 'Email saved to file successfully',
                    'file_path' => $filepath,
                    'to' => $to,
                    'subject' => $subject
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to save email to file'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error saving email to file: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send email via SMTP
     */
    private function sendSMTPEmail($to, $subject, $htmlBody, $textBody = null) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = EmailConfig::getSmtpHost();
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::getSmtpUsername();
            $mail->Password = EmailConfig::getSmtpPassword();
            $mail->SMTPSecure = EmailConfig::getSmtpSecure();
            $mail->Port = EmailConfig::getSmtpPort();
            
            // Recipients
            $mail->setFrom(EmailConfig::getFromEmail(), EmailConfig::getFromName());
            $mail->addAddress($to);
            $mail->addReplyTo(EmailConfig::getReplyToEmail(), EmailConfig::getFromName());
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            if ($textBody) {
                $mail->AltBody = $textBody;
            }
            
            $mail->send();
            
            return [
                'success' => true,
                'message' => 'Email sent successfully via SMTP',
                'method' => 'smtp',
                'to' => $to,
                'subject' => $subject
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SMTP Error: ' . $mail->ErrorInfo,
                'error_details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get email verification template
     */
    private function getVerificationEmailTemplate($firstName, $verificationLink) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Verify Your Orbix Market Account</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 0;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                .container {
                    max-width: 600px;
                    margin: 20px auto;
                    background: white;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
                    color: white;
                    padding: 40px 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 28px;
                    font-weight: 600;
                }
                .content {
                    padding: 40px 30px;
                    text-align: center;
                }
                .welcome-text {
                    font-size: 18px;
                    color: #333;
                    margin-bottom: 30px;
                }
                .verify-btn {
                    display: inline-block;
                    background: linear-gradient(135deg, #FF6B35 0%, #F7931E 100%);
                    color: white;
                    text-decoration: none;
                    padding: 15px 40px;
                    border-radius: 50px;
                    font-weight: 600;
                    font-size: 16px;
                    margin: 20px 0;
                    transition: transform 0.2s;
                }
                .verify-btn:hover {
                    transform: translateY(-2px);
                }
                .footer {
                    background: #f8f9fa;
                    padding: 30px;
                    text-align: center;
                    color: #666;
                    font-size: 14px;
                }
                .logo {
                    width: 60px;
                    height: 60px;
                    margin: 0 auto 20px;
                    background: white;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                }
  
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🚀</div>
                    <h1>Welcome to Orbix Market!</h1>
                </div>
                <div class='content'>
                    <div class='welcome-text'>
                        <h2>Hi {$firstName}! 👋</h2>
                        <p>Thank you for joining Orbix Market! We're excited to have you on board.</p>
                        <p>Please verify your email address to complete your registration and start exploring our amazing templates.</p>
                    </div>
                    
                    <a href='{$verificationLink}' class='verify-btn'>
                        ✅ Verify My Email
                    </a>
                    
                    <div style='margin-top: 30px; color: #666; font-size: 14px;'>
                        <p>If the button doesn't work, copy and paste this link into your browser:</p>
                        <p style='word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 5px;'>
                            {$verificationLink}
                        </p>
                    </div>
                </div>
                <div class='footer'>
                    <p><strong>Orbix Market</strong> - Your Premium Template Marketplace</p>
                    <p>This verification link will expire in 24 hours.</p>
                    <p>If you didn't create this account, please ignore this email.</p>
                </div>
            </div>
        </body>
        </html>";
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($email, $firstName, $resetToken) {
        $baseUrl = EmailConfig::getBaseUrl();
        $resetLink = $baseUrl . '/auth.php?action=reset_password&token=' . $resetToken;
        
        $subject = 'Reset Your Orbix Market Password';
        $htmlBody = $this->getPasswordResetEmailTemplate($firstName, $resetLink);
        $textBody = strip_tags($htmlBody);
        
        if (EmailConfig::isDevelopment() && EmailConfig::DEV_EMAIL_TO_FILE) {
            $result = $this->saveEmailToFile($email, $subject, $htmlBody);
            $result['reset_link'] = $resetLink;
            $result['method'] = 'file';
            return $result;
        } else {
            return $this->sendSMTPEmail($email, $subject, $htmlBody, $textBody);
        }
    }
    
    /**
     * Get password reset email template
     */
    private function getPasswordResetEmailTemplate($firstName, $resetLink) {
        return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reset Your Password - Orbix Market</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; }
        .header { background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
        .content { padding: 40px 30px; }
        .welcome { font-size: 18px; color: #333; margin-bottom: 20px; }
        .message { color: #666; line-height: 1.6; margin-bottom: 30px; }
        .btn-container { text-align: center; margin: 30px 0; }
        .reset-btn { 
            display: inline-block; 
            background: linear-gradient(135deg, #dc3545, #c82333); 
            color: white; 
            padding: 12px 30px; 
            text-decoration: none; 
            border-radius: 25px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
        .reset-btn:hover { transform: translateY(-2px); }
        .alternative { margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; }
        .alternative p { margin: 0; color: #666; font-size: 14px; }
        .link { word-break: break-all; color: #007bff; }
        .footer { background-color: #f8f9fa; padding: 30px; text-align: center; color: #666; font-size: 14px; }
        .footer p { margin: 5px 0; }
        .security-note { background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .security-note strong { color: #856404; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔐 Password Reset Request</h1>
        </div>
        <div class='content'>
            <p class='welcome'>Hello " . htmlspecialchars($firstName) . ",</p>
            <p class='message'>
                We received a request to reset your password for your Orbix Market account. 
                If you made this request, click the button below to set a new password.
            </p>
            
            <div class='btn-container'>
                <a href='" . htmlspecialchars($resetLink) . "' class='reset-btn'>
                    🔑 Reset My Password
                </a>
            </div>
            
            <div class='security-note'>
                <p><strong>⚠️ Security Notice:</strong></p>
                <p>This password reset link will expire in 1 hour for security reasons. If you didn't request this reset, please ignore this email and your password will remain unchanged.</p>
            </div>
            
            <div class='alternative'>
                <p><strong>Alternative method:</strong></p>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p class='link'>" . htmlspecialchars($resetLink) . "</p>
            </div>
        </div>
        <div class='footer'>
            <p><strong>Orbix Market Security Team</strong></p>
            <p>If you didn't request this password reset, someone may be trying to access your account. Please contact support if you have concerns.</p>
            <p>© 2025 Orbix Market. All rights reserved.</p>
        </div>
    </div>
</body>
</html>";
    }
    
    /**
     * Send welcome email after successful verification
     */
    public function sendWelcomeEmail($email, $firstName) {
        $subject = 'Welcome to Orbix Market - You\'re All Set!';
        $htmlBody = $this->getWelcomeEmailTemplate($firstName);
        $textBody = strip_tags($htmlBody);
        
        if (EmailConfig::isDevelopment() && EmailConfig::DEV_EMAIL_TO_FILE) {
            return $this->saveEmailToFile($email, $subject, $htmlBody);
        } else {
            return $this->sendSMTPEmail($email, $subject, $htmlBody, $textBody);
        }
    }
    
    /**
     * Get welcome email template
     */
    private function getWelcomeEmailTemplate($firstName) {
        $baseUrl = EmailConfig::getBaseUrl();
        return "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Welcome to Orbix Market!</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; }
        .header { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 300; }
        .content { padding: 40px 30px; }
        .welcome { font-size: 18px; color: #333; margin-bottom: 20px; }
        .message { color: #666; line-height: 1.6; margin-bottom: 30px; }
        .features { background-color: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .feature-item { margin: 10px 0; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
        .feature-item:last-child { border-bottom: none; }
        .btn-container { text-align: center; margin: 30px 0; }
        .explore-btn { 
            display: inline-block; 
            background: linear-gradient(135deg, #007bff, #0056b3); 
            color: white; 
            padding: 12px 30px; 
            text-decoration: none; 
            border-radius: 25px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }
        .explore-btn:hover { transform: translateY(-2px); }
        .footer { background-color: #f8f9fa; padding: 30px; text-align: center; color: #666; font-size: 14px; }
        .footer p { margin: 5px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🎉 Welcome to Orbix Market!</h1>
        </div>
        <div class='content'>
            <p class='welcome'>Congratulations " . htmlspecialchars($firstName) . "!</p>
            <p class='message'>
                Your account has been successfully verified and you're now part of the Orbix Market community! 
                We're thrilled to have you on board and can't wait for you to explore everything we have to offer.
            </p>
            
            <div class='features'>
                <h3 style='color: #333; margin-top: 0;'>🚀 What's Next?</h3>
                <div class='feature-item'>
                    <strong>📱 Explore Templates:</strong> Browse our extensive collection of website templates
                </div>
                <div class='feature-item'>
                    <strong>⭐ Read Reviews:</strong> Check out testimonials from our satisfied customers
                </div>
                <div class='feature-item'>
                    <strong>💼 Manage Services:</strong> Access our comprehensive service offerings
                </div>
                <div class='feature-item'>
                    <strong>🎯 Get Support:</strong> Our team is here to help you succeed
                </div>
            </div>
            
            <div class='btn-container'>
                <a href='" . htmlspecialchars($baseUrl) . "' class='explore-btn'>
                    🌟 Start Exploring
                </a>
            </div>
            
            <p class='message'>
                <strong>Need help getting started?</strong><br>
                Our support team is ready to assist you. Feel free to reach out if you have any questions 
                or need guidance on how to make the most of your Orbix Market experience.
            </p>
        </div>
        <div class='footer'>
            <p><strong>The Orbix Market Team</strong></p>
            <p>Thank you for choosing Orbix Market for your digital needs!</p>
            <p>© 2025 Orbix Market. All rights reserved.</p>
        </div>
    </div>
</body>
</html>";
    }
    
    /**
     * Test email configuration
     */
    public function testEmailConfig() {
        try {
            // Test SMTP connection
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = EmailConfig::getSmtpHost();
            $mail->SMTPAuth = true;
            $mail->Username = EmailConfig::getSmtpUsername();
            $mail->Password = EmailConfig::getSmtpPassword();
            $mail->SMTPSecure = EmailConfig::getSmtpSecure();
            $mail->Port = EmailConfig::getSmtpPort();
            
            // Try to connect
            $mail->SMTPDebug = 0; // Disable debug output
            $mail->smtpConnect();
            $mail->smtpClose();
            
            return [
                'success' => true,
                'message' => 'Email configuration is valid and SMTP connection successful',
                'config' => [
                    'host' => EmailConfig::getSmtpHost(),
                    'port' => EmailConfig::getSmtpPort(),
                    'username' => EmailConfig::getSmtpUsername(),
                    'from_email' => EmailConfig::getFromEmail(),
                    'development_mode' => EmailConfig::isDevelopment() ? 'Yes' : 'No',
                    'save_to_file' => EmailConfig::DEV_EMAIL_TO_FILE ? 'Yes' : 'No'
                ]
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email configuration test failed: ' . $e->getMessage(),
                'error_details' => $e->getMessage()
            ];
        }
    }
}
?>
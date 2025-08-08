#!/bin/bash

# Orbix Market Deployment Setup Script
echo "🚀 Setting up Orbix Market for deployment..."

# Create .gitignore if not exists
if [ ! -f .gitignore ]; then
cat > .gitignore << 'EOF'
# Environment files
.env
.env.local
.env.production

# Logs
logs/
*.log

# Vendor (will be installed on deployment)
vendor/

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS files
.DS_Store
Thumbs.db

# Local development files
config/local-config.php

# Upload directories (use Cloudinary in production)
uploads/
temp/

# Cache
cache/
*.cache

# Backups
backups/*.json
database/backups/
EOF
echo "✅ Created .gitignore"
fi

# Create production .htaccess
cat > .htaccess << 'EOF'
RewriteEngine On

# Force HTTPS in production
RewriteCond %{HTTP:X-Forwarded-Proto} !https
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Direct all requests to public folder if file doesn't exist in root
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_URI} !^/public/
RewriteRule ^(.*)$ public/$1 [L]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set X-Permitted-Cross-Domain-Policies "none"
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
</IfModule>

# Compress files
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Deny access to sensitive files
<Files ~ "\.(env|log|md)$">
    Deny from all
</Files>

<Files ~ "^\.">
    Deny from all
</Files>
EOF
echo "✅ Updated .htaccess for production"

# Create simple health check endpoint
cat > public/health.php << 'EOF'
<?php
header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '2.0.0'
];

// Test database connection if available
try {
    require_once '../config/database.php';
    $pdo = DatabaseConfig::getConnection();
    $health['database'] = 'connected';
} catch (Exception $e) {
    $health['database'] = 'disconnected';
    $health['status'] = 'error';
}

echo json_encode($health, JSON_PRETTY_PRINT);
EOF
echo "✅ Created health check endpoint"

echo ""
echo "🎉 Deployment setup complete!"
echo ""
echo "📋 Next steps:"
echo "1. Push code to GitHub repository"
echo "2. Deploy on Railway/Render/InfinityFree"
echo "3. Set up environment variables"
echo "4. Import database"
echo "5. Test deployment at: https://your-app.domain.com/health.php"
echo ""
echo "📖 See DEPLOYMENT.md for detailed instructions"

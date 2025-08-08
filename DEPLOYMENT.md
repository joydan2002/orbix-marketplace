# Orbix Market - Complete Deployment Guide

## 🎯 Railway Deployment (READY TO DEPLOY)

### ✅ Problem Fixed: CSS Asset Loading
**Issue**: CSS not loading on Railway due to incorrect asset paths  
**Solution**: All asset paths have been fixed for Railway deployment structure

### 🚀 Quick Deploy Steps

#### 1. Repository Setup
```bash
git add .
git commit -m "Fix asset paths for Railway deployment"
git push origin main
```

#### 2. Railway Deployment
1. Go to [Railway.app](https://railway.app)
2. Sign in with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your orbix-marketplace repository
5. Railway will auto-detect PHP and deploy using:
   - `Procfile`: `web: php -S 0.0.0.0:$PORT -t public`
   - `nixpacks.toml`: PHP 8.1 configuration

#### 3. Add Database
1. In Railway dashboard, click "New" → "Database" → "MySQL"
2. Copy connection details to environment variables

#### 4. Environment Variables
Set these in Railway dashboard → Variables tab:
```
MYSQLHOST=your-railway-mysql-host
MYSQLPORT=3306
MYSQLDATABASE=railway
MYSQLUSER=root
MYSQLPASSWORD=your-railway-mysql-password
CLOUDINARY_CLOUD_NAME=your-cloudinary-name
CLOUDINARY_API_KEY=your-cloudinary-key
CLOUDINARY_API_SECRET=your-cloudinary-secret
```

### 📁 Project Structure (Railway Ready)
```
public/                 # App root (Railway serves from here)
├── assets/            # ✅ CSS, JS, Images (copied and accessible)
│   ├── css/          # All CSS files including universal-fix.css
│   ├── js/           # All JavaScript files
│   └── images/       # All images and fallbacks
├── config/           # ✅ All configuration files
├── includes/         # ✅ All include files
├── auth.php          # ✅ Fixed asset paths
├── index.php         # ✅ Fixed asset paths
└── ... (all other pages with fixed paths)
```

### 🔧 Asset Path Fixes Applied
- ✅ All `../assets/css/` → `assets/css/`
- ✅ All `../assets/js/` → `assets/js/`
- ✅ All `../assets/images/` → `assets/images/`
- ✅ JavaScript strings and error handlers updated
- ✅ Universal CSS fixes for consistent styling

### 🧪 Verification
To test locally (simulates Railway environment):
```bash
cd public
php -S localhost:8000
# Visit http://localhost:8000/auth.php
# CSS and styling should work perfectly
```

---

## 🌐 Alternative Deployment Options
DB_PASSWORD=generated-password
CLOUDINARY_CLOUD_NAME=your-cloudinary-name
CLOUDINARY_API_KEY=your-api-key
CLOUDINARY_API_SECRET=your-api-secret
ENVIRONMENT=production
```

### 5. Import Database
1. Connect to Railway MySQL using provided credentials
2. Import your `database/orbix_market.sql` file

---

## 🌐 Alternative: Deploy to Render

### 1. Create render.yaml
Already included in project

### 2. Deploy Steps
1. Go to [Render.com](https://render.com)
2. Connect GitHub repository
3. Create new "Web Service"
4. Use build command: `composer install --no-dev`
5. Use start command: `php -S 0.0.0.0:$PORT -t public`

---

## 🆓 Alternative: InfinityFree (Traditional Hosting)

### 1. Sign up at [InfinityFree.net](https://infinityfree.net)
2. Create hosting account
3. Upload files via File Manager or FTP:
   - Upload all files to `htdocs` folder
   - Import database via phpMyAdmin
4. Update `config/database.php` with provided credentials

---

## 📝 Pre-deployment Checklist

- [ ] All environment variables configured
- [ ] Database imported and working
- [ ] Cloudinary credentials set up
- [ ] Email service configured
- [ ] Test all major features
- [ ] Check error logs

---

## 🔧 Troubleshooting

### Common Issues:
1. **Database connection failed**: Check environment variables
2. **Images not loading**: Verify Cloudinary setup
3. **Email not sending**: Check SMTP credentials
4. **404 errors**: Ensure .htaccess is working

### Debug Mode:
Add to environment variables:
```
PHP_DISPLAY_ERRORS=1
APP_DEBUG=true
```

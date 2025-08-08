# Orbix Market - Deployment Guide

## 🚀 Deploy to Railway (Recommended)

### 1. Prepare Repository
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/yourusername/orbix-market.git
git push -u origin main
```

### 2. Deploy Steps
1. Go to [Railway.app](https://railway.app)
2. Sign in with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your orbix-market repository
5. Railway will auto-detect PHP and deploy

### 3. Add Database
1. In Railway dashboard, click "New" → "Database" → "MySQL"
2. Copy connection details to environment variables

### 4. Set Environment Variables
In Railway dashboard → Variables tab:
```
DB_HOST=mysql-host-from-railway
DB_NAME=railway
DB_USER=root
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

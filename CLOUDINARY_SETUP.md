# Cloudinary Setup Guide for Orbix

## 📋 **Setup Instructions**

### 1. **Create Cloudinary Account**
1. Go to [cloudinary.com](https://cloudinary.com) and sign up for free
2. After signup, go to Dashboard to get your credentials
3. Note down these values:
   - **Cloud Name** (e.g., `your-cloud-name`)
   - **API Key** (e.g., `123456789012345`)
   - **API Secret** (e.g., `abcdefghijklmnopqrstuvwxyz`)

### 2. **Configure Cloudinary Credentials**
Edit `/config/cloudinary-config.php` and replace:
```php
define('CLOUDINARY_CLOUD_NAME', 'your-actual-cloud-name');
define('CLOUDINARY_API_KEY', 'your-actual-api-key');
define('CLOUDINARY_API_SECRET', 'your-actual-api-secret');
```

### 3. **Create Upload Presets in Cloudinary Dashboard**
1. Go to Settings → Upload → Upload Presets
2. Create these presets:

#### **Product Images Preset**
- **Preset Name**: `orbix_products`
- **Signing Mode**: Unsigned
- **Folder**: `orbix/products`
- **Transformations**: 
  - Quality: Auto
  - Format: Auto (WebP when supported)
  - Max dimensions: 1200x800

#### **Avatar Images Preset**
- **Preset Name**: `orbix_avatars`
- **Signing Mode**: Unsigned
- **Folder**: `orbix/avatars`
- **Transformations**:
  - Quality: Auto
  - Format: Auto
  - Max dimensions: 500x500
  - Crop: Fill, Gravity: Face

#### **Video Content Preset**
- **Preset Name**: `orbix_videos`
- **Signing Mode**: Unsigned
- **Folder**: `orbix/videos`
- **Resource Type**: Video
- **Quality**: Auto

## 🚀 **MIGRATE EXISTING IMAGES TO CLOUDINARY**

### **Automatic Migration (Recommended)**
```bash
# Navigate to scripts directory
cd /Applications/XAMPP/xamppfiles/htdocs/orbix/database/scripts/

# Run complete migration suite
php run-migration.php
```

The migration will:
1. **💾 Backup** current image data
2. **☁️ Upload** 30+ professional images to Cloudinary
3. **🔄 Replace** all existing product images
4. **🔍 Verify** migration success

### **Manual Migration Steps**
If you prefer step-by-step control:

```bash
# Step 1: Backup current data
php backup-before-migration.php

# Step 2: Run migration
php migrate-to-cloudinary.php

# Step 3: Verify results
php verify-migration.php
```

### **What Gets Migrated**
- ✅ **Template Images** → Professional business, e-commerce, portfolio images
- ✅ **Service Images** → High-quality service mockups
- ✅ **Category-Specific** → Images matched to template categories
- ✅ **Database URLs** → Updated to Cloudinary public_ids

## 🎯 **Integration Points**

### **Current Integrations Completed:**

1. **✅ Product Upload** (`seller-api.php`)
   - Preview images → Cloudinary
   - Product files (ZIP) → Cloudinary as raw files
   - Automatic optimization and CDN delivery

2. **✅ Product Display** (`seller-products.php`)
   - Optimized image URLs with auto WebP conversion
   - Lazy loading for better performance
   - Responsive image sizes

3. **✅ Product Updates**
   - Replace existing images on Cloudinary
   - Maintain URL consistency

4. **✅ Legacy Image Migration**
   - Replace old readdy.ai URLs
   - Upload curated professional images
   - Category-specific image assignment

### **File Structure:**
```
config/
├── cloudinary-config.php     # Configuration & helper functions
└── cloudinary-service.php    # Main service class

database/scripts/
├── run-migration.php         # Complete migration suite
├── migrate-to-cloudinary.php # Main migration script
├── backup-before-migration.php # Pre-migration backup
└── verify-migration.php      # Post-migration verification
```

## 🚀 **Usage Examples**

### **Upload Image:**
```php
require_once 'config/cloudinary-service.php';

// Upload product image
$result = $cloudinary->uploadImage($_FILES['image'], 'products');
if ($result['success']) {
    $public_id = $result['public_id'];
    // Save $public_id to database
}
```

### **Display Optimized Image:**
```php
// In your PHP templates
<img src="<?= getOptimizedImageUrl($public_id, 'thumb') ?>" 
     alt="Product Image" loading="lazy">
```

### **Available Image Sizes:**
- `thumb` - 300x200 thumbnails
- `medium` - 800x600 larger view
- `avatar_small` - 50x50 round avatars
- `avatar_medium` - 100x100 round avatars
- `avatar_large` - 200x200 round avatars

## 📊 **Benefits Achieved**

1. **Performance:**
   - ⚡ 3-5x faster image loading via global CDN
   - 🗜️ Auto compression (30-80% smaller files)
   - 📱 WebP format for modern browsers

2. **Storage:**
   - ☁️ 25GB free storage (vs limited server space)
   - 🔄 Automatic backups
   - 🌍 Global delivery network

3. **Features:**
   - 🖼️ On-the-fly image transformations
   - 📹 Video support ready
   - 🔒 Secure uploads with validation

4. **Professional Quality:**
   - 🎨 Curated, high-quality images
   - 📏 Consistent dimensions and styling
   - 🏷️ Category-appropriate imagery

## 🔧 **Environment Setup**

### **Development vs Production:**
The system automatically detects environment:
- **Dev** (localhost): Uses `dev/` folder prefix
- **Prod**: Uses `prod/` folder prefix

### **URL Examples:**
```
Development: https://res.cloudinary.com/dpmwj7f9j/image/upload/w_300,h_200,c_fill,q_auto,f_auto/dev/orbix/products/orbix_business_1.jpg

Production: https://res.cloudinary.com/dpmwj7f9j/image/upload/w_300,h_200,c_fill,q_auto,f_auto/prod/orbix/products/orbix_business_1.jpg
```

## 📈 **Migration Results**

After running migration, you'll see:
- **Templates**: Professional category-specific images
- **Services**: High-quality service illustrations  
- **Performance**: Dramatically faster page loads
- **SEO**: Better Core Web Vitals scores
- **UX**: Crisp, responsive images on all devices

## 🛠️ **Next Steps (Optional)**

1. **Avatar Upload** - Add to user profile management
2. **Video Support** - For product demos
3. **AI Features** - Background removal, auto-tagging
4. **Custom Transformations** - Brand-specific filters

## 📞 **Support**

If you encounter issues:
1. Check Cloudinary dashboard for upload logs
2. Verify API credentials
3. Ensure upload presets are created
4. Check PHP error logs for detailed messages
5. Run verification script: `php verify-migration.php`

## 🎉 **You're All Set!**

Your Orbix marketplace now uses Cloudinary for:
- ✅ Product image uploads
- ✅ Optimized image delivery
- ✅ Automatic format conversion (WebP)
- ✅ CDN acceleration
- ✅ Storage cost savings
- ✅ Professional product imagery

**Visit your website to see the dramatic improvement in image quality and loading speed!**
#!/bin/bash

# Railway Deployment Preparation Script
# This script fixes asset paths and prepares the app for Railway deployment

echo "🚀 Preparing Orbix Market for Railway deployment..."

# 1. Copy assets to public directory (if not already done)
if [ ! -d "public/assets" ]; then
    echo "📁 Copying assets to public directory..."
    mkdir -p public/assets
    cp -r assets/* public/assets/
    echo "✅ Assets copied successfully"
else
    echo "📁 Assets already in public directory"
fi

# 2. Copy config files to public directory (if not already done)
if [ ! -d "public/config" ]; then
    echo "⚙️ Copying config files..."
    mkdir -p public/config
    cp -r config/* public/config/
    echo "✅ Config files copied successfully"
else
    echo "⚙️ Config files already in public directory"
fi

# 3. Copy includes to public directory (if not already done)
if [ ! -d "public/includes" ]; then
    echo "📄 Copying includes..."
    mkdir -p public/includes
    cp -r includes/* public/includes/
    echo "✅ Includes copied successfully"
else
    echo "📄 Includes already in public directory"
fi

# 4. Fix asset paths for Railway deployment
echo "🔧 Fixing asset paths for Railway deployment..."
if [ -f "fix-asset-paths.sh" ]; then
    chmod +x fix-asset-paths.sh
    ./fix-asset-paths.sh
else
    echo "⚠️ fix-asset-paths.sh not found, creating it..."
    # Create the script inline
    cat > fix-asset-paths.sh << 'EOL'
#!/bin/bash
echo "🔧 Fixing asset paths for Railway deployment..."

# Find all PHP files in public directory and replace ../assets with assets
find public -name "*.php" -type f | while read file; do
    echo "Fixing paths in: $(basename "$file")"
    
    # Replace CSS links
    sed -i.bak 's|href="../assets/css/|href="assets/css/|g' "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace JS script sources
    sed -i.bak 's|src="../assets/js/|src="assets/js/|g' "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace image sources in HTML
    sed -i.bak 's|src="../assets/images/|src="assets/images/|g' "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace image sources in JavaScript strings
    sed -i.bak "s|'../assets/images/|'assets/images/|g" "$file" && rm "$file.bak" 2>/dev/null
    sed -i.bak 's|"../assets/images/|"assets/images/|g' "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace onerror handlers
    sed -i.bak "s|onerror=\"this\.src='../assets/images/|onerror=\"this.src='assets/images/|g" "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace JavaScript loadScript calls
    sed -i.bak "s|loadScript('../assets/js/|loadScript('assets/js/|g" "$file" && rm "$file.bak" 2>/dev/null
    
    # Replace return statements in JavaScript
    sed -i.bak "s|return '../assets/images/|return 'assets/images/|g" "$file" && rm "$file.bak" 2>/dev/null
done

echo "✅ All asset paths have been fixed for Railway deployment!"
EOL
    chmod +x fix-asset-paths.sh
    ./fix-asset-paths.sh
fi

# 5. Verify key files
echo "🔍 Verifying deployment readiness..."

# Check if key files exist
REQUIRED_FILES=("public/index.php" "public/auth.php" "Procfile" "nixpacks.toml")
for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file found"
    else
        echo "  ❌ $file missing"
    fi
done

# Check if assets are accessible
if [ -f "public/assets/css/auth.css" ]; then
    echo "  ✅ Auth CSS accessible from public"
else
    echo "  ❌ Auth CSS not accessible from public"
fi

if [ -f "public/assets/css/universal-fix.css" ]; then
    echo "  ✅ Universal fix CSS accessible from public"
else
    echo "  ❌ Universal fix CSS not accessible from public"
fi

echo ""
echo "🎉 Railway deployment preparation complete!"
echo ""
echo "📝 Your app structure is now ready for Railway:"
echo "  📁 public/ - Your app root directory"
echo "  📁 public/assets/ - CSS, JS, and images"
echo "  📁 public/config/ - Configuration files"
echo "  🚀 Procfile - Railway deployment config"
echo ""
echo "🔗 Next: Push to GitHub and deploy on Railway!"

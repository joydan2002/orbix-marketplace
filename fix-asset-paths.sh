#!/bin/bash

# Script to fix all asset paths for Railway deployment
# This script replaces ../assets with assets in all PHP files in the public directory

echo "🔧 Fixing asset paths for Railway deployment..."

# Find all PHP files in public directory and replace ../assets with assets
find /Applications/XAMPP/xamppfiles/htdocs/orbix/public -name "*.php" -type f | while read file; do
    echo "Fixing paths in: $(basename "$file")"
    
    # Replace CSS links
    sed -i '' 's|href="../assets/css/|href="assets/css/|g' "$file"
    
    # Replace JS script sources
    sed -i '' 's|src="../assets/js/|src="assets/js/|g' "$file"
    
    # Replace image sources in HTML
    sed -i '' 's|src="../assets/images/|src="assets/images/|g' "$file"
    
    # Replace image sources in JavaScript strings
    sed -i '' "s|'../assets/images/|'assets/images/|g" "$file"
    sed -i '' 's|"../assets/images/|"assets/images/|g' "$file"
    
    # Replace onerror handlers
    sed -i '' "s|onerror=\"this\.src='../assets/images/|onerror=\"this.src='assets/images/|g" "$file"
    
    # Replace JavaScript loadScript calls
    sed -i '' "s|loadScript('../assets/js/|loadScript('assets/js/|g" "$file"
    
    # Replace return statements in JavaScript
    sed -i '' "s|return '../assets/images/|return 'assets/images/|g" "$file"
done

# Also fix includes (now copied to public/includes)
find /Applications/XAMPP/xamppfiles/htdocs/orbix/public/includes -name "*.php" -type f 2>/dev/null | while read file; do
    echo "Fixing paths in includes: $(basename "$file")"
    
    # Replace CSS links (adjust for being in includes subfolder)
    sed -i '' 's|href="../assets/css/|href="../assets/css/|g' "$file"
    
    # Replace JS script sources (adjust for being in includes subfolder) 
    sed -i '' 's|src="../assets/js/|src="../assets/js/|g' "$file"
    
    # Replace image sources
    sed -i '' 's|src="../assets/images/|src="../assets/images/|g' "$file"
    sed -i '' "s|'../assets/images/|'../assets/images/|g" "$file"
    sed -i '' 's|"../assets/images/|"../assets/images/|g' "$file"
    sed -i '' "s|onerror=\"this\.src='../assets/images/|onerror=\"this.src='../assets/images/|g" "$file"
    sed -i '' "s|return '../assets/images/|return '../assets/images/|g" "$file"
done

echo "✅ All asset paths have been fixed for Railway deployment!"
echo "📝 Summary of changes:"
echo "   - CSS links: ../assets/css/ → assets/css/"
echo "   - JS sources: ../assets/js/ → assets/js/"
echo "   - Image sources: ../assets/images/ → assets/images/"
echo "   - Error handlers and JavaScript strings updated"

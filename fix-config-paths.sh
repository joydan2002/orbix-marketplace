#!/bin/bash
# Fix all config paths in public directory

# Files to update with their current patterns
declare -A files_to_update=(
    ["public/auth.php"]="require_once '../config/"
    ["public/index.php"]="require_once '../config/"
    ["public/templates.php"]="require_once __DIR__ . '/../config/"
    ["public/services.php"]="require_once __DIR__ . '/../config/"
    ["public/cart.php"]="require_once __DIR__ . '/../config/"
    ["public/checkout.php"]="require_once __DIR__ . '/../config/"
    ["public/profile.php"]="require_once __DIR__ . '/../config/"
    ["public/template-detail.php"]="require_once __DIR__ . '/../config/"
    ["public/service-detail.php"]="require_once __DIR__ . '/../config/"
    ["public/support.php"]="require_once __DIR__ . '/../config/"
)

echo "🔧 Fixing config paths in PHP files..."

# Add config helper to each file and replace require statements
for file in "${!files_to_update[@]}"; do
    if [ -f "$file" ]; then
        echo "Fixing $file..."
        
        # Add config helper require if not present
        if ! grep -q "config-helper.php" "$file"; then
            # Find the first require_once line and add helper before it
            sed -i '1,/require_once/ { /require_once/ i\
require_once __DIR__ . \"/config-helper.php\";
            }' "$file"
        fi
        
        # Replace config require patterns
        sed -i 's|require_once __DIR__ \. \"/\.\./config/\([^"]*\)\.php\"|requireConfig("\1.php")|g' "$file"
        sed -i "s|require_once '\.\./config/\([^']*\)\.php'|requireConfig('\1.php')|g" "$file"
        
        echo "✅ Fixed $file"
    else
        echo "❌ File $file not found"
    fi
done

echo "🎉 Config path fixes completed!"

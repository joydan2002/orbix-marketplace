#!/usr/bin/env php
<?php
/**
 * Complete Cloudinary Migration Runner
 * Runs the full migration process: backup → migrate → verify
 */

echo "🚀 ORBIX CLOUDINARY MIGRATION SUITE\n";
echo "===================================\n\n";

echo "This script will:\n";
echo "1. 💾 Backup current image data\n";
echo "2. ☁️  Upload new images to Cloudinary\n";
echo "3. 🔄 Update database with Cloudinary URLs\n";
echo "4. 🔍 Verify migration success\n\n";

echo "⚠️  WARNING: This will replace ALL existing product images!\n";
echo "Make sure you have:\n";
echo "- ✅ Configured Cloudinary API credentials\n";
echo "- ✅ Created upload presets (orbix_products, orbix_avatars, orbix_videos)\n";
echo "- ✅ Stable internet connection\n\n";

// Ask for confirmation
echo "Do you want to proceed? (yes/no): ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes' && strtolower($confirmation) !== 'y') {
    echo "Migration cancelled.\n";
    exit(0);
}

echo "\n🎯 Starting migration process...\n\n";

// Step 1: Backup
echo "STEP 1/4: Creating backup...\n";
echo "============================\n";
ob_start();
include 'backup-before-migration.php';
$backupOutput = ob_get_clean();
echo $backupOutput;

if (strpos($backupOutput, '✅ Backup created successfully!') !== false) {
    echo "\n✅ Backup completed successfully!\n\n";
} else {
    echo "\n❌ Backup failed. Stopping migration.\n";
    exit(1);
}

// Step 2: Migration
echo "STEP 2/4: Running Cloudinary migration...\n";
echo "=========================================\n";
ob_start();
include 'migrate-to-cloudinary.php';
$migrationOutput = ob_get_clean();
echo $migrationOutput;

if (strpos($migrationOutput, '🎉 MIGRATION COMPLETED SUCCESSFULLY!') !== false) {
    echo "\n✅ Migration completed successfully!\n\n";
} else {
    echo "\n⚠️  Migration may have had issues. Continuing to verification...\n\n";
}

// Step 3: Verification
echo "STEP 3/4: Verifying migration results...\n";
echo "========================================\n";
ob_start();
include 'verify-migration.php';
$verificationOutput = ob_get_clean();
echo $verificationOutput;

// Step 4: Final Summary
echo "\nSTEP 4/4: Final Summary\n";
echo "======================\n";

if (strpos($verificationOutput, '🎉 MIGRATION SUCCESSFUL!') !== false) {
    echo "🎉 COMPLETE SUCCESS!\n";
    echo "All your product images are now powered by Cloudinary.\n\n";
    
    echo "🌟 WHAT'S NEW:\n";
    echo "- Lightning-fast image loading via global CDN\n";
    echo "- Automatic WebP conversion for modern browsers\n";
    echo "- Responsive images that adapt to device size\n";
    echo "- Reduced server bandwidth usage\n";
    echo "- Professional, consistent image quality\n\n";
    
    echo "🔗 TEST YOUR WEBSITE:\n";
    echo "Visit: http://localhost/orbix/public/templates.php\n";
    echo "You should see faster loading, crisp images!\n\n";
    
} else if (strpos($verificationOutput, '⚠️  PARTIAL MIGRATION') !== false) {
    echo "⚠️  PARTIAL SUCCESS\n";
    echo "Some images were migrated, but others need attention.\n";
    echo "You can re-run this script to complete the migration.\n\n";
    
} else {
    echo "❌ MIGRATION INCOMPLETE\n";
    echo "Please check your Cloudinary configuration and try again.\n\n";
    
    echo "🔧 TROUBLESHOOTING:\n";
    echo "1. Verify Cloudinary API credentials in /config/cloudinary-config.php\n";
    echo "2. Ensure upload presets are created in Cloudinary dashboard\n";
    echo "3. Check internet connection\n";
    echo "4. Review PHP error logs for detailed messages\n\n";
}

echo "📋 MIGRATION LOG:\n";
echo "Backup: " . (strpos($backupOutput, '✅') !== false ? '✅ Success' : '❌ Failed') . "\n";
echo "Upload: " . (strpos($migrationOutput, '🎉') !== false ? '✅ Success' : '⚠️  Partial') . "\n";
echo "Verify: " . (strpos($verificationOutput, '🎉') !== false ? '✅ Success' : '⚠️  Issues') . "\n\n";

echo "📞 SUPPORT:\n";
echo "If you encounter issues, check:\n";
echo "- Cloudinary dashboard for upload logs\n";
echo "- PHP error logs in XAMPP\n";
echo "- Network connectivity\n\n";

echo "Thank you for using Orbix Cloudinary Migration! 🚀\n";
?>
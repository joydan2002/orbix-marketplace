<?php
/**
 * Pre-Migration Backup Script
 * Backup current image data before Cloudinary migration
 */

require_once '../../config/database.php';

// Fix HTTP_HOST issue for CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "💾 Creating backup before Cloudinary migration...\n\n";
    
    // Create backup directory
    $backupDir = '../../backups';
    if (!file_exists($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . '/image_backup_' . $timestamp . '.json';
    
    // Backup templates
    $stmt = $pdo->query("
        SELECT id, title, preview_image, download_file 
        FROM templates 
        WHERE preview_image IS NOT NULL
    ");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Backup services
    $stmt = $pdo->query("
        SELECT id, title, preview_image 
        FROM services 
        WHERE preview_image IS NOT NULL
    ");
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $backup = [
        'timestamp' => $timestamp,
        'templates' => $templates,
        'services' => $services,
        'total_templates' => count($templates),
        'total_services' => count($services)
    ];
    
    file_put_contents($backupFile, json_encode($backup, JSON_PRETTY_PRINT));
    
    echo "✅ Backup created successfully!\n";
    echo "📁 File: {$backupFile}\n";
    echo "📊 Templates backed up: " . count($templates) . "\n";
    echo "📊 Services backed up: " . count($services) . "\n\n";
    
    echo "🚀 You can now run the migration script:\n";
    echo "   php migrate-to-cloudinary.php\n\n";
    
    echo "🔄 To restore from backup if needed:\n";
    echo "   php restore-from-backup.php {$timestamp}\n";
    
} catch (Exception $e) {
    echo "❌ Backup failed: " . $e->getMessage() . "\n";
}
?>
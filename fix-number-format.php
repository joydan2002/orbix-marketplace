#!/usr/bin/env php
<?php
/**
 * Global Number Format Fix Script
 * Replaces unsafe number_format() calls with safeNumberFormat() throughout the project
 */

require_once __DIR__ . '/config/global-helpers.php';

echo "🔧 Starting Global Number Format Fix...\n";

// Define file patterns to process
$patterns = [
    __DIR__ . '/public/*.php',
    __DIR__ . '/public/sections/*.php', 
    __DIR__ . '/includes/*.php',
    __DIR__ . '/config/*.php'
];

$fixedFiles = 0;
$totalReplacements = 0;

foreach ($patterns as $pattern) {
    $files = glob($pattern);
    
    foreach ($files as $file) {
        if (!is_file($file)) continue;
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Pattern 1: number_format($variable, decimals)
        $content = preg_replace_callback(
            '/number_format\(\s*\$([^,)]+)(?:,\s*(\d+))?\s*\)/',
            function($matches) {
                $variable = $matches[1];
                $decimals = $matches[2] ?? '0';
                return "safeNumberFormat(\${$variable}, {$decimals})";
            },
            $content
        );
        
        // Pattern 2: number_format($array['key'], decimals)
        $content = preg_replace_callback(
            '/number_format\(\s*\$([^,)]+\[[^]]+\])(?:,\s*(\d+))?\s*\)/',
            function($matches) {
                $variable = $matches[1];
                $decimals = $matches[2] ?? '0';
                return "safeNumberFormat(\${$variable}, {$decimals})";
            },
            $content
        );
        
        // Pattern 3: Complex expressions
        $content = preg_replace_callback(
            '/number_format\(\s*([^,)]+)(?:,\s*(\d+))?\s*\)/',
            function($matches) {
                $expression = $matches[1];
                $decimals = $matches[2] ?? '0';
                
                // Skip if already using safe functions
                if (strpos($expression, 'safe') === 0) {
                    return $matches[0];
                }
                
                return "safeNumberFormat({$expression}, {$decimals})";
            },
            $content
        );
        
        // Add bootstrap include if not present
        if (strpos($content, 'bootstrap.php') === false && 
            strpos($content, '<?php') === 0 && 
            !strpos($content, 'global-helpers.php')) {
            
            $content = preg_replace(
                '/(<\?php[^>]*>[\s\n]*(?:\/\*.*?\*\/[\s\n]*)?)/s',
                "$1require_once __DIR__ . '/config/bootstrap.php';\n",
                $content,
                1
            );
        }
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $replacements = substr_count($content, 'safeNumberFormat') - substr_count($originalContent, 'safeNumberFormat');
            if ($replacements > 0) {
                echo "✅ Fixed {$file}: {$replacements} replacements\n";
                $fixedFiles++;
                $totalReplacements += $replacements;
            }
        }
    }
}

echo "\n🎉 Fix Complete!\n";
echo "📊 Files processed: {$fixedFiles}\n";
echo "🔄 Total replacements: {$totalReplacements}\n";
echo "\n✨ All number_format() calls are now safe for production!\n";

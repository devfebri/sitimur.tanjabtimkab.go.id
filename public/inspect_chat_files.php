<?php
/**
 * Chat Storage Inspector
 * View all chat files and their paths
 */

$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage/app/public';

echo "\n=== CHAT FILE STORAGE INSPECTOR ===\n\n";

echo "📁 Storage Base Path: " . $storagePath . "\n\n";

// Check if directory exists
$pengajuanPath = $storagePath . '/pengajuan';
if (!is_dir($pengajuanPath)) {
    echo "❌ Pengajuan directory not found yet (will be created on first file upload)\n\n";
    exit;
}

echo "✓ Pengajuan directory found\n\n";

// Find all files recursively
function scanDirectory($path, $prefix = '') {
    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $path . '/' . $item;
        $relativePath = str_replace(dirname(__DIR__) . '/storage/app/public/', '', $fullPath);

        if (is_dir($fullPath)) {
            echo "📂 {$prefix}{$item}/\n";
            scanDirectory($fullPath, $prefix . '  ');
        } else {
            $fileSize = filesize($fullPath);
            $sizeLabel = $fileSize > 1024 ? round($fileSize / 1024, 2) . ' KB' : $fileSize . ' B';
            echo "📄 {$prefix}{$item} ({$sizeLabel})\n";
            echo "   URL: /storage/{$relativePath}\n";
        }
    }
}

scanDirectory($pengajuanPath);

echo "\n✓ Symlink check:\n";
$symlink = $basePath . '/public/storage';
if (is_link($symlink)) {
    echo "✓ Symlink exists at public/storage\n";
    echo "  Target: " . readlink($symlink) . "\n";
} else {
    echo "❌ Symlink missing! Run: php artisan storage:link\n";
}

echo "\n";

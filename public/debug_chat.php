<?php
/**
 * Debug Chat File Paths
 *
 * This script helps debug file path issues in the chat system
 */

$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage/app/public';
$publicPath = __DIR__;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Chat Files</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1000px; }
        .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .section h2 { margin-top: 0; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        .file-item { padding: 8px; border-bottom: 1px solid #eee; }
        .exists { color: green; }
        .missing { color: red; }
        code { background: #f0f0f0; padding: 2px 5px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Debug Chat File System</h1>

        <div class="section">
            <h2>📁 Paths Configuration</h2>
            <pre><code>
Base Path: <?php echo $basePath; ?>
Storage Path: <?php echo $storagePath; ?>
Public Path: <?php echo $publicPath; ?>
            </code></pre>
        </div>

        <div class="section">
            <h2>📸 Looking for Chat Upload Directories</h2>
            <?php
            $pengajuanDir = $storagePath . '/pengajuan';

            echo '<p><strong>Checking directory:</strong> <code>' . $pengajuanDir . '</code></p>';

            if (is_dir($pengajuanDir)) {
                echo '<p class="exists">✓ Pengajuan directory exists</p>';

                // Recursively find all files
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($pengajuanDir),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                $fileCount = 0;
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $fileCount++;
                        $relativePath = str_replace($storagePath . '/', '', $file->getRealPath());
                        $urlPath = '/storage/' . $relativePath;
                        $exists = file_exists($file->getRealPath()) ? 'exists' : 'missing';

                        echo '<div class="file-item">';
                        echo '<strong>' . basename($file) . '</strong><br>';
                        echo 'Real Path: <code>' . $file->getRealPath() . '</code><br>';
                        echo 'URL Path: <code>' . $urlPath . '</code><br>';
                        echo 'Status: <span class="' . $exists . '">✓ File ' . $exists . '</span>';
                        echo '</div>';
                    }
                }

                if ($fileCount === 0) {
                    echo '<p>No files found in pengajuan directory</p>';
                }
            } else {
                echo '<p class="missing">✗ Pengajuan directory does not exist</p>';
            }
            ?>
        </div>

        <div class="section">
            <h2>🔗 Symlink Status</h2>
            <?php
            $storageLinkPath = $publicPath . '/storage';
            $isSymlink = is_link($storageLinkPath);

            if ($isSymlink) {
                echo '<p class="exists">✓ Storage symlink exists</p>';
                echo '<p>Link target: <code>' . readlink($storageLinkPath) . '</code></p>';
            } else {
                echo '<p class="missing">✗ Storage symlink does not exist</p>';
                echo '<p><strong>To create it, run:</strong></p>';
                echo '<pre><code>php artisan storage:link</code></pre>';
            }
            ?>
        </div>

        <div class="section">
            <h2>📊 Database Chat Messages</h2>
            <?php
            // Try to connect to database
            try {
                // Load Laravel config
                require $basePath . '/vendor/autoload.php';
                $app = require $basePath . '/bootstrap/app.php';

                $db = $app->make('db');
                $messages = $db->table('chat_messages')
                    ->where('file_path', '!=', null)
                    ->select('id', 'message', 'file_path', 'created_at')
                    ->limit(20)
                    ->get();

                if ($messages->count() > 0) {
                    echo '<p><strong>Found ' . $messages->count() . ' messages with files:</strong></p>';
                    foreach ($messages as $msg) {
                        echo '<div class="file-item">';
                        echo 'Message ID: ' . $msg->id . '<br>';
                        echo 'File Path: <code>' . $msg->file_path . '</code><br>';
                        echo 'URL: <code>' . $msg->file_path . '</code><br>';

                        // Check if file exists
                        $realPath = $storagePath . '/' . $msg->file_path;
                        $exists = file_exists($realPath);
                        echo 'Disk Path: <code>' . $realPath . '</code><br>';
                        echo 'Status: <span class="' . ($exists ? 'exists' : 'missing') . '">✓ ' . ($exists ? 'File exists' : 'File missing') . '</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>No messages with files found</p>';
                }
            } catch (Exception $e) {
                echo '<p class="missing">Error connecting to database: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <div class="section">
            <h2>💡 Troubleshooting Tips</h2>
            <ul>
                <li>If symlink is missing, run: <code>php artisan storage:link</code></li>
                <li>Check that <code>storage/app/public</code> directory exists</li>
                <li>Verify file permissions on storage directory</li>
                <li>Check browser console for actual file requests</li>
                <li>Use browser DevTools Network tab to see 404 errors</li>
            </ul>
        </div>
    </div>
</body>
</html>

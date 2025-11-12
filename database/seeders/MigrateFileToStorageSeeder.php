<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * Migrate pengajuan files dari public/ ke storage/
 *
 * Usage:
 * php artisan db:seed --class=MigrateFileToStorageSeeder
 *
 * Atau manual:
 * php artisan tinker
 * > require 'database/seeders/MigrateFileToStorageSeeder.php';
 * > (new MigrateFileToStorageSeeder)->run();
 */
class MigrateFileToStorageSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Starting file migration from public/ to storage/...');

        $total = PengajuanFile::count();
        $this->command->info("Total files to migrate: {$total}");

        $migrated = 0;
        $failed = 0;
        $skipped = 0;

        PengajuanFile::with('pengajuan')
            ->chunk(100, function ($files) use (&$migrated, &$failed, &$skipped) {
                foreach ($files as $file) {
                    try {
                        // Skip if already in storage format
                        if (str_starts_with($file->file_path, 'pengajuan/')) {
                            // Check if it's old format (d-m-Y)
                            $parts = explode('/', $file->file_path);

                            // Check if second part is date format (d-m-Y)
                            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $parts[1])) {
                                // Old format, needs migration
                                $this->migrateFile($file);
                                $migrated++;
                            } else {
                                // Already new format
                                $skipped++;
                            }
                        } else {
                            // File path doesn't start with 'pengajuan/', skip
                            $this->command->warn("⚠️  Skipped: {$file->file_path} (invalid format)");
                            $skipped++;
                        }
                    } catch (\Exception $e) {
                        $failed++;
                        Log::error("Migration failed for file {$file->id}: " . $e->getMessage());
                        $this->command->error("❌ Failed: {$file->file_path} - {$e->getMessage()}");
                    }
                }
            });

        $this->command->info("\n✅ Migration complete!");
        $this->command->info("  Migrated: {$migrated}");
        $this->command->info("  Skipped:  {$skipped}");
        $this->command->error("  Failed:   {$failed}");
    }

    /**
     * Migrate single file
     */
    private function migrateFile(PengajuanFile $file): void
    {
        $pengajuan = $file->pengajuan;
        if (!$pengajuan) {
            throw new \Exception("Pengajuan not found for file {$file->id}");
        }

        // Convert old path to new path
        // Old: pengajuan/19-10-2025/username/123/slug/file.pdf
        // New: pengajuan/2025/10/19/username/123/slug/file.pdf

        $oldPath = $file->file_path;
        $newPath = $this->convertPath($oldPath, $pengajuan);

        $oldFullPath = public_path($oldPath);
        $newFullPath = storage_path('app/public/' . $newPath);

        // Check if old file exists
        if (!file_exists($oldFullPath)) {
            $this->command->warn("⚠️  Source file not found: {$oldFullPath}");
            // Still update database path
            $file->file_path = $newPath;
            $file->save();
            return;
        }

        // Ensure destination directory exists
        $newDir = dirname($newFullPath);
        if (!is_dir($newDir)) {
            mkdir($newDir, 0755, true);
        }

        // Copy file
        if (!copy($oldFullPath, $newFullPath)) {
            throw new \Exception("Failed to copy file from {$oldFullPath} to {$newFullPath}");
        }

        // Verify copy
        if (!file_exists($newFullPath)) {
            throw new \Exception("File copy verification failed for {$newFullPath}");
        }

        // Update database
        $file->file_path = $newPath;
        $file->save();

        // Delete old file
        @unlink($oldFullPath);

        $this->command->line("✓ Migrated: {$oldPath} → {$newPath}");
    }

    /**
     * Convert old path format to new format
     *
     * Old: pengajuan/19-10-2025/username/123/slug/file.pdf
     * New: pengajuan/2025/10/19/username/123/slug/file.pdf
     */
    private function convertPath(string $oldPath, $pengajuan): string
    {
        $parts = explode('/', $oldPath);

        // pengajuan/19-10-2025/username/123/slug/file.pdf
        // 0         1           2        3   4    5

        if (count($parts) < 6) {
            throw new \Exception("Invalid path format: {$oldPath}");
        }

        // Parse old date format (d-m-Y)
        $oldDateStr = $parts[1];
        if (!preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $oldDateStr, $matches)) {
            throw new \Exception("Invalid date format: {$oldDateStr}");
        }

        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];

        // Build new path with Y/m/d format
        $newDatePath = sprintf('%04d/%02d/%02d', $year, $month, $day);

        // Reconstruct path
        $newParts = [
            'pengajuan',
            $newDatePath,
            $parts[2],  // username
            $parts[3],  // pengajuan_id
        ];

        // Add remaining parts (slug, filename, etc)
        for ($i = 4; $i < count($parts); $i++) {
            $newParts[] = $parts[$i];
        }

        return implode('/', $newParts);
    }
}

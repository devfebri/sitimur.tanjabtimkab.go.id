<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * File Storage Helper untuk SITIMUR
 *
 * Menyediakan fungsi-fungsi unified untuk upload files ke storage
 */
class FileStorageHelper
{
    /**
     * Upload file pengajuan ke storage
     *
     * @param UploadedFile $file
     * @param \App\Models\Pengajuan $pengajuan
     * @param string $category (chat_uploads, berkasajuan, revision, etc)
     * @param string|null $customName
     *
     * @return string Relative path from storage/app/public
     */
    public static function uploadPengajuanFile(
        UploadedFile $file,
        $pengajuan,
        string $category = 'berkasajuan',
        ?string $customName = null
    ): string {
        try {
            // Build folder structure
            $dateFolder = $pengajuan->created_at->format('Y/m/d');
            $userFolder = $pengajuan->user->username;
            $pengajuanFolder = $pengajuan->id;

            $folderPath = "pengajuan/{$dateFolder}/{$userFolder}/{$pengajuanFolder}/{$category}";

            // Generate filename
            $filename = $customName ?? (time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension());

            // Store using Storage facade
            $path = $file->storeAs($folderPath, $filename, 'public');

            return $path;

        } catch (\Exception $e) {
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload file chat ke storage
     *
     * @param UploadedFile $file
     * @param \App\Models\Pengajuan $pengajuan
     *
     * @return string Relative path from storage/app/public
     */
    public static function uploadChatFile(
        UploadedFile $file,
        $pengajuan
    ): string {
        return self::uploadPengajuanFile($file, $pengajuan, 'chat_uploads');
    }

    /**
     * Upload file revisi ke storage
     *
     * @param UploadedFile $file
     * @param \App\Models\Pengajuan $pengajuan
     *
     * @return string Relative path from storage/app/public
     */
    public static function uploadRevisionFile(
        UploadedFile $file,
        $pengajuan
    ): string {
        $filename = time() . '-' . uniqid() . '-revisi.' . $file->getClientOriginalExtension();
        return self::uploadPengajuanFile($file, $pengajuan, 'revisions', $filename);
    }

    /**
     * Delete file from storage
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return bool
     */
    public static function deleteFile(string $filePath): bool
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->delete($filePath);
            }
            return true;
        } catch (\Exception $e) {
            \Log::warning("Failed to delete file: {$filePath}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Build public URL for file
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return string URL to access file
     */
    public static function getPublicUrl(string $filePath): string
    {
        if (empty($filePath)) {
            return '';
        }

        if (str_starts_with($filePath, '/')) {
            return $filePath;
        }

        if (str_starts_with($filePath, 'http')) {
            return $filePath;
        }

        return '/storage/' . $filePath;
    }

    /**
     * Get full path on disk
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return string Full filesystem path
     */
    public static function getFullPath(string $filePath): string
    {
        return Storage::disk('public')->path($filePath);
    }

    /**
     * Check if file exists
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return bool
     */
    public static function fileExists(string $filePath): bool
    {
        return Storage::disk('public')->exists($filePath);
    }

    /**
     * Get file size in bytes
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return int|null File size or null if not exists
     */
    public static function getFileSize(string $filePath): ?int
    {
        try {
            if (self::fileExists($filePath)) {
                return Storage::disk('public')->size($filePath);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to get file size: {$filePath}", ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Get file last modified time
     *
     * @param string $filePath Relative path from storage/app/public
     *
     * @return int|null Timestamp or null if not exists
     */
    public static function getLastModified(string $filePath): ?int
    {
        try {
            if (self::fileExists($filePath)) {
                return Storage::disk('public')->lastModified($filePath);
            }
        } catch (\Exception $e) {
            \Log::error("Failed to get last modified: {$filePath}", ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Move file to another location within storage
     *
     * @param string $source Source path (relative)
     * @param string $destination Destination path (relative)
     *
     * @return bool
     */
    public static function moveFile(string $source, string $destination): bool
    {
        try {
            if (Storage::disk('public')->exists($source)) {
                Storage::disk('public')->move($source, $destination);
                return true;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to move file", ['source' => $source, 'destination' => $destination, 'error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Copy file within storage
     *
     * @param string $source Source path (relative)
     * @param string $destination Destination path (relative)
     *
     * @return bool
     */
    public static function copyFile(string $source, string $destination): bool
    {
        try {
            if (Storage::disk('public')->exists($source)) {
                Storage::disk('public')->copy($source, $destination);
                return true;
            }
        } catch (\Exception $e) {
            \Log::error("Failed to copy file", ['source' => $source, 'destination' => $destination, 'error' => $e->getMessage()]);
        }

        return false;
    }
}

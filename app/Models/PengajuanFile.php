<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanFile extends Model
{
    protected $table = 'pengajuan_files';

    protected $fillable =[
        'pengajuan_id',
        'nama_file',
        'slug',
        'file_path',
        'status_verifikator',
        'pesan_verifikator',
        'status_pokjapemilihan',
        'pesan_pokjapemilihan',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'verifikator_updated' => 'datetime',
        'pokja1_updated' => 'datetime',
        'pokja2_updated' => 'datetime',
        'pokja3_updated' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    /**
     * Get file icon based on file extension
     */
    public function getFileIcon()
    {
        $extension = pathinfo($this->file_path, PATHINFO_EXTENSION);
        
        switch (strtolower($extension)) {
            case 'pdf':
                return 'mdi-file-pdf';
            case 'doc':
            case 'docx':
                return 'mdi-file-word';
            case 'xls':
            case 'xlsx':
                return 'mdi-file-excel';
            case 'ppt':
            case 'pptx':
                return 'mdi-file-powerpoint';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'mdi-file-image';
            case 'zip':
            case 'rar':
            case '7z':
                return 'mdi-zip-box';
            case 'txt':
                return 'mdi-file-document';
            default:
                return 'mdi-file-document-outline';
        }
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSize()
    {
        if (!file_exists(public_path($this->file_path))) {
            return 'N/A';
        }
        
        $bytes = filesize(public_path($this->file_path));
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }

    /**
     * Get revision type color for badge
     */
    public function getRevisionTypeColor()
    {
        $jenisRevisi = $this->jenis_revisi ?? 'Revisi File';
        
        if (str_contains($jenisRevisi, 'Verifikator')) {
            return 'primary';
        } elseif (str_contains($jenisRevisi, 'Pokja')) {
            return 'info';
        } else {
            return 'secondary';
        }
    }

    /**
     * Get revision type icon
     */
    public function getRevisionTypeIcon()
    {
        $jenisRevisi = $this->jenis_revisi ?? 'Revisi File';
        
        if (str_contains($jenisRevisi, 'Verifikator')) {
            return 'mdi-shield-check';
        } elseif (str_contains($jenisRevisi, 'Pokja')) {
            return 'mdi-account-group';
        } else {
            return 'mdi-file-document-edit';
        }
    }

    /**
     * Get status color for badge
     */
    public function getStatusColor()
    {
        $status = $this->status ?? 'Aktif';
        
        switch ($status) {
            case 'Disetujui':
                return 'success';
            case 'Perlu Perbaikan':
                return 'warning';
            case 'Proses':
                return 'info';
            case 'Ditolak':
                return 'danger';
            default:
                return 'secondary';
        }
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'pengajuan_id',
        'message',
        'file_path',
        'chat_type',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'read_at' => 'datetime',
    ];

   

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function getFileIcon(): string
    {
        if (!$this->isFile()) return '';
        
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'mdi-file-pdf-box text-danger';
            case 'doc':
            case 'docx':
                return 'mdi-file-word text-primary';
            case 'xls':
            case 'xlsx':
                return 'mdi-file-excel text-success';
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                return 'mdi-file-image text-info';
            case 'zip':
            case 'rar':
                return 'mdi-archive text-warning';
            default:
                return 'mdi-file-document text-secondary';
        }
    }

    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) return '';
        
        $bytes = intval($this->file_size);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}

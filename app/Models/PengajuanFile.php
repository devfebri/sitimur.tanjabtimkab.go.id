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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

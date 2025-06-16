<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $table = 'pengajuans';
    protected $fillable = [
        'kode_rup',
        'nama_paket',
        'perangkat_daerah',
        'rekening_kegiatan',
        'sumber_dana',
        'pagu_anggaran',
        'pagu_hps',
        'jenis_pengadaan',
        'metode_pengadaan',
        'status',
        'created_at',
        'updated_at'
    ];

    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function metodePengadaan()
    {
        return $this->belongsTo(MetodePengadaan::class, 'metode_pengadaan');
    }
}

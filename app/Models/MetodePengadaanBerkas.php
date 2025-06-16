<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePengadaanBerkas extends Model
{
    protected $table = 'metode_pengadaan_berkass';
    protected $fillable = [
        'metode_pengadaan_id',
        'nama_berkas',
        'multiple',
        'status',
        'slug'
    ];

    public function metodePengadaan()
    {
        return $this->belongsTo(MetodePengadaan::class, 'metode_pengadaan_id');
    }
}

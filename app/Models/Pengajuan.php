<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Pengajuan extends Model
{
    use Notifiable;
    protected $table = 'pengajuans';
    protected $fillable = [
        'user_id',
        'kode_rup',
        'nama_paket',
        'perangkat_daerah',
        'rekening_kegiatan',
        'sumber_dana',
        'pagu_anggaran',
        'pagu_hps',
        'jenis_pengadaan',
        'metode_pengadaan_id',
        'verifikator_id',
        'verifikator_status',
        'verifikator_updated',
        'kepalaukpbj_id',
        'kepalaukpbj_status',
        'kepalaukpbj_updated',
        'pokjapemilihan_id',
        'pokjapemilihan_status',
        'pokjapemilihan_updated',
        'status',
        'status_updated',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
       
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'verifikator_updated' => 'datetime',
    ];

    

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function pokja1()
    {
        return $this->belongsTo(User::class, 'pokja1_id');
    }
    public function pokja2()
    {
        return $this->belongsTo(User::class, 'pokja2_id');
    }
    public function pokja3()
    {
        return $this->belongsTo(User::class, 'pokja3_id');
    }
    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
    public function kepalaukpbj()
    {
        return $this->belongsTo(User::class, 'kepalaukpbj_id');
    }
    public function pokjapemilihan()
    {
        return $this->belongsTo(User::class, 'pokjapemilihan_id');
    }

    public function metodePengadaan()
    {
        return $this->belongsTo(MetodePengadaan::class, 'metode_pengadaan_id');
    }
}

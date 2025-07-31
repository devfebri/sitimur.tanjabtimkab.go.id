<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanPokja extends Model
{
    protected $table = 'pengajuan_pokjas';
    protected $fillable = ['pengajuan_id', 'pokja_id', 'keterangan', 'status'];

    public function pengajuan()
    {
        return $this->belongsTo('App\Models\Pengajuan', 'pengajuan_id');
    }

    public function pokja()
    {
        return $this->belongsTo('App\Models\User', 'pokja_id');
    }
}

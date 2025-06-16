<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = new User();
        $data->name = 'Admin';
        $data->email = 'admin@gmail.com';
        $data->username = 'admin';
        $data->role = 'admin';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Verifikator';
        $data->email = 'verifikator@gmail.com';
        $data->username = 'verifikator';
        $data->role = 'verifikator';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Kepala UKPBJ';
        $data->email = 'kepalaukpbj@gmail.com'; 
        $data->username = 'kepalaukpbj';
        $data->role = 'kepalaukpbj';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Pokja Pemilihan';
        $data->email = 'pokjapemilihan@gmail.com';
        $data->username = 'pokjapemilihan';
        $data->role = 'pokjapemilihan';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'ppk';
        $data->email = 'ppk@gmail.com';
        $data->username = 'ppk';
        $data->role = 'ppk';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $mp= new \App\Models\MetodePengadaan();
        $mp->nama_metode_pengadaan = 'Seleksi';
        $mp->slug = 'seleksi';
        $mp->status = 1;
        $mp->save();

        $mp = new \App\Models\MetodePengadaan();
        $mp->nama_metode_pengadaan = 'Tender';
        $mp->slug = 'tender';
        $mp->status = 1;
        $mp->save();

        $mpb = new \App\Models\MetodePengadaanBerkas();
        $mpb->metode_pengadaan_id = 1; // Seleksi
        $mpb->nama_berkas = 'Surat Permohonan';
        $mpb->slug = 'surat_permohonan';
        $mpb->multiple = 0; // Tidak bisa upload lebih dari satu
        $mpb->status = 1;
        $mpb->save();
        $mpb = new \App\Models\MetodePengadaanBerkas();
        $mpb->metode_pengadaan_id = 1; // Seleksi
        $mpb->nama_berkas = 'KAK';
        $mpb->slug = 'kak';
        $mpb->multiple = 1; // Bisa upload lebih dari satu
        $mpb->status = 1;
        $mpb->save();
    }
}

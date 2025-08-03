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
        $data->name = 'Nama Kepala UKPBJ';
        $data->email = 'kepalaukpbj@gmail.com'; 
        $data->username = 'kepalaukpbj';
        $data->role = 'kepalaukpbj';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Nama Pokja 1';
        $data->email = 'pokjapemilihan@gmail.com';
        $data->username = 'pokja1';
        $data->nik = '12345';
        $data->nip = '12345';
        $data->role = 'pokjapemilihan';
        $data->jabatan = 'Kepala 1';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Nama Pokja 2';
        $data->email = 'pokjapemilihan2@gmail.com';
        $data->username = 'pokja2';
        $data->nik = '12345';
        $data->nip = '12345';
        $data->role = 'pokjapemilihan';
        $data->jabatan = 'Kepala 2';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Nama Pokja 3';
        $data->email = 'pokjapemilihan3@gmail.com';
        $data->username = 'pokja3';
        $data->nik = '12345';
        $data->nip = '12345';
        $data->role = 'pokjapemilihan';
        $data->jabatan = 'Kepala 3';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Nama Pokja 4';
        $data->email = 'pokjapemilihan4@gmail.com';
        $data->username = 'pokja4';
        $data->nik = '12345';
        $data->nip = '12345';
        $data->role = 'pokjapemilihan';
        $data->jabatan = 'Kepala 4';
        $data->password = bcrypt('password');
        $data->akses = 1;
        $data->save();

        $data = new User();
        $data->name = 'Nama Pokja 5';
        $data->email = 'pokjapemilihan5@gmail.com';
        $data->username = 'pokja5';
        $data->nik = '12345';
        $data->nip = '12345';
        $data->role = 'pokjapemilihan';
        $data->jabatan = 'Kepala 5';
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
        $mpb->status = 1;
        $mpb->save();
        
        $mpb = new \App\Models\MetodePengadaanBerkas();
        $mpb->metode_pengadaan_id = 1; // Seleksi
        $mpb->nama_berkas = 'KAK';
        $mpb->slug = 'kak';
        $mpb->status = 1;
        $mpb->save();

        $mpb = new \App\Models\MetodePengadaanBerkas();
        $mpb->metode_pengadaan_id = 2; // Seleksi
        $mpb->nama_berkas = 'Surat Permohonan';
        $mpb->slug = 'surat_permohonan';
        $mpb->status = 1;
        $mpb->save();

    }
}

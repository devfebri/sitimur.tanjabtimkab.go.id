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
    }
}

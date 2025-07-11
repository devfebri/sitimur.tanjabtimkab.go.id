<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_files', function (Blueprint $table) {
            $table->id();
            $table->integer('pengajuan_id')->constrained('pengajuans')->onDelete('cascade');
            // $table->integer('metode_pengadaan_berkas_id')->constrained('metode_pengadaan_berkass')->onDelete('cascade');
            $table->string('nama_file');
            $table->integer('revisi_ke')->default(0)->comment('Revisi ke berapa, 0 berarti file asli');
            $table->boolean('status_verifikator')->default(false)->comment(' 0=belum diperiksa, 1=diterima, 2=ditolak 3=dikembalikan');
            $table->text('pesan_verifikator')->nullable();
            $table->boolean('status_pokjapemilihan')->default(false)->comment(' 0=belum diperiksa, 1=diterima, 2=ditolak 3=dikembalikan');
            $table->text('pesan_pokjapemilihan')->nullable();
            $table->boolean('status')->default(false)->comment(' 0=belum fix, 1=fix');
            $table->string('slug');
            $table->text('file_path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_files');
    }
};

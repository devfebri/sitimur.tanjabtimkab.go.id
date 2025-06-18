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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('kode_rup');
            $table->string('nama_paket');
            $table->string('perangkat_daerah');
            $table->string('rekening_kegiatan');
            $table->string('sumber_dana');
            $table->string('pagu_anggaran');
            $table->string('pagu_hps');
            $table->string('jenis_pengadaan');
            $table->string('metode_pengadaan');
            $table->integer('verifikator_id');
            $table->boolean('verifikator_status')->default(false);
            $table->datetime('verifikator_updated');
            $table->integer('kepalaukpbj_id');
            $table->boolean('kepalaukpbj_status')->default(false);
            $table->datetime('kepalaukpbj_updated');
            $table->integer('pokjapemilihan_id');
            $table->boolean('pokjapemilihan_status')->default(false);
            $table->datetime('pokjapemilihan_updated');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};

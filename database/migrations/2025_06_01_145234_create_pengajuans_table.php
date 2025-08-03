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
            $table->integer('user_id');
            $table->integer('metode_pengadaan_id');
            $table->string('kode_rup');
            $table->string('nama_paket');
            $table->string('perangkat_daerah');
            $table->string('rekening_kegiatan');
            $table->string('sumber_dana');
            $table->float('pagu_anggaran');
            $table->float('pagu_hps');
            $table->string('jenis_pengadaan');
            $table->integer('verifikator_id')->nullable();
            $table->boolean('verifikator_status_akhir')->default(false);
            $table->datetime('verifikator_updated_akhir')->nullable();

            $table->integer('kepalaukpbj_id')->nullable();
            $table->boolean('kepalaukpbj_status')->default(false);
            $table->datetime('kepalaukpbj_updated')->nullable();

            $table->integer('pokja1_id')->nullable();
            $table->boolean('pokja1_status_akhir')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->datetime('pokja1_updated_akhir')->nullable();
            
            $table->integer('pokja2_id')->nullable();
            $table->boolean('pokja2_status_akhir')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->datetime('pokja2_updated_akhir')->nullable();

            
            $table->integer('pokja3_id')->nullable();
            $table->boolean('pokja3_status_akhir')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->datetime('pokja3_updated_akhir')->nullable();

            $table->text('pesan_akhir')->nullable();
            $table->boolean('status')->default(false);
            $table->datetime('status_updated')->nullable();
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

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
            $table->boolean('verifikator_status')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->text('verifikator_pesan')->nullable();
            $table->datetime('verifikator_updated')->nullable();


            $table->boolean('pokja1_status')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->text('pokja1_pesan')->nullable();
            $table->datetime('pokja1_updated')->nullable();

            $table->boolean('pokja2_status')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->text('pokja2_pesan')->nullable();
            $table->datetime('pokja2_updated')->nullable();

            $table->boolean('pokja3_status')->default(false)->comment(' 0=belum direviu, 1=sesuai, 2=tidak sesuai 3=perlu perbaikan');
            $table->text('pokja3_pesan')->nullable();
            $table->datetime('pokja3_updated')->nullable();

            $table->boolean('status')->default(false)->comment(' 0=belum sesuai, 1=sesuai');
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

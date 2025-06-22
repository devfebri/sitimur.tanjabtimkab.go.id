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
            $table->string('nama_file');
            $table->text('pesan')->nullable();
            $table->string('slug');
            $table->text('file_path');
            $table->enum('status', ['belum diperiksa','proses', 'diterima', 'ditolak'])->default('belum diperiksa')->comment('belum diperiksa','proses, diterima, ditolak');
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

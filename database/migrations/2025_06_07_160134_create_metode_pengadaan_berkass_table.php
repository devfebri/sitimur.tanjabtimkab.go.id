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
        Schema::create('metode_pengadaan_berkass', function (Blueprint $table) {
            $table->id();
            $table->integer('metode_pengadaan_id');
            $table->string('nama_berkas');
            $table->text('slug')->unique();
            $table->boolean('multiple')->default(true)->comment('1=aktif, 0=nonaktif');
            $table->boolean('status')->default(false)->comment('1=aktif, 0=nonaktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metode_pengadaan_berkass');
    }
};

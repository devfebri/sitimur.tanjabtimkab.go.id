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
        Schema::create('pengajuan_pokjas', function (Blueprint $table) {
            $table->id();
            $table->integer('pengajuan_id');
            $table->integer('pokja_id')->comment('ID User Pokja Pemilihan');
            $table->text('keterangan')->nullable();
            $table->boolean('status')->comment('0=belum direviu, 1=selesai reviu pokja');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pokjas');
    }
};

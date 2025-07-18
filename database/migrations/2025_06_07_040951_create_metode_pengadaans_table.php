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
        Schema::create('metode_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_metode_pengadaan')->unique();
            $table->text('slug')->unique();
            $table->boolean('status')->default(true)->comment('1=aktif, 0=nonaktif'); // true for active, false for inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metode_pengadaans');
    }
};

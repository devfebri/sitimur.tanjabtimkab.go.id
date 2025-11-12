<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('pengajuan_id');
            $table->text('message')->nullable();
            $table->text('file_path')->nullable();
            $table->enum('chat_type', ['verifikator', 'pokja'])->default('verifikator')->comment('verifikator = PPK + Verifikator, pokja = PPK + Pokja1/2/3');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['pengajuan_id', 'chat_type', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->unsignedBigInteger('pengajuan_id')->nullable(); // Link ke pengajuan tertentu
            $table->enum('type', ['direct', 'group'])->default('direct');
            $table->json('participants'); // Array user IDs
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index(['pengajuan_id']);
            $table->index(['last_message_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_conversations');
    }
};

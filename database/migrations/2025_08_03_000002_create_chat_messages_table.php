<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('message');
            $table->enum('type', ['text', 'file', 'system', 'document'])->default('text');
            $table->json('metadata')->nullable(); // untuk file attachments, dll
            $table->string('file_path')->nullable(); // path file yang diupload
            $table->string('file_name')->nullable(); // nama asli file
            $table->string('file_size')->nullable(); // ukuran file
            $table->string('file_type')->nullable(); // tipe file (pdf, doc, image, etc)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['conversation_id', 'created_at']);
            $table->index(['user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
};

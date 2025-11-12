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
        // Schema::table('chat_messages', function (Blueprint $table) {
        //     // Add chat_type column if it doesn't exist
        //     if (!Schema::hasColumn('chat_messages', 'chat_type')) {
        //         $table->enum('chat_type', ['verifikator', 'pokja'])
        //             ->default('verifikator')
        //             ->comment('verifikator = PPK + Verifikator, pokja = PPK + Pokja1/2/3')
        //             ->after('file_path');
        //     }

        //     // Update index
        //     if (Schema::hasTable('chat_messages')) {
        //         $table->dropIndex(['pengajuan_id', 'created_at']);
        //         $table->index(['pengajuan_id', 'chat_type', 'created_at']);
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('chat_messages', function (Blueprint $table) {
        //     if (Schema::hasColumn('chat_messages', 'chat_type')) {
        //         $table->dropColumn('chat_type');
        //     }

        //     // Restore old index
        //     if (Schema::hasTable('chat_messages')) {
        //         $table->dropIndex(['pengajuan_id', 'chat_type', 'created_at']);
        //         $table->index(['pengajuan_id', 'created_at']);
        //     }
        // });
    }
};

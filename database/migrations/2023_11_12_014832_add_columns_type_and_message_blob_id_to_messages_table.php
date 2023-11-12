<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->enum('type', ['text', 'blob', 'text-blob'])->default('text');
            $table->unsignedBigInteger('messages_blob_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            Schema::table('messages', function (Blueprint $table) {
                // Drop the columns added in the 'up' method
                $table->dropColumn(['type', 'messages_blob_id']);
            });
        });
    }
};

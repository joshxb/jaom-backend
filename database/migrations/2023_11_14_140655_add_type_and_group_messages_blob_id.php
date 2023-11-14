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
        Schema::table('group_messages', function (Blueprint $table) {
            $table->text('content')->nullable()->change();
            $table->enum('type', ['text', 'blob', 'text-blob'])->default('text');
            $table->unsignedBigInteger('group_messages_blob_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropColumn('content');
            $table->dropColumn('type');
            $table->dropColumn('group_messages_blob_id');
        });
    }
};

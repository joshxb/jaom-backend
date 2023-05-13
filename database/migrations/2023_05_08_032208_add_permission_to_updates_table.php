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
        Schema::table('updates', function (Blueprint $table) {
            Schema::table('updates', function (Blueprint $table) {
                $table->dropColumn('timestamp');
                $table->enum('permission', ['approved', 'disapproved'])->default('disapproved');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('updates', function (Blueprint $table) {
            Schema::table('updates', function (Blueprint $table) {
                $table->timestamp('timestamp')->nullable();
                $table->dropColumn('permission');
            });
        });
    }
};

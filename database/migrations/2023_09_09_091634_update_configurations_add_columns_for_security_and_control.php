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
        Schema::table('configurations', function (Blueprint $table) {
            $table->boolean('auto_add_room')->default(true)->after('contact_details_object');
            $table->json('login_credentials')->default(json_encode([]))->after('auto_add_room');
            $table->json('account_deactivation')->default(json_encode([]))->after('login_credentials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configurations', function (Blueprint $table) {
            $table->dropColumn('auto_add_room');
            $table->dropColumn('login_credentials');
            $table->dropColumn('account_deactivation');
        });
    }
};

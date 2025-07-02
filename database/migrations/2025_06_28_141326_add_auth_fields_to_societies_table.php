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
        Schema::table('societies', function (Blueprint $table) {
            $table->string('auth_token')->nullable()->after('password');
            $table->timestamp('last_login')->nullable()->after('auth_token');
            $table->timestamp('last_logout')->nullable()->after('last_login');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('societies', function (Blueprint $table) {
            $table->dropColumn(['auth_token', 'last_login', 'last_logout']);
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('phone')->nullable()->after('avatar');
            $table->text('bio')->nullable()->after('phone');
            
            // 2FA fields
            $table->string('google2fa_secret')->nullable()->after('bio');
            $table->boolean('google2fa_enabled')->default(false)->after('google2fa_secret');
            
            // Login tracking
            $table->timestamp('last_login_at')->nullable()->after('google2fa_enabled');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar',
                'phone', 
                'bio',
                'google2fa_secret',
                'google2fa_enabled',
                'last_login_at',
                'last_login_ip'
            ]);
        });
    }
};

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
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'name')) {
                $table->string('name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('comments', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('comments', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('comments', 'ip_address')) {
                $table->ipAddress('ip_address')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('comments', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $drop = [];
            foreach (['user_agent','ip_address','approved_at','email','name'] as $col) {
                if (Schema::hasColumn('comments', $col)) {
                    $drop[] = $col;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};

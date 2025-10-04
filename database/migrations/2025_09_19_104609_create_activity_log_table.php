<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_activity_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');                 // e.g. 'post.created', 'user.deleted', 'custom.export'
            $table->string('model')->nullable();      // e.g. App\Models\Post
            $table->string('model_id')->nullable();   // e.g. 42
            $table->json('changes')->nullable();      // before/after diff
            $table->string('url')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index(['model','model_id']);
            $table->index(['action','created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('activity_logs');
    }
};

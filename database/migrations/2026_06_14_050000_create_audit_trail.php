<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('action'); // created, updated, deleted, login, logout, etc.
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();

                $table->index(['model_type', 'model_id']);
                $table->index('user_id');
                $table->index('created_at');
            });
        }

        if (!Schema::hasTable('login_attempts')) {
            Schema::create('login_attempts', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('ip_address')->nullable();
                $table->boolean('success')->default(false);
                $table->timestamp('attempted_at');
                $table->index(['email', 'attempted_at']);
            });
        }

        if (!Schema::hasTable('ip_whitelist')) {
            Schema::create('ip_whitelist', function (Blueprint $table) {
                $table->id();
                $table->string('ip_address');
                $table->string('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('added_by')->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_whitelist');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('audit_logs');
    }
};

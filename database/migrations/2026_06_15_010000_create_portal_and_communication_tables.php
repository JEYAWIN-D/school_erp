<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Parent ↔ Student link (one parent can have multiple children)
        if (!Schema::hasTable('parent_students')) {
            Schema::create('parent_students', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_user_id')->index();
                $table->unsignedBigInteger('student_id')->index();
                $table->timestamps();
                $table->unique(['parent_user_id', 'student_id']);
            });
        }

        // Communication logs (bulk email / announcements)
        if (!Schema::hasTable('communication_logs')) {
            Schema::create('communication_logs', function (Blueprint $table) {
                $table->id();
                $table->enum('type', ['email', 'internal_notice'])->default('email');
                $table->string('subject');
                $table->longText('body');
                $table->json('audience_meta')->nullable(); // {type: 'class', class_id: 3} etc.
                $table->integer('sent_count')->default(0);
                $table->integer('failed_count')->default(0);
                $table->enum('status', ['draft', 'sending', 'sent', 'failed'])->default('draft');
                $table->unsignedBigInteger('sent_by')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
                $table->index(['type', 'status']);
            });
        }

        // Staff internal notice board
        if (!Schema::hasTable('staff_notices')) {
            Schema::create('staff_notices', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body');
                $table->enum('priority', ['normal', 'urgent'])->default('normal');
                $table->string('target_department')->nullable(); // null = all staff
                $table->unsignedBigInteger('created_by');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->index('priority');
            });
        }

        // Staff notice read receipts
        if (!Schema::hasTable('staff_notice_reads')) {
            Schema::create('staff_notice_reads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('staff_notice_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamp('read_at');
                $table->unique(['staff_notice_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_notice_reads');
        Schema::dropIfExists('staff_notices');
        Schema::dropIfExists('communication_logs');
        Schema::dropIfExists('parent_students');
    }
};

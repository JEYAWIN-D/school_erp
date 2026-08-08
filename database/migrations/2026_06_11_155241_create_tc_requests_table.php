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
        if (!Schema::hasTable('tc_requests')) {
            Schema::create('tc_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->enum('requested_by_type', ['parent', 'admin'])->default('parent');
                $table->foreignId('requested_by_user')->nullable()->constrained('users')->nullOnDelete();
                $table->text('reason')->nullable();
                $table->enum('status', ['pending', 'hod_approved', 'principal_approved', 'issued', 'rejected'])->default('pending');
                $table->foreignId('hod_approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('hod_approved_at')->nullable();
                $table->foreignId('principal_approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('principal_approved_at')->nullable();
                $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('issued_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tc_requests');
    }
};

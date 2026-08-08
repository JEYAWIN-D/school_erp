<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('document_type'); // aadhaar, pan, degree, experience_letter, police_verification, other
                $table->string('document_name');
                $table->string('file_path');
                $table->string('file_original_name')->nullable();
                $table->string('verification_status')->default('pending'); // pending, verified, rejected
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('verified_at')->nullable();
                $table->string('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_certifications')) {
            Schema::create('employee_certifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('certification_name');
                $table->string('issuing_authority')->nullable();
                $table->date('issue_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->string('certificate_number')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_certifications');
        Schema::dropIfExists('employee_documents');
    }
};

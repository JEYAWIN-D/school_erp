<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('gate_visitors')) {
            Schema::create('gate_visitors', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_name');
                $table->string('visitor_phone')->nullable();
                $table->string('visitor_id_type')->nullable(); // aadhaar, pan, dl, etc.
                $table->string('visitor_id_number')->nullable();
                $table->string('purpose');
                $table->string('whom_to_meet')->nullable();
                $table->string('department')->nullable();
                $table->string('vehicle_number')->nullable();
                $table->string('pass_token')->unique()->nullable();
                $table->string('visitor_photo')->nullable(); // base64 path
                $table->datetime('in_time');
                $table->datetime('out_time')->nullable();
                $table->boolean('is_approved')->default(true);
                $table->boolean('is_blacklisted')->default(false);
                $table->foreignId('logged_by')->constrained('users');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gate_blacklist')) {
            Schema::create('gate_blacklist', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('id_number')->nullable();
                $table->text('reason');
                $table->boolean('is_active')->default(true);
                $table->foreignId('added_by')->constrained('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('student_outpasses')) {
            Schema::create('student_outpasses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students');
                $table->string('pass_number')->unique();
                $table->datetime('out_time');
                $table->datetime('expected_return')->nullable();
                $table->datetime('actual_return')->nullable();
                $table->string('reason');
                $table->string('authorized_by');
                $table->enum('status', ['active','returned','overdue'])->default('active');
                $table->foreignId('issued_by')->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_outpasses');
        Schema::dropIfExists('gate_blacklist');
        Schema::dropIfExists('gate_visitors');
    }
};

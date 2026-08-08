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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->date('joining_date');

            // Personal
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group', 5)->nullable();
            $table->string('nationality')->default('Indian');
            $table->string('religion')->nullable();
            $table->enum('category', ['general', 'sc', 'st', 'obc', 'ews'])->default('general');
            $table->string('aadhaar_no', 12)->nullable();
            $table->string('pan_no', 10)->nullable();
            $table->string('photo')->nullable();

            // Contact
            $table->string('mobile', 15)->nullable();
            $table->string('personal_email')->nullable();
            $table->string('official_email')->nullable();
            $table->text('residential_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_mobile', 15)->nullable();

            // Job
            $table->string('designation')->nullable();
            $table->string('department')->nullable();
            $table->enum('employee_type', ['teaching', 'non_teaching', 'contract', 'part_time'])->default('teaching');

            // Bank
            $table->string('bank_account_no')->nullable();
            $table->string('bank_ifsc', 15)->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('pf_account_no')->nullable();
            $table->string('esi_no')->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

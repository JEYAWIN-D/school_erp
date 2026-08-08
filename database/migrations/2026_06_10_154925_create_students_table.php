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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('admission_no')->unique();
            $table->date('admission_date');

            // Personal
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->date('dob');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('nationality')->default('Indian');
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->string('sub_caste')->nullable();
            $table->enum('category', ['general', 'sc', 'st', 'obc', 'ews', 'minority'])->default('general');
            $table->string('mother_tongue')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('aadhaar_no', 12)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_disabled')->default(false);
            $table->text('disability_description')->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('mobile', 15)->nullable();
            $table->text('residential_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('pincode', 10)->nullable();

            // Family
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_mobile', 15)->nullable();
            $table->string('father_email')->nullable();
            $table->string('father_aadhaar', 12)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_mobile', 15)->nullable();
            $table->string('mother_email')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_mobile', 15)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_mobile', 15)->nullable();
            $table->decimal('annual_family_income', 10, 2)->nullable();

            // Previous school
            $table->string('previous_school_name')->nullable();
            $table->string('previous_school_board')->nullable();
            $table->string('tc_number')->nullable();
            $table->date('tc_date')->nullable();

            // Medical
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('doctor_phone', 15)->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'transferred', 'left', 'alumni'])->default('active');
            $table->enum('student_type', ['day_scholar', 'hosteller', 'day_boarder'])->default('day_scholar');

            // Sibling
            $table->unsignedBigInteger('sibling_group_id')->nullable()->index();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'gender', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Departments & Designations (HR)
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->string('grade')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        // Seat Capacities (Admissions)
        Schema::create('seat_capacities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('category')->default('general');
            $table->unsignedInteger('total_seats')->default(0);
            $table->unsignedInteger('filled_seats')->default(0);
            $table->timestamps();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        // Student Documents
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('document_type'); // aadhaar, birth_certificate, tc, marksheet, caste, address_proof, medical, other
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Student Medical Records
        Schema::create('student_medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->text('allergies')->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->text('medications')->nullable();
            $table->string('family_doctor')->nullable();
            $table->string('doctor_mobile')->nullable();
            $table->string('nearest_hospital')->nullable();
            $table->string('health_insurance_no')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        Schema::create('student_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('vaccine_name');
            $table->date('date_given');
            $table->string('dose')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Disciplinary Records
        Schema::create('student_disciplinary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('incident_date');
            $table->string('incident_type'); // misconduct, absenteeism, bullying, property_damage, cheating, other
            $table->text('description');
            $table->string('action_taken')->nullable();
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Scholarship & Concession
        Schema::create('scholarship_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // percentage, flat
            $table->decimal('value', 10, 2)->default(0);
            $table->text('applicable_fee_heads')->nullable(); // JSON array
            $table->string('criteria_type')->nullable(); // merit, government, manual
            $table->decimal('marks_threshold', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('student_concessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('scholarship_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->string('concession_type'); // scholarship, sibling, staff_ward, manual
            $table->string('value_type'); // percentage, flat
            $table->decimal('value', 10, 2)->default(0);
            $table->text('applicable_fee_heads')->nullable();
            $table->date('valid_from');
            $table->date('valid_to')->nullable();
            $table->unsignedBigInteger('granted_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Fee Installment Plans
        Schema::create('fee_installment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->unsignedInteger('installments_count')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('fee_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedInteger('installment_number');
            $table->date('due_date');
            $table->decimal('amount_percentage', 5, 2)->default(0);
            $table->timestamps();
            $table->foreign('plan_id')->references('id')->on('fee_installment_plans')->cascadeOnDelete();
        });

        // Late Fee Rules
        Schema::create('late_fee_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fee_head_id')->nullable();
            $table->string('rule_type'); // per_day, flat
            $table->decimal('amount', 10, 2)->default(0);
            $table->unsignedInteger('grace_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Grading Schemes
        Schema::create('grading_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('scheme_type')->default('marks'); // marks, grade, gpa
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('grading_scheme_ranges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('scheme_id');
            $table->decimal('min_marks', 5, 2);
            $table->decimal('max_marks', 5, 2);
            $table->string('grade');
            $table->string('description')->nullable();
            $table->decimal('gpa_points', 4, 2)->nullable();
            $table->timestamps();
            $table->foreign('scheme_id')->references('id')->on('grading_schemes')->cascadeOnDelete();
        });

        // Student Leave Requests
        Schema::create('student_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('leave_type'); // sick, personal, function
            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedInteger('days')->default(1);
            $table->text('reason');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Vehicle Maintenance & Fuel
        Schema::create('vehicle_maintenance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->date('maintenance_date');
            $table->string('maintenance_type'); // scheduled, breakdown
            $table->text('work_done');
            $table->string('vendor')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('next_service_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });

        Schema::create('vehicle_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->date('log_date');
            $table->decimal('quantity_litres', 8, 2);
            $table->decimal('cost_per_litre', 6, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->unsignedInteger('odometer_reading')->nullable();
            $table->string('filled_by')->nullable();
            $table->timestamps();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
        });

        // Hostel Visitors
        Schema::create('hostel_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->string('visitor_name');
            $table->string('visitor_mobile')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('relation')->nullable();
            $table->text('purpose')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->unsignedBigInteger('registered_by')->nullable();
            $table->timestamps();
        });

        // Hostel Maintenance Complaints
        Schema::create('hostel_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('complaint_type'); // electrical, plumbing, room, facility, other
            $table->text('description');
            $table->string('status')->default('open'); // open, in_progress, resolved
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // Hostel Disciplinary
        Schema::create('hostel_disciplinary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->date('incident_date');
            $table->text('description');
            $table->string('action_taken')->nullable();
            $table->boolean('parent_notified')->default(false);
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Book Reservations
        Schema::create('book_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedInteger('queue_position')->default(1);
            $table->string('status')->default('waiting'); // waiting, ready, cancelled, fulfilled
            $table->timestamp('reserved_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->foreign('book_id')->references('id')->on('books')->cascadeOnDelete();
        });

        // Digital Resources (Library)
        Schema::create('digital_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('resource_type'); // ebook, pdf, url, video
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });

        // Question Bank (Examinations)
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->string('question_type'); // mcq, short, long, true_false
            $table->text('question');
            $table->text('option_a')->nullable();
            $table->text('option_b')->nullable();
            $table->text('option_c')->nullable();
            $table->text('option_d')->nullable();
            $table->text('correct_answer')->nullable();
            $table->decimal('marks', 5, 2)->default(1);
            $table->string('difficulty')->default('medium'); // easy, medium, hard
            $table->string('chapter')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
        });

        // Teacher Subject Allocation
        Schema::create('teacher_subject_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete();
        });

        // Staff Attendance
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('date');
            $table->string('status')->default('present'); // present, absent, half_day, on_leave, holiday
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
        });

        // Notification Log
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // sms, whatsapp, email, push
            $table->string('recipient');
            $table->text('message');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('response')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('trigger_event')->nullable();
            $table->timestamps();
        });

        // Student Promotion Records
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('from_class_id');
            $table->unsignedBigInteger('to_class_id')->nullable();
            $table->unsignedBigInteger('from_academic_year_id');
            $table->unsignedBigInteger('to_academic_year_id')->nullable();
            $table->string('status'); // promoted, detained, left, transferred
            $table->unsignedBigInteger('promoted_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('promoted_at');
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        // Admission Stages (pipeline)
        Schema::create('admission_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enquiry_id');
            $table->string('stage'); // enquiry, application, entrance_test, interview, document_verification, confirmed, enrolled, rejected
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->string('venue')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->foreign('enquiry_id')->references('id')->on('enquiries')->cascadeOnDelete();
        });

        // Communication Settings
        Schema::create('communication_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // sms_gateway, whatsapp, email_smtp, razorpay, payu, fcm
            $table->json('settings'); // key-value pairs
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_settings');
        Schema::dropIfExists('admission_stages');
        Schema::dropIfExists('student_promotions');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('teacher_subject_allocations');
        Schema::dropIfExists('question_bank');
        Schema::dropIfExists('digital_resources');
        Schema::dropIfExists('book_reservations');
        Schema::dropIfExists('hostel_disciplinary');
        Schema::dropIfExists('hostel_complaints');
        Schema::dropIfExists('hostel_visitors');
        Schema::dropIfExists('vehicle_fuel_logs');
        Schema::dropIfExists('vehicle_maintenance');
        Schema::dropIfExists('student_leave_requests');
        Schema::dropIfExists('grading_scheme_ranges');
        Schema::dropIfExists('grading_schemes');
        Schema::dropIfExists('late_fee_rules');
        Schema::dropIfExists('fee_installments');
        Schema::dropIfExists('fee_installment_plans');
        Schema::dropIfExists('student_concessions');
        Schema::dropIfExists('scholarship_schemes');
        Schema::dropIfExists('student_disciplinary');
        Schema::dropIfExists('student_vaccinations');
        Schema::dropIfExists('student_medical_records');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('seat_capacities');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hostel outpass
        if (!Schema::hasTable('hostel_outpasses')) {
            Schema::create('hostel_outpasses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
                $table->unsignedBigInteger('allotment_id')->nullable();
                $table->foreign('allotment_id')->references('id')->on('hostel_allotments')->nullOnDelete();
                $table->dateTime('from_datetime');
                $table->dateTime('to_datetime');
                $table->string('reason')->nullable();
                $table->string('parent_contact')->nullable();
                $table->string('destination')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
                $table->dateTime('approved_at')->nullable();
                $table->dateTime('actual_return_time')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        // Hostel fee structures
        if (!Schema::hasTable('hostel_fee_structures')) {
            Schema::create('hostel_fee_structures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hostel_id')->nullable();
                $table->foreign('hostel_id')->references('id')->on('hostels')->nullOnDelete();
                $table->string('room_type');
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
                $table->decimal('monthly_fee', 10, 2)->default(0);
                $table->decimal('admission_fee', 10, 2)->default(0);
                $table->decimal('mess_fee', 10, 2)->default(0);
                $table->decimal('security_deposit', 10, 2)->default(0);
                $table->timestamps();
            });
        }

        // Transport stops
        if (!Schema::hasTable('transport_stops')) {
            Schema::create('transport_stops', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('route_id')->nullable();
                $table->foreign('route_id')->references('id')->on('transport_routes')->nullOnDelete();
                $table->string('name');
                $table->integer('stop_order')->default(0);
                $table->decimal('distance_km', 8, 2)->nullable();
                $table->time('arrival_time')->nullable();
                $table->timestamps();
            });
        }

        // Add missing columns to fee_payments if needed
        if (Schema::hasTable('fee_payments')) {
            if (!Schema::hasColumn('fee_payments', 'enrollment_id')) {
                Schema::table('fee_payments', function (Blueprint $table) {
                    $table->unsignedBigInteger('enrollment_id')->nullable()->after('student_id');
                    $table->foreign('enrollment_id')->references('id')->on('student_enrollments')->nullOnDelete();
                });
            }
            if (!Schema::hasColumn('fee_payments', 'is_cancelled')) {
                Schema::table('fee_payments', function (Blueprint $table) {
                    $table->boolean('is_cancelled')->default(false)->after('remarks');
                    $table->string('cancel_reason')->nullable()->after('is_cancelled');
                    $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancel_reason');
                    $table->dateTime('cancelled_at')->nullable()->after('cancelled_by');
                });
            }
            if (!Schema::hasColumn('fee_payments', 'amount_paid')) {
                Schema::table('fee_payments', function (Blueprint $table) {
                    $table->decimal('amount_paid', 10, 2)->default(0)->after('discount');
                });
            }
        }

        // Add missing columns to vehicles if needed
        if (Schema::hasTable('vehicles')) {
            foreach (['fitness_expiry', 'insurance_expiry', 'permit_expiry', 'puc_expiry', 'tax_expiry'] as $col) {
                if (!Schema::hasColumn('vehicles', $col)) {
                    Schema::table('vehicles', function (Blueprint $table) use ($col) {
                        $table->date($col)->nullable();
                    });
                }
            }
            if (!Schema::hasColumn('vehicles', 'make')) {
                Schema::table('vehicles', function (Blueprint $table) {
                    $table->string('make')->nullable()->after('id');
                    $table->string('model')->nullable()->after('make');
                    $table->string('vehicle_type')->nullable()->after('model');
                });
            }
        }

        // Add department_id to employees
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'department_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('department_id')->nullable()->after('department');
                $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
            });
        }

        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'bank_name')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('bank_name')->nullable();
            });
        }

        // Add period_data to attendance_records
        if (Schema::hasTable('attendance_records') && !Schema::hasColumn('attendance_records', 'period_data')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->json('period_data')->nullable()->after('remark');
            });
        }

        // Book issues extra columns
        if (Schema::hasTable('book_issues')) {
            foreach (['fine_waived', 'waiver_amount', 'waiver_reason', 'waived_by', 'waived_at'] as $col) {
                if (!Schema::hasColumn('book_issues', $col)) {
                    Schema::table('book_issues', function (Blueprint $table) use ($col) {
                        if ($col === 'fine_waived') $table->boolean($col)->default(false);
                        elseif (in_array($col, ['waiver_amount'])) $table->decimal($col, 10, 2)->nullable();
                        elseif ($col === 'waiver_reason') $table->string($col)->nullable();
                        elseif ($col === 'waived_by') $table->unsignedBigInteger($col)->nullable();
                        elseif ($col === 'waived_at') $table->dateTime($col)->nullable();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_outpasses');
        Schema::dropIfExists('hostel_fee_structures');
        Schema::dropIfExists('transport_stops');
    }
};

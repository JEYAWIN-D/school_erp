<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Module 3 — Academic Terms
        if (!Schema::hasTable('academic_terms')) {
            Schema::create('academic_terms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id');
                $table->string('name');
                $table->enum('type', ['term', 'semester', 'quarter'])->default('term');
                $table->date('start_date');
                $table->date('end_date');
                $table->unsignedTinyInteger('order_position')->default(1);
                $table->timestamps();
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            });
        }

        // Module 9 — Distance per stop
        if (!Schema::hasColumn('transport_stops', 'distance_km')) {
            Schema::table('transport_stops', function (Blueprint $table) {
                $table->decimal('distance_km', 6, 2)->nullable()->after('stop_order');
            });
        }

        // Module 5 — Grace marks on exam_schedules
        if (!Schema::hasColumn('exam_schedules', 'grace_marks')) {
            Schema::table('exam_schedules', function (Blueprint $table) {
                $table->integer('grace_marks')->default(0)->after('passing_marks');
            });
        }

        // Module 10 — Hostel floors
        if (!Schema::hasTable('hostel_floors')) Schema::create('hostel_floors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostel_id');
            $table->string('name');
            $table->unsignedTinyInteger('floor_number')->default(0);
            $table->timestamps();
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
        });

        // Module 10 — add floor_id to hostel_rooms
        if (Schema::hasTable('hostel_rooms') && !Schema::hasColumn('hostel_rooms', 'floor_id')) {
            Schema::table('hostel_rooms', function (Blueprint $table) {
                $table->unsignedBigInteger('floor_id')->nullable()->after('hostel_id');
                $table->foreign('floor_id')->references('id')->on('hostel_floors')->nullOnDelete();
            });
        }

        // Module 8 — Library settings (fine rates per member type)
        if (!Schema::hasTable('library_settings')) Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('member_type')->default('student');
            $table->decimal('fine_per_day', 6, 2)->default(1.00);
            $table->unsignedInteger('loan_days')->default(14);
            $table->unsignedInteger('max_books')->default(2);
            $table->unsignedInteger('max_renewals')->default(1);
            $table->timestamps();
        });

        // Module 7 — half-day leave on leave_requests
        if (Schema::hasTable('leave_requests') && !Schema::hasColumn('leave_requests', 'is_half_day')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                $table->boolean('is_half_day')->default(false)->after('to_date');
                $table->enum('half_day_session', ['morning', 'afternoon'])->nullable()->after('is_half_day');
            });
        }

        // Module 4 — Attendance condonation
        if (!Schema::hasTable('attendance_condonations')) Schema::create('attendance_condonations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedInteger('days_condoned')->default(0);
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('condoned_by');
            $table->date('condoned_on');
            $table->timestamps();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->foreign('condoned_by')->references('id')->on('users')->cascadeOnDelete();
        });

        // Module 10 — room transfer fields on hostel_allotments
        if (Schema::hasTable('hostel_allotments') && !Schema::hasColumn('hostel_allotments', 'transferred_from_room_id')) {
            Schema::table('hostel_allotments', function (Blueprint $table) {
                $table->unsignedBigInteger('transferred_from_room_id')->nullable()->after('room_id');
                $table->date('transfer_date')->nullable()->after('transferred_from_room_id');
                $table->string('transfer_reason')->nullable()->after('transfer_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hostel_allotments')) {
            Schema::table('hostel_allotments', function (Blueprint $table) {
                $table->dropColumn(array_filter(['transferred_from_room_id', 'transfer_date', 'transfer_reason'],
                    fn($c) => Schema::hasColumn('hostel_allotments', $c)));
            });
        }
        Schema::dropIfExists('attendance_condonations');
        if (Schema::hasTable('leave_requests')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                collect(['is_half_day', 'half_day_session'])->filter(fn($c) => Schema::hasColumn('leave_requests', $c))
                    ->whenNotEmpty(fn($cols) => $table->dropColumn($cols->values()->all()));
            });
        }
        Schema::dropIfExists('library_settings');
        if (Schema::hasTable('hostel_rooms') && Schema::hasColumn('hostel_rooms', 'floor_id')) {
            Schema::table('hostel_rooms', function (Blueprint $table) {
                $table->dropForeign(['floor_id']);
                $table->dropColumn('floor_id');
            });
        }
        Schema::dropIfExists('hostel_floors');
        if (Schema::hasColumn('exam_schedules', 'grace_marks')) {
            Schema::table('exam_schedules', function (Blueprint $table) { $table->dropColumn('grace_marks'); });
        }
        if (Schema::hasColumn('transport_stops', 'distance_km')) {
            Schema::table('transport_stops', function (Blueprint $table) { $table->dropColumn('distance_km'); });
        }
        Schema::dropIfExists('academic_terms');
    }
};

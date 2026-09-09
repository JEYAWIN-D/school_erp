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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'emis_no')) {
                $table->string('emis_no', 30)->nullable()->after('admission_no');
            }
            if (!Schema::hasColumn('students', 'identification_mark_1')) {
                $table->string('identification_mark_1', 255)->nullable()->after('blood_group');
            }
            if (!Schema::hasColumn('students', 'identification_mark_2')) {
                $table->string('identification_mark_2', 255)->nullable()->after('identification_mark_1');
            }
            if (!Schema::hasColumn('students', 'is_asp')) {
                $table->boolean('is_asp')->default(false)->after('student_type');
            }
            if (!Schema::hasColumn('students', 'asp_fee')) {
                $table->decimal('asp_fee', 10, 2)->default(0)->after('is_asp');
            }
            if (!Schema::hasColumn('students', 'concession_type')) {
                $table->string('concession_type', 50)->nullable()->after('asp_fee');
            }
            if (!Schema::hasColumn('students', 'concession_amount')) {
                $table->decimal('concession_amount', 10, 2)->default(0)->after('concession_type');
            }
            if (!Schema::hasColumn('students', 'concession_remarks')) {
                $table->string('concession_remarks', 255)->nullable()->after('concession_amount');
            }
            if (!Schema::hasColumn('students', 'transport_route_id')) {
                $table->unsignedBigInteger('transport_route_id')->nullable()->after('concession_remarks');
            }
            if (!Schema::hasColumn('students', 'transport_stop_id')) {
                $table->unsignedBigInteger('transport_stop_id')->nullable()->after('transport_route_id');
            }
            if (!Schema::hasColumn('students', 'transport_distance_km')) {
                $table->decimal('transport_distance_km', 6, 2)->nullable()->after('transport_stop_id');
            }
            if (!Schema::hasColumn('students', 'transport_fee')) {
                $table->decimal('transport_fee', 10, 2)->default(0)->after('transport_distance_km');
            }
            if (!Schema::hasColumn('students', 'sibling_name')) {
                $table->string('sibling_name', 100)->nullable()->after('sibling_group_id');
            }
            if (!Schema::hasColumn('students', 'sibling_admission_no')) {
                $table->string('sibling_admission_no', 30)->nullable()->after('sibling_name');
            }
            if (!Schema::hasColumn('students', 'sibling_class')) {
                $table->string('sibling_class', 50)->nullable()->after('sibling_admission_no');
            }
            if (!Schema::hasColumn('students', 'documents_submitted')) {
                $table->json('documents_submitted')->nullable()->after('sibling_class');
            }
            if (!Schema::hasColumn('students', 'selected_eca')) {
                $table->json('selected_eca')->nullable()->after('documents_submitted');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'last_school_studied')) {
                $table->string('last_school_studied', 150)->nullable()->after('previous_school');
            }
            if (!Schema::hasColumn('enquiries', 'last_class_studied')) {
                $table->string('last_class_studied', 50)->nullable()->after('previous_class');
            }
            if (!Schema::hasColumn('enquiries', 'father_name')) {
                $table->string('father_name', 100)->nullable()->after('parent_name');
            }
            if (!Schema::hasColumn('enquiries', 'father_mobile')) {
                $table->string('father_mobile', 20)->nullable()->after('father_name');
            }
            if (!Schema::hasColumn('enquiries', 'father_occupation')) {
                $table->string('father_occupation', 100)->nullable()->after('father_mobile');
            }
            if (!Schema::hasColumn('enquiries', 'mother_name')) {
                $table->string('mother_name', 100)->nullable()->after('father_occupation');
            }
            if (!Schema::hasColumn('enquiries', 'mother_mobile')) {
                $table->string('mother_mobile', 20)->nullable()->after('mother_name');
            }
            if (!Schema::hasColumn('enquiries', 'mother_occupation')) {
                $table->string('mother_occupation', 100)->nullable()->after('mother_mobile');
            }
            if (!Schema::hasColumn('enquiries', 'referred_by')) {
                $table->string('referred_by', 100)->nullable()->after('referral_name');
            }
            if (!Schema::hasColumn('enquiries', 'follow_up_remarks')) {
                $table->text('follow_up_remarks')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'emis_no',
                'identification_mark_1',
                'identification_mark_2',
                'is_asp',
                'asp_fee',
                'concession_type',
                'concession_amount',
                'concession_remarks',
                'transport_route_id',
                'transport_stop_id',
                'transport_distance_km',
                'transport_fee',
                'sibling_name',
                'sibling_admission_no',
                'sibling_class',
                'documents_submitted',
                'selected_eca',
            ]);
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn([
                'last_school_studied',
                'last_class_studied',
                'father_name',
                'father_mobile',
                'father_occupation',
                'mother_name',
                'mother_mobile',
                'mother_occupation',
                'referred_by',
                'follow_up_remarks',
            ]);
        });
    }
};

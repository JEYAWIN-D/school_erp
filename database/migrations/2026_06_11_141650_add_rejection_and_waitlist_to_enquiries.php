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
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('enquiries', 'waitlist_position')) {
                $table->unsignedSmallInteger('waitlist_position')->nullable()->after('rejection_reason');
            }
            if (!Schema::hasColumn('enquiries', 'entrance_test_date')) {
                $table->date('entrance_test_date')->nullable()->after('waitlist_position');
            }
            if (!Schema::hasColumn('enquiries', 'entrance_test_venue')) {
                $table->string('entrance_test_venue', 200)->nullable()->after('entrance_test_date');
            }
            if (!Schema::hasColumn('enquiries', 'entrance_test_marks')) {
                $table->decimal('entrance_test_marks', 5, 2)->nullable()->after('entrance_test_venue');
            }
            if (!Schema::hasColumn('enquiries', 'interview_date')) {
                $table->date('interview_date')->nullable()->after('entrance_test_marks');
            }
            if (!Schema::hasColumn('enquiries', 'interview_feedback')) {
                $table->text('interview_feedback')->nullable()->after('interview_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $cols = ['rejection_reason', 'waitlist_position', 'entrance_test_date', 'entrance_test_venue',
                     'entrance_test_marks', 'interview_date', 'interview_feedback'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('enquiries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

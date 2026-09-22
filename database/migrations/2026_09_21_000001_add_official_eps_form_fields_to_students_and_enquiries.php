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
            if (!Schema::hasColumn('students', 'father_qualification')) {
                $table->string('father_qualification', 100)->nullable()->after('father_occupation');
            }
            if (!Schema::hasColumn('students', 'mother_qualification')) {
                $table->string('mother_qualification', 100)->nullable()->after('mother_occupation');
            }
            if (!Schema::hasColumn('students', 'father_income')) {
                $table->string('father_income', 100)->nullable()->after('father_qualification');
            }
            if (!Schema::hasColumn('students', 'mother_income')) {
                $table->string('mother_income', 100)->nullable()->after('mother_qualification');
            }
            if (!Schema::hasColumn('students', 'stream_group')) {
                $table->string('stream_group', 150)->nullable()->after('previous_percentage');
            }
            if (!Schema::hasColumn('students', 'stream_group_allotted')) {
                $table->string('stream_group_allotted', 150)->nullable()->after('stream_group');
            }
            if (!Schema::hasColumn('students', 'is_tc_enclosed')) {
                $table->boolean('is_tc_enclosed')->default(false)->after('tc_number');
            }
            if (!Schema::hasColumn('students', 'is_qualified_promotion')) {
                $table->string('is_qualified_promotion', 100)->nullable()->after('is_tc_enclosed');
            }
            if (!Schema::hasColumn('students', 'year_of_passing')) {
                $table->string('year_of_passing', 20)->nullable()->after('previous_school_board');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'father_qualification')) {
                $table->string('father_qualification', 100)->nullable()->after('father_occupation');
            }
            if (!Schema::hasColumn('enquiries', 'mother_qualification')) {
                $table->string('mother_qualification', 100)->nullable()->after('mother_occupation');
            }
            if (!Schema::hasColumn('enquiries', 'father_income')) {
                $table->string('father_income', 100)->nullable()->after('father_qualification');
            }
            if (!Schema::hasColumn('enquiries', 'mother_income')) {
                $table->string('mother_income', 100)->nullable()->after('mother_qualification');
            }
            if (!Schema::hasColumn('enquiries', 'stream_group')) {
                $table->string('stream_group', 150)->nullable()->after('previous_percentage');
            }
            if (!Schema::hasColumn('enquiries', 'year_of_passing')) {
                $table->string('year_of_passing', 20)->nullable()->after('stream_group');
            }
            if (!Schema::hasColumn('enquiries', 'previous_school_attended')) {
                $table->string('previous_school_attended', 150)->nullable()->after('previous_school');
            }
            if (!Schema::hasColumn('enquiries', 'board')) {
                $table->string('board', 50)->nullable()->after('previous_school_attended');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = [
                'father_qualification', 'mother_qualification', 'father_income', 'mother_income',
                'stream_group', 'stream_group_allotted', 'is_tc_enclosed', 'is_qualified_promotion', 'year_of_passing'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $cols = [
                'father_qualification', 'mother_qualification', 'father_income', 'mother_income',
                'stream_group', 'year_of_passing', 'previous_school_attended', 'board'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('enquiries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'entrance_test_time')) {
                $table->string('entrance_test_time', 10)->nullable()->after('entrance_test_date');
            }
            if (!Schema::hasColumn('enquiries', 'entrance_test_invigilator')) {
                $table->string('entrance_test_invigilator')->nullable()->after('entrance_test_venue');
            }
            if (!Schema::hasColumn('enquiries', 'interview_time')) {
                $table->string('interview_time', 10)->nullable()->after('interview_date');
            }
            if (!Schema::hasColumn('enquiries', 'interview_interviewer')) {
                $table->string('interview_interviewer')->nullable()->after('interview_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['entrance_test_time', 'entrance_test_invigilator', 'interview_time', 'interview_interviewer']);
        });
    }
};

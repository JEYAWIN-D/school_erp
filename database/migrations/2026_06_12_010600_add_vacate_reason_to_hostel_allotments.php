<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hostel_allotments', function (Blueprint $table) {
            if (!Schema::hasColumn('hostel_allotments', 'vacate_reason')) {
                $table->string('vacate_reason')->nullable()->after('vacating_date');
            }
            if (!Schema::hasColumn('hostel_allotments', 'vacated_by')) {
                $table->unsignedBigInteger('vacated_by')->nullable()->after('vacate_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hostel_allotments', function (Blueprint $table) {
            $table->dropColumn(['vacate_reason', 'vacated_by']);
        });
    }
};

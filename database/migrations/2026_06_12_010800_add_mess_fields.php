<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('hostel_fee_structures') && !Schema::hasColumn('hostel_fee_structures', 'mess_included_default')) {
            Schema::table('hostel_fee_structures', function (Blueprint $table) {
                $table->boolean('mess_included_default')->default(true)->after('mess_fee')
                    ->comment('Whether mess fee is included by default for new allotments');
            });
        }

        if (Schema::hasTable('hostel_allotments') && !Schema::hasColumn('hostel_allotments', 'mess_included')) {
            Schema::table('hostel_allotments', function (Blueprint $table) {
                $table->boolean('mess_included')->default(true)
                    ->comment('Whether mess fee applies to this student');
                $table->text('mess_exclusion_reason')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hostel_fee_structures') && Schema::hasColumn('hostel_fee_structures', 'mess_included_default')) {
            Schema::table('hostel_fee_structures', function (Blueprint $table) {
                $table->dropColumn('mess_included_default');
            });
        }
        if (Schema::hasTable('hostel_allotments') && Schema::hasColumn('hostel_allotments', 'mess_included')) {
            Schema::table('hostel_allotments', function (Blueprint $table) {
                $table->dropColumn(['mess_included', 'mess_exclusion_reason']);
            });
        }
    }
};

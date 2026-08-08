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
            if (!Schema::hasColumn('students', 'tc_document')) {
                $table->string('tc_document')->nullable()->after('tc_date');
            }
            if (!Schema::hasColumn('students', 'marksheet_document')) {
                $table->string('marksheet_document')->nullable()->after('tc_document');
            }
            if (!Schema::hasColumn('students', 'migration_document')) {
                $table->string('migration_document')->nullable()->after('marksheet_document');
            }
            if (!Schema::hasColumn('students', 'scholarship_name')) {
                $table->string('scholarship_name')->nullable()->after('migration_document');
            }
            if (!Schema::hasColumn('students', 'scholarship_amount')) {
                $table->decimal('scholarship_amount', 10, 2)->nullable()->after('scholarship_name');
            }
            if (!Schema::hasColumn('students', 'scholarship_sanction_letter')) {
                $table->string('scholarship_sanction_letter')->nullable()->after('scholarship_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['tc_document', 'marksheet_document', 'migration_document',
                'scholarship_name', 'scholarship_amount', 'scholarship_sanction_letter']);
        });
    }
};

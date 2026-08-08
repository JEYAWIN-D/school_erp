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
            if (!Schema::hasColumn('students', 'previous_percentage')) {
                $table->decimal('previous_percentage', 5, 2)->nullable()->after('tc_date');
            }
            if (!Schema::hasColumn('students', 'migration_certificate_number')) {
                $table->string('migration_certificate_number', 50)->nullable()->after('previous_percentage');
            }
            if (!Schema::hasColumn('students', 'migration_certificate_date')) {
                $table->date('migration_certificate_date')->nullable()->after('migration_certificate_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            foreach (['previous_percentage', 'migration_certificate_number', 'migration_certificate_date'] as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

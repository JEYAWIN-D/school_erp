<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('employees')) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE employees ALTER COLUMN aadhaar_no TYPE VARCHAR(30)");
                DB::statement("ALTER TABLE employees ALTER COLUMN pan_no TYPE VARCHAR(20)");
            } else {
                Schema::table('employees', function (Blueprint $table) {
                    $table->string('aadhaar_no', 30)->nullable()->change();
                    $table->string('pan_no', 20)->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('students')) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE students ALTER COLUMN aadhaar_no TYPE VARCHAR(30)");
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('aadhaar_no', 30)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        // No down migration needed for increasing varchar lengths
    }
};

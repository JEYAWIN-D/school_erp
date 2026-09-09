<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Expands sensitive identity columns to TEXT to accommodate encrypted ciphertext lengths.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('students')) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE students ALTER COLUMN aadhaar_no TYPE TEXT");
                DB::statement("ALTER TABLE students ALTER COLUMN father_aadhaar TYPE TEXT");
                DB::statement("ALTER TABLE students ALTER COLUMN passport_number TYPE TEXT");
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->text('aadhaar_no')->nullable()->change();
                    $table->text('father_aadhaar')->nullable()->change();
                    $table->text('passport_number')->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('employees')) {
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE employees ALTER COLUMN aadhaar_no TYPE TEXT");
                DB::statement("ALTER TABLE employees ALTER COLUMN pan_no TYPE TEXT");
                DB::statement("ALTER TABLE employees ALTER COLUMN bank_account_no TYPE TEXT");
            } else {
                Schema::table('employees', function (Blueprint $table) {
                    $table->text('aadhaar_no')->nullable()->change();
                    $table->text('pan_no')->nullable()->change();
                    $table->text('bank_account_no')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Decreasing column length is omitted to prevent data truncation
    }
};

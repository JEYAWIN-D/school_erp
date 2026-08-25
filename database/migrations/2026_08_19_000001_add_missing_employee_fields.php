<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('employees')) {
            $driver = DB::getDriverName();
            if ($driver === 'pgsql') {
                DB::statement("ALTER TABLE employees ALTER COLUMN employee_type TYPE VARCHAR(50) USING employee_type::varchar");
            }

            Schema::table('employees', function (Blueprint $table) use ($driver) {
                if ($driver !== 'pgsql') {
                    $table->string('employee_type', 50)->change();
                }

                if (!Schema::hasColumn('employees', 'qualification')) {
                    $table->string('qualification', 200)->nullable()->after('designation');
                }
                if (!Schema::hasColumn('employees', 'address')) {
                    $table->text('address')->nullable()->after('residential_address');
                }
                if (!Schema::hasColumn('employees', 'email')) {
                    $table->string('email', 100)->nullable()->after('official_email');
                }
                if (!Schema::hasColumn('employees', 'basic_salary')) {
                    $table->decimal('basic_salary', 10, 2)->nullable()->after('esi_no');
                }
                if (!Schema::hasColumn('employees', 'hra')) {
                    $table->decimal('hra', 10, 2)->nullable()->after('basic_salary');
                }
                if (!Schema::hasColumn('employees', 'ta')) {
                    $table->decimal('ta', 10, 2)->nullable()->after('hra');
                }
                if (!Schema::hasColumn('employees', 'gross_salary')) {
                    $table->decimal('gross_salary', 10, 2)->nullable()->after('ta');
                }
                if (!Schema::hasColumn('employees', 'experience_years')) {
                    $table->integer('experience_years')->nullable()->after('qualification');
                }
                if (!Schema::hasColumn('employees', 'driver_name')) {
                    $table->string('driver_name', 100)->nullable();
                }
                if (!Schema::hasColumn('employees', 'driver_mobile')) {
                    $table->string('driver_mobile', 15)->nullable();
                }
                if (!Schema::hasColumn('employees', 'license_number')) {
                    $table->string('license_number', 100)->nullable();
                }
                if (!Schema::hasColumn('employees', 'license_expiry')) {
                    $table->date('license_expiry')->nullable();
                }
                if (!Schema::hasColumn('employees', 'assigned_vehicle')) {
                    $table->string('assigned_vehicle', 100)->nullable();
                }
                if (!Schema::hasColumn('employees', 'assigned_block')) {
                    $table->string('assigned_block', 100)->nullable();
                }
                if (!Schema::hasColumn('employees', 'shift_timing')) {
                    $table->string('shift_timing', 100)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $cols = [
                    'qualification', 'address', 'email', 'basic_salary', 'hra', 'ta', 'gross_salary',
                    'experience_years', 'driver_name', 'driver_mobile', 'license_number',
                    'license_expiry', 'assigned_vehicle', 'assigned_block', 'shift_timing'
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('employees', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};

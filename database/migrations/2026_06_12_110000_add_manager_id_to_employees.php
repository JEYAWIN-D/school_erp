<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'manager_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $column = $table->unsignedBigInteger('manager_id')->nullable();

                if (Schema::hasColumn('employees', 'designation_id')) {
                    $column->after('designation_id');
                } elseif (Schema::hasColumn('employees', 'department')) {
                    $column->after('department');
                }

                $table->foreign('manager_id')->references('id')->on('employees')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeignIfExists(['manager_id']);
                $table->dropColumnIfExists('manager_id');
            });
        }
    }
};

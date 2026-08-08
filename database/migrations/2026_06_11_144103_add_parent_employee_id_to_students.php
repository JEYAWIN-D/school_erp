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
            if (!Schema::hasColumn('students', 'parent_employee_id')) {
                $table->foreignId('parent_employee_id')->nullable()->constrained('employees')->nullOnDelete()->after('sibling_group_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Employee::class, 'parent_employee_id');
        });
    }
};

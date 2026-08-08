<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('book_issues') && !Schema::hasColumn('book_issues', 'employee_id')) {
            Schema::table('book_issues', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('book_issues', 'employee_id')) {
            Schema::table('book_issues', function (Blueprint $table) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            });
        }
    }
};

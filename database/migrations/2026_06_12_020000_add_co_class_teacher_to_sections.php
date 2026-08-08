<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('sections') && !Schema::hasColumn('sections', 'co_class_teacher_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->foreignId('co_class_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sections', 'co_class_teacher_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropForeign(['co_class_teacher_id']);
                $table->dropColumn('co_class_teacher_id');
            });
        }
    }
};

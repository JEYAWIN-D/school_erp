<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('syllabus', function (Blueprint $table) {
            if (!Schema::hasColumn('syllabus', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('completed_date');
            }
            if (!Schema::hasColumn('syllabus', 'description')) {
                $table->text('description')->nullable()->after('topics');
            }
        });
    }

    public function down(): void
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('syllabus', 'sort_order'))   $cols[] = 'sort_order';
            if (Schema::hasColumn('syllabus', 'description'))   $cols[] = 'description';
            if ($cols) $table->dropColumn($cols);
        });
    }
};

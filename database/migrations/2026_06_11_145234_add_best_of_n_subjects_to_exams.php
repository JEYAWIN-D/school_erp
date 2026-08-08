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
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'best_of_n_subjects')) {
                $table->unsignedTinyInteger('best_of_n_subjects')->nullable()
                    ->comment('If set, only count top N subjects for percentage/CGPA')->after('marks_locked_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('best_of_n_subjects');
        });
    }
};

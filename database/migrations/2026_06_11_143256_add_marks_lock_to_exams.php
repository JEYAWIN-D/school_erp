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
            if (!Schema::hasColumn('exams', 'marks_locked')) {
                $table->boolean('marks_locked')->default(false)->after('supplementary_threshold');
            }
            if (!Schema::hasColumn('exams', 'marks_locked_at')) {
                $table->timestamp('marks_locked_at')->nullable()->after('marks_locked');
            }
            if (!Schema::hasColumn('exams', 'marks_locked_by')) {
                $table->foreignId('marks_locked_by')->nullable()->constrained('users')->nullOnDelete()->after('marks_locked_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['marks_locked', 'marks_locked_at', 'marks_locked_by']);
        });
    }
};

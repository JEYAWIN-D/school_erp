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
        Schema::table('employee_appraisals', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_appraisals', 'self_ratings')) {
                $table->json('self_ratings')->nullable()->after('ratings');
                $table->decimal('self_score', 4, 2)->nullable()->after('self_ratings');
                $table->text('self_remarks')->nullable()->after('self_score');
                $table->timestamp('self_submitted_at')->nullable()->after('self_remarks');
            }
            if (!Schema::hasColumn('employee_appraisals', 'increment_amount')) {
                $table->decimal('increment_amount', 10, 2)->nullable()->after('appraised_at');
                $table->decimal('increment_percent', 5, 2)->nullable()->after('increment_amount');
                $table->date('increment_effective_date')->nullable()->after('increment_percent');
                $table->foreignId('increment_approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('increment_approved_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_appraisals', function (Blueprint $table) {
            $table->dropColumnIfExists([
                'self_ratings','self_score','self_remarks','self_submitted_at',
                'increment_amount','increment_percent','increment_effective_date',
                'increment_approved_by','increment_approved_at',
            ]);
        });
    }
};

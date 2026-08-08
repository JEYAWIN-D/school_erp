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
        if (!Schema::hasTable('employee_appraisals')) {
            Schema::create('employee_appraisals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
                $table->unsignedSmallInteger('appraisal_year');
                $table->json('ratings')->nullable();
                $table->decimal('overall_score', 4, 2)->nullable();
                $table->string('rating_label', 30)->nullable();
                $table->text('hod_remarks')->nullable();
                $table->text('principal_remarks')->nullable();
                $table->foreignId('appraised_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('appraised_at')->nullable();
                $table->timestamps();
                $table->unique(['employee_id', 'appraisal_year']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_appraisals');
    }
};

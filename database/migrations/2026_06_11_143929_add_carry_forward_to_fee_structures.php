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
        // carry_forward_balance per student per academic year
        if (!Schema::hasTable('fee_carry_forwards')) {
            Schema::create('fee_carry_forwards', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('from_academic_year_id')->constrained('academic_years');
                $table->foreignId('to_academic_year_id')->constrained('academic_years');
                $table->decimal('outstanding_amount', 10, 2)->default(0);
                $table->decimal('recovered_amount', 10, 2)->default(0);
                $table->string('note')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['student_id', 'from_academic_year_id', 'to_academic_year_id'], 'fcf_student_year_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_carry_forwards');
    }
};

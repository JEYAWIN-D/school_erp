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
        if (!Schema::hasTable('student_custom_fees')) {
            Schema::create('student_custom_fees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->foreignId('fee_head_id')->constrained()->cascadeOnDelete();
                $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
                $table->decimal('custom_amount', 10, 2);
                $table->text('reason')->nullable();
                $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->unique(['student_id', 'fee_head_id', 'academic_year_id'], 'scf_student_head_year');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_custom_fees');
    }
};

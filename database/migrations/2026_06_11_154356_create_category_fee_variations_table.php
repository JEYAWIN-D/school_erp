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
        if (!Schema::hasTable('category_fee_variations')) {
            Schema::create('category_fee_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_structure_id')->constrained()->cascadeOnDelete();
                $table->foreignId('fee_head_id')->constrained()->cascadeOnDelete();
                $table->string('student_category', 20); // General, SC, ST, OBC, EWS, Minority
                $table->decimal('amount', 10, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->unique(['fee_structure_id', 'fee_head_id', 'student_category'], 'cfv_structure_head_cat');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_fee_variations');
    }
};

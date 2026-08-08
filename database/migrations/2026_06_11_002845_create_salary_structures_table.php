<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 8, 2)->default(0);
            $table->decimal('da', 8, 2)->default(0);
            $table->decimal('ta', 8, 2)->default(0);
            $table->decimal('medical_allowance', 8, 2)->default(0);
            $table->decimal('other_allowances', 8, 2)->default(0);
            $table->decimal('pf_employee', 8, 2)->default(0);
            $table->decimal('pf_employer', 8, 2)->default(0);
            $table->decimal('esi_employee', 8, 2)->default(0);
            $table->decimal('esi_employer', 8, 2)->default(0);
            $table->decimal('tds', 8, 2)->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('salary_structures'); }
};

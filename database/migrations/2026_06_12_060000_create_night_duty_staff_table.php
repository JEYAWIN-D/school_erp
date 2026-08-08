<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('night_duty_staff')) {
            Schema::create('night_duty_staff', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hostel_id')->constrained('hostels')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('duty_date');
                $table->string('shift')->default('night'); // night, full
                $table->string('notes')->nullable();
                $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['hostel_id', 'employee_id', 'duty_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('night_duty_staff');
    }
};

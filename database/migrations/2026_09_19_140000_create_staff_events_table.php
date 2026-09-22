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
        if (!Schema::hasTable('staff_events')) {
            Schema::create('staff_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->string('event_type', 50); // birthday, wedding, wedding_anniversary, joining_anniversary, retirement, other
                $table->string('title', 255);
                $table->date('event_date');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['event_date', 'event_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_events');
    }
};

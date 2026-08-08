<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('mess_attendance')) {
            Schema::create('mess_attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('allotment_id')->constrained('hostel_allotments')->cascadeOnDelete();
                $table->date('date');
                $table->string('meal'); // breakfast, lunch, snacks, dinner
                $table->boolean('is_present')->default(true);
                $table->timestamps();

                $table->unique(['allotment_id', 'date', 'meal']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_attendance');
    }
};

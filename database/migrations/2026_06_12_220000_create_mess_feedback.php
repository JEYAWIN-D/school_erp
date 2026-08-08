<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('mess_feedback')) {
            Schema::create('mess_feedback', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('hostel_id')->nullable();
                $table->date('feedback_date');
                $table->enum('meal_type', ['breakfast', 'lunch', 'snacks', 'dinner']);
                $table->unsignedTinyInteger('rating'); // 1-5
                $table->text('comment')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
                $table->unique(['student_id', 'feedback_date', 'meal_type'], 'uniq_mess_feedback');
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('mess_feedback');
    }
};

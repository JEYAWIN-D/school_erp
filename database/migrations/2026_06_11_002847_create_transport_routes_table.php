<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transport_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('route_number', 20)->unique()->nullable();
            $table->string('from_location');
            $table->string('to_location');
            $table->text('stops')->nullable(); // JSON list
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->decimal('distance_km', 6, 2)->nullable();
            $table->decimal('fee', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('transport_allotments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('route_id');
            $table->string('pickup_stop')->nullable();
            $table->unsignedBigInteger('academic_year_id');
            $table->boolean('is_active')->default(true);
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('route_id')->references('id')->on('transport_routes')->cascadeOnDelete();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete();
            $table->unique(['student_id', 'academic_year_id']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('transport_allotments');
        Schema::dropIfExists('transport_routes');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('bus_attendance')) {
            Schema::create('bus_attendance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id');
                $table->unsignedBigInteger('route_id')->nullable();
                $table->date('date');
                $table->string('trip_type')->default('morning'); // morning / afternoon / both
                $table->unsignedBigInteger('student_id');
                $table->string('status')->default('present');   // present / absent
                $table->string('boarding_stop')->nullable();
                $table->string('remarks')->nullable();
                $table->unsignedBigInteger('marked_by')->nullable();
                $table->timestamps();
                $table->unique(['vehicle_id', 'date', 'trip_type', 'student_id']);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('bus_attendance');
    }
};

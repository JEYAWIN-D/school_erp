<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_number')->unique();
            $table->string('vehicle_type', 50); // Bus, Van, Auto
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('seating_capacity')->default(0);
            $table->string('driver_name')->nullable();
            $table->string('driver_mobile', 15)->nullable();
            $table->string('driver_license')->nullable();
            $table->date('rc_expiry')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreign('route_id')->references('id')->on('transport_routes')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('vehicles'); }
};

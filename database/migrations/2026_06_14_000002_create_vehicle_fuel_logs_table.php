<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('vehicle_fuel_logs')) {
            Schema::create('vehicle_fuel_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id');
                $table->date('fill_date');
                $table->decimal('litres', 8, 2);
                $table->decimal('rate_per_litre', 6, 2)->nullable();
                $table->decimal('amount', 10, 2)->nullable();
                $table->integer('odometer')->nullable();
                $table->string('fuel_type', 20)->nullable();  // diesel, petrol, cng
                $table->string('station')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('vehicle_fuel_logs'); }
};

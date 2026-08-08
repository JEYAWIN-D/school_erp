<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('vehicle_maintenances')) {
            Schema::create('vehicle_maintenances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id');
                $table->string('maintenance_type', 50);  // scheduled, breakdown, tyre, battery, etc.
                $table->date('service_date');
                $table->integer('odometer')->nullable();
                $table->decimal('cost', 10, 2)->nullable();
                $table->string('vendor')->nullable();
                $table->text('description')->nullable();
                $table->text('work_done')->nullable();
                $table->date('next_service_date')->nullable();
                $table->string('remarks')->nullable();
                $table->timestamps();
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
            });
        }
    }
    public function down(): void { Schema::dropIfExists('vehicle_maintenances'); }
};

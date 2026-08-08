<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('hostel_beds')) {
            Schema::create('hostel_beds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('room_id');
                $table->string('bed_number', 20);
                $table->enum('status', ['available', 'occupied', 'maintenance'])->default('available');
                $table->string('notes', 200)->nullable();
                $table->timestamps();

                $table->foreign('room_id')->references('id')->on('hostel_rooms')->onDelete('cascade');
                $table->unique(['room_id', 'bed_number']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hostel_beds');
    }
};

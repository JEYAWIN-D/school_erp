<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hostels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['boys','girls','mixed'])->default('boys');
            $table->string('warden_name')->nullable();
            $table->string('warden_mobile', 15)->nullable();
            $table->integer('total_capacity')->default(0);
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hostel_id');
            $table->string('room_number', 20);
            $table->string('floor', 10)->nullable();
            $table->enum('room_type', ['single','double','triple','dormitory'])->default('double');
            $table->integer('capacity')->default(2);
            $table->integer('occupied')->default(0);
            $table->decimal('fee_per_month', 8, 2)->default(0);
            $table->enum('status', ['available','full','maintenance'])->default('available');
            $table->foreign('hostel_id')->references('id')->on('hostels')->cascadeOnDelete();
            $table->unique(['hostel_id', 'room_number']);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('hostel_rooms');
        Schema::dropIfExists('hostels');
    }
};

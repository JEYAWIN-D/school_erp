<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('fee_change_logs')) {
            Schema::create('fee_change_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->nullable();
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('fee_head_id')->nullable();
                $table->unsignedBigInteger('academic_year_id')->nullable();
                $table->string('change_type', 30); // revision, concession, override, cancellation
                $table->decimal('old_amount', 10, 2)->nullable();
                $table->decimal('new_amount', 10, 2)->nullable();
                $table->string('reason', 500)->nullable();
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_change_logs');
    }
};

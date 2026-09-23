<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                     // e.g. "Hostellers", "Female Students - Class X"
            $table->string('module', 50)->default('student'); // extendable: 'student' for now
            $table->json('filters')->nullable();              // {"class_id":3,"gender":"female","status":"active"}
            $table->string('icon_color', 80)->default('from-violet-500 to-purple-600'); // Tailwind gradient
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};

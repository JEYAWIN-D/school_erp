<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('alumni')) {
            Schema::create('alumni', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->year('passing_year');
                $table->string('last_class')->nullable();
                $table->string('current_occupation')->nullable();
                $table->string('current_employer')->nullable();
                $table->string('current_city')->nullable();
                $table->text('achievements')->nullable();
                $table->string('profile_photo')->nullable();
                $table->string('linkedin_url')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('alumni_events')) {
            Schema::create('alumni_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('event_date');
                $table->text('description')->nullable();
                $table->string('venue')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('alumni_event_rsvps')) {
            Schema::create('alumni_event_rsvps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('alumni_events')->cascadeOnDelete();
                $table->foreignId('alumni_id')->constrained('alumni')->cascadeOnDelete();
                $table->boolean('attending')->default(true);
                $table->timestamps();
                $table->unique(['event_id', 'alumni_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_event_rsvps');
        Schema::dropIfExists('alumni_events');
        Schema::dropIfExists('alumni');
    }
};

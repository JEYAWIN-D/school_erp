<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->enum('event_type', ['academic','cultural','sports','holiday','meeting','other'])->default('other');
                $table->date('event_date');
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->string('venue')->nullable();
                $table->text('description')->nullable();
                $table->string('banner_image')->nullable();
                $table->boolean('is_published')->default(false);
                $table->boolean('allow_rsvp')->default(false);
                $table->integer('max_rsvp')->nullable();
                $table->string('audience')->default('all'); // all/students/staff/parents
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('event_rsvps')) {
            Schema::create('event_rsvps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->enum('status', ['attending','not_attending','maybe'])->default('attending');
                $table->integer('guest_count')->default(0);
                $table->text('note')->nullable();
                $table->timestamps();
                $table->unique(['event_id', 'user_id']);
            });
        }

        if (!Schema::hasTable('event_photos')) {
            Schema::create('event_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
                $table->string('photo_path');
                $table->string('caption')->nullable();
                $table->foreignId('uploaded_by')->constrained('users');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('event_photos');
        Schema::dropIfExists('event_rsvps');
        Schema::dropIfExists('events');
    }
};

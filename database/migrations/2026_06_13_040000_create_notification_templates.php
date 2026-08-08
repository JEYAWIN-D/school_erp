<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
                $table->id();
                $table->string('event_type');   // fee_due, attendance_absent, exam_result, etc.
                $table->string('channel')->default('email'); // email / sms / whatsapp
                $table->string('subject')->nullable();
                $table->text('body');
                $table->time('trigger_time')->nullable();    // e.g. 09:30 for daily scheduled send
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->unique(['event_type', 'channel']);
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('notification_templates');
    }
};

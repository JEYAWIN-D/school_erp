<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('fee_reminder_configs')) {
            Schema::create('fee_reminder_configs', function (Blueprint $table) {
                $table->id();
                $table->string('name')->default('Default Schedule');
                $table->json('before_due_days')->nullable();   // [3, 1] = 3 days before, 1 day before
                $table->boolean('on_due_date')->default(true);
                $table->json('after_due_days')->nullable();    // [7, 15, 30]
                $table->string('channel')->default('email');  // email / sms / whatsapp
                $table->string('email_subject')->nullable();
                $table->text('email_body')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('fee_reminder_configs');
    }
};

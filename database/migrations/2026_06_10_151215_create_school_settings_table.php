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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('school_code')->nullable();
            $table->string('affiliation_no')->nullable();
            $table->string('board')->default('CBSE'); // CBSE | Tamil Nadu State Board | ICSE
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_signature')->nullable();
            $table->string('school_stamp')->nullable();
            $table->string('gstin', 20)->nullable();
            $table->string('pan', 20)->nullable();
            $table->string('primary_color', 7)->default('#3B82F6');
            $table->string('currency_symbol', 5)->default('₹');
            $table->string('date_format')->default('d/m/Y');
            $table->string('timezone')->default('Asia/Kolkata');
            $table->string('academic_year_format')->default('April-March');
            $table->string('medium')->default('English');
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('online_payment_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};

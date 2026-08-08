<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('hostel_complaints')) {
            Schema::table('hostel_complaints', function (Blueprint $table) {
                if (!Schema::hasColumn('hostel_complaints', 'category'))
                    $table->string('category')->nullable()->after('room_id');
                if (!Schema::hasColumn('hostel_complaints', 'priority'))
                    $table->string('priority')->default('medium')->after('category');
                if (!Schema::hasColumn('hostel_complaints', 'vendor_name'))
                    $table->string('vendor_name')->nullable()->after('assigned_to');
                if (!Schema::hasColumn('hostel_complaints', 'reported_by'))
                    $table->unsignedBigInteger('reported_by')->nullable()->after('vendor_name');
            });
        }

        // Mess rebate table
        if (!Schema::hasTable('mess_rebates')) {
            Schema::create('mess_rebates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('allotment_id');
                $table->date('from_date');
                $table->date('to_date');
                $table->unsignedInteger('days_absent');
                $table->decimal('rebate_per_day', 8, 2)->default(0);
                $table->decimal('total_rebate', 10, 2)->default(0);
                $table->string('reason')->nullable();
                $table->string('status')->default('pending'); // pending, approved, applied
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->foreign('allotment_id')->references('id')->on('hostel_allotments')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_rebates');
    }
};

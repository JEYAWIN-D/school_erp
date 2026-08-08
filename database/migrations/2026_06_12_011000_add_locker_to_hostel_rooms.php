<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('hostel_rooms') && !Schema::hasColumn('hostel_rooms', 'has_locker')) {
            Schema::table('hostel_rooms', function (Blueprint $table) {
                $table->boolean('has_locker')->default(false);
                $table->string('fan_type', 20)->nullable(); // ceiling_fan, table_fan, none
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hostel_rooms') && Schema::hasColumn('hostel_rooms', 'has_locker')) {
            Schema::table('hostel_rooms', function (Blueprint $table) {
                $table->dropColumn(['has_locker', 'fan_type']);
            });
        }
    }
};

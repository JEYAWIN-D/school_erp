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
        Schema::table('transport_stops', function (Blueprint $table) {
            if (!Schema::hasColumn('transport_stops', 'vehicle_id')) {
                $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete()->after('route_id');
            }
            if (!Schema::hasColumn('transport_stops', 'van_number')) {
                $table->string('van_number', 50)->nullable()->after('name');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'gps_enabled')) {
                $table->boolean('gps_enabled')->default(true)->after('is_active');
            }
            if (!Schema::hasColumn('vehicles', 'gps_device_id')) {
                $table->string('gps_device_id', 50)->nullable()->after('gps_enabled');
            }
            if (!Schema::hasColumn('vehicles', 'gps_imei')) {
                $table->string('gps_imei', 30)->nullable()->after('gps_device_id');
            }
            if (!Schema::hasColumn('vehicles', 'gps_status')) {
                $table->string('gps_status', 30)->default('online')->after('gps_imei'); // online, in_transit, idle, offline
            }
            if (!Schema::hasColumn('vehicles', 'current_latitude')) {
                $table->decimal('current_latitude', 10, 7)->nullable()->after('gps_status');
            }
            if (!Schema::hasColumn('vehicles', 'current_longitude')) {
                $table->decimal('current_longitude', 10, 7)->nullable()->after('current_latitude');
            }
            if (!Schema::hasColumn('vehicles', 'current_location_name')) {
                $table->string('current_location_name', 150)->nullable()->after('current_longitude');
            }
            if (!Schema::hasColumn('vehicles', 'current_speed_kmh')) {
                $table->integer('current_speed_kmh')->default(0)->after('current_location_name');
            }
            if (!Schema::hasColumn('vehicles', 'battery_level')) {
                $table->integer('battery_level')->default(98)->after('current_speed_kmh');
            }
            if (!Schema::hasColumn('vehicles', 'ignition_status')) {
                $table->string('ignition_status', 20)->default('on')->after('battery_level'); // on, off
            }
            if (!Schema::hasColumn('vehicles', 'last_gps_ping')) {
                $table->timestamp('last_gps_ping')->nullable()->after('ignition_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_stops', function (Blueprint $table) {
            if (Schema::hasColumn('transport_stops', 'vehicle_id')) {
                $table->dropForeign(['vehicle_id']);
                $table->dropColumn('vehicle_id');
            }
            if (Schema::hasColumn('transport_stops', 'van_number')) {
                $table->dropColumn('van_number');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn([
                'gps_enabled', 'gps_device_id', 'gps_imei', 'gps_status',
                'current_latitude', 'current_longitude', 'current_location_name',
                'current_speed_kmh', 'battery_level', 'ignition_status', 'last_gps_ping'
            ]);
        });
    }
};

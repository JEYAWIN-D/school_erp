<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Add missing columns to vehicles
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'registration_number'))
                $table->string('registration_number', 30)->nullable()->after('vehicle_number');
            if (!Schema::hasColumn('vehicles', 'capacity'))
                $table->integer('capacity')->default(0)->after('seating_capacity');
            if (!Schema::hasColumn('vehicles', 'driver_contact'))
                $table->string('driver_contact', 20)->nullable()->after('driver_mobile');
            if (!Schema::hasColumn('vehicles', 'license_number'))
                $table->string('license_number', 30)->nullable()->after('driver_license');
            if (!Schema::hasColumn('vehicles', 'license_expiry'))
                $table->date('license_expiry')->nullable()->after('license_number');
            if (!Schema::hasColumn('vehicles', 'permit_expiry'))
                $table->date('permit_expiry')->nullable()->after('fitness_expiry');
            if (!Schema::hasColumn('vehicles', 'puc_expiry'))
                $table->date('puc_expiry')->nullable()->after('permit_expiry');
            if (!Schema::hasColumn('vehicles', 'tax_expiry'))
                $table->date('tax_expiry')->nullable()->after('puc_expiry');
            if (!Schema::hasColumn('vehicles', 'status'))
                $table->string('status', 20)->default('active')->after('is_active');
        });

        // Add missing columns to transport_allotments
        Schema::table('transport_allotments', function (Blueprint $table) {
            if (!Schema::hasColumn('transport_allotments', 'enrollment_id'))
                $table->unsignedBigInteger('enrollment_id')->nullable()->after('student_id');
            if (!Schema::hasColumn('transport_allotments', 'stop_id'))
                $table->unsignedBigInteger('stop_id')->nullable()->after('route_id');
            if (!Schema::hasColumn('transport_allotments', 'vehicle_id'))
                $table->unsignedBigInteger('vehicle_id')->nullable()->after('stop_id');
            if (!Schema::hasColumn('transport_allotments', 'fee'))
                $table->decimal('fee', 8, 2)->nullable()->after('vehicle_id');
            if (!Schema::hasColumn('transport_allotments', 'pickup_time'))
                $table->time('pickup_time')->nullable()->after('fee');
            if (!Schema::hasColumn('transport_allotments', 'drop_time'))
                $table->time('drop_time')->nullable()->after('pickup_time');
            if (!Schema::hasColumn('transport_allotments', 'boarding_stop'))
                $table->string('boarding_stop')->nullable()->after('drop_time');
        });
    }

    public function down(): void {
        Schema::table('vehicles', function (Blueprint $table) {
            $cols = ['registration_number', 'capacity', 'driver_contact', 'license_number',
                     'license_expiry', 'permit_expiry', 'puc_expiry', 'tax_expiry', 'status'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('vehicles', $c)) $table->dropColumn($c);
            }
        });
        Schema::table('transport_allotments', function (Blueprint $table) {
            $cols = ['enrollment_id', 'stop_id', 'vehicle_id', 'fee', 'pickup_time', 'drop_time', 'boarding_stop'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('transport_allotments', $c)) $table->dropColumn($c);
            }
        });
    }
};

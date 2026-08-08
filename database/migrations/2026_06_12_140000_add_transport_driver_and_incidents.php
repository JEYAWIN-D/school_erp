<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                if (!Schema::hasColumn('vehicles', 'driver_license_expiry'))
                    $table->date('driver_license_expiry')->nullable()->after('driver_license');
                if (!Schema::hasColumn('vehicles', 'police_verification_status'))
                    $table->enum('police_verification_status', ['pending', 'verified', 'expired'])->default('pending')->after('driver_license_expiry');
                if (!Schema::hasColumn('vehicles', 'police_verification_date'))
                    $table->date('police_verification_date')->nullable()->after('police_verification_status');
                if (!Schema::hasColumn('vehicles', 'police_verification_notes'))
                    $table->string('police_verification_notes')->nullable()->after('police_verification_date');
            });
        }

        if (!Schema::hasTable('transport_incidents')) {
            Schema::create('transport_incidents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vehicle_id')->nullable();
                $table->unsignedBigInteger('route_id')->nullable();
                $table->date('incident_date');
                $table->enum('incident_type', ['accident', 'breakdown', 'theft', 'vandalism', 'other']);
                $table->string('location')->nullable();
                $table->text('description');
                $table->enum('severity', ['minor', 'moderate', 'major'])->default('minor');
                $table->text('action_taken')->nullable();
                $table->string('reported_by')->nullable();
                $table->string('fir_number')->nullable();
                $table->decimal('estimated_loss', 10, 2)->nullable();
                $table->enum('status', ['open', 'under_review', 'resolved', 'closed'])->default('open');
                $table->string('resolution_notes')->nullable();
                $table->date('resolved_date')->nullable();
                $table->timestamps();
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->nullOnDelete();
                $table->foreign('route_id')->references('id')->on('transport_routes')->nullOnDelete();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('transport_incidents');
        if (Schema::hasTable('vehicles')) {
            Schema::table('vehicles', function (Blueprint $table) {
                $table->dropColumn(['driver_license_expiry', 'police_verification_status',
                    'police_verification_date', 'police_verification_notes']);
            });
        }
    }
};

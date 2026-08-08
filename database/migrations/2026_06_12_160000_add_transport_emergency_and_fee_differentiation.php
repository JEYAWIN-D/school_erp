<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('transport_emergency_contacts')) {
            Schema::create('transport_emergency_contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('route_id');
                $table->string('name');
                $table->string('phone', 20);
                $table->string('relationship')->nullable(); // Police, Hospital, Admin, Parent Rep
                $table->string('designation')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->string('notes')->nullable();
                $table->timestamps();
                $table->foreign('route_id')->references('id')->on('transport_routes')->cascadeOnDelete();
            });
        }

        // Fee structure: applies to new_admission / existing / all
        if (Schema::hasTable('fee_structures') && !Schema::hasColumn('fee_structures', 'applies_to')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                $table->enum('applies_to', ['all', 'new_admission', 'existing'])->default('all')->after('amount');
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('transport_emergency_contacts');
        if (Schema::hasTable('fee_structures') && Schema::hasColumn('fee_structures', 'applies_to')) {
            Schema::table('fee_structures', function (Blueprint $table) {
                $table->dropColumn('applies_to');
            });
        }
    }
};

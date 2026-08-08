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
            $table->time('pickup_time')->nullable()->after('arrival_time');
            $table->time('drop_time')->nullable()->after('pickup_time');
            $table->decimal('fare', 8, 2)->nullable()->after('drop_time');
            $table->string('landmark')->nullable()->after('fare');
        });
    }

    public function down(): void
    {
        Schema::table('transport_stops', function (Blueprint $table) {
            $table->dropColumn(['pickup_time', 'drop_time', 'fare', 'landmark']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('designations')) {
            Schema::table('designations', function (Blueprint $table) {
                if (!Schema::hasColumn('designations', 'pay_band_min'))
                    $table->decimal('pay_band_min', 10, 2)->nullable()->after('grade');
                if (!Schema::hasColumn('designations', 'pay_band_max'))
                    $table->decimal('pay_band_max', 10, 2)->nullable()->after('pay_band_min');
                if (!Schema::hasColumn('designations', 'pay_scale'))
                    $table->string('pay_scale')->nullable()->after('pay_band_max');
            });
        }
    }

    public function down(): void {}
};

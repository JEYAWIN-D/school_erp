<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('designations', 'spatie_role')) {
            Schema::table('designations', function (Blueprint $table) {
                $table->string('spatie_role')->nullable()->after('grade')
                      ->comment('Spatie permission role name assigned to this designation');
            });
        }
    }

    public function down(): void {
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn('spatie_role');
        });
    }
};

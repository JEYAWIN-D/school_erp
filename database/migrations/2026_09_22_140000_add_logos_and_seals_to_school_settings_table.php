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
        if (Schema::hasTable('school_settings')) {
            Schema::table('school_settings', function (Blueprint $table) {
                // Secondary school logo / crest / emblem (Slot 2)
                if (!Schema::hasColumn('school_settings', 'logo_2')) {
                    $table->string('logo_2')->nullable()->after('logo');
                }

                // Dedicated Seal Slots (1 through 6)
                if (!Schema::hasColumn('school_settings', 'seal_1')) {
                    $table->string('seal_1')->nullable()->after('school_stamp');
                }
                if (!Schema::hasColumn('school_settings', 'seal_2')) {
                    $table->string('seal_2')->nullable()->after('seal_1');
                }
                if (!Schema::hasColumn('school_settings', 'seal_3')) {
                    $table->string('seal_3')->nullable()->after('seal_2');
                }
                if (!Schema::hasColumn('school_settings', 'seal_4')) {
                    $table->string('seal_4')->nullable()->after('seal_3');
                }
                if (!Schema::hasColumn('school_settings', 'seal_5')) {
                    $table->string('seal_5')->nullable()->after('seal_4');
                }
                if (!Schema::hasColumn('school_settings', 'seal_6')) {
                    $table->string('seal_6')->nullable()->after('seal_5');
                }

                // Custom metadata for seal labels / purposes
                if (!Schema::hasColumn('school_settings', 'seals_meta')) {
                    $table->json('seals_meta')->nullable()->after('seal_6');
                }
            });

            // If existing school_stamp is set, initialize seal_1 with it for backwards compatibility
            $existing = \Illuminate\Support\Facades\DB::table('school_settings')->first();
            if ($existing && !empty($existing->school_stamp) && empty($existing->seal_1)) {
                \Illuminate\Support\Facades\DB::table('school_settings')
                    ->where('id', $existing->id)
                    ->update(['seal_1' => $existing->school_stamp]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('school_settings')) {
            Schema::table('school_settings', function (Blueprint $table) {
                $columns = ['logo_2', 'seal_1', 'seal_2', 'seal_3', 'seal_4', 'seal_5', 'seal_6', 'seals_meta'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('school_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

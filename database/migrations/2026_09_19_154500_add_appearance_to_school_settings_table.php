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
                if (!Schema::hasColumn('school_settings', 'font_size')) {
                    $table->string('font_size', 20)->default('default')->after('primary_color');
                }
                if (!Schema::hasColumn('school_settings', 'font_family')) {
                    $table->string('font_family', 30)->default('default')->after('font_size');
                }
                if (!Schema::hasColumn('school_settings', 'theme_mode')) {
                    $table->string('theme_mode', 20)->default('light')->after('font_family');
                }
                if (!Schema::hasColumn('school_settings', 'ui_density')) {
                    $table->string('ui_density', 20)->default('comfortable')->after('theme_mode');
                }
                if (!Schema::hasColumn('school_settings', 'sidebar_preference')) {
                    $table->string('sidebar_preference', 20)->default('expanded')->after('ui_density');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('school_settings')) {
            Schema::table('school_settings', function (Blueprint $table) {
                $columns = ['font_size', 'font_family', 'theme_mode', 'ui_density', 'sidebar_preference'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('school_settings', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

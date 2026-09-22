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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'dress_size')) {
                $table->string('dress_size', 50)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('students', 'shoe_size')) {
                $table->string('shoe_size', 50)->nullable()->after('dress_size');
            }
            if (!Schema::hasColumn('students', 'second_language')) {
                $table->string('second_language', 100)->nullable()->after('mother_tongue');
            }
            if (!Schema::hasColumn('students', 'custom_kit_items')) {
                $table->json('custom_kit_items')->nullable()->after('selected_eca');
            }
            if (!Schema::hasColumn('students', 'textbook_custom_fields')) {
                $table->json('textbook_custom_fields')->nullable()->after('custom_kit_items');
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'dress_size')) {
                $table->string('dress_size', 50)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('enquiries', 'shoe_size')) {
                $table->string('shoe_size', 50)->nullable()->after('dress_size');
            }
            if (!Schema::hasColumn('enquiries', 'second_language')) {
                $table->string('second_language', 100)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = ['dress_size', 'shoe_size', 'second_language', 'custom_kit_items', 'textbook_custom_fields'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('enquiries', function (Blueprint $table) {
            $cols = ['dress_size', 'shoe_size', 'second_language'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('enquiries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

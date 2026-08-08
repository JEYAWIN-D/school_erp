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
        if (!Schema::hasColumn('exams', 'is_external')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->boolean('is_external')->default(false)->after('type');
                $table->string('conducting_body')->nullable()->after('is_external');
            });
        }
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['is_external', 'conducting_body']);
        });
    }
};

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
            if (!Schema::hasColumn('students', 'leaving_date')) {
                $table->date('leaving_date')->nullable();
            }
            if (!Schema::hasColumn('students', 'leaving_reason')) {
                $table->string('leaving_reason')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['leaving_date', 'leaving_reason']);
        });
    }
};

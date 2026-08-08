<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'passport_number')) {
                $table->string('passport_number', 50)->nullable()->after('aadhaar_no');
            }
            if (!Schema::hasColumn('students', 'passport_expiry')) {
                $table->date('passport_expiry')->nullable()->after('passport_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['passport_number', 'passport_expiry']);
        });
    }
};

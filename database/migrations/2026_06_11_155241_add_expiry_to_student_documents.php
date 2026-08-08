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
        Schema::table('student_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('student_documents', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('student_documents', 'reminder_sent')) {
                $table->boolean('reminder_sent')->default(false)->after('expiry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $cols = array_filter(['expiry_date','reminder_sent'], fn($c) => Schema::hasColumn('student_documents', $c));
            if ($cols) $table->dropColumn($cols);
        });
    }
};

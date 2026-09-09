<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('students', 'document_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('document_token', 64)->nullable()->unique()->after('status');
            });

            // Backfill existing students with secure random tokens
            $students = DB::table('students')->whereNull('document_token')->select('id')->get();
            foreach ($students as $student) {
                DB::table('students')->where('id', $student->id)->update([
                    'document_token' => Str::random(32),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'document_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('document_token');
            });
        }
    }
};
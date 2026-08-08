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
        Schema::table('syllabus', function (Blueprint $table) {
            if (!Schema::hasColumn('syllabus', 'term')) {
                $table->string('term')->nullable()->after('academic_year_id');
            }
            if (!Schema::hasColumn('syllabus', 'document_path')) {
                $table->string('document_path')->nullable()->after('term');
            }
        });
    }

    public function down(): void
    {
        Schema::table('syllabus', function (Blueprint $table) {
            $table->dropColumn(['term', 'document_path']);
        });
    }
};

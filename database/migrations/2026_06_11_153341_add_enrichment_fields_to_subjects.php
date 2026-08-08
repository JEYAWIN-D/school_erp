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
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'is_elective')) {
                $table->boolean('is_elective')->default(false)->after('is_active');
            }
            if (!Schema::hasColumn('subjects', 'language_type')) {
                $table->enum('language_type', ['first', 'second', 'third', 'none'])->default('none')->after('is_elective');
            }
            if (!Schema::hasColumn('subjects', 'board_curriculum')) {
                $table->enum('board_curriculum', ['cbse', 'state', 'both'])->default('cbse')->after('language_type');
            }
            if (!Schema::hasColumn('subjects', 'credit_hours')) {
                $table->unsignedTinyInteger('credit_hours')->default(0)->after('board_curriculum');
            }
            if (!Schema::hasColumn('subjects', 'stream')) {
                $table->string('stream', 20)->nullable()->after('credit_hours')
                    ->comment('science,commerce,arts,vocational — for XI-XII');
            }
            if (!Schema::hasColumn('subjects', 'medium')) {
                $table->enum('medium', ['english', 'tamil', 'both'])->default('english')->after('stream');
            }
            if (!Schema::hasColumn('subjects', 'is_coscholastic')) {
                $table->boolean('is_coscholastic')->default(false)->after('medium')
                    ->comment('True for art/sport/values — graded A-D not numeric');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['is_elective', 'language_type', 'board_curriculum', 'credit_hours', 'stream', 'medium', 'is_coscholastic']);
        });
    }
};

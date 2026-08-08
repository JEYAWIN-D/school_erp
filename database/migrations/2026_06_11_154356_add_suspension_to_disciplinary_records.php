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
        $tbl = 'student_disciplinaries';
        if (!Schema::hasTable($tbl)) {
            Schema::create($tbl, function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->cascadeOnDelete();
                $table->date('incident_date');
                $table->string('incident_type', 50);
                $table->text('description');
                $table->text('action_taken')->nullable();
                $table->enum('action_type', ['warning','suspension','expulsion','positive_award','other'])->default('warning');
                $table->date('suspension_from')->nullable();
                $table->date('suspension_to')->nullable();
                $table->string('award_name', 100)->nullable();
                $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('parent_notified')->default(false);
                $table->timestamp('parent_notified_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table($tbl, function (Blueprint $table) use ($tbl) {
                if (!Schema::hasColumn($tbl, 'action_type')) {
                    $table->enum('action_type', ['warning','suspension','expulsion','positive_award','other'])
                        ->default('warning')->after('action_taken');
                }
                if (!Schema::hasColumn($tbl, 'suspension_from')) {
                    $table->date('suspension_from')->nullable()->after('action_type');
                }
                if (!Schema::hasColumn($tbl, 'suspension_to')) {
                    $table->date('suspension_to')->nullable()->after('suspension_from');
                }
                if (!Schema::hasColumn($tbl, 'award_name')) {
                    $table->string('award_name', 100)->nullable()->after('suspension_to');
                }
                if (!Schema::hasColumn($tbl, 'parent_notified_at')) {
                    $table->timestamp('parent_notified_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_disciplinaries', function (Blueprint $table) {
            $cols = ['action_type','suspension_from','suspension_to','award_name','parent_notified_at'];
            $toDrop = array_filter($cols, fn($c) => Schema::hasColumn('student_disciplinaries', $c));
            if ($toDrop) $table->dropColumn($toDrop);
        });
    }
};

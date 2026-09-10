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
            // Parent & Guardian Photos for Campus Visitor Pass
            if (!Schema::hasColumn('students', 'father_photo')) {
                $table->string('father_photo')->nullable()->after('father_email');
            }
            if (!Schema::hasColumn('students', 'mother_photo')) {
                $table->string('mother_photo')->nullable()->after('mother_email');
            }
            if (!Schema::hasColumn('students', 'guardian_photo')) {
                $table->string('guardian_photo')->nullable()->after('guardian_mobile');
            }

            // 2-Tier Admission Approval Hierarchy
            if (!Schema::hasColumn('students', 'principal_approved_at')) {
                $table->timestamp('principal_approved_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'principal_approved_by')) {
                $table->foreignId('principal_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('principal_approved_at');
            }
            if (!Schema::hasColumn('students', 'principal_notes')) {
                $table->text('principal_notes')->nullable()->after('principal_approved_by');
            }

            if (!Schema::hasColumn('students', 'admin_approved_at')) {
                $table->timestamp('admin_approved_at')->nullable()->after('principal_notes');
            }
            if (!Schema::hasColumn('students', 'admin_approved_by')) {
                $table->foreignId('admin_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('admin_approved_at');
            }
            if (!Schema::hasColumn('students', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('admin_approved_by');
            }

            if (!Schema::hasColumn('students', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('students', 'rejected_by')) {
                $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete()->after('rejection_reason');
            }
            if (!Schema::hasColumn('students', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('rejected_by');
            }

            // Public / Gate Security Token for Parent & Guardian Visitor Card
            if (!Schema::hasColumn('students', 'parent_visitor_pass_token')) {
                $table->string('parent_visitor_pass_token', 64)->nullable()->unique()->after('rejected_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $cols = [
                'father_photo', 'mother_photo', 'guardian_photo',
                'principal_approved_at', 'principal_approved_by', 'principal_notes',
                'admin_approved_at', 'admin_approved_by', 'admin_notes',
                'rejection_reason', 'rejected_by', 'rejected_at',
                'parent_visitor_pass_token'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

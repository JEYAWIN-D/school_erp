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
        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'applied_by')) {
                $table->foreignId('applied_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('leave_requests', 'applied_on_behalf')) {
                $table->boolean('applied_on_behalf')->default(false)->after('applied_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            if (Schema::hasColumn('leave_requests', 'applied_by')) {
                $table->dropForeign(['applied_by']);
                $table->dropColumn('applied_by');
            }
            if (Schema::hasColumn('leave_requests', 'applied_on_behalf')) {
                $table->dropColumn('applied_on_behalf');
            }
        });
    }
};

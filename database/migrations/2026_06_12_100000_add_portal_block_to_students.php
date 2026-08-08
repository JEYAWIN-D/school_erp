<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'portal_blocked')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('portal_blocked')->default(false)->after('status');
                $table->string('portal_block_reason')->nullable()->after('portal_blocked');
                $table->timestamp('portal_blocked_at')->nullable()->after('portal_block_reason');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumnIfExists(['portal_blocked', 'portal_block_reason', 'portal_blocked_at']);
            });
        }
    }
};

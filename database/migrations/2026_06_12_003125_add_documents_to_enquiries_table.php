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
        if (!Schema::hasColumn('enquiries', 'documents')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->json('documents')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('enquiries', 'documents')) {
            Schema::table('enquiries', function (Blueprint $table) {
                $table->dropColumn('documents');
            });
        }
    }
};

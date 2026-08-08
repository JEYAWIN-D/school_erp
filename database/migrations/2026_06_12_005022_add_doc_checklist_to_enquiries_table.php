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
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'doc_checklist')) {
                $table->json('doc_checklist')->nullable()->after('documents');
            }
            if (!Schema::hasColumn('enquiries', 'referral_name')) {
                $table->string('referral_name')->nullable()->after('source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['doc_checklist', 'referral_name']);
        });
    }
};

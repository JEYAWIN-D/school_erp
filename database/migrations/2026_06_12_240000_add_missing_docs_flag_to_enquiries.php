<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('enquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('enquiries', 'missing_docs')) {
                $table->json('missing_docs')->nullable()->after('doc_checklist')
                      ->comment('Documents flagged as missing, notified to parent');
            }
            if (!Schema::hasColumn('enquiries', 'docs_flag_note')) {
                $table->string('docs_flag_note', 500)->nullable()->after('missing_docs');
            }
        });
    }

    public function down(): void {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn(['missing_docs', 'docs_flag_note']);
        });
    }
};

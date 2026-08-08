<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('location');
            }
            if (!Schema::hasColumn('books', 'is_deaccessioned')) {
                $table->boolean('is_deaccessioned')->default(false)->after('is_available');
            }
            if (!Schema::hasColumn('books', 'deaccession_date')) {
                $table->date('deaccession_date')->nullable()->after('is_deaccessioned');
            }
            if (!Schema::hasColumn('books', 'deaccession_reason')) {
                $table->string('deaccession_reason')->nullable()->after('deaccession_date');
            }
            if (!Schema::hasColumn('books', 'deaccession_by')) {
                $table->unsignedBigInteger('deaccession_by')->nullable()->after('deaccession_reason');
            }
        });

        Schema::table('book_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('book_issues', 'fine_collected_at')) {
                $table->timestamp('fine_collected_at')->nullable()->after('fine_paid');
            }
            if (!Schema::hasColumn('book_issues', 'fine_collection_mode')) {
                $table->string('fine_collection_mode', 30)->nullable()->after('fine_collected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'is_deaccessioned', 'deaccession_date', 'deaccession_reason', 'deaccession_by']);
        });
        Schema::table('book_issues', function (Blueprint $table) {
            $table->dropColumn(['fine_collected_at', 'fine_collection_mode']);
        });
    }
};

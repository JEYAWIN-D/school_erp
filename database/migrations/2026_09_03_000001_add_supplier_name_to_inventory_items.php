<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_items', 'supplier_name')) {
                    $table->string('supplier_name', 150)->nullable()->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                if (Schema::hasColumn('inventory_items', 'supplier_name')) {
                    $table->dropColumn('supplier_name');
                }
            });
        }
    }
};

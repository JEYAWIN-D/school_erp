<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Extend inventory_items
        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                if (!Schema::hasColumn('inventory_items', 'inventory_type')) {
                    $table->string('inventory_type')->default('academic')->after('category_id'); // academic vs general_operations
                }
                if (!Schema::hasColumn('inventory_items', 'student_price')) {
                    $table->decimal('student_price', 10, 2)->default(0.00)->after('unit_price');
                }
                if (!Schema::hasColumn('inventory_items', 'purchase_cost')) {
                    $table->decimal('purchase_cost', 10, 2)->default(0.00)->after('unit_price');
                }
                if (!Schema::hasColumn('inventory_items', 'academic_category')) {
                    $table->string('academic_category')->nullable()->after('inventory_type');
                }
            });
        }

        // 2. Add is_inventory_issued flag to enquiries
        if (Schema::hasTable('enquiries')) {
            Schema::table('enquiries', function (Blueprint $table) {
                if (!Schema::hasColumn('enquiries', 'is_inventory_issued')) {
                    $table->boolean('is_inventory_issued')->default(false)->after('status');
                }
            });
        }

        // 3. Admission Kit Configurations
        if (!Schema::hasTable('admission_kit_configs')) {
            Schema::create('admission_kit_configs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->integer('default_quantity')->default(1);
                $table->string('unit')->default('pcs');
                $table->boolean('is_default_included')->default(true);
                $table->boolean('allow_additional_qty')->default(false); // true for notebooks, dress, socks
                $table->decimal('student_charge', 10, 2)->default(0.00);
                $table->timestamps();

                $table->unique(['class_id', 'item_id', 'academic_year_id'], 'adm_kit_class_item_year_unique');
            });
        }

        // 4. Inventory Transactions Audit Ledger
        if (!Schema::hasTable('inventory_transactions')) {
            Schema::create('inventory_transactions', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_code')->unique();
                $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->string('transaction_type'); // stock_in, manual_stock_out, admission_issue, adjustment_increase, adjustment_decrease, return_reversal
                $table->integer('quantity');
                $table->integer('previous_stock');
                $table->integer('quantity_changed');
                $table->integer('new_stock');
                $table->decimal('unit_cost', 10, 2)->default(0.00);
                $table->decimal('total_cost', 10, 2)->default(0.00);
                $table->string('reference_type')->nullable(); // admission, manual, vendor_invoice, adjustment
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('invoice_number')->nullable();
                $table->date('invoice_date')->nullable();
                $table->string('supplier_name')->nullable();
                $table->string('invoice_path')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 5. Admission Inventory Issues (Per Student / Admission audit)
        if (!Schema::hasTable('admission_inventory_issues')) {
            Schema::create('admission_inventory_issues', function (Blueprint $table) {
                $table->id();
                $table->foreignId('enquiry_id')->nullable()->constrained('enquiries')->nullOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
                $table->integer('default_quantity')->default(0);
                $table->integer('additional_quantity')->default(0);
                $table->integer('total_quantity')->default(0);
                $table->decimal('unit_charge', 10, 2)->default(0.00);
                $table->decimal('additional_charge', 10, 2)->default(0.00);
                $table->foreignId('inventory_transaction_id')->nullable()->constrained('inventory_transactions')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_inventory_issues');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('admission_kit_configs');

        if (Schema::hasTable('enquiries')) {
            Schema::table('enquiries', function (Blueprint $table) {
                if (Schema::hasColumn('enquiries', 'is_inventory_issued')) {
                    $table->dropColumn('is_inventory_issued');
                }
            });
        }

        if (Schema::hasTable('inventory_items')) {
            Schema::table('inventory_items', function (Blueprint $table) {
                $table->dropColumn(['inventory_type', 'student_price', 'purchase_cost', 'academic_category']);
            });
        }
    }
};

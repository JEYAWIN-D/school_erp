<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inventory_categories')) {
            Schema::create('inventory_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique()->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('inventory_categories')->cascadeOnDelete();
                $table->string('name');
                $table->string('item_code')->unique();
                $table->string('unit'); // pcs, kg, litre, box, etc.
                $table->decimal('unit_price', 10, 2)->default(0);
                $table->integer('reorder_level')->default(0);
                $table->integer('current_stock')->default(0);
                $table->string('location')->nullable(); // shelf/room
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('vendors')) {
            Schema::create('vendors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique()->nullable();
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('gstin')->nullable();
                $table->string('pan')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_requisitions')) {
            Schema::create('purchase_requisitions', function (Blueprint $table) {
                $table->id();
                $table->string('pr_number')->unique();
                $table->date('required_by');
                $table->text('purpose')->nullable();
                $table->enum('status', ['pending','approved','rejected','ordered'])->default('pending');
                $table->foreignId('requested_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->timestamp('approved_at')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_requisition_items')) {
            Schema::create('purchase_requisition_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('requisition_id')->constrained('purchase_requisitions')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items');
                $table->integer('quantity');
                $table->decimal('estimated_price', 10, 2)->nullable();
                $table->text('remark')->nullable();
            });
        }

        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->string('po_number')->unique();
                $table->foreignId('vendor_id')->constrained('vendors');
                $table->foreignId('requisition_id')->nullable()->constrained('purchase_requisitions');
                $table->date('order_date');
                $table->date('expected_delivery')->nullable();
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->enum('status', ['draft','sent','partial','received','cancelled'])->default('draft');
                $table->text('terms')->nullable();
                $table->foreignId('created_by')->constrained('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('po_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items');
                $table->integer('quantity');
                $table->decimal('unit_price', 10, 2);
                $table->decimal('total_price', 10, 2)->storedAs('quantity * unit_price');
                $table->integer('received_qty')->default(0);
            });
        }

        if (!Schema::hasTable('grn_records')) {
            Schema::create('grn_records', function (Blueprint $table) {
                $table->id();
                $table->string('grn_number')->unique();
                $table->foreignId('po_id')->constrained('purchase_orders');
                $table->date('received_date');
                $table->string('invoice_number')->nullable();
                $table->date('invoice_date')->nullable();
                $table->decimal('invoice_amount', 12, 2)->nullable();
                $table->text('remarks')->nullable();
                $table->foreignId('received_by')->constrained('users');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('grn_items')) {
            Schema::create('grn_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('grn_id')->constrained('grn_records')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items');
                $table->foreignId('po_item_id')->nullable()->constrained('purchase_order_items');
                $table->integer('received_qty');
                $table->integer('accepted_qty');
                $table->integer('rejected_qty')->default(0);
                $table->text('rejection_reason')->nullable();
            });
        }

        if (!Schema::hasTable('stock_issuances')) {
            Schema::create('stock_issuances', function (Blueprint $table) {
                $table->id();
                $table->string('issue_number')->unique();
                $table->date('issue_date');
                $table->string('issued_to'); // name/department
                $table->foreignId('issued_by')->constrained('users');
                $table->text('purpose')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stock_issuance_items')) {
            Schema::create('stock_issuance_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('issuance_id')->constrained('stock_issuances')->cascadeOnDelete();
                $table->foreignId('item_id')->constrained('inventory_items');
                $table->integer('quantity');
                $table->text('remark')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'stock_issuance_items','stock_issuances',
            'grn_items','grn_records',
            'purchase_order_items','purchase_orders',
            'purchase_requisition_items','purchase_requisitions',
            'vendors','inventory_items','inventory_categories',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};

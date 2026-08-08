<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Library member status on students
        if (Schema::hasTable('students') && !Schema::hasColumn('students', 'library_suspended')) {
            Schema::table('students', function (Blueprint $table) {
                $table->boolean('library_suspended')->default(false)->after('portal_blocked');
                $table->string('library_suspension_reason')->nullable()->after('library_suspended');
            });
        }

        // Hostel disciplinary: warning fields
        if (Schema::hasTable('hostel_disciplinary')) {
            if (!Schema::hasColumn('hostel_disciplinary', 'is_warning')) {
                Schema::table('hostel_disciplinary', function (Blueprint $table) {
                    $table->boolean('is_warning')->default(false)->after('action_taken');
                    $table->unsignedTinyInteger('warning_number')->nullable()->after('is_warning');
                    $table->date('warning_date')->nullable()->after('warning_number');
                    $table->string('severity')->default('minor')->after('warning_date'); // minor, moderate, severe
                    $table->string('vendor_name')->nullable()->after('severity');
                    $table->unsignedBigInteger('assigned_to')->nullable()->after('vendor_name');
                    $table->string('priority')->default('medium')->after('assigned_to');
                    $table->string('category')->nullable()->after('priority');
                });
            }
        }

        // Mess expenses table
        if (!Schema::hasTable('mess_expenses')) {
            Schema::create('mess_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hostel_id')->nullable();
                $table->date('expense_date');
                $table->string('category'); // ingredients, vendor, utilities, staff, other
                $table->string('description');
                $table->decimal('amount', 10, 2);
                $table->string('vendor')->nullable();
                $table->string('invoice_number')->nullable();
                $table->string('meal_type')->nullable(); // breakfast, lunch, snacks, dinner, all
                $table->unsignedBigInteger('recorded_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Vehicle: next service km tracker
        if (Schema::hasTable('vehicle_maintenance') && !Schema::hasColumn('vehicle_maintenance', 'current_km')) {
            Schema::table('vehicle_maintenance', function (Blueprint $table) {
                $table->unsignedInteger('current_km')->nullable()->after('cost');
                $table->unsignedInteger('next_service_km')->nullable()->after('current_km');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mess_expenses');
        if (Schema::hasTable('students')) {
            collect(['library_suspended', 'library_suspension_reason'])
                ->filter(fn($c) => Schema::hasColumn('students', $c))
                ->whenNotEmpty(fn() => Schema::table('students', fn($t) => $t->dropColumn(['library_suspended', 'library_suspension_reason'])));
        }
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Add pass token, photo, hostel_id to hostel_visitors
        if (Schema::hasTable('hostel_visitors')) {
            if (!Schema::hasColumn('hostel_visitors', 'pass_token')) {
                Schema::table('hostel_visitors', function (Blueprint $table) {
                    $table->string('hostel_id')->nullable()->after('student_id');
                    $table->string('pass_token', 64)->nullable()->unique()->after('id');
                    $table->timestamp('pass_valid_until')->nullable()->after('pass_token');
                    $table->string('visitor_photo')->nullable()->after('id_number');
                    $table->string('visitor_phone')->nullable()->after('visitor_mobile');
                    $table->string('destination')->nullable()->after('purpose');
                    $table->string('in_time')->nullable()->after('entry_time');
                    $table->string('out_time')->nullable()->after('exit_time');
                    $table->unsignedBigInteger('logged_by')->nullable()->after('registered_by');
                });
            }
        }

        // Hostel visiting hours config
        if (!Schema::hasTable('hostel_visiting_hours')) {
            Schema::create('hostel_visiting_hours', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('hostel_id')->nullable();
                $table->string('day_type')->default('all');  // all / weekday / weekend / holiday
                $table->time('from_time');
                $table->time('to_time');
                $table->boolean('is_active')->default(true);
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void {
        Schema::dropIfExists('hostel_visiting_hours');
    }
};

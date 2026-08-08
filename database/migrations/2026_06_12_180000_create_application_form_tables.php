<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Application Form Configurations (builder)
        if (!Schema::hasTable('application_form_configs')) {
            Schema::create('application_form_configs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('academic_year_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->json('fields');        // [{name,label,type,required,options}]
                $table->json('document_fields'); // [{name,label,types,max_mb,required}]
                $table->decimal('application_fee', 10, 2)->default(0);
                $table->date('open_from')->nullable();
                $table->date('open_until')->nullable();
                $table->string('link_token')->unique()->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Submitted Applications
        if (!Schema::hasTable('student_applications')) {
            Schema::create('student_applications', function (Blueprint $table) {
                $table->id();
                $table->string('application_number')->unique();
                $table->unsignedBigInteger('form_config_id');
                $table->unsignedBigInteger('academic_year_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('enquiry_id')->nullable();
                $table->json('form_data');      // submitted field values
                $table->json('documents')->nullable(); // uploaded file paths
                $table->enum('status', ['submitted','under_review','shortlisted','rejected','admitted'])->default('submitted');
                $table->string('parent_name')->nullable();
                $table->string('parent_mobile', 20)->nullable();
                $table->string('parent_email')->nullable();
                $table->string('student_name')->nullable();
                $table->text('admin_notes')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamps();
                $table->foreign('form_config_id')->references('id')->on('application_form_configs')->cascadeOnDelete();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('student_applications');
        Schema::dropIfExists('application_form_configs');
    }
};

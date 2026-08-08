<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_settings')) {
            Schema::table('school_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('school_settings', 'id_card_header_color')) {
                    $table->string('id_card_header_color', 7)->default('#1e3a5f')->after('primary_color');
                    $table->string('id_card_text_color', 7)->default('#1e3a5f')->after('id_card_header_color');
                    $table->string('id_card_bg_color', 7)->default('#ffffff')->after('id_card_text_color');
                    $table->string('id_card_header_text')->nullable()->after('id_card_bg_color');
                    $table->string('id_card_footer_text')->nullable()->after('id_card_header_text');
                    $table->boolean('id_card_show_blood_group')->default(true)->after('id_card_footer_text');
                    $table->boolean('id_card_show_dob')->default(true)->after('id_card_show_blood_group');
                    $table->boolean('id_card_show_qr')->default(true)->after('id_card_show_dob');
                    $table->boolean('id_card_show_mobile')->default(true)->after('id_card_show_qr');
                    $table->boolean('id_card_show_address')->default(false)->after('id_card_show_mobile');
                    $table->boolean('id_card_show_photo')->default(true)->after('id_card_show_address');
                }
            });
        }
    }

    public function down(): void {}
};

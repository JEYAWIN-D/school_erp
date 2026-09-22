<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE student_enrollments DROP CONSTRAINT IF EXISTS student_enrollments_status_check");
            DB::statement("ALTER TABLE student_enrollments ADD CONSTRAINT student_enrollments_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'promoted'::character varying, 'detained'::character varying, 'transferred'::character varying, 'left'::character varying, 'pending'::character varying, 'rejected'::character varying]::text[]))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE student_enrollments DROP CONSTRAINT IF EXISTS student_enrollments_status_check");
            DB::statement("ALTER TABLE student_enrollments ADD CONSTRAINT student_enrollments_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'promoted'::character varying, 'detained'::character varying, 'transferred'::character varying, 'left'::character varying]::text[]))");
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old check constraint if it exists
        DB::statement("ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check");

        // Recreate check constraint with pending_principal, principal_approved, and rejected included
        DB::statement("ALTER TABLE students ADD CONSTRAINT students_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'inactive'::character varying, 'transferred'::character varying, 'left'::character varying, 'alumni'::character varying, 'pending_principal'::character varying, 'principal_approved'::character varying, 'rejected'::character varying]::text[]))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE students DROP CONSTRAINT IF EXISTS students_status_check");
        DB::statement("ALTER TABLE students ADD CONSTRAINT students_status_check CHECK (status::text = ANY (ARRAY['active'::character varying, 'inactive'::character varying, 'transferred'::character varying, 'left'::character varying, 'alumni'::character varying]::text[]))");
    }
};

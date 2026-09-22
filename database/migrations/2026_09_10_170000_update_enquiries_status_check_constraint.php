<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Update enquiries_status_check to allow all pipeline and approval statuses
            DB::statement("ALTER TABLE enquiries DROP CONSTRAINT IF EXISTS enquiries_status_check");
            DB::statement("ALTER TABLE enquiries ADD CONSTRAINT enquiries_status_check CHECK (status::text = ANY (ARRAY[
                'new'::character varying,
                'follow_up'::character varying,
                'contacted'::character varying,
                'campus_visit'::character varying,
                'application'::character varying,
                'assessment'::character varying,
                'entrance_test'::character varying,
                'interview'::character varying,
                'document_verification'::character varying,
                'waitlisted'::character varying,
                'offered'::character varying,
                'confirmed'::character varying,
                'enrolled'::character varying,
                'pending_principal'::character varying,
                'pending_principal_approval'::character varying,
                'principal_approved'::character varying,
                'converted'::character varying,
                'rejected'::character varying,
                'lost'::character varying
            ]::text[]))");

            // Also update enquiry_follow_ups_status_check to match
            DB::statement("ALTER TABLE enquiry_follow_ups DROP CONSTRAINT IF EXISTS enquiry_follow_ups_status_check");
            DB::statement("ALTER TABLE enquiry_follow_ups ADD CONSTRAINT enquiry_follow_ups_status_check CHECK (status::text = ANY (ARRAY[
                'new'::character varying,
                'follow_up'::character varying,
                'contacted'::character varying,
                'campus_visit'::character varying,
                'application'::character varying,
                'assessment'::character varying,
                'entrance_test'::character varying,
                'interview'::character varying,
                'document_verification'::character varying,
                'waitlisted'::character varying,
                'offered'::character varying,
                'confirmed'::character varying,
                'enrolled'::character varying,
                'pending_principal'::character varying,
                'pending_principal_approval'::character varying,
                'principal_approved'::character varying,
                'converted'::character varying,
                'rejected'::character varying,
                'lost'::character varying
            ]::text[]))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE enquiries DROP CONSTRAINT IF EXISTS enquiries_status_check");
            DB::statement("ALTER TABLE enquiries ADD CONSTRAINT enquiries_status_check CHECK (status::text = ANY (ARRAY['new'::character varying, 'follow_up'::character varying, 'converted'::character varying, 'lost'::character varying]::text[]))");

            DB::statement("ALTER TABLE enquiry_follow_ups DROP CONSTRAINT IF EXISTS enquiry_follow_ups_status_check");
            DB::statement("ALTER TABLE enquiry_follow_ups ADD CONSTRAINT enquiry_follow_ups_status_check CHECK (status::text = ANY (ARRAY['new'::character varying, 'follow_up'::character varying, 'converted'::character varying, 'lost'::character varying]::text[]))");
        }
    }
};

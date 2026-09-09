<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Employee;

class EncryptExistingPii extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:encrypt-pii {--force : Force execution without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt legacy plaintext PII fields (Aadhaar, Passport, PAN, Bank account) across students and employees.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting PII encryption migration...');

        // 1. Process Students
        $students = Student::withTrashed()->get();
        $studentCount = 0;

        foreach ($students as $student) {
            $updated = false;

            // aadhaar_no
            $rawAadhaar = $student->getRawOriginal('aadhaar_no');
            if ($rawAadhaar && !$this->isEncrypted($rawAadhaar)) {
                $student->aadhaar_no = $rawAadhaar;
                $updated = true;
            }

            // father_aadhaar
            $rawFatherAadhaar = $student->getRawOriginal('father_aadhaar');
            if ($rawFatherAadhaar && !$this->isEncrypted($rawFatherAadhaar)) {
                $student->father_aadhaar = $rawFatherAadhaar;
                $updated = true;
            }

            // passport_number
            $rawPassport = $student->getRawOriginal('passport_number');
            if ($rawPassport && !$this->isEncrypted($rawPassport)) {
                $student->passport_number = $rawPassport;
                $updated = true;
            }

            if ($updated) {
                // Save directly to trigger EncryptedStringResilient set cast
                $student->saveQuietly();
                $studentCount++;
            }
        }

        $this->info("Encrypted {$studentCount} student records.");

        // 2. Process Employees
        $employees = Employee::withTrashed()->get();
        $employeeCount = 0;

        foreach ($employees as $employee) {
            $updated = false;

            // aadhaar_no
            $rawAadhaar = $employee->getRawOriginal('aadhaar_no');
            if ($rawAadhaar && !$this->isEncrypted($rawAadhaar)) {
                $employee->aadhaar_no = $rawAadhaar;
                $updated = true;
            }

            // pan_no
            $rawPan = $employee->getRawOriginal('pan_no');
            if ($rawPan && !$this->isEncrypted($rawPan)) {
                $employee->pan_no = $rawPan;
                $updated = true;
            }

            // bank_account_no
            $rawBank = $employee->getRawOriginal('bank_account_no');
            if ($rawBank && !$this->isEncrypted($rawBank)) {
                $employee->bank_account_no = $rawBank;
                $updated = true;
            }

            if ($updated) {
                $employee->saveQuietly();
                $employeeCount++;
            }
        }

        $this->info("Encrypted {$employeeCount} employee records.");
        $this->info('PII Encryption completed successfully!');

        return Command::SUCCESS;
    }

    /**
     * Check if a value is already an encrypted string.
     */
    private function isEncrypted(?string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        try {
            Crypt::decryptString($value);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

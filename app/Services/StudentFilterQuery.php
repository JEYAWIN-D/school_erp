<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;

/**
 * Reusable student filter service.
 *
 * Extracts the same filtering logic used in StudentController@index
 * so that the dashboard custom count cards use IDENTICAL query logic
 * and always match the Student Listing filtered count.
 *
 * Accepted filter keys (same as StudentController@index query params):
 *   class_id      – integer  (filters via currentEnrollment.class_id)
 *   section       – string   (A/B/C/D — filters via currentEnrollment.section.name)
 *   gender        – string   (male/female/other)
 *   student_type  – string   (day_scholar/hosteller/day_boarder)
 *   status        – string   (active/inactive/transferred/left/alumni — default: active)
 *   search        – string   (name / admission_no / mobile / father_name)
 */
class StudentFilterQuery
{
    /**
     * Build an Eloquent query builder with the given filters applied.
     * Does NOT paginate — caller decides what to do with the builder.
     */
    public static function build(array $filters = []): Builder
    {
        $status     = $filters['status']       ?? 'active';
        $classId    = $filters['class_id']     ?? null;
        $section    = $filters['section']      ?? null;
        $gender     = $filters['gender']       ?? null;
        $studentType= $filters['student_type'] ?? null;
        $search     = $filters['search']       ?? null;

        $query = Student::query()

            // Search (name, admission_no, mobile, father_name)
            ->when($search, function ($q) use ($search) {
                $v = strtolower(trim($search));
                $q->where(function ($sq) use ($v) {
                    $sq->whereRaw("LOWER(first_name) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(last_name) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name)) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(admission_no) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(mobile) LIKE ?", ["%$v%"])
                       ->orWhereRaw("LOWER(father_name) LIKE ?", ["%$v%"])
                       ->orWhereHas('currentEnrollment', function ($eq) use ($v) {
                           $eq->whereRaw("LOWER(roll_number) LIKE ?", ["%$v%"]);
                       });
                });
            })

            // Class filter
            ->when($classId, fn($q) => $q->whereHas(
                'currentEnrollment',
                fn($q) => $q->where('class_id', $classId)
            ))

            // Section filter (matches StudentController exactly)
            ->when($section, fn($q) => $q->whereHas(
                'currentEnrollment.section',
                function ($sq) use ($section) {
                    $rawSec   = trim($section);
                    $cleanSec = trim(preg_replace('/^section\s*/i', '', $rawSec));
                    $sq->whereRaw("UPPER(name) = ?", [strtoupper($cleanSec)])
                       ->orWhereRaw("UPPER(name) = ?", ['SECTION ' . strtoupper($cleanSec)])
                       ->orWhereRaw("UPPER(name) LIKE ?", ['%' . strtoupper($cleanSec) . '%']);
                }
            ))

            // Gender
            ->when($gender, fn($q) => $q->where('gender', $gender))

            // Student type
            ->when($studentType, fn($q) => $q->where('student_type', $studentType))

            // Status — default active (mirrors StudentController behaviour)
            ->when(
                $status && $status !== 'all',
                fn($q) => $q->where('status', $status),
                fn($q) => $q  // 'all' = no status filter
            );

        // If status not explicitly given at all, default to 'active'
        if (!isset($filters['status'])) {
            $query->where('status', 'active');
        }

        return $query;
    }

    /**
     * Return only the count for the given filters.
     * Used by the dashboard widget count cards.
     */
    public static function count(array $filters = []): int
    {
        return static::build($filters)->count();
    }

    /**
     * Build the Student Listing URL for a given filter array.
     * The "View students →" link in the dashboard custom card uses this
     * to ensure the linked listing shows exactly the same students.
     */
    public static function listingUrl(array $filters = []): string
    {
        $params = array_filter($filters, fn($v) => $v !== null && $v !== '');
        return route('students.index') . (count($params) ? '?' . http_build_query($params) : '');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Injects data-scope attributes into the request for non-admin roles.
 * Controllers use these to filter their queries.
 *
 * Sets on request:
 *   scope_class_ids    — array of class IDs the user may access (teachers)
 *   scope_section_ids  — array of section IDs the user may access (teachers)
 *   scope_student_ids  — array of student IDs the user may access (parents)
 *   scope_student_id   — single student ID (student portal users)
 *   scope_employee_id  — the user's own employee ID (staff members)
 *   is_scoped          — true when any scope constraint is active
 */
class ScopeToUser
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Full-access roles — no scoping
        if ($user->hasAnyRole(['super_admin', 'admin', 'owner', 'principal', 'vice_principal'])) {
            return $next($request);
        }

        $scope = [
            'scope_class_ids'   => [],
            'scope_section_ids' => [],
            'scope_student_ids' => [],
            'scope_student_id'  => null,
            'scope_employee_id' => $user->employee_id,
            'is_scoped'         => false,
        ];

        // ── Teachers: scope to their assigned classes/sections ─────
        if ($user->hasAnyRole(['class_teacher', 'subject_teacher', 'teacher', 'hod'])) {
            if ($user->employee_id) {
                $currentYearId = \App\Models\AcademicYear::current()?->id;
                $rows = DB::table('teacher_subject_allocations')
                    ->where('employee_id', $user->employee_id)
                    ->when($currentYearId, fn($q) => $q->where('academic_year_id', $currentYearId))
                    ->get(['class_id', 'section_id']);

                $scope['scope_class_ids']   = $rows->pluck('class_id')->unique()->filter()->values()->toArray();
                $scope['scope_section_ids'] = $rows->pluck('section_id')->unique()->filter()->values()->toArray();
                $scope['is_scoped']         = count($scope['scope_class_ids']) > 0;
            }
        }

        // ── Parent: scope to their children ───────────────────────
        if ($user->hasRole('parent')) {
            $scope['scope_student_ids'] = DB::table('parent_students')
                ->where('parent_user_id', $user->id)
                ->pluck('student_id')
                ->toArray();
            $scope['is_scoped'] = true;
        }

        // ── Student: scope to themselves ──────────────────────────
        if ($user->hasRole('student') && $user->student_id) {
            $scope['scope_student_id']  = $user->student_id;
            $scope['scope_student_ids'] = [$user->student_id];
            $scope['is_scoped']         = true;
        }

        $request->merge($scope);

        return $next($request);
    }
}

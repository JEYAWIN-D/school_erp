<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\DashboardWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * DashboardCardService
 *
 * Central registry that resolves any DashboardWidget to live data.
 * Returns:
 *   [
 *     'value'       => 621,
 *     'display'     => '621',
 *     'details'     => ['Boys' => 320, 'Girls' => 301], // or string e.g. "Arun, Priya..."
 *     'link_url'    => '/students?...',
 *     'link_label'  => 'View students →',
 *     'badge'       => 'Active',
 *   ]
 */
class DashboardCardService
{
    /**
     * Resolve a widget to its live data.
     */
    public static function resolve(DashboardWidget $widget): array
    {
        $cacheKey = "card_resolve_{$widget->id}_" . ($widget->updated_at?->timestamp ?? 0);
        return Cache::remember($cacheKey, 300, function () use ($widget) {
            $source   = $widget->source ?? 'student_count';
            $filters  = $widget->filters ?? [];
            $catalog  = DashboardWidget::sourceCatalog();
            $config   = $catalog[$source] ?? $catalog['student_count'];

            $resolvers = static::resolvers();
            $resolver  = $resolvers[$source] ?? $resolvers['student_count'];

            $value   = 0;
            $details = null;

            try {
                $result = $resolver($filters);
                if (is_array($result) && array_key_exists('value', $result)) {
                    $value   = $result['value'];
                    $details = $result['details'] ?? null;
                } else {
                    $value   = $result;
                    $details = null;
                }
            } catch (\Exception $e) {
                $value   = 0;
                $details = null;
            }

            $display    = static::format($value, $config['value_format']);
            $link_url   = static::buildLinkUrl($source, $filters, $config);
            $link_label = $config['link_label'] ?? 'View →';
            $badge      = static::badgeText($source, $config['value_format']);

            return compact('value', 'display', 'details', 'link_url', 'link_label', 'badge');
        });
    }

    /**
     * Preview count for the Add Card panel (JSON API).
     */
    public static function preview(string $source, array $filters = []): array
    {
        $widget          = new DashboardWidget();
        $widget->source  = $source;
        $widget->filters = $filters;
        $result = static::resolve($widget);
        return [
            'value'    => $result['value'],
            'display'  => $result['display'],
            'details'  => $result['details'],
            'link_url' => $result['link_url'],
        ];
    }

    // ── Private Helpers ───────────────────────────────────────────────────

    /**
     * Registry of resolver closures.
     */
    private static function resolvers(): array
    {
        return [

            // ── Students ─────────────────────────────────────────────────
            'students_total' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $row = DB::table('student_enrollments as se')
                    ->leftJoin('students as s', 's.id', '=', 'se.student_id')
                    ->where('se.status', 'active')
                    ->when($currentYear, fn($q) => $q->where('se.academic_year_id', $currentYear->id))
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN s.gender = 'male' THEN 1 END) as male, COUNT(CASE WHEN s.gender = 'female' THEN 1 END) as female")
                    ->first();
                return [
                    'value'   => $row->total ?? 0,
                    'details' => ['Boys' => $row->male ?? 0, 'Girls' => $row->female ?? 0],
                ];
            },

            'students_boys' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $row = DB::table('student_enrollments as se')
                    ->leftJoin('students as s', 's.id', '=', 'se.student_id')
                    ->where('se.status', 'active')
                    ->when($currentYear, fn($q) => $q->where('se.academic_year_id', $currentYear->id))
                    ->selectRaw("COUNT(CASE WHEN s.gender = 'male' THEN 1 END) as total, COUNT(*) as all_count")
                    ->first();
                $total = (int) ($row->total ?? 0);
                $all   = (int) ($row->all_count ?? 0);
                $pct   = $all > 0 ? round(($total / $all) * 100, 1) : 0;
                return [
                    'value'   => $total,
                    'details' => ['Share' => $pct . '%', 'Total' => $all],
                ];
            },

            'students_girls' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $row = DB::table('student_enrollments as se')
                    ->leftJoin('students as s', 's.id', '=', 'se.student_id')
                    ->where('se.status', 'active')
                    ->when($currentYear, fn($q) => $q->where('se.academic_year_id', $currentYear->id))
                    ->selectRaw("COUNT(CASE WHEN s.gender = 'female' THEN 1 END) as total, COUNT(*) as all_count")
                    ->first();
                $total = (int) ($row->total ?? 0);
                $all   = (int) ($row->all_count ?? 0);
                $pct   = $all > 0 ? round(($total / $all) * 100, 1) : 0;
                return [
                    'value'   => $total,
                    'details' => ['Share' => $pct . '%', 'Total' => $all],
                ];
            },

            'student_count' => function (array $filters) {
                $query = StudentFilterQuery::build($filters);
                $row = (clone $query)->selectRaw("COUNT(*) as total, COUNT(CASE WHEN gender = 'male' THEN 1 END) as male, COUNT(CASE WHEN gender = 'female' THEN 1 END) as female")->first();
                return [
                    'value'   => $row->total ?? 0,
                    'details' => ['Boys' => $row->male ?? 0, 'Girls' => $row->female ?? 0],
                ];
            },

            'student_birthdays_today' => function (array $filters) {
                $students = DB::table('students')
                    ->where('status', 'active')
                    ->whereRaw('EXTRACT(MONTH FROM dob) = ?', [now()->month])
                    ->whereRaw('EXTRACT(DAY FROM dob) = ?', [now()->day])
                    ->select('first_name', 'last_name')
                    ->get();
                $count = $students->count();
                $names = $students->take(3)->map(fn($s) => $s->first_name . ' ' . substr($s->last_name, 0, 1) . '.')->implode(', ');
                if ($count > 3) $names .= ' +' . ($count - 3) . ' more';
                return [
                    'value'   => $count,
                    'details' => $count > 0 ? $names : 'No birthdays today',
                ];
            },

            'admissions_month' => function (array $filters) {
                $row = DB::table('students')
                    ->where('status', 'active')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN student_type = 'day_scholar' THEN 1 END) as day_scholar, COUNT(CASE WHEN student_type = 'hosteller' THEN 1 END) as hosteller")
                    ->first();
                return [
                    'value'   => $row->total ?? 0,
                    'details' => ['Day Scholar' => $row->day_scholar ?? 0, 'Hosteller' => $row->hosteller ?? 0],
                ];
            },

            // ── Staff & HR ────────────────────────────────────────────────
            'staff_teachers' => function (array $filters) {
                $row = DB::table('employees')->where('is_active', true)
                    ->selectRaw("COUNT(CASE WHEN employee_type = 'teaching' THEN 1 END) as teaching, COUNT(*) as total")
                    ->first();
                return [
                    'value'   => $row->teaching ?? 0,
                    'details' => ['Total Staff' => $row->total ?? 0],
                ];
            },

            'staff_non_teaching' => function (array $filters) {
                $row = DB::table('employees')->where('is_active', true)
                    ->selectRaw("COUNT(CASE WHEN employee_type != 'teaching' THEN 1 END) as non_teaching, COUNT(*) as total")
                    ->first();
                return [
                    'value'   => $row->non_teaching ?? 0,
                    'details' => ['Total Staff' => $row->total ?? 0],
                ];
            },

            'staff_total' => function (array $filters) {
                $row = DB::table('employees')->where('is_active', true)
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN employee_type = 'teaching' THEN 1 END) as teaching, COUNT(CASE WHEN employee_type != 'teaching' THEN 1 END) as non_teaching")
                    ->first();
                return [
                    'value'   => $row->total ?? 0,
                    'details' => ['Teaching' => $row->teaching ?? 0, 'Non-Teaching' => $row->non_teaching ?? 0],
                ];
            },

            'staff_present_today' => function (array $filters) {
                $present = DB::table('staff_attendance')->whereDate('date', today())->where('status', 'present')->count();
                $total = DB::table('employees')->where('is_active', true)->count();
                $onLeave = DB::table('leave_requests')->where('status', 'approved')->whereDate('from_date', '<=', today())->whereDate('to_date', '>=', today())->count();
                return [
                    'value'   => $present,
                    'details' => ['Total Staff' => $total, 'On Leave' => $onLeave],
                ];
            },

            'staff_absent_today' => function (array $filters) {
                $totalStaff = DB::table('employees')->where('is_active', true)->count();
                $present = DB::table('staff_attendance')->whereDate('date', today())->where('status', 'present')->count();
                $onLeave = DB::table('leave_requests')->where('status', 'approved')->whereDate('from_date', '<=', today())->whereDate('to_date', '>=', today())->count();
                $absent = max($onLeave, max(0, $totalStaff - $present));
                return [
                    'value'   => $absent,
                    'details' => ['On Leave' => $onLeave, 'Present' => $present],
                ];
            },

            'staff_on_leave_today' => function (array $filters) {
                $approved = DB::table('leave_requests')->where('status', 'approved')->whereDate('from_date', '<=', today())->whereDate('to_date', '>=', today())->count();
                $pending = DB::table('leave_requests')->where('status', 'pending')->count();
                return [
                    'value'   => $approved,
                    'details' => ['Approved' => $approved, 'Pending' => $pending],
                ];
            },

            'leave_pending' => function (array $filters) {
                $count = DB::table('leave_requests')->where('status', 'pending')->count();
                return [
                    'value'   => $count,
                    'details' => ['Awaiting Review' => $count],
                ];
            },

            // ── Admissions ───────────────────────────────────────────────
            'admissions_total' => function (array $filters) {
                $row = DB::table('enquiries')
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status = 'new' THEN 1 END) as new_count, COUNT(CASE WHEN status = 'converted' THEN 1 END) as converted")
                    ->first();
                return [
                    'value'   => (int) ($row->total ?? 0),
                    'details' => ['New' => (int) ($row->new_count ?? 0), 'Converted' => (int) ($row->converted ?? 0)],
                ];
            },

            'admissions_pending' => function (array $filters) {
                $row = DB::table('enquiries')
                    ->selectRaw("COUNT(CASE WHEN status = 'new' THEN 1 END) as new_count, COUNT(CASE WHEN status IN ('pending', 'pending_principal_approval') THEN 1 END) as pending")
                    ->first();
                $new = (int) ($row->new_count ?? 0);
                $pending = (int) ($row->pending ?? 0);
                return [
                    'value'   => $new + $pending,
                    'details' => ['New' => $new, 'Pending Approval' => $pending],
                ];
            },

            // ── Approvals & Tasks ─────────────────────────────────────────
            'pending_approvals' => function (array $filters) {
                $admissions = DB::table('enquiries')->whereIn('status', ['new', 'pending', 'pending_principal_approval'])->count();
                $leaves = DB::table('leave_requests')->where('status', 'pending')->count();
                $tc = DB::table('tc_requests')->where('status', 'pending')->count();
                $total = $admissions + $leaves + $tc;
                return [
                    'value'   => $total,
                    'details' => ['Admissions' => $admissions, 'Leave Requests' => $leaves, 'TC Requests' => $tc],
                ];
            },

            // ── Fees ─────────────────────────────────────────────────────
            'fee_collected_today' => function (array $filters) {
                $row = DB::table('fee_payments')->where('is_cancelled', false)
                    ->whereDate('payment_date', today())
                    ->selectRaw("COALESCE(SUM(amount), 0) as total, COUNT(*) as count")
                    ->first();
                return [
                    'value'   => (float) ($row->total ?? 0),
                    'details' => ['Receipts' => (int) ($row->count ?? 0)],
                ];
            },

            'fee_collected_month' => function (array $filters) {
                $row = DB::table('fee_payments')->where('is_cancelled', false)
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->selectRaw("COALESCE(SUM(amount), 0) as total, COUNT(*) as count")
                    ->first();
                return [
                    'value'   => (float) ($row->total ?? 0),
                    'details' => ['Payments' => (int) ($row->count ?? 0)],
                ];
            },

            'fee_outstanding' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $totalPaid = DB::table('fee_payments')
                    ->where('is_cancelled', false)
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                    ->sum('amount');
                $totalDue  = DB::table('fee_structures')
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                    ->sum('amount');
                $outstanding = (float) max(0, $totalDue - $totalPaid);
                return [
                    'value'   => $outstanding,
                    'details' => ['Total Due' => '₹' . number_format($totalDue), 'Paid' => '₹' . number_format($totalPaid)],
                ];
            },

            // ── Attendance ───────────────────────────────────────────────
            'attendance_present_today' => function (array $filters) {
                $stats = DB::table('attendance_records')->whereDate('date', today())
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status IN ('present','half_day') THEN 1 END) as present, COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent")
                    ->first();
                $present = (int) ($stats->present ?? 0);
                $total = (int) ($stats->total ?? 0);
                $pct = $total > 0 ? round(($present / $total) * 100, 1) . '%' : 'N/A';
                return [
                    'value'   => $present,
                    'details' => ['Rate' => $pct, 'Absent' => (int) ($stats->absent ?? 0)],
                ];
            },

            'attendance_absent_today' => function (array $filters) {
                $stats = DB::table('attendance_records')->whereDate('date', today())
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent, COUNT(CASE WHEN status IN ('present','half_day') THEN 1 END) as present")
                    ->first();
                $absent = (int) ($stats->absent ?? 0);
                $total = (int) ($stats->total ?? 0);
                return [
                    'value'   => $absent,
                    'details' => ['Present' => (int) ($stats->present ?? 0), 'Total Marked' => $total],
                ];
            },

            'attendance_rate_today' => function (array $filters) {
                $stats = DB::table('attendance_records')
                    ->whereDate('date', today())
                    ->selectRaw("
                        COUNT(*) as total,
                        COUNT(CASE WHEN status IN ('present','half_day') THEN 1 END) as present,
                        COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
                        COUNT(CASE WHEN status = 'late' THEN 1 END) as late
                    ")->first();
                if (!$stats || $stats->total == 0) {
                    return [
                        'value'   => 0.0,
                        'details' => 'Not marked yet',
                    ];
                }
                $rate = round($stats->present / $stats->total * 100, 1);
                return [
                    'value'   => $rate,
                    'details' => ['Present' => (int) $stats->present, 'Absent' => (int) $stats->absent, 'Late' => (int) $stats->late],
                ];
            },

            // ── Finance ──────────────────────────────────────────────────
            'revenue_total' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $sum = (float) DB::table('fee_payments')->where('is_cancelled', false)
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
                $month = (float) DB::table('fee_payments')->where('is_cancelled', false)
                    ->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount');
                return [
                    'value'   => $sum,
                    'details' => ['This Month' => '₹' . number_format($month)],
                ];
            },

            'expenses_total' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $sum = (float) DB::table('expenses')->where('approval_status', 'approved')
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
                $pendingCount = DB::table('expenses')->whereIn('approval_status', ['pending', 'verified'])->count();
                return [
                    'value'   => $sum,
                    'details' => ['Pending Approvals' => $pendingCount],
                ];
            },

            // ── Library ──────────────────────────────────────────────────
            'library_issued' => function (array $filters) {
                $issued = DB::table('book_issues')->whereNull('return_date')->count();
                $overdue = DB::table('book_issues')->whereNull('return_date')->where('due_date', '<', today())->count();
                return [
                    'value'   => $issued,
                    'details' => ['Overdue' => $overdue],
                ];
            },

            'library_overdue' => function (array $filters) {
                $overdue = DB::table('book_issues')->whereNull('return_date')->where('due_date', '<', today())->count();
                $issued = DB::table('book_issues')->whereNull('return_date')->count();
                return [
                    'value'   => $overdue,
                    'details' => ['Total Issued' => $issued],
                ];
            },

            // ── Hostel ───────────────────────────────────────────────────
            'hostel_residents' => function (array $filters) {
                $count = DB::table('hostel_allotments')->where('status', 'active')->count();
                return [
                    'value'   => $count,
                    'details' => ['Active Residents' => $count],
                ];
            },

            // ── Transport ────────────────────────────────────────────────
            'transport_students' => function (array $filters) {
                $students = DB::table('transport_allotments')->where('is_active', true)->count();
                $routes = DB::table('transport_routes')->where('is_active', true)->count();
                return [
                    'value'   => $students,
                    'details' => ['Routes' => $routes],
                ];
            },

            // ── Campus & Communications ──────────────────────────────────
            'events_upcoming' => function (array $filters) {
                $events = DB::table('events')->where('is_published', true)->where('event_date', '>=', today())->orderBy('event_date')->get();
                $count = $events->count();
                $next = $events->first();
                $details = $next ? $next->name . ' (' . \Carbon\Carbon::parse($next->event_date)->format('d M') . ')' : 'No upcoming events';
                return [
                    'value'   => $count,
                    'details' => $details,
                ];
            },

            'notices_active' => function (array $filters) {
                $count = DB::table('notices')->where('is_published', true)->where('notice_type', '!=', 'circular')->count();
                $latest = DB::table('notices')->where('is_published', true)->where('notice_type', '!=', 'circular')->latest('publish_date')->first();
                return [
                    'value'   => $count,
                    'details' => $latest ? \Illuminate\Support\Str::limit($latest->title, 24) : 'No notices',
                ];
            },

            'circulars_active' => function (array $filters) {
                $count = DB::table('notices')->where('is_published', true)->where('notice_type', 'circular')->count();
                $latest = DB::table('notices')->where('is_published', true)->where('notice_type', 'circular')->latest('publish_date')->first();
                return [
                    'value'   => $count,
                    'details' => $latest ? \Illuminate\Support\Str::limit($latest->title, 24) : 'No circulars',
                ];
            },

            // ── Academics & Classes ──────────────────────────────────────
            'classes_total' => function (array $filters) {
                $total = DB::table('classes')->where('is_active', true)->count();
                return [
                    'value'   => $total,
                    'details' => ['Active Classes' => $total],
                ];
            },

            'sections_total' => function (array $filters) {
                $total = DB::table('sections')->count();
                return [
                    'value'   => $total,
                    'details' => ['Total Sections' => $total],
                ];
            },

            // ── Dashboard Modules / Sections ─────────────────────────────
            'revenue_analytics' => function (array $filters) {
                $currentYear = AcademicYear::current();
                $totalIncome = (float) DB::table('fee_payments')->where('is_cancelled', false)
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
                $totalExp = (float) DB::table('expenses')->where('approval_status', 'approved')
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
                $net = $totalIncome - $totalExp;
                return [
                    'value'   => $totalIncome,
                    'details' => ['Expenses' => '₹' . number_format($totalExp), 'Net' => '₹' . number_format($net)],
                ];
            },

            'events_notices' => function (array $filters) {
                $events = DB::table('events')->where('is_published', true)->where('event_date', '>=', today())->count();
                $notices = DB::table('notices')->where('is_published', true)->count();
                return [
                    'value'   => $events + $notices,
                    'details' => ['Upcoming Events' => $events, 'Notices' => $notices],
                ];
            },

            'birthday_wishes' => function (array $filters) {
                $currentMonth = now()->month;
                $currentDay   = now()->day;
                $students = DB::table('students')->where('status', 'active')->whereNotNull('dob')
                    ->whereRaw('EXTRACT(MONTH FROM dob) = ?', [$currentMonth])
                    ->whereRaw('EXTRACT(DAY FROM dob) = ?', [$currentDay])->count();
                $staff = DB::table('employees')->where('is_active', true)->whereNull('deleted_at')->whereNotNull('dob')
                    ->whereRaw('EXTRACT(MONTH FROM dob) = ?', [$currentMonth])
                    ->whereRaw('EXTRACT(DAY FROM dob) = ?', [$currentDay])->count();
                $total = $students + $staff;
                return [
                    'value'   => $total,
                    'details' => ['Students' => $students, 'Staff' => $staff],
                ];
            },
        ];
    }

    /**
     * Format a raw value based on value_format.
     */
    public static function format($value, string $format): string
    {
        return match ($format) {
            'currency' => '₹' . number_format((float) $value),
            'percent'  => $value . '%',
            default    => number_format((int) $value),
        };
    }

    /**
     * Build the "View all →" URL for a card.
     */
    private static function buildLinkUrl(string $source, array $filters, array $config): string
    {
        if ($source === 'student_count') {
            return StudentFilterQuery::listingUrl($filters);
        }
        if ($source === 'circulars_active') {
            try {
                return route('academics.notices', ['type' => 'circular']);
            } catch (\Exception $e) {
                return '#';
            }
        }
        try {
            return route($config['link_route']);
        } catch (\Exception $e) {
            return '#';
        }
    }

    /**
     * Small badge text shown on the card corner (e.g. "Active", "Today", "Due").
     */
    private static function badgeText(string $source, string $format): string
    {
        return match (true) {
            str_contains($source, 'today') => 'Today',
            str_contains($source, 'month') => 'Month',
            $source === 'fee_outstanding'   => 'Due',
            $source === 'leave_pending'     => 'Pending',
            $source === 'pending_approvals' => 'Action',
            $source === 'library_overdue'   => 'Overdue',
            $source === 'admissions_pending'=> 'Pending',
            $source === 'revenue_total'     => 'Year',
            $source === 'expenses_total'    => 'Year',
            $source === 'notices_active'    => 'Notices',
            $source === 'circulars_active'  => 'Circulars',
            default                         => 'Active',
        };
    }
}

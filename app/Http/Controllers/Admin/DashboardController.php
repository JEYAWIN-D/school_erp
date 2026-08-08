<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = auth()->user();
        $userRoles   = $user->getRoleNames()->toArray();
        $currentYear = AcademicYear::current();

        $managementRoles  = ['owner', 'admin', 'principal', 'vice_principal', 'it_admin', 'super_admin'];
        $teachingRoles    = ['hod', 'class_teacher', 'teacher', 'subject_teacher'];
        $financeRoles     = ['accountant'];
        $hrRoles          = ['hr_manager'];
        $libraryRoles     = ['librarian'];
        $transportRoles   = ['transport_manager'];
        $hostelRoles      = ['hostel_warden', 'warden'];
        $admissionRoles   = ['admission_counsellor', 'receptionist'];
        $operationsRoles  = ['inventory_manager', 'event_coordinator', 'alumni_coordinator'];
        $portalRoles      = ['student', 'parent'];

        if (array_intersect($userRoles, $portalRoles)) {
            return redirect()->route('portal.dashboard');
        }

        if (array_intersect($userRoles, $teachingRoles)) {
            return $this->teacherDashboard($user, $currentYear);
        }
        if (array_intersect($userRoles, $financeRoles)) {
            return $this->financeDashboard($currentYear);
        }
        if (array_intersect($userRoles, $hrRoles)) {
            return $this->hrDashboard($currentYear);
        }
        if (array_intersect($userRoles, $libraryRoles)) {
            return $this->libraryDashboard();
        }
        if (array_intersect($userRoles, $transportRoles)) {
            return $this->transportDashboard();
        }
        if (array_intersect($userRoles, $hostelRoles)) {
            return $this->hostelDashboard();
        }
        if (array_intersect($userRoles, $admissionRoles)) {
            return $this->admissionDashboard($currentYear);
        }
        if (array_intersect($userRoles, $operationsRoles)) {
            return $this->operationsDashboard($user, $userRoles);
        }

        // Default: management / admin dashboard
        return $this->managementDashboard($currentYear);
    }

    private function managementDashboard($currentYear)
    {
        $cacheKey = 'mgmt_dashboard_v1_' . ($currentYear?->id ?? 0);

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () use ($currentYear) {
            $stats = [
                'total_students'       => DB::table('students')->where('status', 'active')->count(),
                'total_staff'          => DB::table('employees')->where('is_active', true)->count(),
                'total_users'          => User::where('is_active', true)->count(),
                'academic_year'        => $currentYear?->name ?? '—',
                'new_admissions_month' => DB::table('students')
                    ->where('status', 'active')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];

            $todayAttendance = null;
            if ($currentYear) {
                $attStats = DB::table('attendance_records')
                    ->whereDate('date', today())
                    ->selectRaw("COUNT(*) as total, COUNT(CASE WHEN status IN ('present', 'half_day') THEN 1 END) as present")
                    ->first();
                $todayAttendance = ($attStats && $attStats->total > 0) ? round($attStats->present / $attStats->total * 100, 1) : null;
            }

            $todayCollection = DB::table('fee_payments')->where('is_cancelled', false)
                ->whereDate('payment_date', today())->sum('amount');

            $totalPaid = DB::table('fee_payments')->where('is_cancelled', false)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
            $totalDue  = DB::table('fee_structures')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
            $outstanding = max(0, $totalDue - $totalPaid);

            $classStrength = DB::table('student_enrollments as se')
                ->join('classes as c', 'c.id', '=', 'se.class_id')
                ->where('se.status', 'active')
                ->when($currentYear, fn($q) => $q->where('se.academic_year_id', $currentYear->id))
                ->select('c.name as class_name', DB::raw('COUNT(*) as student_count'))
                ->groupBy('c.id', 'c.name')->orderBy('c.name')->get();

            $staffPresent = 0;
            try {
                $staffPresent = DB::table('staff_attendance')->whereDate('date', today())
                    ->where('status', 'present')->count();
            } catch (\Exception $e) {}

            $collectionTrend = DB::table('fee_payments')
                ->where('is_cancelled', false)
                ->whereRaw("payment_date >= CURRENT_DATE - INTERVAL '5 months'")
                ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month, SUM(amount) as total")
                ->groupByRaw("TO_CHAR(payment_date, 'YYYY-MM')")
                ->orderBy('month')
                ->get();

            $recentNotices = DB::table('notices')->where('is_published', true)
                ->latest('publish_date')->limit(5)->get(['id', 'title', 'notice_type', 'publish_date']);

            $upcomingEvents = DB::table('events')->where('is_published', true)
                ->where('event_date', '>=', today())->orderBy('event_date')->limit(5)
                ->get(['id', 'name', 'event_type', 'event_date'])
                ->map(fn($e) => tap($e, fn($e) => $e->event_date = \Carbon\Carbon::parse($e->event_date)));

            $todayBirthdays = DB::table('students')
                ->where('status', 'active')
                ->whereRaw('EXTRACT(MONTH FROM dob) = ?', [now()->month])
                ->whereRaw('EXTRACT(DAY FROM dob) = ?', [now()->day])
                ->select('id', 'first_name', 'last_name', 'dob')
                ->limit(10)
                ->get();

            return compact(
                'stats', 'todayAttendance', 'todayCollection', 'outstanding',
                'classStrength', 'staffPresent', 'collectionTrend',
                'recentNotices', 'upcomingEvents', 'todayBirthdays'
            );
        });

        return view('dashboard.index', $data + ['dashboardType' => 'management']);
    }

    private function teacherDashboard($user, $currentYear)
    {
        $employee = null;
        $myClasses  = collect();
        $myScheduleToday = collect();

        try {
            $employee = DB::table('employees')->where('user_id', $user->id)->first();
        } catch (\Exception $e) {}

        if ($employee) {
            try {
                $myClasses = DB::table('timetables as t')
                    ->join('classes as c', 'c.id', '=', 't.class_id')
                    ->leftJoin('subjects as s', 's.id', '=', 't.subject_id')
                    ->where('t.teacher_id', $employee->id)
                    ->when($currentYear, fn($q) => $q->where('t.academic_year_id', $currentYear->id))
                    ->select('c.name as class_name', 's.name as subject_name', 't.day_of_week', 't.start_time', 't.end_time')
                    ->orderBy('c.name')->orderBy('t.start_time')
                    ->get();

                $todayDay = now()->format('l');
                $myScheduleToday = $myClasses->filter(fn($r) => strtolower($r->day_of_week) === strtolower($todayDay));
            } catch (\Exception $e) {}
        }

        $myAttendancePending = 0;
        try {
            $classIds = $myClasses->pluck('class_id')->unique();
            if ($classIds->isNotEmpty()) {
                $total = DB::table('student_enrollments')
                    ->whereIn('class_id', $classIds)->where('status', 'active')
                    ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                    ->count();
                $marked = DB::table('attendance_records')
                    ->whereDate('date', today())->whereIn('class_id', $classIds)->count();
                $myAttendancePending = max(0, $total - $marked);
            }
        } catch (\Exception $e) {}

        $pendingHomework = 0;
        try {
            $pendingHomework = DB::table('homeworks')
                ->where('teacher_id', $employee?->id ?? 0)
                ->where('due_date', '>=', today())->count();
        } catch (\Exception $e) {}

        $upcomingExams = collect();
        try {
            $upcomingExams = DB::table('exams as e')
                ->join('classes as c', 'c.id', '=', 'e.class_id')
                ->where('e.exam_date', '>=', today())
                ->orderBy('e.exam_date')->limit(5)
                ->get(['e.id', 'e.title', 'e.exam_date', 'c.name as class_name'])
                ->map(fn($r) => tap($r, fn($r) => $r->exam_date = \Carbon\Carbon::parse($r->exam_date)));
        } catch (\Exception $e) {}

        $recentNotices = collect();
        try {
            $recentNotices = DB::table('notices')->where('is_published', true)
                ->latest('publish_date')->limit(4)->get(['id', 'title', 'publish_date']);
        } catch (\Exception $e) {}

        return view('dashboard.index', compact(
            'employee', 'myClasses', 'myScheduleToday', 'myAttendancePending',
            'pendingHomework', 'upcomingExams', 'recentNotices'
        ) + ['dashboardType' => 'teaching', 'currentYear' => $currentYear]);
    }

    private function financeDashboard($currentYear)
    {
        $todayCollection = DB::table('fee_payments')->where('is_cancelled', false)
            ->whereDate('payment_date', today())->sum('amount');

        $monthCollection = DB::table('fee_payments')->where('is_cancelled', false)
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)->sum('amount');

        $totalPaid = DB::table('fee_payments')->where('is_cancelled', false)
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
        $totalDue  = DB::table('fee_structures')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))->sum('amount');
        $outstanding = max(0, $totalDue - $totalPaid);

        $paymentModes = DB::table('fee_payments')->where('is_cancelled', false)
            ->whereMonth('payment_date', now()->month)
            ->selectRaw('payment_mode, SUM(amount) as total')
            ->groupBy('payment_mode')->get();

        $recentPayments = DB::table('fee_payments as fp')
            ->join('students as s', 's.id', '=', 'fp.student_id')
            ->where('fp.is_cancelled', false)
            ->orderByDesc('fp.payment_date')->limit(10)
            ->select('fp.receipt_number', 'fp.amount', 'fp.payment_date', 'fp.payment_mode',
                     's.first_name', 's.last_name', 's.admission_no')->get();

        $collectionTrend = DB::table('fee_payments')->where('is_cancelled', false)
            ->whereRaw("payment_date >= CURRENT_DATE - INTERVAL '5 months'")
            ->selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month, SUM(amount) as total")
            ->groupByRaw("TO_CHAR(payment_date, 'YYYY-MM')")->orderBy('month')->get();

        return view('dashboard.index', compact(
            'todayCollection', 'monthCollection', 'outstanding',
            'paymentModes', 'recentPayments', 'collectionTrend', 'currentYear'
        ) + ['dashboardType' => 'finance', 'stats' => ['academic_year' => $currentYear?->name ?? '—']]);
    }

    private function hrDashboard($currentYear)
    {
        $totalStaff  = DB::table('employees')->where('is_active', true)->count();
        $staffPresent = 0;
        try {
            $staffPresent = DB::table('staff_attendance')->whereDate('date', today())
                ->where('status', 'present')->count();
        } catch (\Exception $e) {}

        $pendingLeaves = 0;
        try {
            $pendingLeaves = DB::table('leave_applications')
                ->where('status', 'pending')->count();
        } catch (\Exception $e) {}

        $departmentStrength = collect();
        try {
            $departmentStrength = DB::table('employees as e')
                ->leftJoin('departments as d', 'd.id', '=', 'e.department_id')
                ->where('e.is_active', true)
                ->select('d.name as department', DB::raw('COUNT(*) as count'))
                ->groupBy('d.id', 'd.name')->orderBy('d.name')->get();
        } catch (\Exception $e) {}

        $recentJoinings = DB::table('employees')
            ->where('is_active', true)->orderByDesc('joining_date')
            ->limit(5)->get(['id', 'name', 'designation', 'joining_date']);

        return view('dashboard.index', compact(
            'totalStaff', 'staffPresent', 'pendingLeaves',
            'departmentStrength', 'recentJoinings'
        ) + ['dashboardType' => 'hr', 'stats' => ['academic_year' => $currentYear?->name ?? '—']]);
    }

    private function libraryDashboard()
    {
        $totalBooks   = 0;
        $issuedBooks  = 0;
        $overdueBooks = 0;
        $recentIssues = collect();

        try {
            $totalBooks  = DB::table('books')->count();
            $issuedBooks = DB::table('book_issues')->whereNull('return_date')->count();
            $overdueBooks = DB::table('book_issues')
                ->whereNull('return_date')
                ->where('due_date', '<', today())->count();
            $recentIssues = DB::table('book_issues as bi')
                ->join('books as b', 'b.id', '=', 'bi.book_id')
                ->leftJoin('students as s', 's.id', '=', 'bi.student_id')
                ->orderByDesc('bi.issue_date')->limit(10)
                ->select('b.title', 's.first_name', 's.last_name', 'bi.issue_date', 'bi.due_date', 'bi.return_date')
                ->get();
        } catch (\Exception $e) {}

        return view('dashboard.index', compact(
            'totalBooks', 'issuedBooks', 'overdueBooks', 'recentIssues'
        ) + ['dashboardType' => 'library', 'stats' => ['academic_year' => '—']]);
    }

    private function transportDashboard()
    {
        $totalVehicles  = 0;
        $activeRoutes   = 0;
        $studentsOnBus  = 0;
        $recentFuelLogs = collect();

        try {
            $totalVehicles  = DB::table('vehicles')->where('status', 'active')->count();
            $activeRoutes   = DB::table('transport_routes')->where('is_active', true)->count();
            $studentsOnBus  = DB::table('student_transports')->where('status', 'active')->count();
            $recentFuelLogs = DB::table('vehicle_fuel_logs as f')
                ->join('vehicles as v', 'v.id', '=', 'f.vehicle_id')
                ->orderByDesc('f.date')->limit(8)
                ->select('v.registration_number', 'f.date', 'f.litres', 'f.amount', 'f.odometer')
                ->get();
        } catch (\Exception $e) {}

        return view('dashboard.index', compact(
            'totalVehicles', 'activeRoutes', 'studentsOnBus', 'recentFuelLogs'
        ) + ['dashboardType' => 'transport', 'stats' => ['academic_year' => '—']]);
    }

    private function hostelDashboard()
    {
        $totalRooms     = 0;
        $occupiedRooms  = 0;
        $totalResidents = 0;
        $pendingOutpass = 0;

        try {
            $totalRooms     = DB::table('hostel_rooms')->count();
            $occupiedRooms  = DB::table('hostel_rooms')->where('status', 'occupied')->count();
            $totalResidents = DB::table('hostel_allotments')->where('status', 'active')->count();
            $pendingOutpass = DB::table('outpass_requests')->where('status', 'pending')->count();
        } catch (\Exception $e) {}

        $recentOutpass = collect();
        try {
            $recentOutpass = DB::table('outpass_requests as o')
                ->join('students as s', 's.id', '=', 'o.student_id')
                ->orderByDesc('o.created_at')->limit(8)
                ->select('s.first_name', 's.last_name', 'o.reason', 'o.from_date', 'o.to_date', 'o.status')
                ->get();
        } catch (\Exception $e) {}

        return view('dashboard.index', compact(
            'totalRooms', 'occupiedRooms', 'totalResidents', 'pendingOutpass', 'recentOutpass'
        ) + ['dashboardType' => 'hostel', 'stats' => ['academic_year' => '—']]);
    }

    private function admissionDashboard($currentYear)
    {
        $totalEnquiries   = 0;
        $pendingFollowups = 0;
        $convertedMonth   = 0;
        $recentEnquiries  = collect();

        try {
            $totalEnquiries   = DB::table('admissions')->count();
            $pendingFollowups = DB::table('admissions')->where('status', 'pending')->count();
            $convertedMonth   = DB::table('admissions')->where('status', 'converted')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count();
            $recentEnquiries  = DB::table('admissions')
                ->orderByDesc('created_at')->limit(10)
                ->get(['id', 'student_name', 'class_applied', 'phone', 'status', 'created_at']);
        } catch (\Exception $e) {}

        $newAdmissionsMonth = DB::table('students')->where('status', 'active')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        return view('dashboard.index', compact(
            'totalEnquiries', 'pendingFollowups', 'convertedMonth',
            'recentEnquiries', 'newAdmissionsMonth'
        ) + ['dashboardType' => 'admissions', 'stats' => ['academic_year' => $currentYear?->name ?? '—']]);
    }

    private function operationsDashboard($user, $userRoles)
    {
        $subType = 'general';
        if (in_array('inventory_manager', $userRoles))   $subType = 'inventory';
        elseif (in_array('event_coordinator', $userRoles)) $subType = 'events';
        elseif (in_array('alumni_coordinator', $userRoles)) $subType = 'alumni';

        $data = ['dashboardType' => 'operations', 'subType' => $subType,
                 'stats' => ['academic_year' => '—']];

        if ($subType === 'inventory') {
            try {
                $data['totalItems']     = DB::table('inventory_items')->count();
                $data['lowStockItems']  = DB::table('inventory_items')->whereRaw('quantity <= minimum_stock')->count();
                $data['recentMovements'] = DB::table('inventory_movements as m')
                    ->join('inventory_items as i', 'i.id', '=', 'm.item_id')
                    ->orderByDesc('m.created_at')->limit(8)
                    ->select('i.name', 'm.type', 'm.quantity', 'm.created_at')->get();
            } catch (\Exception $e) {
                $data += ['totalItems' => 0, 'lowStockItems' => 0, 'recentMovements' => collect()];
            }
        } elseif ($subType === 'events') {
            try {
                $data['upcomingEvents'] = DB::table('events')->where('is_published', true)
                    ->where('event_date', '>=', today())->orderBy('event_date')->limit(10)
                    ->get(['id', 'name', 'event_type', 'event_date'])
                    ->map(fn($e) => tap($e, fn($e) => $e->event_date = \Carbon\Carbon::parse($e->event_date)));
                $data['totalEvents']    = DB::table('events')->count();
            } catch (\Exception $e) {
                $data += ['upcomingEvents' => collect(), 'totalEvents' => 0];
            }
        } elseif ($subType === 'alumni') {
            try {
                $data['totalAlumni']   = DB::table('alumni')->count();
                $data['recentAlumni']  = DB::table('alumni')->orderByDesc('created_at')->limit(8)
                    ->get(['id', 'name', 'graduation_year', 'current_occupation']);
            } catch (\Exception $e) {
                $data += ['totalAlumni' => 0, 'recentAlumni' => collect()];
            }
        }

        return view('dashboard.index', $data);
    }

    public function executiveDashboard()
    {
        return $this->managementDashboard(AcademicYear::current());
    }

    // ── Standard Registers ────────────────────────────────

    public function generalRegister(Request $request)
    {
        $currentYear = AcademicYear::current();
        $classes     = Classes::active()->orderBy('name')->get();

        $query = DB::table('students as s')
            ->leftJoin('student_enrollments as se', function($j) use ($currentYear) {
                $j->on('se.student_id', '=', 's.id')
                  ->where('se.status', 'active');
                if ($currentYear) $j->where('se.academic_year_id', $currentYear->id);
            })
            ->leftJoin('classes as c', 'c.id', '=', 'se.class_id')
            ->where('s.status', 'active')
            ->select('s.id', 's.first_name', 's.last_name', 's.admission_no',
                     's.admission_date', 's.dob', 's.gender', 's.religion', 's.caste',
                     's.blood_group', 's.father_name', 's.mother_name', 's.mobile',
                     's.address', 'c.name as class_name')
            ->when($request->class_id, fn($q, $v) => $q->where('se.class_id', $v))
            ->when($request->gender, fn($q, $v) => $q->where('s.gender', $v))
            ->orderBy('s.admission_no');

        $students = $query->paginate(50)->withQueryString();

        return view('reports.general-register', compact('students', 'classes', 'currentYear'));
    }

    public function tcRegister(Request $request)
    {
        $students = DB::table('students as s')
            ->join('student_tcs as tc', 'tc.student_id', '=', 's.id')
            ->select('s.first_name', 's.last_name', 's.admission_no', 's.dob',
                     's.gender', 's.father_name', 'tc.tc_number', 'tc.issue_date',
                     'tc.leaving_date', 'tc.reason', 'tc.last_class')
            ->when($request->from, fn($q, $v) => $q->whereDate('tc.issue_date', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('tc.issue_date', '<=', $v))
            ->orderBy('tc.tc_number')
            ->paginate(50)->withQueryString();

        return view('reports.tc-register', compact('students'));
    }

    public function feeCollectionRegister(Request $request)
    {
        $currentYear = AcademicYear::current();
        $from = $request->from ?? today()->startOfMonth()->toDateString();
        $to   = $request->to   ?? today()->toDateString();

        $payments = DB::table('fee_payments as fp')
            ->join('students as s', 's.id', '=', 'fp.student_id')
            ->leftJoin('student_enrollments as se', function($j) use ($currentYear) {
                $j->on('se.student_id', '=', 'fp.student_id')
                  ->where('se.status', 'active');
                if ($currentYear) $j->where('se.academic_year_id', $currentYear->id);
            })
            ->leftJoin('classes as c', 'c.id', '=', 'se.class_id')
            ->leftJoin('fee_heads as fh', 'fh.id', '=', 'fp.fee_head_id')
            ->where('fp.is_cancelled', false)
            ->whereBetween('fp.payment_date', [$from, $to])
            ->select('fp.receipt_number', 'fp.payment_date', 'fp.amount', 'fp.payment_mode',
                     's.first_name', 's.last_name', 's.admission_no',
                     'c.name as class_name', 'fh.name as fee_head')
            ->orderBy('fp.payment_date')->orderBy('fp.receipt_number')
            ->paginate(50)->withQueryString();

        $totalAmount = DB::table('fee_payments')
            ->where('is_cancelled', false)
            ->whereBetween('payment_date', [$from, $to])->sum('amount');

        return view('reports.fee-collection-register', compact('payments', 'from', 'to', 'totalAmount'));
    }

    public function attendanceRegister(Request $request)
    {
        $classes     = Classes::active()->orderBy('name')->get();
        $currentYear = AcademicYear::current();

        $month   = $request->month ?? now()->month;
        $year    = $request->year  ?? now()->year;
        $classId = $request->class_id;

        $students = collect();
        $days     = [];
        $records  = collect();

        if ($classId) {
            $start = \Carbon\Carbon::createFromDate($year, $month, 1);
            $end   = $start->copy()->endOfMonth();
            $days  = range(1, $end->day);

            $students = DB::table('student_enrollments as se')
                ->join('students as s', 's.id', '=', 'se.student_id')
                ->where('se.class_id', $classId)
                ->where('se.status', 'active')
                ->when($currentYear, fn($q) => $q->where('se.academic_year_id', $currentYear->id))
                ->orderBy('s.first_name')
                ->select('s.id', 's.first_name', 's.last_name', 's.admission_no')
                ->get();

            $records = DB::table('attendance_records')
                ->whereIn('student_id', $students->pluck('id'))
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->get()
                ->groupBy(fn($r) => $r->student_id . '_' . \Carbon\Carbon::parse($r->date)->day);
        }

        return view('reports.attendance-register', compact(
            'classes', 'students', 'days', 'records', 'month', 'year', 'classId'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceCondonation;
use App\Models\AttendanceRecord;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Section;
use App\Models\StaffAttendance;
use App\Models\StudentEnrollment;
use App\Models\StudentLeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::current();
        $classes     = Classes::active()->get();

        $present = AttendanceRecord::whereDate('date', today())->whereIn('status', ['present', 'late', 'half_day'])->count();
        $absent  = AttendanceRecord::whereDate('date', today())->where('status', 'absent')->count();
        $late    = AttendanceRecord::whereDate('date', today())->where('status', 'late')->count();
        $total   = $present + $absent;
        $pct     = $total > 0 ? round($present / $total * 100, 1) : 0;

        $todayStats = compact('present', 'absent', 'late', 'total', 'pct');

        // Which classes have NOT marked attendance today?
        $markedClassIds = AttendanceRecord::whereDate('date', today())
            ->distinct()->pluck('class_id');
        $unmarkedClasses = $classes->whereNotIn('id', $markedClassIds)->values();

        // Pending leave requests
        $pendingLeaves = DB::table('student_leave_requests')
            ->where('status', 'pending')->count();

        return view('attendance.index', compact(
            'classes', 'currentYear', 'todayStats',
            'unmarkedClasses', 'pendingLeaves'
        ));
    }

    public function mark(Request $request)
    {
        $classes  = Classes::active()->get();
        $sections = collect();
        $students = collect();
        $existing = collect();

        $classId   = $request->class_id;
        $sectionId = $request->section_id;
        $date      = $request->date ?? today()->toDateString();

        if ($classId) {
            $currentYear = AcademicYear::current();

            // Load sections for current year; fall back to any year if none found
            $sections = Section::where('class_id', $classId)
                ->where('is_active', true)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->get();
            if ($sections->isEmpty()) {
                $sections = Section::where('class_id', $classId)->where('is_active', true)->get();
            }

            $query = StudentEnrollment::with('student')
                ->where('class_id', $classId)
                ->where('status', 'active');
            if ($sectionId) $query->where('section_id', $sectionId);
            if ($currentYear) $query->where('academic_year_id', $currentYear->id);

            $students = $query->get()->pluck('student')->filter();

            // Fallback: no students in current year — load without year filter
            if ($students->isEmpty()) {
                $students = StudentEnrollment::with('student')
                    ->where('class_id', $classId)
                    ->where('status', 'active')
                    ->when($sectionId, fn($q) => $q->where('section_id', $sectionId))
                    ->get()->pluck('student')->filter();
            }

            if ($students->count()) {
                $existing = AttendanceRecord::whereDate('date', $date)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->keyBy('student_id');

                // Pre-populate "on_leave" for students with approved leave on this date
                $approvedLeaves = StudentLeaveRequest::where('status', 'approved')
                    ->where('from_date', '<=', $date)
                    ->where('to_date', '>=', $date)
                    ->whereIn('student_id', $students->pluck('id'))
                    ->pluck('student_id');
                foreach ($approvedLeaves as $sid) {
                    if (!isset($existing[$sid])) {
                        $existing[$sid] = (object)['status' => 'leave', 'remark' => 'Approved Leave'];
                    }
                }
            }
        }

        return view('attendance.mark', compact('classes', 'sections', 'students', 'existing', 'date', 'classId', 'sectionId'));
    }

    public function saveAttendance(Request $request)
    {
        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'date'      => 'required|date|before_or_equal:today',
            'attendance' => 'required|array',
        ]);

        // Cutoff check: if today's attendance and past cutoff time, require override
        $cutoffTime = \App\Models\SchoolSetting::get('attendance_cutoff_time', '12:00');
        $isToday    = $request->date === today()->toDateString();
        if ($isToday && $cutoffTime && now()->format('H:i') > $cutoffTime) {
            if (!$request->boolean('override_cutoff')) {
                return back()->with('error', "Attendance cutoff time ({$cutoffTime}) has passed. Check 'Override Cutoff' to save.")
                    ->withInput();
            }
        }

        $currentYear = AcademicYear::current();
        $date        = $request->date;

        $lateTime       = \App\Models\SchoolSetting::get('late_arrival_time', '09:30');
        $isOverride     = $request->boolean('override_cutoff');

        DB::transaction(function () use ($request, $currentYear, $date, $lateTime, $isOverride) {
            foreach ($request->attendance as $studentId => $status) {
                $arrivalTime = $request->arrival_time[$studentId] ?? null;
                $isLate      = $arrivalTime && $arrivalTime > $lateTime;
                AttendanceRecord::updateOrCreate(
                    ['student_id' => $studentId, 'date' => $date],
                    [
                        'class_id'           => $request->class_id,
                        'section_id'         => $request->section_id ?? null,
                        'academic_year_id'   => $currentYear?->id,
                        'status'             => $status,
                        'remark'             => $request->remarks[$studentId] ?? null,
                        'arrival_time'       => $arrivalTime,
                        'departure_time'     => $request->departure_time[$studentId] ?? null,
                        'is_late'            => $isLate,
                        'cutoff_override'    => $isOverride,
                        'cutoff_override_by' => $isOverride ? Auth::id() : null,
                        'marked_by'          => Auth::id(),
                    ]
                );
            }
        });

        return redirect()->route('attendance.mark', [
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'date' => $date,
        ])->with('success', 'Attendance saved for ' . count($request->attendance) . ' students.');
    }

    public function report(Request $request)
    {
        $classes = Classes::active()->get();
        $data    = collect();

        if ($request->class_id && $request->month) {
            [$year, $month] = explode('-', $request->month);
            $data = AttendanceRecord::with('student')
                ->where('class_id', $request->class_id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->groupBy('student_id');
        }

        return view('attendance.report', compact('classes', 'data'));
    }

    public function shortage(Request $request)
    {
        $classes  = Classes::active()->get();
        $students = collect();

        if ($request->class_id) {
            $currentYear = AcademicYear::current();
            $totalDays   = AttendanceRecord::where('class_id', $request->class_id)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->select('date')->distinct()->count();

            $students = AttendanceRecord::with('student')
                ->where('class_id', $request->class_id)
                ->where('status', 'present')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->select('student_id', DB::raw('COUNT(*) as present_days'))
                ->groupBy('student_id')
                ->get()
                ->filter(fn($r) => $totalDays > 0 && ($r->present_days / $totalDays * 100) < 75);
        }

        return view('attendance.shortage', compact('classes', 'students', 'totalDays'));
    }

    public function shortageLetter(int $id)
    {
        $currentYear = AcademicYear::current();
        $enrollment  = StudentEnrollment::with(['student', 'class'])->findOrFail($id);
        $totalDays   = AttendanceRecord::where('class_id', $enrollment->class_id)
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->select('date')->distinct()->count();
        $present = AttendanceRecord::where('student_id', $enrollment->student_id)
            ->where('status', 'present')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->count();
        $stats = ['total_days' => $totalDays, 'present' => $present, 'absent' => $totalDays - $present, 'percentage' => $totalDays > 0 ? round($present / $totalDays * 100, 1) : 0];
        $school = (object)['name' => config('school.name', 'School Name'), 'address' => config('school.address', '')];
        $pdf = Pdf::loadView('pdf.shortage-letter', compact('school', 'enrollment', 'stats', 'currentYear'));
        return $pdf->download('shortage-letter-' . $enrollment->student?->admission_number . '.pdf');
    }

    public function periodWise(Request $request)
    {
        $classes  = Classes::active()->get();
        $sections = collect();
        $students = collect();
        if ($request->class_id) {
            $sections = Section::where('class_id', $request->class_id)->get();
            $currentYear = AcademicYear::current();
            $query = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($request->section_id) $query->where('section_id', $request->section_id);
            if ($currentYear) $query->where('academic_year_id', $currentYear->id);
            $students = $query->get();
        }
        return view('attendance.period-wise', compact('classes', 'sections', 'students'));
    }

    public function savePeriodAttendance(Request $request)
    {
        $request->validate(['class_id' => 'required', 'date' => 'required|date', 'periods' => 'required|array']);
        $currentYear = AcademicYear::current();
        DB::transaction(function () use ($request, $currentYear) {
            foreach ($request->periods as $studentId => $periods) {
                $presentCount = collect($periods)->filter(fn($s) => in_array($s, ['present', 'late']))->count();
                $totalPeriods = count($periods);
                AttendanceRecord::updateOrCreate(
                    ['student_id' => $studentId, 'date' => $request->date],
                    ['class_id' => $request->class_id, 'section_id' => $request->section_id ?? null,
                     'academic_year_id' => $currentYear?->id,
                     'status' => $presentCount >= $totalPeriods / 2 ? 'present' : 'absent',
                     'period_data' => json_encode($periods),
                     'marked_by' => Auth::id()]
                );
            }
        });
        return back()->with('success', 'Period-wise attendance saved.');
    }

    public function studentLeave(Request $request)
    {
        $classes = Classes::active()->get();
        $leaves  = StudentLeaveRequest::with(['student.currentEnrollment.class'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->class_id, fn($q, $v) => $q->whereHas('student.enrollments', fn($q) => $q->where('class_id', $v)->where('status', 'active')))
            ->latest()->paginate(20)->withQueryString();
        return view('attendance.student-leave', compact('leaves', 'classes'));
    }

    public function approveLeave(int $id)
    {
        StudentLeaveRequest::findOrFail($id)->update(['status' => 'approved', 'approved_by' => Auth::id()]);
        return back()->with('success', 'Leave approved.');
    }

    public function rejectLeave(int $id)
    {
        StudentLeaveRequest::findOrFail($id)->update(['status' => 'rejected', 'approved_by' => Auth::id()]);
        return back()->with('success', 'Leave rejected.');
    }

    public function staffDashboard()
    {
        $today = today()->toDateString();

        $totalStaff = Employee::where('is_active', true)->count();

        $attendances = StaffAttendance::where('date', $today)->get();
        $stats = [
            'present'  => $attendances->where('status', 'present')->count(),
            'absent'   => $attendances->where('status', 'absent')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
            'half_day' => $attendances->where('status', 'half_day')->count(),
            'not_marked' => $totalStaff - $attendances->count(),
            'total'    => $totalStaff,
        ];

        $departmentStats = Department::where('is_active', true)
            ->withCount(['employees as total_count' => fn($q) => $q->where('is_active', true)])
            ->get()
            ->map(function ($dept) use ($today) {
                $deptAttendances = StaffAttendance::where('date', $today)
                    ->whereHas('employee', fn($q) => $q->where('department_id', $dept->id))
                    ->get();
                $dept->present_count = $deptAttendances->where('status', 'present')->count();
                $dept->absent_count  = $deptAttendances->where('status', 'absent')->count();
                return $dept;
            });

        $absentToday = Employee::with('department')
            ->where('is_active', true)
            ->whereHas('staffAttendances', fn($q) => $q->where('date', $today)->where('status', 'absent'))
            ->get();

        $notMarked = Employee::with('department')
            ->where('is_active', true)
            ->whereDoesntHave('staffAttendances', fn($q) => $q->where('date', $today))
            ->get();

        return view('attendance.staff-dashboard', compact('stats', 'departmentStats', 'absentToday', 'notMarked', 'today'));
    }

    public function staffAttendance(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $employees   = collect();
        $attendances = [];
        if ($request->filled('action') && $request->department_id) {
            $employees   = Employee::where('is_active', true)->where('department_id', $request->department_id)->orderBy('first_name')->get();
            $attendances = StaffAttendance::where('date', $request->date ?? today())
                ->whereIn('employee_id', $employees->pluck('id'))
                ->get()->keyBy('employee_id');
        }
        return view('attendance.staff-attendance', compact('departments', 'employees', 'attendances'));
    }

    public function saveStaffAttendance(Request $request)
    {
        $request->validate(['date' => 'required|date', 'attendance' => 'required|array']);
        $lateThreshold = \App\Models\SchoolSetting::get('staff_late_time', '09:30');

        DB::transaction(function () use ($request, $lateThreshold) {
            foreach ($request->attendance as $empId => $data) {
                $checkIn    = $data['in_time'] ?? $data['check_in'] ?? null;
                $isLate     = false;
                $lateMinutes = 0;

                if ($checkIn && $lateThreshold) {
                    $inTime = \Carbon\Carbon::createFromTimeString($checkIn);
                    $thresh = \Carbon\Carbon::createFromTimeString($lateThreshold);
                    if ($inTime->gt($thresh)) {
                        $isLate      = true;
                        $lateMinutes = (int) $inTime->diffInMinutes($thresh);
                    }
                }

                StaffAttendance::updateOrCreate(
                    ['employee_id' => $empId, 'date' => $request->date],
                    [
                        'status'       => $data['status'] ?? 'present',
                        'check_in'     => $checkIn,
                        'check_out'    => $data['out_time'] ?? $data['check_out'] ?? null,
                        'remarks'      => $data['remarks'] ?? null,
                        'is_late'      => $isLate,
                        'late_minutes' => $lateMinutes,
                    ]
                );
            }
        });
        return back()->with('success', 'Staff attendance saved.');
    }

    public function staffRegister(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $employees   = collect();
        $attendances = [];
        if ($request->filled('action') && $request->department_id) {
            $employees   = Employee::where('is_active', true)->where('department_id', $request->department_id)->orderBy('first_name')->get();
            $attendances = StaffAttendance::where('date', $request->date ?? today())
                ->whereIn('employee_id', $employees->pluck('id'))
                ->get()->keyBy('employee_id');
        }
        return view('attendance.staff-attendance', compact('departments', 'employees', 'attendances'));
    }

    public function register(Request $request)
    {
        $classes  = Classes::active()->get();
        $sections = collect();
        $students = collect();
        $dates    = collect();
        $attendanceMap = [];
        if ($request->class_id && $request->month) {
            $sections = Section::where('class_id', $request->class_id)->get();
            [$yr, $mo] = explode('-', $request->month);
            $period = CarbonPeriod::create("$yr-$mo-01", "last day of $yr-$mo");
            $dates  = collect($period)->map(fn($d) => $d);
            $currentYear = AcademicYear::current();
            $query = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($request->section_id) $query->where('section_id', $request->section_id);
            if ($currentYear) $query->where('academic_year_id', $currentYear->id);
            $students = $query->get();
            $records = AttendanceRecord::where('class_id', $request->class_id)
                ->whereYear('date', $yr)->whereMonth('date', $mo)
                ->whereIn('student_id', $students->pluck('student_id'))
                ->get();
            foreach ($records as $r) {
                $attendanceMap[$r->student_id][$r->date->toDateString()] = $r->status;
            }
        }
        return view('attendance.register', compact('classes', 'sections', 'students', 'dates', 'attendanceMap'));
    }

    public function registerPdf(Request $request)
    {
        $classes  = Classes::active()->get();
        $sections = collect();
        $students = collect();
        $dates    = collect();
        $attendanceMap = [];
        if ($request->class_id && $request->month) {
            $sections = Section::where('class_id', $request->class_id)->get();
            [$yr, $mo] = explode('-', $request->month);
            $period = CarbonPeriod::create("$yr-$mo-01", "last day of $yr-$mo");
            $dates  = collect($period)->map(fn($d) => $d);
            $currentYear = AcademicYear::current();
            $query = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($request->section_id) $query->where('section_id', $request->section_id);
            if ($currentYear) $query->where('academic_year_id', $currentYear->id);
            $students = $query->get();
            $records = AttendanceRecord::where('class_id', $request->class_id)
                ->whereYear('date', $yr)->whereMonth('date', $mo)
                ->whereIn('student_id', $students->pluck('student_id'))->get();
            foreach ($records as $r) {
                $attendanceMap[$r->student_id][$r->date->toDateString()] = $r->status;
            }
        }
        $pdf = Pdf::setPaper('a3', 'landscape')->loadView('pdf.attendance-register', compact('students', 'dates', 'attendanceMap'));
        return $pdf->download('attendance-register.pdf');
    }

    public function chronicAbsentees(Request $request)
    {
        $classes     = Classes::active()->get();
        $currentYear = AcademicYear::current();
        $threshold   = (int)($request->threshold ?? 75);
        $students    = collect();

        if ($request->class_id || $request->all) {
            $totalDaysQuery = AttendanceRecord::select('class_id', DB::raw('COUNT(DISTINCT date) as total_days'))
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
                ->groupBy('class_id')->get()->keyBy('class_id');

            $students = AttendanceRecord::with(['student.currentEnrollment.class'])
                ->select('student_id', 'class_id', DB::raw('COUNT(*) as present_days'))
                ->where('status', 'present')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
                ->groupBy('student_id', 'class_id')
                ->get()
                ->filter(function ($row) use ($totalDaysQuery, $threshold) {
                    $total = $totalDaysQuery[$row->class_id]->total_days ?? 0;
                    return $total > 0 && ($row->present_days / $total * 100) < $threshold;
                })
                ->map(function ($row) use ($totalDaysQuery) {
                    $total = $totalDaysQuery[$row->class_id]->total_days ?? 1;
                    $row->total_days = $total;
                    $row->percentage = round($row->present_days / $total * 100, 1);
                    return $row;
                })
                ->sortBy('percentage');
        }

        return view('attendance.chronic-absentees', compact('classes', 'students', 'threshold', 'currentYear'));
    }

    public function dateWiseStrength(Request $request)
    {
        $currentYear = AcademicYear::current();
        $month       = $request->month ?? now()->format('Y-m');
        [$yr, $mo]   = explode('-', $month);

        $data = AttendanceRecord::select(
                'date',
                DB::raw("SUM(CASE WHEN status IN ('present','late','half_day') THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw('COUNT(*) as total')
            )
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->whereYear('date', $yr)
            ->whereMonth('date', $mo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('attendance.date-strength', compact('data', 'month', 'currentYear'));
    }

    public function classSummary(Request $request)
    {
        $currentYear = AcademicYear::current();
        $month       = $request->month ?? now()->format('Y-m');
        [$yr, $mo]   = explode('-', $month);

        $summary = AttendanceRecord::with('class')
            ->select(
                'class_id',
                DB::raw("SUM(CASE WHEN status IN ('present','late') THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw('COUNT(*) as total')
            )
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->whereYear('date', $yr)
            ->whereMonth('date', $mo)
            ->groupBy('class_id')
            ->get()
            ->map(function ($row) {
                $row->percentage = $row->total > 0 ? round($row->present / $row->total * 100, 1) : 0;
                return $row;
            })
            ->sortByDesc('percentage');

        return view('attendance.class-summary', compact('summary', 'month', 'currentYear'));
    }

    // ── Attendance Condonation ────────────────────────────

    public function condonation(Request $request)
    {
        $classes     = Classes::active()->get();
        $currentYear = AcademicYear::current();
        $students    = collect();

        if ($request->class_id) {
            $students = StudentEnrollment::with(['student', 'student.condonations' => function ($q) use ($currentYear) {
                $q->where('academic_year_id', $currentYear?->id);
            }])
                ->where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->get();
        }

        $recentCondonations = AttendanceCondonation::with(['student', 'condonedBy'])
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear?->id))
            ->latest()->limit(50)->get();

        return view('attendance.condonation', compact('classes', 'students', 'currentYear', 'recentCondonations'));
    }

    public function storeCondonation(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'days_condoned'=> 'required|integer|min:1|max:365',
            'reason'       => 'required|string|max:500',
        ]);
        $currentYear = AcademicYear::current();
        AttendanceCondonation::create([
            'student_id'       => $request->student_id,
            'academic_year_id' => $currentYear?->id,
            'days_condoned'    => $request->days_condoned,
            'reason'           => $request->reason,
            'condoned_by'      => Auth::id(),
            'condoned_on'      => today(),
        ]);
        return back()->with('success', $request->days_condoned . ' day(s) condoned successfully.');
    }

    // ── Subject-wise (period-wise) attendance report per student ──

    public function subjectAttendanceReport(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = collect();
        $report   = collect();
        $student  = null;

        if ($request->student_id) {
            $student  = \App\Models\Student::findOrFail($request->student_id);
            $year     = AcademicYear::current();
            $enrollment = StudentEnrollment::where('student_id', $student->id)->where('status', 'active')
                ->when($year, fn($q) => $q->where('academic_year_id', $year->id))->first();

            if ($enrollment) {
                $subjects = \App\Models\Timetable::where('class_id', $enrollment->class_id)
                    ->where('period_type', 'class')
                    ->whereNotNull('subject_id')
                    ->with('subject')
                    ->get()->unique('subject_id')->pluck('subject');

                $records = AttendanceRecord::where('student_id', $student->id)
                    ->when($request->from_date, fn($q) => $q->where('date', '>=', $request->from_date))
                    ->when($request->to_date,   fn($q) => $q->where('date', '<=', $request->to_date))
                    ->whereNotNull('period_data')
                    ->get();

                // Build per-subject totals from period_data
                $perSubject = [];
                foreach ($records as $rec) {
                    $periods = json_decode($rec->period_data, true) ?? [];
                    // period_data keys are period_number; match to timetable
                    $dayName = \Carbon\Carbon::parse($rec->date)->format('l');
                    $dayNum  = \Carbon\Carbon::parse($rec->date)->dayOfWeekIso;
                    $ttSlots = \App\Models\Timetable::where('class_id', $enrollment->class_id)
                        ->where('period_type', 'class')
                        ->where(fn($q) => $q->where('day_of_week', $dayNum)->orWhere('day', $dayName))
                        ->get()->keyBy('period_number');

                    foreach ($periods as $periodNum => $status) {
                        $subjectId = $ttSlots[$periodNum]?->subject_id ?? null;
                        if (!$subjectId) continue;
                        if (!isset($perSubject[$subjectId])) {
                            $perSubject[$subjectId] = ['total' => 0, 'present' => 0];
                        }
                        $perSubject[$subjectId]['total']++;
                        if (in_array($status, ['present', 'late'])) {
                            $perSubject[$subjectId]['present']++;
                        }
                    }
                }

                foreach ($subjects as $subj) {
                    $data = $perSubject[$subj->id] ?? ['total' => 0, 'present' => 0];
                    $report->push([
                        'subject'    => $subj->name,
                        'total'      => $data['total'],
                        'present'    => $data['present'],
                        'absent'     => $data['total'] - $data['present'],
                        'percentage' => $data['total'] > 0 ? round(($data['present'] / $data['total']) * 100, 1) : 0,
                    ]);
                }
            }
        }

        $students = collect();
        if ($request->class_id) {
            $year = AcademicYear::current();
            $students = StudentEnrollment::with('student')
                ->where('class_id', $request->class_id)->where('status', 'active')
                ->when($year, fn($q) => $q->where('academic_year_id', $year->id))->get();
        }

        return view('attendance.subject-report', compact('classes', 'students', 'student', 'report'));
    }

    public function teacherAttendanceReport(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $employees   = Employee::where('is_active', true)
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->orderBy('first_name')->get();

        $month   = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);

        $report = collect();

        if ($request->filled('action') || $request->filled('month')) {
            $period = \Carbon\CarbonPeriod::create("$yr-$mo-01", "last day of $yr-$mo");
            $workingDays = collect($period)->filter(fn($d) => !in_array($d->dayOfWeek, [0]))->count(); // exclude Sunday

            $records = StaffAttendance::whereYear('date', $yr)
                ->whereMonth('date', $mo)
                ->whereIn('employee_id', $employees->pluck('id'))
                ->get()
                ->groupBy('employee_id');

            foreach ($employees as $emp) {
                $empRecords = $records->get($emp->id, collect());
                $present    = $empRecords->whereIn('status', ['present'])->count();
                $halfDay    = $empRecords->where('status', 'half_day')->count();
                $onLeave    = $empRecords->where('status', 'on_leave')->count();
                $absent     = $empRecords->where('status', 'absent')->count();
                $effectivePresent = $present + ($halfDay * 0.5);
                $percentage = $workingDays > 0 ? round(($effectivePresent / $workingDays) * 100, 1) : 0;

                $report->push([
                    'employee'    => $emp,
                    'working'     => $workingDays,
                    'present'     => $present,
                    'half_day'    => $halfDay,
                    'on_leave'    => $onLeave,
                    'absent'      => $absent,
                    'unmarked'    => max(0, $workingDays - ($present + $halfDay + $onLeave + $absent)),
                    'percentage'  => $percentage,
                ]);
            }
        }

        return view('attendance.teacher-report', compact('departments', 'employees', 'report', 'month'));
    }

    // ── Late Arrival Tracking for Staff ──────────────────

    public function lateArrivalReport(Request $request)
    {
        $month       = $request->month ?? now()->format('Y-m');
        $departments = \App\Models\Department::orderBy('name')->get();

        [$yr, $mo] = explode('-', $month);
        $start = \Carbon\Carbon::create($yr, $mo, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth();

        $records = StaffAttendance::with('employee.department')
            ->where('is_late', true)
            ->whereBetween('date', [$start, $end])
            ->when($request->department_id, function ($q, $deptId) {
                $q->whereHas('employee', fn($eq) => $eq->where('department_id', $deptId));
            })
            ->orderBy('date')
            ->get();

        // Group by employee
        $byEmployee = $records->groupBy('employee_id')->map(function ($recs) {
            $emp = $recs->first()->employee;
            return [
                'employee'    => $emp,
                'late_count'  => $recs->count(),
                'total_mins'  => $recs->sum('late_minutes'),
                'dates'       => $recs->pluck('date'),
                'records'     => $recs,
            ];
        })->sortByDesc('late_count');

        $lateThreshold = \App\Models\SchoolSetting::get('staff_late_time', '09:30');

        return view('attendance.late-arrival', compact('records', 'byEmployee', 'month', 'departments', 'lateThreshold'));
    }
}

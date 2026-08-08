<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AttendanceRecord;
use App\Models\FeePayment;
use App\Models\StudentFeeCharge;
use App\Models\ExamMark;
use App\Models\Exam;
use App\Models\BookIssue;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::current();

        // ── Enrollment summary ────────────────────────────────
        $totalStudents = Student::where('status', 'active')->count();
        $classSummary  = DB::table('student_enrollments as se')
            ->join('classes as c', 'c.id', '=', 'se.class_id')
            ->where('se.status', 'active')
            ->when($year, fn($q) => $q->where('se.academic_year_id', $year->id))
            ->select('c.name as class_name', DB::raw('COUNT(*) as total'))
            ->groupBy('c.id', 'c.name', 'c.sort_order')
            ->orderBy('c.sort_order')
            ->get();

        // ── Fee collection (current year) ─────────────────────
        $totalCollected = FeePayment::where('is_cancelled', false)
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->sum('total_paid');

        $totalDemand = StudentFeeCharge::when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->sum('amount');

        $monthlyCollection = FeePayment::where('is_cancelled', false)
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->select(
                DB::raw("TO_CHAR(payment_date, 'YYYY-MM') as month"),
                DB::raw('SUM(total_paid) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // ── Attendance (today) ────────────────────────────────
        $todayPresent = AttendanceRecord::whereDate('date', today())
            ->whereIn('status', ['present', 'late', 'half_day'])->count();
        $todayAbsent  = AttendanceRecord::whereDate('date', today())
            ->where('status', 'absent')->count();
        $todayTotal   = $todayPresent + $todayAbsent;
        $todayPct     = $todayTotal > 0 ? round($todayPresent / $todayTotal * 100, 1) : 0;

        // Month-wise attendance for current year
        $attMonthly = AttendanceRecord::when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->select(
                DB::raw("TO_CHAR(date, 'YYYY-MM') as month"),
                DB::raw("SUM(CASE WHEN status IN ('present','late','half_day') THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month'   => $r->month,
                'pct'     => $r->total > 0 ? round($r->present / $r->total * 100, 1) : 0,
                'present' => $r->present,
                'absent'  => $r->absent,
            ]);

        // ── Exam results (latest published exam) ──────────────
        $latestExam  = Exam::where('is_published', true)
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->latest('start_date')->first();

        $examResults = collect();
        if ($latestExam) {
            $examResults = DB::table('exam_marks as em')
                ->join('exam_schedules as es', 'es.id', '=', 'em.exam_schedule_id')
                ->join('student_enrollments as se', function ($j) use ($latestExam) {
                    $j->on('se.student_id', '=', 'em.student_id')
                      ->where('se.status', 'active');
                    if ($latestExam->academic_year_id) {
                        $j->where('se.academic_year_id', $latestExam->academic_year_id);
                    }
                })
                ->join('classes as c', 'c.id', '=', 'se.class_id')
                ->where('es.exam_id', $latestExam->id)
                ->select(
                    'c.name as class_name', 'c.sort_order',
                    DB::raw('COUNT(DISTINCT em.student_id) as appeared'),
                    DB::raw('SUM(CASE WHEN em.is_absent=0 THEN 1 ELSE 0 END) as present_count'),
                    DB::raw('AVG(em.marks_obtained) as avg_marks')
                )
                ->groupBy('c.id', 'c.name', 'c.sort_order')
                ->orderBy('c.sort_order')
                ->get();
        }

        // ── Library ──────────────────────────────────────────
        $booksIssued  = BookIssue::where('status', 'issued')->count();
        $booksOverdue = BookIssue::where('status', 'issued')
            ->where('due_date', '<', today())->count();

        // ── HR ───────────────────────────────────────────────
        $totalStaff   = Employee::where('is_active', true)->count();

        return view('reports.index', compact(
            'year', 'totalStudents', 'classSummary',
            'totalCollected', 'totalDemand', 'monthlyCollection',
            'todayPresent', 'todayAbsent', 'todayTotal', 'todayPct',
            'attMonthly', 'latestExam', 'examResults',
            'booksIssued', 'booksOverdue', 'totalStaff'
        ));
    }

    public function feeReport(Request $request)
    {
        $year    = AcademicYear::current();
        $classes = Classes::active()->get();

        $yearStart = $year ? Carbon::parse($year->start_date) : now()->startOfYear();
        $from = $request->from ? Carbon::parse($request->from) : $yearStart;
        $to   = $request->to   ? Carbon::parse($request->to)   : now();

        $classwise = DB::table('fee_payments as fp')
            ->join('student_enrollments as se', function ($j) use ($year) {
                $j->on('se.student_id', '=', 'fp.student_id')
                  ->where('se.status', 'active')
                  ->when($year, fn($j2) => $j2->where('se.academic_year_id', $year->id));
            })
            ->join('classes as c', 'c.id', '=', 'se.class_id')
            ->where('fp.is_cancelled', false)
            ->whereBetween('fp.payment_date', [$from->toDateString(), $to->toDateString()])
            ->when($request->class_id, fn($q, $v) => $q->where('c.id', $v))
            ->select('c.name as class_name', 'c.sort_order',
                DB::raw('COUNT(DISTINCT fp.student_id) as payers'),
                DB::raw('SUM(fp.total_paid) as collected'))
            ->groupBy('c.id', 'c.name', 'c.sort_order')
            ->orderBy('c.sort_order')
            ->get();

        // Demand: total fee charges per class for current year
        $demandByClass = DB::table('student_fee_charges as sfc')
            ->join('student_enrollments as se', function ($j) use ($year) {
                $j->on('se.student_id', '=', 'sfc.student_id')
                  ->where('se.status', 'active')
                  ->when($year, fn($j2) => $j2->where('se.academic_year_id', $year->id));
            })
            ->join('classes as c', 'c.id', '=', 'se.class_id')
            ->where('sfc.is_active', true)
            ->when($year, fn($q) => $q->where('sfc.academic_year_id', $year->id))
            ->select('c.name as class_name', DB::raw('SUM(sfc.amount) as demand'))
            ->groupBy('c.name')
            ->pluck('demand', 'class_name');

        $classwise = $classwise->map(function ($row) use ($demandByClass) {
            $row->demand      = $demandByClass[$row->class_name] ?? 0;
            $row->outstanding = max(0, $row->demand - $row->collected);
            return $row;
        });

        return view('reports.fee', compact('classwise', 'from', 'to', 'classes', 'year'));
    }

    public function attendanceReport(Request $request)
    {
        $year    = AcademicYear::current();
        $month   = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);

        $classes   = Classes::active()->orderBy('sort_order')->get();
        $classwise = AttendanceRecord::with('class')
            ->select(
                'class_id',
                DB::raw("SUM(CASE WHEN status IN ('present','late','half_day') THEN 1 ELSE 0 END) as present"),
                DB::raw("SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent"),
                DB::raw('COUNT(*) as total')
            )
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->whereYear('date', $yr)->whereMonth('date', $mo)
            ->groupBy('class_id')
            ->get()
            ->map(fn($r) => [
                'class'   => $r->class?->name ?? 'Unknown',
                'present' => $r->present,
                'absent'  => $r->absent,
                'total'   => $r->total,
                'pct'     => $r->total > 0 ? round($r->present / $r->total * 100, 1) : 0,
            ])
            ->sortByDesc('pct');

        return view('reports.attendance', compact('classwise', 'month', 'year', 'classes'));
    }

    public function feeReportExcel(Request $request)
    {
        $year    = AcademicYear::current();
        $yearStart = $year ? Carbon::parse($year->start_date) : now()->startOfYear();
        $from = $request->from ? Carbon::parse($request->from) : $yearStart;
        $to   = $request->to   ? Carbon::parse($request->to)   : now();

        $rows = DB::table('fee_payments as fp')
            ->join('student_enrollments as se', fn($j) => $j->on('se.student_id','=','fp.student_id')->where('se.status','active'))
            ->join('classes as c', 'c.id', '=', 'se.class_id')
            ->where('fp.is_cancelled', false)
            ->whereBetween('fp.payment_date', [$from->toDateString(), $to->toDateString()])
            ->select('c.name as class_name', DB::raw('COUNT(DISTINCT fp.student_id) as payers'), DB::raw('SUM(fp.total_paid) as collected'))
            ->groupBy('c.id','c.name','c.sort_order')->orderBy('c.sort_order')->get();

        $headers = ['Class', 'Payers', 'Collected (₹)'];
        $data    = $rows->map(fn($r) => [$r->class_name, $r->payers, $r->collected])->toArray();

        return Excel::download(new \App\Exports\ArrayExport($data, $headers), 'fee-report-' . $from->format('Ymd') . '-to-' . $to->format('Ymd') . '.xlsx');
    }

    public function attendanceReportExcel(Request $request)
    {
        $year  = AcademicYear::current();
        $month = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);

        $rows = AttendanceRecord::with('class')
            ->select('class_id', DB::raw("SUM(CASE WHEN status IN ('present','late','half_day') THEN 1 ELSE 0 END) as present"), DB::raw("SUM(CASE WHEN status='absent' THEN 1 ELSE 0 END) as absent"), DB::raw('COUNT(*) as total'))
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->whereYear('date', $yr)->whereMonth('date', $mo)
            ->groupBy('class_id')->get();

        $headers = ['Class', 'Present', 'Absent', 'Total', 'Attendance %'];
        $data    = $rows->map(fn($r) => [$r->class?->name, $r->present, $r->absent, $r->total, $r->total > 0 ? round($r->present/$r->total*100,1) : 0])->toArray();

        return Excel::download(new \App\Exports\ArrayExport($data, $headers), 'attendance-report-' . $month . '.xlsx');
    }
}
